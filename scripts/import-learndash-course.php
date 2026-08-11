<?php
/**
 * import-learndash-course.php
 * -----------------------------------------------------------------------------
 * Import a LearnDash course archive produced by export-learndash-course.php into
 * THIS WordPress site (course + lessons + topics + quizzes + questions + media).
 *
 * Nothing existing is modified or deleted: the course is created fresh with new
 * post IDs, and every internal reference (course/lesson/topic/quiz/question IDs,
 * WP-Pro-Quiz IDs, featured images, inline images, media URLs) is rewritten to
 * point at the newly created content on this site.
 *
 * USAGE
 *   php import-learndash-course.php                       # asks for the zip path
 *   php import-learndash-course.php /path/to/course.zip
 *   php import-learndash-course.php course.zip --dry-run  # report only, no writes
 *
 * OPTIONS
 *   --dry-run            Validate + report what would be imported. No changes.
 *   --status=draft       Import course/lessons/topics/quizzes as drafts
 *                        (default: keep the status they had on the source site).
 *   --author=<id|login>  Owner of the imported posts (default: the first admin).
 *   --duplicate-media    Always create new media, even when an identical upload
 *                        path already exists here (default: reuse it).
 *   --overwrite-media    Overwrite upload files that already exist on disk
 *                        (default: keep this site's existing file).
 *   --yes, -y            Don't ask for confirmation.
 *   --wp=/path/wp-load.php   Point at WordPress explicitly (auto-detected
 *                        otherwise by walking up from the current directory).
 *
 * Run it from the WordPress root of the destination site:
 *   cd /path/to/wordpress && php import-learndash-course.php ~/course.zip
 *
 * On WP Engine:
 *   scp -i ~/.ssh/<key> import-learndash-course.php <install>@<install>.ssh.wpengine.net:~/
 *   scp -i ~/.ssh/<key> ld-course-*.zip             <install>@<install>.ssh.wpengine.net:~/
 *   ssh -i ~/.ssh/<key> <install>@<install>.ssh.wpengine.net \
 *       'cd ~/sites/<install> && php ~/import-learndash-course.php ~/ld-course-*.zip -y'
 *
 * Via wp-cli (WordPress already loaded):
 *   wp eval-file import-learndash-course.php /path/to/course.zip
 *
 * REQUIREMENTS
 *   PHP 7.0+ with the zip extension, LearnDash (sfwd-lms) active on this site,
 *   and a writable wp-content/uploads.
 * -----------------------------------------------------------------------------
 */

// ---------------------------------------------------------------------------
// Arguments (works both under plain `php` and `wp eval-file`).
// ---------------------------------------------------------------------------
$IS_WP_CLI = defined('WP_CLI') && WP_CLI;

@ini_set('memory_limit', '2048M');
@set_time_limit(0);

// State shared with the helper functions below. Bound through $GLOBALS so the
// script also works under `wp eval-file`, which runs it inside a function.
$GLOBALS['LCI'] = array('warnings' => array());
$id_map       = &$GLOBALS['LCI']['id_map'];        // source post ID => new post ID
$url_pairs    = &$GLOBALS['LCI']['url_pairs'];
$meta_by_post = &$GLOBALS['LCI']['meta_by_post'];
$author_id    = &$GLOBALS['LCI']['author_id'];
$id_map = array(); $url_pairs = array(); $meta_by_post = array(); $author_id = 0;

$opt = array(
    'zip'             => '',
    'wp'              => '',
    'dry_run'         => false,
    'status'          => 'keep',
    'author'          => '',
    'duplicate_media' => false,
    'overwrite_media' => false,
    'yes'             => false,
);

$raw_args = $IS_WP_CLI
    ? (isset($args) && is_array($args) ? $args : array())
    : array_slice($argv, 1);

foreach ($raw_args as $a) {
    if (strpos($a, '--wp=') === 0)                 { $opt['wp'] = substr($a, 5); }
    elseif ($a === '--dry-run')                    { $opt['dry_run'] = true; }
    elseif (strpos($a, '--status=') === 0)         { $opt['status'] = strtolower(substr($a, 9)); }
    elseif (strpos($a, '--author=') === 0)         { $opt['author'] = substr($a, 9); }
    elseif ($a === '--duplicate-media')            { $opt['duplicate_media'] = true; }
    elseif ($a === '--overwrite-media')            { $opt['overwrite_media'] = true; }
    elseif ($a === '--yes' || $a === '-y')         { $opt['yes'] = true; }
    elseif (strpos($a, '--') === 0)                { /* ignore unknown flags */ }
    elseif ($opt['zip'] === '')                    { $opt['zip'] = $a; }
}

function lci_out($msg) {
    if (defined('WP_CLI') && WP_CLI) { WP_CLI::log($msg); return; }
    fwrite(STDOUT, $msg . "\n");
}
function lci_warn($msg) {
    $GLOBALS['LCI']['warnings'][] = $msg;
    lci_out('  ! ' . $msg);
}
function lci_fail($msg) {
    if (defined('WP_CLI') && WP_CLI) { WP_CLI::error($msg); }
    fwrite(STDERR, "\nERROR: $msg\n");
    exit(1);
}
function lci_prompt($question, $default = '') {
    if (!defined('STDIN')) { return $default; }
    fwrite(STDOUT, $question);
    $line = fgets(STDIN);
    if ($line === false) { return $default; }
    $line = trim($line);
    // Terminals quote/escape drag-and-dropped paths.
    $line = trim($line, "'\"");
    $line = str_replace('\\ ', ' ', $line);
    return $line === '' ? $default : $line;
}

// Ask for the archive if it wasn't given on the command line.
if ($opt['zip'] === '') {
    lci_out('LearnDash course importer');
    $opt['zip'] = lci_prompt('Path to the course .zip file: ');
}
if ($opt['zip'] === '') {
    lci_fail("No archive given. Usage: php import-learndash-course.php <course.zip> [--dry-run]");
}
if (strpos($opt['zip'], '~/') === 0) {
    $home = getenv('HOME');
    if ($home) { $opt['zip'] = $home . substr($opt['zip'], 1); }
}
$zip_path = realpath($opt['zip']);
if (!$zip_path || !is_file($zip_path)) {
    lci_fail("Archive not found: {$opt['zip']}");
}
if (!preg_match('/\.zip$/i', $zip_path)) {
    lci_fail("Expected a .zip produced by export-learndash-course.php, got: " . basename($zip_path));
}
if (!in_array($opt['status'], array('keep', 'draft', 'publish', 'private'), true)) {
    lci_fail("--status must be one of: keep, draft, publish, private");
}

// ---------------------------------------------------------------------------
// Bootstrap WordPress if we're not already inside it.
// ---------------------------------------------------------------------------
if (!defined('ABSPATH')) {
    $wp_load = $opt['wp'];
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
        lci_fail("Could not locate wp-load.php. Run this from the WordPress root, or pass --wp=/path/to/wp-load.php");
    }
    define('WP_USE_THEMES', false);
    require $wp_load;
}

global $wpdb;

// ---------------------------------------------------------------------------
// Pre-flight checks.
// ---------------------------------------------------------------------------
if (!class_exists('ZipArchive')) {
    lci_fail("PHP's zip extension is required to read the archive.");
}
if (!post_type_exists('sfwd-courses')) {
    lci_fail("LearnDash (sfwd-lms) does not appear to be active on this site — activate it first.");
}

$upload   = wp_get_upload_dir();
$base_dir = untrailingslashit($upload['basedir']);
$base_url = untrailingslashit($upload['baseurl']);
if (!empty($upload['error'])) { lci_fail("Uploads directory error: " . $upload['error']); }
if (!is_dir($base_dir) || !is_writable($base_dir)) {
    lci_fail("Uploads directory is not writable: $base_dir");
}

// WP-Pro-Quiz table names on THIS site.
if (class_exists('LDLMS_DB')) {
    $tbl_master   = LDLMS_DB::get_table_name('quiz_master');
    $tbl_question = LDLMS_DB::get_table_name('quiz_question');
    $tbl_category = LDLMS_DB::get_table_name('quiz_category');
} else {
    $tbl_master   = $wpdb->prefix . 'wp_pro_quiz_master';
    $tbl_question = $wpdb->prefix . 'wp_pro_quiz_question';
    $tbl_category = $wpdb->prefix . 'wp_pro_quiz_category';
}
$has_proquiz_tables = (bool) $wpdb->get_var("SHOW TABLES LIKE '" . esc_sql($tbl_master) . "'");

// Author for the imported content.
$author_id = 0;
if ($opt['author'] !== '') {
    $u = is_numeric($opt['author']) ? get_user_by('id', (int) $opt['author']) : get_user_by('login', $opt['author']);
    if (!$u) { lci_fail("--author: no such user '{$opt['author']}'"); }
    $author_id = (int) $u->ID;
} else {
    if (function_exists('wp_get_current_user') && is_user_logged_in()) { $author_id = get_current_user_id(); }
    if (!$author_id) {
        $admins = get_users(array('role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'fields' => 'ID'));
        $author_id = !empty($admins) ? (int) $admins[0] : 1;
    }
}

// ---------------------------------------------------------------------------
// Read the archive.
// ---------------------------------------------------------------------------
$zip = new ZipArchive();
if ($zip->open($zip_path) !== true) { lci_fail("Cannot open archive: $zip_path"); }

$json = $zip->getFromName('course-export.json');
if ($json === false) {
    lci_fail("course-export.json not found inside the archive — is this a course export made by export-learndash-course.php?");
}
$data = json_decode($json, true);
unset($json);
if (!is_array($data) || empty($data['posts'])) { lci_fail("course-export.json is unreadable or empty."); }
if ((int) $data['format_version'] > 1) {
    lci_fail("Archive format version {$data['format_version']} is newer than this importer (1). Use a matching importer.");
}

$src         = isset($data['source']) ? $data['source'] : array();
$src_uploads = isset($src['uploads_url']) ? untrailingslashit($src['uploads_url']) : '';
$src_home    = isset($src['home_url']) ? untrailingslashit($src['home_url']) : '';
$src_site    = isset($src['site_url']) ? untrailingslashit($src['site_url']) : '';
$src_course  = (int) $data['course_id'];

foreach (array('postmeta', 'terms', 'term_taxonomy', 'term_relationships') as $k) {
    if (!isset($data[$k]) || !is_array($data[$k])) { $data[$k] = array(); }
}
foreach (array('master', 'question', 'category') as $k) {
    if (!isset($data['proquiz'][$k]) || !is_array($data['proquiz'][$k])) { $data['proquiz'][$k] = array(); }
}

$posts_by_id = array();
foreach ($data['posts'] as $row) { $posts_by_id[(int) $row['ID']] = $row; }
if (!isset($posts_by_id[$src_course])) { lci_fail("The archive does not contain its own course post (#$src_course)."); }

foreach ($data['postmeta'] as $m) { $meta_by_post[(int) $m['post_id']][] = $m; }

$attachment_rows = array();
$content_rows    = array();
foreach ($posts_by_id as $id => $row) {
    if ($row['post_type'] === 'revision') { continue; }
    if ($row['post_type'] === 'attachment') { $attachment_rows[$id] = $row; }
    else { $content_rows[$id] = $row; }
}

$type_counts = array();
foreach ($content_rows as $row) {
    $t = $row['post_type'];
    $type_counts[$t] = isset($type_counts[$t]) ? $type_counts[$t] + 1 : 1;
}

$asset_entries = array();
for ($i = 0; $i < $zip->numFiles; $i++) {
    $name = $zip->getNameIndex($i);
    if (strpos($name, 'assets/uploads/') !== 0) { continue; }
    $rel = substr($name, strlen('assets/uploads/'));
    if ($rel === '' || substr($rel, -1) === '/') { continue; }
    $asset_entries[$name] = $rel;
}

lci_out('');
lci_out('  Course      : ' . $posts_by_id[$src_course]['post_title'] . ' (source #' . $src_course . ')');
lci_out('  From        : ' . ($src_home ? $src_home : 'unknown')
    . (!empty($src['ld_version']) ? ' — LearnDash ' . $src['ld_version'] : ''));
lci_out('  Exported    : ' . (isset($data['exported_at']) ? $data['exported_at'] : '?'));
lci_out('  Contains    : ' . implode(', ', array_map(function ($t, $n) { return "$n $t"; }, array_keys($type_counts), $type_counts)));
lci_out('  Media       : ' . count($attachment_rows) . ' attachments, ' . count($asset_entries) . ' files');
lci_out('  Quiz data   : ' . count($data['proquiz']['master']) . ' quizzes, ' . count($data['proquiz']['question']) . ' questions (WP-Pro-Quiz)');
lci_out('  Into        : ' . home_url() . ' — LearnDash ' . (defined('LEARNDASH_VERSION') ? LEARNDASH_VERSION : '?'));
lci_out('  As author   : ' . get_the_author_meta('user_login', $author_id) . " (#$author_id)");
lci_out('');

$manifest_json = $zip->getFromName('manifest.json');
if ($manifest_json) {
    $manifest = json_decode($manifest_json, true);
    if (!empty($manifest['missing_asset_files'])) {
        lci_warn(count($manifest['missing_asset_files']) . ' media file(s) were missing when the archive was exported '
            . '— those images will be broken here too (see manifest.json in the zip).');
    }
}

// Warn if this course looks like it was already imported.
$dupe = $wpdb->get_var($wpdb->prepare(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type='sfwd-courses' AND post_title=%s AND post_status!='trash' LIMIT 1",
    $posts_by_id[$src_course]['post_title']
));
if ($dupe) {
    lci_warn("A course titled \"{$posts_by_id[$src_course]['post_title']}\" already exists here (#$dupe). "
        . "This import will create a SECOND, separate copy.");
}

if ($opt['dry_run']) {
    lci_out('DRY RUN — nothing will be written. Re-run without --dry-run to import.');
    lci_out('');
}
if (!$opt['dry_run'] && !$opt['yes'] && !$IS_WP_CLI) {
    $answer = strtolower(lci_prompt('Import this course into ' . home_url() . '? [y/N] ', 'n'));
    if ($answer !== 'y' && $answer !== 'yes') { lci_out('Aborted.'); exit(0); }
    lci_out('');
}

// ---------------------------------------------------------------------------
// Helpers: rewriting text, serialized values and IDs.
// ---------------------------------------------------------------------------

// URL pairs applied to content and meta (longest/most specific first).
if ($src_uploads && $src_uploads !== $base_url) {
    foreach (array($src_uploads, set_url_scheme($src_uploads, 'http'), set_url_scheme($src_uploads, 'https')) as $u) {
        $url_pairs[$u] = $base_url;
    }
}
foreach (array($src_home, $src_site) as $h) {
    if (!$h) { continue; }
    foreach (array($h, set_url_scheme($h, 'http'), set_url_scheme($h, 'https')) as $u) {
        if ($u !== untrailingslashit(home_url())) { $url_pairs[$u] = untrailingslashit(home_url()); }
    }
}
// Longest search strings first so the uploads URL wins over the bare home URL.
uksort($url_pairs, function ($a, $b) { return strlen($b) - strlen($a); });

/**
 * Re-emit a PHP-serialized string, running $fn over every string value it
 * contains and fixing the s:<len> prefixes. Used instead of unserialize() so
 * that values holding objects (WP-Pro-Quiz answer data) survive untouched.
 * Returns null when $s is not parseable as serialized data.
 */
function lci_serialized_map($s, $fn) {
    $pos = 0;
    $out = lci_serialized_node($s, $pos, $fn);
    if ($out === null || $pos !== strlen($s)) { return null; }
    return $out;
}
function lci_serialized_node($s, &$pos, $fn) {
    $type = isset($s[$pos]) ? $s[$pos] : '';
    switch ($type) {
        case 'N': // N;
            if (substr($s, $pos, 2) !== 'N;') { return null; }
            $pos += 2;
            return 'N;';
        case 'b':
        case 'i':
        case 'd':
        case 'R':
        case 'r':
            $end = strpos($s, ';', $pos);
            if ($end === false) { return null; }
            $tok = substr($s, $pos, $end - $pos + 1);
            $pos = $end + 1;
            return $tok;
        case 's':
            if (substr($s, $pos, 2) !== 's:') { return null; }
            $colon = strpos($s, ':', $pos + 2);
            if ($colon === false) { return null; }
            $len = (int) substr($s, $pos + 2, $colon - $pos - 2);
            if ($s[$colon + 1] !== '"') { return null; }
            $val = substr($s, $colon + 2, $len);
            if (strlen($val) !== $len || substr($s, $colon + 2 + $len, 2) !== '";') { return null; }
            $pos = $colon + 2 + $len + 2;
            $new = call_user_func($fn, $val);
            return 's:' . strlen($new) . ':"' . $new . '";';
        case 'a':
        case 'O':
            if ($type === 'a') {
                if (!preg_match('/^a:(\d+):\{/', substr($s, $pos, 24), $m)) { return null; }
                $count  = (int) $m[1];
                $header = $m[0];
                $pos   += strlen($m[0]);
            } else {
                if (!preg_match('/^O:(\d+):"/', substr($s, $pos, 24), $m)) { return null; }
                $nlen  = (int) $m[1];
                $start = $pos + strlen($m[0]);
                $cls   = substr($s, $start, $nlen);
                $after = $start + $nlen;
                if (substr($s, $after, 2) !== '":') { return null; }
                if (!preg_match('/^:(\d+):\{/', substr($s, $after + 1, 24), $m2)) { return null; }
                $count  = (int) $m2[1];
                $header = 'O:' . $nlen . ':"' . $cls . '"' . $m2[0];
                $pos    = $after + 1 + strlen($m2[0]);
            }
            $body  = '';
            $items = $type === 'a' ? $count * 2 : $count * 2; // key + value pairs
            for ($i = 0; $i < $items; $i++) {
                $node = lci_serialized_node($s, $pos, $fn);
                if ($node === null) { return null; }
                $body .= $node;
            }
            if (!isset($s[$pos]) || $s[$pos] !== '}') { return null; }
            $pos++;
            return $header . $body . '}';
    }
    return null;
}

/** Apply the URL/text replacements to a value, serialized-safe. */
function lci_rewrite_text($value) {
    $url_pairs = $GLOBALS['LCI']['url_pairs'];
    if (!is_string($value) || $value === '' || empty($url_pairs)) { return $value; }
    $search  = array_keys($url_pairs);
    $replace = array_values($url_pairs);
    $hit = false;
    foreach ($search as $needle) { if (strpos($value, $needle) !== false) { $hit = true; break; } }
    if (!$hit) { return $value; }
    if (is_serialized($value)) {
        $mapped = lci_serialized_map($value, function ($str) use ($search, $replace) {
            return str_replace($search, $replace, $str);
        });
        if ($mapped !== null) { return $mapped; }
        lci_warn('Could not safely rewrite URLs inside a serialized value; left as-is.');
        return $value;
    }
    return str_replace($search, $replace, $value);
}

/** Rewrite post_content: URLs, inline image IDs and LearnDash shortcode IDs. */
function lci_rewrite_content($content) {
    $id_map = $GLOBALS['LCI']['id_map'];
    if (!is_string($content) || $content === '') { return $content; }
    $content = lci_rewrite_text($content);
    // class="wp-image-123" on inline images
    $content = preg_replace_callback('/wp-image-(\d+)/', function ($m) use ($id_map) {
        $old = (int) $m[1];
        return 'wp-image-' . (isset($id_map[$old]) ? $id_map[$old] : $old);
    }, $content);
    // Shortcode attributes that carry LearnDash post IDs
    $content = preg_replace_callback(
        '/\b(course_id|lesson_id|topic_id|quiz_id|question_id|post_id|id)\s*=\s*("|\')(\d+)\2/',
        function ($m) use ($id_map) {
            $old = (int) $m[3];
            if (!isset($id_map[$old])) { return $m[0]; }
            return $m[1] . '=' . $m[2] . $id_map[$old] . $m[2];
        },
        $content
    );
    return $content;
}

/** Deep-remap integer IDs inside an unserialized value using $map. */
function lci_remap_ids($value, array $map, $drop_unmapped_keys = array()) {
    if (is_array($value)) {
        $new = array();
        foreach ($value as $k => $v) {
            $nk = (is_int($k) && isset($map[$k])) ? $map[$k] : $k;
            $new[$nk] = lci_remap_ids($v, $map, $drop_unmapped_keys);
        }
        return $new;
    }
    if (is_int($value)) { return isset($map[$value]) ? $map[$value] : $value; }
    if (is_string($value) && $value !== '' && ctype_digit($value)) {
        $n = (int) $value;
        return isset($map[$n]) ? (string) $map[$n] : $value;
    }
    return $value;
}

// Meta keys whose (possibly serialized) values hold post IDs.
$meta_post_id_keys = array(
    '_sfwd-courses', '_sfwd-lessons', '_sfwd-topic', '_sfwd-quiz', '_sfwd-question',
    'ld_course_steps', 'course_id', 'lesson_id', 'topic_id', 'quiz_id', '_thumbnail_id',
    '_ld_certificate', 'related_courses', '_learndash_course_grid_image',
);
// Meta keys we never carry over to the destination site.
$meta_skip_keys = array(
    '_edit_lock', '_edit_last', '_wp_old_slug', '_wp_old_date', '_wp_desired_post_slug',
    'wrld_course_users',           // source-site enrolments (user IDs are meaningless here)
    'mdd_hash', 'mdd_size',        // source-site media-dedupe plugin bookkeeping
    '_learndash_transient_cache',
);

// ---------------------------------------------------------------------------
// Step 1 — media files.
// ---------------------------------------------------------------------------
$stats = array(
    'files_copied' => 0, 'files_skipped' => 0, 'files_failed' => 0,
    'attachments_created' => 0, 'attachments_reused' => 0,
    'posts_created' => 0, 'slugs_renamed' => 0, 'meta_rows' => 0, 'terms_created' => 0, 'terms_reused' => 0,
    'proquiz_master' => 0, 'proquiz_questions' => 0, 'proquiz_categories' => 0,
);

lci_out('[1/6] Media files...');
foreach ($asset_entries as $entry => $rel) {
    if (strpos($rel, '..') !== false || strpos($rel, "\0") !== false) {
        lci_warn("Skipped suspicious archive path: $rel");
        continue;
    }
    $dest = $base_dir . '/' . $rel;
    if (file_exists($dest) && !$opt['overwrite_media']) { $stats['files_skipped']++; continue; }
    if ($opt['dry_run']) { $stats['files_copied']++; continue; }
    if (!wp_mkdir_p(dirname($dest))) { $stats['files_failed']++; continue; }
    $in = $zip->getStream($entry);
    if (!$in) { $stats['files_failed']++; continue; }
    $out = @fopen($dest, 'wb');
    if (!$out) { fclose($in); $stats['files_failed']++; lci_warn("Cannot write $rel"); continue; }
    stream_copy_to_stream($in, $out);
    fclose($in); fclose($out);
    $stats['files_copied']++;
}
lci_out(sprintf($opt['dry_run'] ? '      %d to copy, %d already present, %d failed (of %d files)'
                                : '      %d copied, %d already present, %d failed (of %d files)',
    $stats['files_copied'], $stats['files_skipped'], $stats['files_failed'], count($asset_entries)));

// ---------------------------------------------------------------------------
// Step 2 — attachments.
// ---------------------------------------------------------------------------
$reused_attachments = array();   // source attachment ID => true (media already on this site)

function lci_meta_value($post_id, $key) {
    $meta_by_post = $GLOBALS['LCI']['meta_by_post'];
    if (empty($meta_by_post[$post_id])) { return null; }
    foreach ($meta_by_post[$post_id] as $m) {
        if ($m['meta_key'] === $key) { return $m['meta_value']; }
    }
    return null;
}

function lci_unique_slug($slug, $post_type) {
    global $wpdb;
    if ($slug === '') { return ''; }
    $base = $slug; $n = 1;
    while ($wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s LIMIT 1", $slug, $post_type
    ))) {
        $n++;
        $slug = $base . '-' . $n;
        if ($n > 200) { break; }
    }
    return $slug;
}

/** Insert a row into wp_posts verbatim (minus its ID) and return the new ID. */
function lci_insert_post_row($row, $overrides = array()) {
    global $wpdb;
    $insert = array(
        'post_author'           => $GLOBALS['LCI']['author_id'],
        'post_date'             => $row['post_date'],
        'post_date_gmt'         => $row['post_date_gmt'],
        'post_content'          => $row['post_content'],
        'post_title'            => $row['post_title'],
        'post_excerpt'          => $row['post_excerpt'],
        'post_status'           => $row['post_status'],
        'comment_status'        => $row['comment_status'],
        'ping_status'           => $row['ping_status'],
        'post_password'         => $row['post_password'],
        'post_name'             => $row['post_name'],
        'to_ping'               => $row['to_ping'],
        'pinged'                => $row['pinged'],
        'post_modified'         => $row['post_modified'],
        'post_modified_gmt'     => $row['post_modified_gmt'],
        'post_content_filtered' => $row['post_content_filtered'],
        'post_parent'           => 0,
        'guid'                  => '',
        'menu_order'            => $row['menu_order'],
        'post_type'             => $row['post_type'],
        'post_mime_type'        => $row['post_mime_type'],
        'comment_count'         => 0,
    );
    foreach ($overrides as $k => $v) { $insert[$k] = $v; }
    $ok = $wpdb->insert($wpdb->posts, $insert);
    if (!$ok) { lci_fail("Failed to insert post \"{$row['post_title']}\": " . $wpdb->last_error); }
    return (int) $wpdb->insert_id;
}

lci_out('[2/6] Media library entries...');
foreach ($attachment_rows as $old_id => $row) {
    $rel = lci_meta_value($old_id, '_wp_attached_file');

    // Reuse an identical upload that already exists on this site.
    if ($rel && !$opt['duplicate_media']) {
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT pm.post_id FROM {$wpdb->postmeta} pm
               JOIN {$wpdb->posts} p ON p.ID = pm.post_id AND p.post_type = 'attachment'
              WHERE pm.meta_key = '_wp_attached_file' AND pm.meta_value = %s
              ORDER BY pm.post_id ASC LIMIT 1", $rel
        ));
        if ($existing && ($opt['dry_run'] || file_exists($base_dir . '/' . $rel))) {
            $id_map[$old_id] = (int) $existing;
            $reused_attachments[$old_id] = true;
            $stats['attachments_reused']++;
            continue;
        }
    }
    if ($opt['dry_run']) { $stats['attachments_created']++; continue; }

    $new_id = lci_insert_post_row($row, array(
        'post_name'    => lci_unique_slug($row['post_name'], 'attachment'),
        'post_status'  => 'inherit',
        'post_content' => lci_rewrite_content($row['post_content']),
        'post_excerpt' => lci_rewrite_text($row['post_excerpt']),
        'guid'         => $rel ? $base_url . '/' . $rel : '',
    ));
    $id_map[$old_id] = $new_id;
    $stats['attachments_created']++;
}
lci_out(sprintf($opt['dry_run'] ? '      %d to create, %d already in this media library'
                                : '      %d created, %d reused from this site',
    $stats['attachments_created'], $stats['attachments_reused']));

// ---------------------------------------------------------------------------
// Step 3 — course, lessons, topics, quizzes, questions.
// ---------------------------------------------------------------------------
lci_out('[3/6] Course content...');
$order = array('sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-question');
$sorted = $content_rows;
uasort($sorted, function ($a, $b) use ($order) {
    $ia = array_search($a['post_type'], $order, true); $ib = array_search($b['post_type'], $order, true);
    if ($ia === false) { $ia = 99; } if ($ib === false) { $ib = 99; }
    if ($ia !== $ib) { return $ia - $ib; }
    return (int) $a['ID'] - (int) $b['ID'];
});

foreach ($sorted as $old_id => $row) {
    if (!post_type_exists($row['post_type'])) {
        lci_warn("Post type '{$row['post_type']}' is not registered here — skipping \"{$row['post_title']}\".");
        continue;
    }
    $slug = lci_unique_slug($row['post_name'], $row['post_type']);
    if ($slug !== $row['post_name']) { $stats['slugs_renamed']++; }
    if ($opt['dry_run']) { $stats['posts_created']++; continue; }
    $status = $row['post_status'];
    if ($opt['status'] !== 'keep' && $status !== 'trash') { $status = $opt['status']; }
    $new_id = lci_insert_post_row($row, array(
        'post_name'   => $slug,
        'post_status' => $status,
    ));
    $id_map[$old_id] = $new_id;
    $stats['posts_created']++;
}
$new_course_id = isset($id_map[$src_course]) ? $id_map[$src_course] : 0;

// Second pass: content, parents and guids now that every ID is known.
if (!$opt['dry_run']) {
    foreach ($id_map as $old_id => $new_id) {
        if (!isset($posts_by_id[$old_id]) || isset($reused_attachments[$old_id])) { continue; }
        $row = $posts_by_id[$old_id];
        $old_parent = (int) $row['post_parent'];
        $update = array(
            'post_parent' => ($old_parent && isset($id_map[$old_parent])) ? $id_map[$old_parent] : 0,
        );
        if ($row['post_type'] !== 'attachment') {
            $update['post_content']          = lci_rewrite_content($row['post_content']);
            $update['post_excerpt']          = lci_rewrite_text($row['post_excerpt']);
            $update['post_content_filtered'] = lci_rewrite_content($row['post_content_filtered']);
            $update['guid']                  = home_url('/?post_type=' . $row['post_type'] . '&p=' . $new_id);
        }
        $wpdb->update($wpdb->posts, $update, array('ID' => $new_id));
    }
}
lci_out(sprintf($opt['dry_run'] ? '      %d posts to create%s' : '      %d posts created%s',
    $stats['posts_created'], $new_course_id ? " (course is now #$new_course_id)" : ''));
if ($stats['slugs_renamed']) {
    lci_warn($stats['slugs_renamed'] . ' post slug(s) already existed here and were given a numbered suffix. '
        . 'Links written into the course content that pointed at the original URLs may not resolve.');
}

// ---------------------------------------------------------------------------
// Step 4 — WP-Pro-Quiz tables (must run before postmeta, which references them).
// ---------------------------------------------------------------------------
lci_out('[4/6] Quiz data...');
$pro_map     = array();   // old pro-quiz master id => new
$pro_cat_map = array();   // old category id => new
$pro_q_map   = array();   // old pro question id => new

if (!$has_proquiz_tables) {
    lci_warn("WP-Pro-Quiz tables ($tbl_master) not found — quiz questions cannot be imported.");
} elseif ($opt['dry_run']) {
    $stats['proquiz_master']    = count($data['proquiz']['master']);
    $stats['proquiz_questions'] = count($data['proquiz']['question']);
    foreach ($data['proquiz']['category'] as $cat) {
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT category_id FROM $tbl_category WHERE category_name = %s LIMIT 1", $cat['category_name']
        ));
        if (!$existing) { $stats['proquiz_categories']++; }
    }
} else {
    // Categories: reuse by name where possible.
    foreach ($data['proquiz']['category'] as $cat) {
        $old = (int) $cat['category_id'];
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT category_id FROM $tbl_category WHERE category_name = %s LIMIT 1", $cat['category_name']
        ));
        if ($existing) { $pro_cat_map[$old] = (int) $existing; continue; }
        $row = $cat; unset($row['category_id']);
        if ($wpdb->insert($tbl_category, $row)) {
            $pro_cat_map[$old] = (int) $wpdb->insert_id;
            $stats['proquiz_categories']++;
        }
    }
    // Quiz masters.
    foreach ($data['proquiz']['master'] as $m) {
        $old = (int) $m['id'];
        $row = $m; unset($row['id']);
        foreach ($row as $k => $v) { $row[$k] = lci_rewrite_text($v); }
        if (isset($row['prerequisite'])) { $row['prerequisite'] = 0; }
        if (!$wpdb->insert($tbl_master, $row)) {
            lci_warn("Could not create quiz \"{$m['name']}\": " . $wpdb->last_error);
            continue;
        }
        $pro_map[$old] = (int) $wpdb->insert_id;
        $stats['proquiz_master']++;
    }
    // Questions.
    foreach ($data['proquiz']['question'] as $q) {
        $old      = (int) $q['id'];
        $old_quiz = (int) $q['quiz_id'];
        if (!isset($pro_map[$old_quiz])) {
            lci_warn("Question #$old belongs to quiz #$old_quiz which was not imported — skipped.");
            continue;
        }
        $row = $q; unset($row['id']);
        $row['quiz_id']     = $pro_map[$old_quiz];
        $row['category_id'] = isset($pro_cat_map[(int) $q['category_id']]) ? $pro_cat_map[(int) $q['category_id']] : 0;
        if (isset($row['previous_id'])) { $row['previous_id'] = 0; }
        foreach (array('question', 'correct_msg', 'incorrect_msg', 'tip_msg', 'answer_data', 'title') as $f) {
            if (isset($row[$f])) { $row[$f] = lci_rewrite_text($row[$f]); }
        }
        if (!$wpdb->insert($tbl_question, $row)) {
            lci_warn("Could not create question \"{$q['title']}\": " . $wpdb->last_error);
            continue;
        }
        $pro_q_map[$old] = (int) $wpdb->insert_id;
        $stats['proquiz_questions']++;
    }
}
lci_out(sprintf('      %d quizzes, %d questions, %d categories',
    $stats['proquiz_master'], $stats['proquiz_questions'], $stats['proquiz_categories']));

// ---------------------------------------------------------------------------
// Step 5 — taxonomy terms.
// ---------------------------------------------------------------------------
lci_out('[5/6] Categories & tags...');
$term_map = array();   // old term_id => new term_id
$tt_map   = array();   // old term_taxonomy_id => new term_taxonomy_id

$terms_by_id = array();
foreach ($data['terms'] as $t) { $terms_by_id[(int) $t['term_id']] = $t; }

if ($opt['dry_run']) {
    foreach ($data['term_taxonomy'] as $tt) {
        if (!isset($terms_by_id[(int) $tt['term_id']]) || !taxonomy_exists($tt['taxonomy'])) { continue; }
        $term = $terms_by_id[(int) $tt['term_id']];
        $existing = get_term_by('slug', $term['slug'], $tt['taxonomy']);
        if (!$existing) { $existing = get_term_by('name', $term['name'], $tt['taxonomy']); }
        if ($existing) { $stats['terms_reused']++; } else { $stats['terms_created']++; }
    }
} else {
    foreach ($data['term_taxonomy'] as $tt) {
        $term_id  = (int) $tt['term_id'];
        $taxonomy = $tt['taxonomy'];
        if (!isset($terms_by_id[$term_id])) { continue; }
        if (!taxonomy_exists($taxonomy)) {
            lci_warn("Taxonomy '$taxonomy' is not registered here — its terms were skipped.");
            continue;
        }
        $term = $terms_by_id[$term_id];
        $existing = get_term_by('slug', $term['slug'], $taxonomy);
        if (!$existing) { $existing = get_term_by('name', $term['name'], $taxonomy); }
        if ($existing) {
            $term_map[$term_id] = (int) $existing->term_id;
            $tt_map[(int) $tt['term_taxonomy_id']] = (int) $existing->term_taxonomy_id;
            $stats['terms_reused']++;
            continue;
        }
        $created = wp_insert_term($term['name'], $taxonomy, array(
            'slug'        => $term['slug'],
            'description' => $tt['description'],
        ));
        if (is_wp_error($created)) {
            lci_warn("Could not create term '{$term['name']}' in $taxonomy: " . $created->get_error_message());
            continue;
        }
        $term_map[$term_id] = (int) $created['term_id'];
        $tt_map[(int) $tt['term_taxonomy_id']] = (int) $created['term_taxonomy_id'];
        $stats['terms_created']++;
    }
    // Parents, now that every term exists.
    foreach ($data['term_taxonomy'] as $tt) {
        $parent = (int) $tt['parent'];
        $term_id = (int) $tt['term_id'];
        if (!$parent || !isset($term_map[$term_id], $term_map[$parent])) { continue; }
        wp_update_term($term_map[$term_id], $tt['taxonomy'], array('parent' => $term_map[$parent]));
    }
    // Relationships.
    $rel_count = 0;
    foreach ($data['term_relationships'] as $rel) {
        $obj = (int) $rel['object_id'];
        $tt  = (int) $rel['term_taxonomy_id'];
        if (!isset($id_map[$obj], $tt_map[$tt])) { continue; }
        $wpdb->replace($wpdb->term_relationships, array(
            'object_id'        => $id_map[$obj],
            'term_taxonomy_id' => $tt_map[$tt],
            'term_order'       => (int) $rel['term_order'],
        ));
        $rel_count++;
    }
    $tt_by_tax = array();
    foreach ($data['term_taxonomy'] as $tt) {
        $old_tt = (int) $tt['term_taxonomy_id'];
        if (isset($tt_map[$old_tt])) { $tt_by_tax[$tt['taxonomy']][] = $tt_map[$old_tt]; }
    }
    foreach ($tt_by_tax as $taxonomy => $ids) { wp_update_term_count($ids, $taxonomy); }
}
lci_out(sprintf($opt['dry_run'] ? '      %d terms to create, %d already here'
                                : '      %d terms created, %d matched existing',
    $stats['terms_created'], $stats['terms_reused']));

// ---------------------------------------------------------------------------
// Step 6 — postmeta, with every reference remapped.
// ---------------------------------------------------------------------------
lci_out('[6/6] Settings & relationships...');
$cert_warned = false;

if ($opt['dry_run']) {
    foreach ($meta_by_post as $old_post_id => $rows) {
        if (isset($reused_attachments[$old_post_id]) || !isset($posts_by_id[$old_post_id])) { continue; }
        foreach ($rows as $m) {
            if (in_array($m['meta_key'], $meta_skip_keys, true)) { continue; }
            $stats['meta_rows']++;
        }
    }
} else {
    foreach ($meta_by_post as $old_post_id => $rows) {
        if (!isset($id_map[$old_post_id])) { continue; }
        // Attachments reused from this site keep their own metadata.
        if (isset($reused_attachments[$old_post_id])) { continue; }
        $new_post_id = $id_map[$old_post_id];

        foreach ($rows as $m) {
            $key   = $m['meta_key'];
            $value = $m['meta_value'];
            if (in_array($key, $meta_skip_keys, true)) { continue; }
            if (strpos($key, '_learndash_transient') === 0) { continue; }

            // --- keys that embed an ID -------------------------------------
            if (preg_match('/^ld_course_(\d+)$/', $key, $mm)) {
                $old = (int) $mm[1];
                if (!isset($id_map[$old])) { continue; }
                $key   = 'ld_course_' . $id_map[$old];
                $value = (string) $id_map[$old];
            } elseif (preg_match('/^(quiz_pro_id|quiz_pro_primary)_(\d+)$/', $key, $mm)) {
                $old = (int) $mm[2];
                if (!isset($pro_map[$old])) { continue; }
                $key   = $mm[1] . '_' . $pro_map[$old];
                $value = (string) $pro_map[$old];
            }

            // --- values that are IDs ---------------------------------------
            elseif ($key === 'quiz_pro_id') {
                $old = (int) $value;
                if (!isset($pro_map[$old])) { continue; }
                $value = (string) $pro_map[$old];
            } elseif ($key === 'question_pro_id') {
                $old = (int) $value;
                if (!isset($pro_q_map[$old])) { continue; }
                $value = (string) $pro_q_map[$old];
            } elseif ($key === 'question_pro_category') {
                $old = (int) $value;
                $value = (string) (isset($pro_cat_map[$old]) ? $pro_cat_map[$old] : 0);
            } elseif ($key === 'ld_quiz_questions') {
                // question post ID => WP-Pro-Quiz question ID
                $arr = maybe_unserialize($value);
                if (is_array($arr)) {
                    $new = array();
                    foreach ($arr as $qpost => $qpro) {
                        if (!isset($id_map[(int) $qpost], $pro_q_map[(int) $qpro])) { continue; }
                        $new[$id_map[(int) $qpost]] = $pro_q_map[(int) $qpro];
                    }
                    $value = $new;
                }
            } elseif ($key === '_ld_certificate') {
                $old = (int) $value;
                if ($old && !isset($id_map[$old])) {
                    if (!$cert_warned) {
                        lci_warn('The source course/quizzes point at a LearnDash certificate that is not part of '
                            . 'this export — the certificate setting was left empty. Assign one here if needed.');
                        $cert_warned = true;
                    }
                    $value = '';
                } elseif ($old) {
                    $value = (string) $id_map[$old];
                }
            } elseif (in_array($key, $meta_post_id_keys, true)) {
                $arr = maybe_unserialize($value);
                if (is_array($arr)) {
                    $arr = lci_remap_ids($arr, $id_map);
                    // Quiz settings also carry the WP-Pro-Quiz master ID + certificate.
                    foreach ($arr as $k2 => $v2) {
                        if (!is_string($k2)) { continue; }
                        if (substr($k2, -9) === '_quiz_pro') {
                            $arr[$k2] = isset($pro_map[(int) $v2]) ? $pro_map[(int) $v2] : $v2;
                        } elseif (substr($k2, -12) === '_certificate' && $v2 !== '' && !isset($id_map[(int) $v2])) {
                            $arr[$k2] = '';
                        }
                    }
                    $value = $arr;
                } elseif (is_numeric($arr)) {
                    $old = (int) $arr;
                    if (isset($id_map[$old])) { $value = (string) $id_map[$old]; }
                }
            } elseif (preg_match('/^_yoast_wpseo_primary_/', $key)) {
                $old = (int) $value;
                if (!isset($term_map[$old])) { continue; }
                $value = (string) $term_map[$old];
            }

            // --- URLs inside any remaining value ---------------------------
            if (is_string($value)) { $value = lci_rewrite_text($value); }
            if (is_array($value))  { $value = maybe_serialize($value); }

            $wpdb->insert($wpdb->postmeta, array(
                'post_id'    => $new_post_id,
                'meta_key'   => $key,
                'meta_value' => $value,
            ));
            $stats['meta_rows']++;
        }
    }
}
lci_out(sprintf($opt['dry_run'] ? '      %d meta rows to write' : '      %d meta rows written', $stats['meta_rows']));

// ---------------------------------------------------------------------------
// Finish: caches, LearnDash step rebuild, report.
// ---------------------------------------------------------------------------
$zip->close();

if (!$opt['dry_run']) {
    foreach ($id_map as $new_id) { clean_post_cache($new_id); }
    if ($new_course_id) {
        if (function_exists('learndash_course_set_steps_dirty')) {
            learndash_course_set_steps_dirty($new_course_id);
        }
        if (function_exists('learndash_get_course_steps')) {
            learndash_get_course_steps($new_course_id, array('sfwd-lessons', 'sfwd-topic', 'sfwd-quiz'));
        }
        // NB: do NOT mark quiz questions dirty here. LearnDash would rebuild
        // 'ld_quiz_questions' from wp_pro_quiz_question.quiz_id, which empties
        // the list for quizzes whose questions belong to another quiz's
        // WP-Pro-Quiz master (what happens when a quiz was copied). The
        // 'ld_quiz_questions' map imported above is already the correct one.
        foreach ($content_rows as $old_id => $row) {
            if ($row['post_type'] !== 'sfwd-quiz' || !isset($id_map[$old_id])) { continue; }
            delete_post_meta($id_map[$old_id], 'ld_quiz_questions_dirty');
        }
    }
    wp_cache_flush();

    $report = array(
        'imported_at'   => gmdate('c'),
        'archive'       => basename($zip_path),
        'source_site'   => $src_home,
        'source_course' => $src_course,
        'new_course'    => $new_course_id,
        'author'        => $author_id,
        'stats'         => $stats,
        'warnings'      => $GLOBALS['LCI']['warnings'],
        'post_id_map'   => $id_map,
        'proquiz_map'   => array('master' => $pro_map, 'question' => $pro_q_map, 'category' => $pro_cat_map),
        'term_map'      => $term_map,
    );
    $report_path = dirname($zip_path) . '/ld-import-report-' . gmdate('Ymd-His') . '.json';
    @file_put_contents($report_path, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

lci_out('');
if ($opt['dry_run']) {
    lci_out('DRY RUN complete — no changes were made.');
    exit(0);
}
if (!empty($GLOBALS['LCI']['warnings'])) {
    lci_out(count($GLOBALS['LCI']['warnings']) . ' warning(s) — see above.');
}
lci_out('DONE. Imported "' . $posts_by_id[$src_course]['post_title'] . '" as course #' . $new_course_id);
lci_out('  View : ' . get_permalink($new_course_id));
lci_out('  Edit : ' . admin_url('post.php?post=' . $new_course_id . '&action=edit'));
if (isset($report_path)) { lci_out('  Log  : ' . $report_path); }
lci_out('');
lci_out('Next: open the course builder to confirm the lesson/topic/quiz tree, and check a quiz.');

if ($IS_WP_CLI) { WP_CLI::success("Imported course #$new_course_id"); }
