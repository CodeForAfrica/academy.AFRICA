<?php
/**
 * Shared helper for the CI regression gates: enumerate the repository's *custom*
 * PHP files (the code the runtime audit covered). Third-party plugins and WP
 * core are intentionally excluded.
 *
 * Usage: $files = require __DIR__ . '/_custom_php_files.php';
 *
 * @return string[] Absolute paths to custom .php files.
 */

$root = dirname(__DIR__, 2); // repo root (…/sacademy)

$targets = array(
    $root . '/wp-content/themes/academyAfrica',
    $root . '/wp-content/plugins/academy-africa', // removed by #53; guarded below
    $root . '/wp-content/mu-plugins/academy-error-logger.php',
);

$files = array();
foreach ($targets as $target) {
    if (!file_exists($target)) {
        continue;
    }
    if (is_file($target)) {
        if ('php' === pathinfo($target, PATHINFO_EXTENSION)) {
            $files[] = $target;
        }
        continue;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if ($file->isFile() && 'php' === strtolower($file->getExtension())) {
            $files[] = $file->getPathname();
        }
    }
}

sort($files);
return $files;
