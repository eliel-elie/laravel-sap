# Testing Guide

This document provides information about the test suite for the Laravel SAP Driver package.

## Overview

The test suite is built using [Pest PHP](https://pestphp.com/), a modern testing framework for PHP. Tests are organized into two main categories:

- **Unit Tests**: Test individual classes and methods in isolation
- **Feature Tests**: Test integrated functionality (most require SAP connection or mocking)

## Running Tests

### Run All Tests
```bash
php vendor/bin/pest
```

### Run Tests with Output
```bash
php vendor/bin/pest --testdox
```

### Run Specific Test File
```bash
php vendor/bin/pest tests/Unit/GuidTest.php
```

### Run Tests with Coverage
```bash
php vendor/bin/pest --coverage
```

### Run Tests in Parallel
```bash
php vendor/bin/pest --parallel
```

## Test Structure

### Unit Tests (`tests/Unit/`)

These tests do not require a SAP connection and test isolated functionality:

#### ✅ **GuidTest.php**
Tests the GUID helper for converting between byte and char32 formats.

```php
describe('Guid Helper', function () {
    it('converts byte to char32 format', function () {
        $guid = new Guid();
        $result = $guid->byteToChar32(pack('H*', 'a1b2c3d4e5f6'));
        
        expect($result)->toBe('A1B2C3D4E5F6');
    });
});
```

#### ✅ **ArrTest.php**
Tests array helper methods for trimming, UTF-8 conversion, and GUID handling.

#### ✅ **QueryBuilderTest.php**
Tests the query builder for constructing SAP WHERE clauses:
- Simple where conditions
- OR/AND conditions
- Grouped conditions with closures
- Array values (IN clause simulation)
- Complex nested queries

#### ✅ **ServerTest.php**
Tests server configuration validation and parameter requirements.

### Feature Tests (`tests/Feature/`)

These tests require SAP connection mocking or are marked as skipped:

#### ⏭️ **ConnectionTest.php**
Tests for SAP connection management (requires mocking).

#### ⏭️ **SapTest.php**
Tests for the main Sap manager class:
- Connection management
- Configuration handling
- Multiple connections
- Connection testing

#### ⏭️ **FunctionModuleTest.php**
Tests for RFC function module calls:
- Parameter validation
- Type checking
- Execution
- Error handling

#### ⏭️ **TableTest.php**
Tests for RFC_READ_TABLE wrapper:
- Query building
- Field selection
- Data parsing
- Date conversion
- Integration with QueryBuilder

## Test Coverage

### Currently Implemented: ✅

| Component | Coverage | Status |
|-----------|----------|--------|
| Guid Helper | 100% | ✅ Complete |
| Arr Helper | 100% | ✅ Complete |
| QueryBuilder | 100% | ✅ Complete |
| Server | 100% | ✅ Complete |

### Requires SAP Connection: ⏭️

| Component | Status | Notes |
|-----------|--------|-------|
| Connection | ⏭️ Skipped | Requires SAPNWRFC extension or advanced mocking |
| Sap Manager | ⏭️ Partial | Basic tests pass, connection tests skipped |
| FunctionModule | ⏭️ Skipped | Requires connection mock |
| Table | ⏭️ Skipped | Requires connection mock |

## Writing Tests

### Test Structure with `describe` Blocks

All tests use the `describe` syntax for better organization:

```php
describe('ClassName', function () {
    beforeEach(function () {
        // Setup before each test in this block
        $this->instance = new ClassName();
    });

    describe('methodName', function () {
        it('does something specific', function () {
            $result = $this->instance->methodName();
            
            expect($result)->toBe('expected');
        });

        it('handles edge case', function () {
            expect(fn() => $this->instance->methodName(null))
                ->toThrow(Exception::class);
        });
    });
});
```

### Expectations

Common expectations used in tests:

```php
// Value assertions
expect($value)->toBe('exact match');
expect($value)->toEqual(['array', 'match']);
expect($value)->toBeTrue();
expect($value)->toBeFalse();
expect($value)->toBeNull();

// Type assertions
expect($value)->toBeString();
expect($value)->toBeInt();
expect($value)->toBeArray();
expect($value)->toBeInstanceOf(ClassName::class);

// Collection assertions
expect($array)->toHaveCount(5);
expect($array)->toContain('value');
expect($array)->toHaveKey('key');

// String assertions
expect($string)->toContain('substring');
expect($string)->toHaveLength(10);

// Exception assertions
expect(fn() => someFunction())
    ->toThrow(Exception::class);

expect(fn() => someFunction())
    ->toThrow(Exception::class, 'error message');

// Chaining
expect($result)
    ->toBeString()
    ->toHaveLength(32)
    ->toBe('EXPECTED_VALUE');
```

### Mocking (for Feature Tests)

When SAP connection is required, use Laravel's Config facade mocking:

```php
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::shouldReceive('get')
        ->with('sap.connections.default')
        ->andReturn([
            'user' => 'testuser',
            'passwd' => 'testpass',
            'client' => '100',
            'ashost' => 'localhost',
            'sysnr' => '00',
            'lang' => 'EN',
        ]);
});
```

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: mbstring
          
      - name: Install Dependencies
        run: composer install --no-interaction
        
      - name: Run Tests
        run: php vendor/bin/pest --testdox
```

## Notes

### SAP Extension Requirement

Most feature tests require the `sapnwrfc` extension which is not typically available in CI/CD environments. These tests are marked as skipped when the extension is not loaded.

To run these tests locally with SAP:
1. Install the [php7-sapnwrfc](https://gkralik.github.io/php7-sapnwrfc/) extension
2. Configure SAP connection in your environment
3. Tests will automatically detect the extension and run

### Adding New Tests

When adding new features:
1. Write unit tests first (if possible without SAP connection)
2. Add feature tests with proper mocking or skip markers
3. Document any special requirements
4. Update this guide with new test information

## Best Practices

1. ✅ Use `describe` blocks to organize related tests
2. ✅ Write descriptive test names using `it('does something')`
3. ✅ Keep tests isolated and independent
4. ✅ Mock external dependencies
5. ✅ Test both success and failure cases
6. ✅ Use `beforeEach` for common setup
7. ✅ Chain expectations for cleaner assertions
8. ✅ Mark tests that require special setup as skipped

## Resources

- [Pest PHP Documentation](https://pestphp.com/docs)
- [Laravel Testing](https://laravel.com/docs/testing)
- [php7-sapnwrfc](https://gkralik.github.io/php7-sapnwrfc/)
