# Object caching in the academyAfrica theme

All custom object caches live in a single group, **`academy_africa`**, and are
accessed exclusively through the helper in
[`includes/utils/cache.php`](includes/utils/cache.php)
(`AcademyAfrica\Theme\Cache\Cache`). Do **not** call `wp_cache_*()` directly for
theme caches — always go through the helper so keys stay versioned and
invalidation stays reliable.

## Why the helper exists

Invalidating "everything in a group" needs `wp_cache_flush_group()`, which many
object-cache backends (most non-Redis-Object-Cache-Pro setups) do not support.
To invalidate reliably on **every** backend, the helper namespaces each key with
a version counter:

```
actual key = "<logical key>:v<N>"      where N = academy_africa_cache_version
```

- `Cache::get()/set()/delete()` transparently append `:v<N>`.
- `Cache::flush()` bumps `N`. Every previously written key becomes unreachable,
  so reads miss and regenerate — no group-flush support required.
- When the backend *does* support group flushing (checked via
  `wp_cache_supports('flush_group')`), `Cache::flush()` also flushes the group
  to reclaim the stranded entries immediately.

> Historical note: before this change the fallback bumped a version key that was
> never part of any cache key, so on backends without `flush_group` **nothing was
> invalidated** until TTL expiry (audit findings 27 & 28).

## Cache inventory

Legend — **Scope**: `content` = user-agnostic, invalidated by version bump;
`per-user` = keyed by user, invalidated by targeted delete + version bump.

| Logical key | Stores | TTL | Scope | Owner (set site) |
|---|---|---|---|---|
| `academy_organizations` | All published organizations (id, title, excerpt) | 1 h | content | `CoursesFunctions::getOrganizations()` |
| `academy_all_learning_paths` | All published learning paths (id, title, excerpt) | 1 h | content | `CoursesFunctions::getAllLearningPaths()` |
| `academy_learning_paths_{md5}` | Paginated learning paths + nested courses + count | 1 w | content | `CoursesFunctions::getLearningPaths()` |
| `academy_instructors` | Instructor list from distinct course authors | 1 h | content | `CoursesFunctions::getAllInstructors()` |
| `course_lessons_{course_id}` | Lesson list for a course (count + first-lesson URL) | 1 h | content | `learndash/ld30/{course,lesson,topic,quiz}.php` |
| `course_author_data_{course_id}` | Per-course author profiles (name, avatar, socials) | 1 h | content | `learndash/ld30/course.php` |
| `course_orgs_data_{course_id}` | Per-course organization data (title, socials, site) | 1 h | content | `learndash/ld30/course.php` |
| `course_content_html_{course_id}` | Rendered `[course_content]` for **anonymous/unenrolled** visitors | 1 h | content | `learndash/ld30/course.php` |
| `{course_id}_students_count` | Enrolled-student count (fallback path only) | 1 h | content | `academyafrica_count_students()` (`includes/functions/learndash.php`) |
| `verified_users_count` | Count of verified users (hero widget) | 1 h | content | `Academy_Africa_Hero` (`includes/widgets/hero.php`) |
| `published_courses_count` | Count of published courses (hero widget) | 1 h | content | `Academy_Africa_Hero` |
| `published_events_count` | Count of published events (hero widget) | 1 h | content | `Academy_Africa_Hero` |
| `course_status_u{uid}_c{cid}` | Per-user LearnDash course status string | 5 m | per-user | `learndash/ld30/{course,lesson,topic}.php` |
| `course_progress_u{uid}_c{cid}` | Rendered `[learndash_course_progress]` per user | 5 m | per-user | `learndash/ld30/{course,lesson,topic,quiz}.php` |
| `course_content_u{uid}_c{cid}` | Rendered `[course_content]` per enrolled user | 5 m | per-user | `learndash/ld30/{lesson,topic,quiz}.php` |
| `academy_africa_cache_version` | Group version counter (internal) | 1 w | — | `Cache` helper |

## Invalidation triggers

All hooks are registered centrally in `includes/utils/cache.php`.

### Content invalidation → `Cache::flush()` (bumps the version)

| Hook | Fires when |
|---|---|
| `save_post_ac-learning-path` | A learning path is created/updated |
| `save_post_sfwd-courses` | A course is created/updated |
| `save_post_sfwd-lessons` | A lesson is created/updated (affects `course_lessons_*`, curricula) |
| `save_post_sfwd-topic` / `save_post_sfwd-quiz` | A topic/quiz is created/updated |
| `save_post_ac-organization` | An organization is edited |
| `save_post_event` | An event is created/updated (hero events count) |
| `delete_post` | Any of the above post types is deleted |
| `profile_update` | A user profile changes (author/instructor data) |

Autosaves and revisions are skipped. Invalidation is intentionally **global**:
one counter covers the whole group, so any content save clears every content
cache. Writes are rare (admin edits), so the re-warm cost is negligible and
correctness is guaranteed.

### Per-user invalidation → targeted `Cache::delete()`

| Hook | Clears |
|---|---|
| `learndash_update_course_access` (enrollment / access change) | `course_status_u{uid}_c{cid}`, `course_progress_…`, `course_content_…`, `{cid}_students_count` |
| `learndash_lesson_completed` / `…_topic_completed` / `…_quiz_completed` | `course_status_…`, `course_progress_…`, `course_content_…` for that user+course |
| `added_user_meta` / `updated_user_meta` / `deleted_user_meta` (key `is_verified`) | `verified_users_count` |

## Tests

`tests/cache-invalidation-test.php` stubs the WordPress cache API and verifies
versioned keys, the group-flush path, the **no-group-flush fallback** (the
original bug), and every targeted-delete hook. Run:

```bash
php wp-content/themes/academyAfrica/tests/cache-invalidation-test.php
```

Exit code `0` = all pass.
