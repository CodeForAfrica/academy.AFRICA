<?php
/**
 * Detector for unescaped direct superglobal output (audit findings around
 * unsafe output / issue #55). Flags request data echoed straight to the page
 * with no escaping, e.g. `echo $_GET['x']` or `<?= $_POST['y'] ?>`.
 *
 * Reported, not blocking, in CI (the legacy base still has occurrences that
 * PRs #54/#55 address); wire it as a required check once those land. Exits 1
 * when occurrences exist so it is trivial to flip to blocking.
 */

$files = require __DIR__ . '/_custom_php_files.php';
$root  = dirname(__DIR__, 2);

// echo/print/<?= immediately followed by a raw superglobal (no esc_/sanitize/
// intval/absint wrapper in between).
$pattern = '/(?:\becho\b|\bprint\b|<\?=)\s*\$_(GET|POST|REQUEST|COOKIE|SERVER)\b/';

$findings = array();
foreach ($files as $file) {
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    if (false === $lines) {
        continue;
    }
    $rel = ltrim(str_replace($root, '', $file), '/');
    foreach ($lines as $i => $line) {
        if (preg_match($pattern, $line)) {
            $findings[] = sprintf('%s:%d  %s', $rel, $i + 1, trim($line));
        }
    }
}

if (!empty($findings)) {
    fwrite(STDERR, "⚠️  Unescaped direct superglobal output (escape with esc_html/esc_attr/esc_url):\n");
    foreach ($findings as $f) {
        fwrite(STDERR, "   $f\n");
    }
    fwrite(STDERR, sprintf("\n%d occurrence(s).\n", count($findings)));
    exit(1);
}

fwrite(STDOUT, "✅ No unescaped direct superglobal output detected.\n");
exit(0);
