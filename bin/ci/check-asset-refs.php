<?php
/**
 * Regression gate for broken local asset references (audit finding #43-class /
 * issue #53: the theme pointed at plugin SVGs that could vanish).
 *
 * Scans custom PHP for static references to local theme/plugin assets and fails
 * (exit 1) if any referenced file does not exist on disk. Only statically
 * resolvable references are checked:
 *   - literal root-relative paths under the custom theme/plugin asset dirs
 *   - get_stylesheet_directory_uri() . '<literal path>'
 * Dynamic (variable-built) paths and /wp-content/uploads/ are skipped.
 */

$files = require __DIR__ . '/_custom_php_files.php';
$root  = dirname(__DIR__, 2);
$theme_dir = $root . '/wp-content/themes/academyAfrica';

$ext = 'svg|png|jpe?g|gif|webp|ico|css|js|woff2?|ttf|eot|pdf';

$missing = array();

foreach ($files as $file) {
    $code = file_get_contents($file);
    if (false === $code) {
        continue;
    }
    $rel_file = ltrim(str_replace($root, '', $file), '/');

    // (A) Literal root-relative references to custom theme/plugin asset dirs.
    if (preg_match_all(
        '#(/wp-content/(?:themes/academyAfrica|plugins/academy-africa)/[A-Za-z0-9_./-]+\.(?:' . $ext . '))#',
        $code,
        $m
    )) {
        foreach (array_unique($m[1]) as $path) {
            if (!file_exists($root . $path)) {
                $missing[] = "$rel_file → $path";
            }
        }
    }

    // (B) get_stylesheet_directory_uri() . '<literal path>'
    if (preg_match_all(
        '#get_stylesheet_directory_uri\(\)\s*\.\s*[\'"](/[A-Za-z0-9_./-]+\.(?:' . $ext . '))[\'"]#',
        $code,
        $m2
    )) {
        foreach (array_unique($m2[1]) as $path) {
            if (!file_exists($theme_dir . $path)) {
                $missing[] = "$rel_file → (theme)$path";
            }
        }
    }
}

if (!empty($missing)) {
    fwrite(STDERR, "❌ Broken local asset reference(s):\n");
    foreach (array_unique($missing) as $line) {
        fwrite(STDERR, "   $line\n");
    }
    exit(1);
}

fwrite(STDOUT, "✅ All statically-referenced local theme/plugin assets exist.\n");
exit(0);
