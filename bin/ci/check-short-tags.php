<?php
/**
 * Regression gate for audit finding #5 / issue #45.
 *
 * Fails (exit 1) if any *configuration-dependent* short open tag `<?` remains in
 * custom code. `<?php`, `<?=` (always enabled since PHP 5.4) and `<?xml` are
 * allowed; a bare `<?` breaks bootstrap/templates on hosts with
 * short_open_tag=Off.
 *
 * `php -l -d short_open_tag=Off` does not catch inline `<? echo ... ?>` (it just
 * renders as literal text), so this scanner is the authoritative short-tag gate.
 */

$files = require __DIR__ . '/_custom_php_files.php';
$root  = dirname(__DIR__, 2);

$offenders = array();
foreach ($files as $file) {
    $code = file_get_contents($file);
    if (false === $code) {
        continue;
    }
    if (preg_match_all('/<\?(?!php\b|php\s|=|xml)/', $code, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $line = substr_count(substr($code, 0, $match[1]), "\n") + 1;
            $offenders[] = ltrim(str_replace($root, '', $file), '/') . ':' . $line;
        }
    }
}

if (!empty($offenders)) {
    fwrite(STDERR, "❌ Configuration-dependent short tags found (use <?php):\n");
    foreach ($offenders as $offender) {
        fwrite(STDERR, "   $offender\n");
    }
    fwrite(STDERR, sprintf("\n%d occurrence(s) across custom code.\n", count($offenders)));
    exit(1);
}

fwrite(STDOUT, "✅ No configuration-dependent short tags in custom code (" . count($files) . " files scanned).\n");
exit(0);
