# GitHub Workflows Documentation

This project uses GitHub Actions for automated testing and releases.

## 📋 Workflows Overview

### 1. **Tests Workflow** (`tests.yml`)
Runs on every push and pull request to `main` and `develop` branches.

#### Jobs:
- **tests**: Runs tests across multiple PHP and Laravel versions
  - PHP: 8.2, 8.3, 8.4
  - Laravel: 10.*, 11.*, 12.*
  - Excludes: PHP 8.2 + Laravel 12 (incompatible)
  - Steps:
    1. Checkout code
    2. Setup PHP with required extensions
    3. Install dependencies
    4. Run Pint (code style check)
    5. Execute Pest tests

- **coverage**: Generates code coverage report
  - PHP 8.3 with Xdebug
  - Uploads to Codecov (optional)

### 2. **Release Workflow** (`release.yml`)
Triggered when you push a version tag (e.g., `v1.0.6`).

#### Steps:
1. Extract version from tag
2. Extract release notes from CHANGELOG.md
3. Create GitHub release with notes

## 🚀 How to Create a Release

### Step 1: Update CHANGELOG.md
Move changes from `[Unreleased]` to a new version section:

```markdown
## [Unreleased]

## [v1.0.6] - 2026-03-04

### Added
- New feature X
- New feature Y

### Fixed
- Bug fix Z
```

### Step 2: Commit and Push
```bash
git add CHANGELOG.md
git commit -m "Release v1.0.6"
git push origin main
```

### Step 3: Create and Push Tag
```bash
# Create tag
git tag v1.0.6

# Push tag to trigger release
git push origin v1.0.6
```

### Step 4: Verify
- GitHub Actions will automatically:
  1. Create a GitHub Release
  2. Extract notes from CHANGELOG.md
  3. Publish the release

## 🛠️ Local Commands

### Testing
```bash
# Run all tests
composer test

# Run with detailed output
composer test:dox

# Run with coverage
composer test:coverage
```

### Code Style
```bash
# Check code style
composer lint

# Fix code style automatically
composer format
```

## 📊 Test Matrix

| PHP Version | Laravel 10 | Laravel 11 | Laravel 12 |
|-------------|-----------|-----------|-----------|
| 8.2 | ✅ | ✅ | ❌ |
| 8.3 | ✅ | ✅ | ✅ |
| 8.4 | ✅ | ✅ | ✅ |

## 🔍 Workflow Triggers

### Tests Workflow
```yaml
on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]
```
Runs on:
- Every push to `main` or `develop`
- Every pull request targeting `main` or `develop`

### Release Workflow
```yaml
on:
  push:
    tags:
      - 'v*'
```
Runs on:
- Pushing a tag starting with `v` (e.g., `v1.0.0`, `v2.1.3`)

## 🎯 Best Practices

### For Contributors
1. ✅ Always run `composer lint` before committing
2. ✅ Always run `composer test` before pushing
3. ✅ Add tests for new features
4. ✅ Update CHANGELOG.md for your changes

### For Maintainers
1. ✅ Keep `[Unreleased]` section updated
2. ✅ Follow semantic versioning (MAJOR.MINOR.PATCH)
3. ✅ Create meaningful release notes
4. ✅ Test locally before creating tags

## 🐛 Troubleshooting

### Tests Failing in CI but Pass Locally
- Check PHP version compatibility
- Verify Laravel version compatibility
- Clear composer cache: `composer clear-cache`

### Release Not Created
- Verify tag format starts with `v` (e.g., `v1.0.0`)
- Check CHANGELOG.md has matching version header
- Verify GitHub token permissions

### Code Style Failures
- Run `composer format` to auto-fix
- Check `.editorconfig` for consistency

## 📚 Related Files

- `.github/workflows/tests.yml` - Tests workflow
- `.github/workflows/release.yml` - Release workflow
- `.github/CONTRIBUTING.md` - Contribution guidelines
- `composer.json` - Scripts definition
- `CHANGELOG.md` - Version history
- `TESTING.md` - Testing guide

## 🔗 External Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Semantic Versioning](https://semver.org/)
- [Keep a Changelog](https://keepachangelog.com/)
- [Pest PHP](https://pestphp.com/)
- [Laravel Pint](https://laravel.com/docs/pint)
