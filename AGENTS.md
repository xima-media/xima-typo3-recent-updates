# AGENTS.md

Guidance for coding agents working in this repository.

## Project overview

`xima/xima-typo3-recent-updates` is a TYPO3 extension (`xima_typo3_recent_updates`) that provides a dashboard widget listing recently updated content elements, based on the `sys_log` table.

- PHP: `~8.1 || ~8.2 || ~8.3 || ~8.4`
- TYPO3: `^11.5 || ^12.4 || ^13.4`
- Namespace: `Xima\XimaTypo3RecentUpdates\` maps to `Classes/`

## Structure

- `Classes/Widgets/RecentUpdates.php`: the widget (`WidgetInterface`)
- `Classes/Widgets/Provider/`: data provider (queries `sys_log`) and button provider
- `Classes/Domain/Model/Dto/ListItem.php`: DTO for list entries, with version-specific factories (`createFromV11Log()`, `createFromV12Log()`)
- `Classes/Utility/`, `Classes/ViewHelpers/`, `Classes/Configuration.php` (extension key and name constants)
- `Configuration/`: `Services.yaml` (dependency injection and widget registration), `Icons.php`
- `Resources/Private/`: Fluid templates, partials, layouts, language files
- `Tests/Unit/`: PHPUnit tests mirroring `Classes/`
- `Documentation/`: TYPO3 documentation source
- `.ddev/`: DDEV setup with TYPO3 11, 12 and 13 instances
- Tool configs sit in the repository root: `php-cs-fixer.php`, `phpstan.neon`, `rector.php`, `phpunit.xml`

## Development commands

```bash
ddev start
ddev composer install
ddev install all               # or 11, 12, 13
ddev launch                    # open the development site
ddev 12 typo3 cache:flush      # TYPO3 commands per version
```

## Testing

There is a unit suite only.

```bash
composer test                  # phpunit -c phpunit.xml, no coverage
composer test:coverage         # runs with XDEBUG_MODE=coverage
vendor/bin/phpunit --filter <name>
```

CI (`.github/workflows/tests.yml`) uses a reusable workflow from `konradmichalik/reusable-github-actions`.

## Code style and static analysis

```bash
composer lint                  # composer, editorconfig, language, php, typoscript, yaml
composer fix                   # composer, editorconfig, php
composer sca                   # PHPStan level 5
composer migration             # Rector
```

- Individual targets: `lint:composer`, `lint:editorconfig`, `lint:language`, `lint:php`, `lint:typoscript`, `lint:yaml`, `fix:composer`, `fix:editorconfig`, `fix:php`, `sca:php`, `migration:rector`
- PHP CS Fixer: `php-cs-fixer.php`, Rector: `rector.php`
- PHPStan: `phpstan.neon` with strict rules, deprecation rules and a baseline (`phpstan-baseline.neon`). It analyses `Classes`, `Configuration`, `Resources` and `Tests/Unit`.
- Type coverage: return types 100%, parameters 95%, properties 95%
- Disallowed: `var_dump()`, `debug()`, `xdebug_break()`, `header()` (use the PSR-7 API) and the superglobals `$_GET`, `$_POST`, `$_FILES`
- CI runs the CGL workflow (`.github/workflows/cgl.yml`) on every push

## Git workflow

- Commit format: `<type>: <description>` with type one of `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`, `ci`
- Do not add co-author trailers
- Run lint, static analysis and tests before opening a pull request
