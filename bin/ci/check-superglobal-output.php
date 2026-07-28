<?php
/**
 * Regression gate for unescaped direct superglobal output (audit / issue #55):
 * request data echoed straight to the page, e.g. `echo $_GET['x']` or
 * `<?= $_POST['y'] ?>`.
 *
 * Baseline-based: the legacy base still has known occurrences (cleared by PRs
 * #54/#55). Those are recorded in bin/ci/baselines/superglobal-output.txt so
 * this gate passes today but FAILS on any *new* occurrence. Remove entries from
 * the baseline as they are fixed.
 *
 * Usage:
 *   php bin/ci/check-superglobal-output.php                  # gate (exit 1 on new)
 *   php bin/ci/check-superglobal-output.php --update-baseline # rewrite baseline
 */

$files = require __DIR__ . '/_custom_php_files.php';
$root  = dirname(__DIR__, 2);
$baseline_file = __DIR__ . '/baselines/superglobal-output.txt';
$update = in_array('--update-baseline', $argv, true);

// echo/print/<?= immediately followed by a raw superglobal (no esc_/sanitize
// wrapper in between).
$pattern = '/(?:\becho\b|\bprint\b|<\?=)\s*\$_(GET|POST|REQUEST|COOKIE|SERVER)\b/';

// Signature = relative path + trimmed offending line, so it survives line moves
// but flags genuinely new occurrences.
$signatures = array();
$display = array();
foreach ($files as $file) {
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    if (false === $lines) {
        continue;
    }
    $rel = ltrim(str_replace($root, '', $file), '/');
    foreach ($lines as $i => $line) {
        if (preg_match($pattern, $line)) {
            $sig = $rel . "\t" . trim($line);
            $signatures[$sig] = true;
            $display[$sig] = sprintf('%s:%d  %s', $rel, $i + 1, trim($line));
        }
    }
}
ksort($signatures);

if ($update) {
    @mkdir(dirname($baseline_file), 0777, true);
    file_put_contents($baseline_file, implode("\n", array_keys($signatures)) . (empty($signatures) ? '' : "\n"));
    fwrite(STDOUT, sprintf("Baseline updated: %d known occurrence(s).\n", count($signatures)));
    exit(0);
}

$baseline = array();
if (is_file($baseline_file)) {
    foreach (file($baseline_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $sig) {
        $baseline[$sig] = true;
    }
}

$new = array_diff_key($signatures, $baseline);

if (!empty($new)) {
    fwrite(STDERR, "❌ New unescaped direct superglobal output (escape with esc_html/esc_attr/esc_url):\n");
    foreach ($new as $sig => $_) {
        fwrite(STDERR, "   " . $display[$sig] . "\n");
    }
    fwrite(STDERR, sprintf("\n%d new occurrence(s) beyond the baseline (%d known).\n", count($new), count($baseline)));
    exit(1);
}

fwrite(STDOUT, sprintf("✅ No new unescaped superglobal output (%d known, tracked in baseline for #54/#55).\n", count($baseline)));
exit(0);
