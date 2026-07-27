# CI regression gates (issue #48)

Automated gates for the failure classes found by the runtime/security audit
(tracker #58). They run in `.github/workflows/ci.yml` on pull requests and on
pushes to `develop`/`main`, and each is runnable locally.

## Blocking gates

| Check | Command | Guards |
|-------|---------|--------|
| PHP parse + short tags | `php bin/ci/lint-php.php` | Parse errors and hard short-tag breakage — runs `php -l` with `short_open_tag=Off` on every custom file, across the PHP matrix (8.1/8.2/8.3). |
| Short tags | `php bin/ci/check-short-tags.php` | Any configuration-dependent `<?` short open tag (#45). Catches inline `<? echo ?>` that `php -l` treats as text. |
| Local assets | `php bin/ci/check-asset-refs.php` | Statically-referenced theme/plugin assets (`/wp-content/...`, `get_stylesheet_directory_uri() . '...'`) that don't exist on disk (#53). |
| JavaScript | `bash bin/ci/check-js.sh` | Syntax of custom source JS via `node --check`. |
| Unit tests | `composer test` (PHPUnit) | Regression coverage for the malformed-metadata failure class (event date normalization, #57). |

## Reported (non-blocking for now)

Promote to required once PRs #54/#55 land and the counts reach zero.

| Check | Command | Reports |
|-------|---------|---------|
| WordPress standards | `composer phpcs` | `WordPress.Security.*` (escaping, nonces, input) + short tags + PHP-version compatibility (`phpcs.xml.dist`). |
| Superglobal output | `php bin/ci/check-superglobal-output.php` | Request data echoed without escaping. |

## Scope

"Custom code" = `wp-content/themes/academyAfrica`, `wp-content/plugins/academy-africa`
(if present), and `wp-content/mu-plugins/academy-error-logger.php` — see
`bin/ci/_custom_php_files.php`. Third-party plugins and WordPress core are excluded.

## Local setup

```bash
composer install        # PHPUnit + PHP_CodeSniffer + WPCS (dev-only, not deployed)
composer test           # unit tests
composer phpcs          # standards report
php bin/ci/lint-php.php  # (and the other bin/ci checks)
```

## Not yet covered

Full end-to-end integration coverage for authentication, password reset, and
LearnDash completion/certificate journeys needs a booted WordPress test
environment (`wp-env` / `wp-phpunit` + a database). The current unit suite covers
the isolated, pure logic; the booted-WP journeys are a follow-up.
