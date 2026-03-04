# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v1.0.7] - 2026-03-04

### Added
- Comprehensive test suite using Pest PHP (44 tests passing)
- Unit tests for Guid, Arr, QueryBuilder, and Server classes
- Feature test stubs for SAP-dependent components
- TESTING.md documentation for test guidelines
- TEST_SUMMARY.md with detailed test coverage report
- Composer scripts for testing and linting (`composer test`, `composer lint`, etc.)
- Separate GitHub Actions workflows for tests and releases
- WORKFLOWS.md documentation for CI/CD processes
- Mockery dependency for advanced mocking

### Changed
- Fixed type hints in QueryBuilder to support Closure parameters
- Fixed Arr::utf8ize() return type to support mixed types
- Improved QueryBuilder type safety with union types
- Separated tests workflow from release workflow
- Updated CONTRIBUTING.md with new test commands
- Improved GitHub Actions workflow dependencies
- Release workflow now triggers only on version tags

### Fixed
- Connection test to expect Error instead of Exception for missing SAPNWRFC
- GitHub Actions workflow missing dependencies (illuminate/console)

## [v1.0.5] - 2026-03-04

### Changed
- Updated PHP requirement to ^8.2
- Updated Laravel support to versions ^10, ^11, ^12

### Added
- Support for Laravel 12
- Laravel Pint for code formatting (^1.27)
- Pest PHP for testing (^4.4)

## [v1.0.0] - Initial Release

### Added
- SAP connection integration with Laravel
- Support for SAP Function Module calls
- Query Builder for RFC_READ_TABLE
- Configuration file with multiple connection support
- Environment variables configuration
- Service Provider and Facade
- Connection management (open/close)
- Function Module parameter handling
- Table query builder with where/orWhere/limit methods
- Support for PHP >= 8.0
- Support for Laravel 10, 11

### Requirements
- PHP >= 8.0
- php7-sapnwrfc extension
- Laravel 10 or higher

[Unreleased]: https://github.com/elielelie/laravel-sap/compare/v1.0.5...HEAD
[1.0.5]: https://github.com/elielelie/laravel-sap/compare/v1.0.0...v1.0.5
[1.0.0]: https://github.com/elielelie/laravel-sap/releases/tag/v1.0.0
