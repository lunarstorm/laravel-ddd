# Changelog

All notable changes to `laravel-ddd` will be documented in this file.

## [0.2.0] - 2023-02-20
### Added
- Support for Laravel 10.

### Changed
- Install command now publishes config, registers the default domain path in composer.json, and prompts to publish stubs.
- Generator command signatures simplified to `ddd:*` (previously `ddd:make:*`). 

## [0.1.0] - 2023-01-19
### Added
- Early version of generator commands for domain models, dto, value objects, view models.
- `ddd:install` command to automatically register the domain folder inside the application's composer.json (experimental)
