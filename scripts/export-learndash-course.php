<?php
/**
 * export-learndash-course.php
 * -----------------------------------------------------------------------------
 * Export a single LearnDash course + ALL of its child steps (lessons, topics,
 * quizzes and quiz questions) together with every referenced media asset, into
 * one self-contained .zip.
 *
 * It bootstraps WordPress, so it connects to whatever site/DB that WordPress is
 * configured for. Point it at PROD (run it on the WP Engine server) to export
 * from prod — no separate DB credentials are needed; it uses the site wp-config.
 *
 * WHAT'S IN THE ZIP
 *   course-export.json   posts (course, lessons, topics, quizzes, questions,
 *                        and the attachment posts), postmeta, taxonomy terms +
 *                        relationships, and the WP-Pro-Quiz tables (master,
 *                        question, category) for the course's quizzes.
 *   assets/uploads/...   the actual media files (featured images, inline images,
 *                        size variants, lesson materials/downloads), preserving
 *                        their wp-content/uploads-relative path.
 *   manifest.json        summary + counts.
 *
 * USAGE (standalone, from anywhere; auto-finds wp-load.php by walking up):
 *   php export-learndash-course.php <course_id> [output_dir] [--wp=/path/wp-load.php]
 *
 * On WP Engine (prod):
 *   scp -i ~/.ssh/wpengine_ed25519 scripts/export-learndash-course.php \
 *       academyafrica1@academyafrica1.ssh.wpengine.net:~/export-course.php
 *   ssh -i ~/.ssh/wpengine_ed25519 academyafrica1@academyafrica1.ssh.wpengine.net \
 *       'cd ~/sites/academyafrica1 && php ~/export-course.php 186645 ~/'
 *   # then scp the printed .zip path back down.
 *
 * Via wp-cli (WordPress already loaded):
 *   wp eval-file export-learndash-course.php 186645 /tmp
 * -----------------------------------------------------------------------------
 */

// ---------------------------------------------------------------------------
// Argument parsing (works both under plain `php` and `wp eval-file`).
// ---------------------------------------------------------------------------
$IS_WP_CLI = defined('WP_CLI') && WP_CLI;
$wp_load_override = '';

if ($IS_WP_CLI) {
    // `wp eval-file file.php a b` exposes positional args in $args.
    $positional = isset($args) && is_array($args) ? $args : array();
} else {
    $positional = array();
    foreach (array_slice($argv, 1) as $a) {
        if (strpos($a, '--wp=') === 0) { $wp_load_override = substr($a, 5); }
        elseif (strpos($a, '--') === 0) { /* ignore unknown flags */ }
        else { $positional[] = $a; }
    }
}
$course_id = isset($positional[0]) ? (int) $positional[0] : 0;
$out_dir   = isset($positional[1]) && $positional[1] !== '' ? rtrim($positional[1], '/') : getcwd();

function fail($msg) {
    if (defined('WP_CLI') && WP_CLI) { WP_CLI::error($msg); }
    fwrite(STDERR, "ERROR: $msg\n");
    exit(1);
}
function info($msg) {
    if (defined('WP_CLI') && WP_CLI) { WP_CLI::log($msg); return; }
    fwrite(STDOUT, $msg . "\n");
}

if (!$course_id) {
    fail("Usage: php export-learndash-course.php <course_id> [output_dir] [--wp=/path/wp-load.php]");
}

// ---------------------------------------------------------------------------
// Bootstrap WordPress if we're not already inside it.
// ---------------------------------------------------------------------------
if (!defined('ABSPATH')) {
    $wp_load = $wp_load_override;
    if (!$wp_load) {
        foreach (array(getcwd(), __DIR__) as $start) {
            $dir = $start;
            for ($i = 0; $i < 10; $i++) {
                if (file_exists($dir . '/wp-load.php')) { $wp_load = $dir . '/wp-load.php'; break 2; }
                $parent = dirname($dir);
                if ($parent === $dir) { break; }
                $dir = $parent;
            }
        }
    }
    if (!$wp_load || !file_exists($wp_load)) {
        fail("Could not locate wp-load.php. Run from the WordPress root or pass --wp=/path/to/wp-load.php");
    }
    define('WP_USE_THEMES', false);
    require $wp_load;
}

global $wpdb;

// ---------------------------------------------------------------------------
// Validate the course.
// ---------------------------------------------------------------------------
$course = get_post($course_id);
if (!$course) { fail("No post with ID $course_id found."); }
if ($course->post_type !== 'sfwd-courses') {
    fail("Post $course_id is a '{$course->post_type}', not a LearnDash course (sfwd-courses).");
}
info("Course: #{$course_id} — " . $course->post_title);

// ---------------------------------------------------------------------------
// 1. Resolve the full step tree (lessons, topics, quizzes).
// ---------------------------------------------------------------------------
$step_ids = array();
if (function_exists('learndash_get_course_steps')) {
    $step_ids = learndash_get_course_steps($course_id, array('sfwd-lessons', 'sfwd-topic', 'sfwd-quiz'));
}
$step_ids = array_map('intval', (array) $step_ids);

// Fallback: if the API returned nothing, gather any post linked to this course.
if (empty($step_ids)) {
    $linked = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key='course_id' AND meta_value=%d",
        $course_id
    ));
    $step_ids = array_map('intval', (array) $linked);
}

$content_ids = array_values(array_unique(array_merge(array($course_id), $step_ids)));
$quiz_ids = array_values(array_filter($content_ids, function ($id) {
    return get_post_type($id) === 'sfwd-quiz';
}));

// ---------------------------------------------------------------------------
// 2. Quiz questions — both the modern 'sfwd-question' post type and the
//    legacy WP-Pro-Quiz tables.
// ---------------------------------------------------------------------------
$question_post_ids = array();
if (!empty($quiz_ids)) {
    $in = implode(',', array_map('intval', $quiz_ids));
    $rows = $wpdb->get_col(
        "SELECT DISTINCT pm.post_id
           FROM {$wpdb->postmeta} pm
           JOIN {$wpdb->posts} p ON p.ID = pm.post_id AND p.post_type = 'sfwd-question'
          WHERE pm.meta_key = 'quiz_id' AND pm.meta_value IN ($in)"
    );
    $question_post_ids = array_map('intval', (array) $rows);
}

// Map each quiz post to its WP-Pro-Quiz master id.
$pro_quiz_ids = array();
foreach ($quiz_ids as $qid) {
    $pro = 0;
    if (function_exists('learndash_get_setting')) {
        $pro = (int) learndash_get_setting($qid, 'quiz_pro');
    }
    if (!$pro) { $pro = (int) get_post_meta($qid, 'quiz_pro_id', true); }
    if ($pro) { $pro_quiz_ids[$qid] = $pro; }
}

// ---------------------------------------------------------------------------
// 3. Collect all post IDs whose DB rows we export (content + questions), then
//    discover attachments referenced by any of them.
// ---------------------------------------------------------------------------
$all_content_ids = array_values(array_unique(array_merge($content_ids, $question_post_ids)));

$upload = wp_get_upload_dir();
$base_dir = untrailingslashit($upload['basedir']);
$base_url = untrailingslashit($upload['baseurl']);

$attachment_ids = array();
$asset_files    = array();   // absolute paths -> copied into the zip
$missing_files  = array();   // referenced by an attachment but not present locally

// $expected=true marks files we KNOW should exist (attachment originals + size
// variants); if such a file is absent it's recorded as missing (useful when the
// DB is live-from-prod but assets are read from a local mirror).
$add_file = function ($abs, $expected = false) use (&$asset_files, &$missing_files, $base_dir) {
    if (!$abs || strpos($abs, $base_dir) !== 0) { return; }
    if (is_file($abs)) { $asset_files[$abs] = true; }
    elseif ($expected)  { $missing_files[$abs] = true; }
};
$add_attachment = function ($att_id) use (&$attachment_ids, &$add_file) {
    $att_id = (int) $att_id;
    if (!$att_id || get_post_type($att_id) !== 'attachment') { return; }
    $attachment_ids[$att_id] = true;
    $main = get_attached_file($att_id);
    if ($main) {
        $add_file($main, true);
        // include intermediate size files that live alongside the original
        $meta = wp_get_attachment_metadata($att_id);
        if (!empty($meta['sizes']) && is_array($meta['sizes'])) {
            $dir = dirname($main);
            foreach ($meta['sizes'] as $s) {
                if (!empty($s['file'])) { $add_file($dir . '/' . $s['file'], true); }
            }
        }
    }
};

foreach ($all_content_ids as $pid) {
    // featured image
    $add_attachment(get_post_thumbnail_id($pid));
    // attachments whose parent is this post
    $children = get_children(array('post_parent' => $pid, 'post_type' => 'attachment', 'numberposts' => -1));
    foreach ((array) $children as $child) { $add_attachment($child->ID); }
    // inline images referenced by class="wp-image-123"
    $post = get_post($pid);
    if ($post && preg_match_all('/wp-image-(\d+)/', (string) $post->post_content, $m)) {
        foreach ($m[1] as $iid) { $add_attachment($iid); }
    }
}

// Scan post_content + all postmeta of the exported posts for any URL under the
// uploads base_url, and copy those files too (covers lesson materials, download
// buttons, background images, galleries, etc. that aren't formal attachments).
$scan_ids = array_merge($all_content_ids, array_keys($attachment_ids));
if (!empty($scan_ids)) {
    $in = implode(',', array_map('intval', array_unique($scan_ids)));
    $texts = array();
    foreach ($wpdb->get_col("SELECT post_content FROM {$wpdb->posts} WHERE ID IN ($in)") as $t) { $texts[] = $t; }
    foreach ($wpdb->get_col("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id IN ($in)") as $t) { $texts[] = $t; }
    $needle = preg_quote($base_url, '#');
    foreach ($texts as $t) {
        if ($t === '' || $t === null) { continue; }
        if (preg_match_all('#' . $needle . '/([\w./\- %]+?\.[A-Za-z0-9]{2,5})#', $t, $mm)) {
            foreach ($mm[1] as $rel) {
                $rel = rawurldecode(trim($rel));
                $add_file($base_dir . '/' . ltrim($rel, '/'));
            }
        }
    }
}

$attachment_ids = array_map('intval', array_keys($attachment_ids));

// ---------------------------------------------------------------------------
// 4. Pull DB rows.
// ---------------------------------------------------------------------------
$export_post_ids = array_values(array_unique(array_merge($all_content_ids, $attachment_ids)));
$in_posts = implode(',', array_map('intval', $export_post_ids));

$posts    = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE ID IN ($in_posts)", ARRAY_A);
$postmeta = $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE post_id IN ($in_posts)", ARRAY_A);

// Taxonomy: term relationships for the exported posts + their terms.
$term_rels = $wpdb->get_results("SELECT * FROM {$wpdb->term_relationships} WHERE object_id IN ($in_posts)", ARRAY_A);
$tt_ids = array_unique(array_map(function ($r) { return (int) $r['term_taxonomy_id']; }, $term_rels));
$terms = array(); $term_taxonomy = array();
if (!empty($tt_ids)) {
    $in_tt = implode(',', $tt_ids);
    $term_taxonomy = $wpdb->get_results("SELECT * FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id IN ($in_tt)", ARRAY_A);
    $term_ids = array_unique(array_map(function ($r) { return (int) $r['term_id']; }, $term_taxonomy));
    if (!empty($term_ids)) {
        $in_terms = implode(',', $term_ids);
        $terms = $wpdb->get_results("SELECT * FROM {$wpdb->terms} WHERE term_id IN ($in_terms)", ARRAY_A);
    }
}

// WP-Pro-Quiz tables (prefix is e.g. wp_wp_pro_quiz_*).
$pq = $wpdb->prefix . 'wp_pro_quiz_';
$proquiz = array('master' => array(), 'question' => array(), 'category' => array());
$pro_ids = array_values(array_unique(array_map('intval', $pro_quiz_ids)));
if (!empty($pro_ids)) {
    $in_pro = implode(',', $pro_ids);
    if ($wpdb->get_var("SHOW TABLES LIKE '{$pq}master'")) {
        $proquiz['master'] = $wpdb->get_results("SELECT * FROM {$pq}master WHERE id IN ($in_pro)", ARRAY_A);
    }
    if ($wpdb->get_var("SHOW TABLES LIKE '{$pq}question'")) {
        $proquiz['question'] = $wpdb->get_results("SELECT * FROM {$pq}question WHERE quiz_id IN ($in_pro)", ARRAY_A);
    }
    if ($wpdb->get_var("SHOW TABLES LIKE '{$pq}category'")) {
        // categories are global + small; export them all so question refs resolve
        $proquiz['category'] = $wpdb->get_results("SELECT * FROM {$pq}category", ARRAY_A);
    }
}

// ---------------------------------------------------------------------------
// 5. Assemble the export payload + manifest.
// ---------------------------------------------------------------------------
$export = array(
    'format_version' => 1,
    'exported_at'    => gmdate('c'),
    'source'         => array(
        'site_url'      => get_site_url(),
        'home_url'      => get_home_url(),
        'db_prefix'     => $wpdb->prefix,
        'uploads_url'   => $base_url,
        'ld_version'    => defined('LEARNDASH_VERSION') ? LEARNDASH_VERSION : null,
    ),
    'course_id'      => $course_id,
    'ids'            => array(
        'course'    => $course_id,
        'steps'     => $step_ids,
        'quizzes'   => $quiz_ids,
        'questions' => $question_post_ids,
        'attachments' => $attachment_ids,
        'pro_quiz_map' => $pro_quiz_ids, // quiz_post_id => pro_quiz_master_id
    ),
    'posts'          => $posts,
    'postmeta'       => $postmeta,
    'terms'          => $terms,
    'term_taxonomy'  => $term_taxonomy,
    'term_relationships' => $term_rels,
    'proquiz'        => $proquiz,
);

$counts = array(
    'posts'       => count($posts),
    'steps'       => count($step_ids),
    'quizzes'     => count($quiz_ids),
    'questions'   => count($question_post_ids),
    'attachments' => count($attachment_ids),
    'asset_files' => count($asset_files),
    'missing_asset_files' => count($missing_files),
    'proquiz_master'   => count($proquiz['master']),
    'proquiz_question' => count($proquiz['question']),
);
$missing_rel = array_map(function ($abs) use ($base_dir) {
    return ltrim(substr($abs, strlen($base_dir)), '/');
}, array_keys($missing_files));
sort($missing_rel);
$manifest = array(
    'course_id'    => $course_id,
    'course_title' => $course->post_title,
    'course_slug'  => $course->post_name,
    'exported_at'  => gmdate('c'),
    'source_site'  => get_site_url(),
    'counts'       => $counts,
    'missing_asset_files' => $missing_rel, // expected attachment files not found locally
);

info("Collected: " . json_encode($counts));
if (!empty($missing_rel)) {
    info("WARNING: " . count($missing_rel) . " referenced asset file(s) were NOT found locally "
        . "(DB is likely newer than this machine's uploads mirror). They are listed in manifest.json "
        . "under 'missing_asset_files'. Re-sync uploads or run this on the server for a complete export.");
}

// ---------------------------------------------------------------------------
// 6. Write the zip.
// ---------------------------------------------------------------------------
if (!is_dir($out_dir) && !@mkdir($out_dir, 0775, true)) {
    fail("Output directory not writable: $out_dir");
}
$slug = $course->post_name ? $course->post_name : ('course-' . $course_id);
$zip_path = $out_dir . '/ld-course-' . $course_id . '-' . $slug . '-' . gmdate('Ymd-His') . '.zip';

$json = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
$manifest_json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        fail("Cannot create zip at $zip_path");
    }
    $zip->addFromString('course-export.json', $json);
    $zip->addFromString('manifest.json', $manifest_json);
    foreach (array_keys($asset_files) as $abs) {
        $rel = ltrim(substr($abs, strlen($base_dir)), '/');
        $zip->addFile($abs, 'assets/uploads/' . $rel);
    }
    $zip->close();
} else {
    // Fallback: tar.gz via Phar when the zip extension is unavailable.
    info("ZipArchive not available; falling back to .tar.gz");
    $zip_path = preg_replace('/\.zip$/', '.tar.gz', $zip_path);
    $tar_path = preg_replace('/\.gz$/', '', $zip_path);
    @unlink($tar_path); @unlink($zip_path);
    $phar = new PharData($tar_path);
    $phar->addFromString('course-export.json', $json);
    $phar->addFromString('manifest.json', $manifest_json);
    foreach (array_keys($asset_files) as $abs) {
        $rel = ltrim(substr($abs, strlen($base_dir)), '/');
        $phar->addFile($abs, 'assets/uploads/' . $rel);
    }
    $phar->compress(Phar::GZ);
    unset($phar);
    @unlink($tar_path);
}

if (!file_exists($zip_path)) { fail("Archive was not created."); }
$size = size_format(filesize($zip_path), 1);
info("DONE. Archive: $zip_path ($size)");
if (defined('WP_CLI') && WP_CLI) { WP_CLI::success("Exported course #$course_id -> $zip_path ($size)"); }
