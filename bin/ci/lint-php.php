<?php
/**
 * Regression gate for parse errors and hard short-tag breakage.
 *
 * Runs `php -l` on every custom PHP file with `short_open_tag=Off` so anything
 * that would fatal on a host with short tags disabled fails here. Uses the
 * running PHP binary, so the CI matrix exercises each supported PHP version.
 */

$files = require __DIR__ . '/_custom_php_files.php';
$root  = dirname(__DIR__, 2);
$php   = PHP_BINARY;

$failed = array();
foreach ($files as $file) {
    $cmd = escapeshellarg($php) . ' -d short_open_tag=Off -d display_errors=1 -l ' . escapeshellarg($file) . ' 2>&1';
    exec($cmd, $out, $code);
    if (0 !== $code) {
        $failed[ltrim(str_replace($root, '', $file), '/')] = implode("\n", $out);
    }
    $out = array();
}

if (!empty($failed)) {
    fwrite(STDERR, "❌ PHP parse errors (short_open_tag=Off) on " . PHP_VERSION . ":\n");
    foreach ($failed as $rel => $msg) {
        fwrite(STDERR, "   $rel\n      " . str_replace("\n", "\n      ", $msg) . "\n");
    }
    exit(1);
}

fwrite(STDOUT, sprintf("✅ %d custom PHP file(s) pass php -l (short_open_tag=Off) on PHP %s.\n", count($files), PHP_VERSION));
exit(0);
