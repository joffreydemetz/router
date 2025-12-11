# Router Test Suite

Complete test coverage for the JDZ Router package.

## Test Structure

```
tests/
├── ExceptionTest.php              # Tests for RouterException and NoRoutesException
├── GeneratorTest.php              # Legacy compatibility tests
├── RouteTest.php                  # Tests for Route class (route loading)
├── RouterTest.php                 # Tests for Router class (main router functionality)
└── Generator/
    ├── RouteTest.php              # Tests for Generator\Route class
    ├── RoutesTest.php             # Tests for Generator\Routes class
    └── RoutesImmutableTest.php    # Tests for Generator\RoutesImmutable class
```

## Running Tests

### Run all tests
```bash
vendor/bin/phpunit
```

### Run with test descriptions
```bash
vendor/bin/phpunit --testdox
```

### Run specific test file
```bash
vendor/bin/phpunit tests/RouterTest.php
```

### Run specific test method
```bash
vendor/bin/phpunit --filter testRouterConstruction
```

### Run with coverage (requires Xdebug)
```bash
vendor/bin/phpunit --coverage-html coverage
```

## Test Coverage

### Router Class (RouterTest.php)
- ✅ Router construction
- ✅ Loading YAML route files
- ✅ Route matching
- ✅ URL generation (relative and absolute)
- ✅ Redirect path management
- ✅ Current URL/path retrieval
- ✅ Multiple YAML file support
- ✅ Error handling

### Route Class (RouteTest.php)
- ✅ Route construction
- ✅ Loading routes from router
- ✅ JSON route detection
- ✅ Request attribute parsing
- ✅ Query string handling
- ✅ Exception handling with request info

### Generator\Route Class (Generator/RouteTest.php)
- ✅ Route creation with auto-naming
- ✅ URL, name, title management
- ✅ Variables (vars) management
- ✅ Options management
- ✅ HTTP methods configuration
- ✅ JSON flag support
- ✅ Array export functionality
- ✅ Fluent interface
- ✅ Null value handling

### Generator\Routes Class (Generator/RoutesTest.php)
- ✅ Routes collection management
- ✅ Adding routes (objects and arrays)
- ✅ Creating routes
- ✅ Replacing vs. throwing on duplicates
- ✅ Route retrieval
- ✅ Export to array format
- ✅ Reset functionality
- ✅ Complex workflow scenarios

### Generator\RoutesImmutable Class (Generator/RoutesImmutableTest.php)
- ✅ Immutable route collection
- ✅ Prevention of modifications to existing routes
- ✅ Allowing new route additions
- ✅ Exception handling for mutation attempts
- ✅ Array and object route support
- ✅ All inherited functionality from Routes

### Exception Classes (ExceptionTest.php)
- ✅ RouterException as RuntimeException
- ✅ Request URI/path storage
- ✅ Fluent interface
- ✅ NoRoutesException inheritance
- ✅ Exception chaining

## Test Statistics

- **Total Tests**: 100
- **Total Assertions**: 196
- **Test Files**: 7
- **Coverage**: Comprehensive coverage of all public APIs

## Requirements

- PHP 8.2+
- PHPUnit 10.0+
- Symfony components (routing, http-foundation, config, string, yaml)

## Notes

- Tests use actual YAML files from `examples/routes/` for integration testing
- All tests are compatible with PHP 8.2+ features
- Tests verify both success and error scenarios
- Fluent interfaces are tested for proper return types
