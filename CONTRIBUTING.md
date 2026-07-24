# Contributing to Cloner Smart Image Optimizer

First off — thank you for taking the time to contribute! 🎉

This document explains how to set up the project, the coding standards we follow, and how to submit changes.

---

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Messages](#commit-messages)
- [Branching Strategy](#branching-strategy)
- [Pull Request Process](#pull-request-process)
- [Reporting Bugs](#reporting-bugs)
- [Translations](#translations)

---

## Code of Conduct

This project follows the [Code of Conduct](CODE_OF_CONDUCT.md). By participating you are expected to uphold it.

---

## Getting Started

1. **Fork** the repository on GitHub.
2. **Clone** your fork:
   ```bash
   git clone https://github.com/YOUR-Shayan-alinezhad/smart-image-optimizer.git
   cd smart-image-optimizer
   ```
3. **Add the upstream remote** so you can stay in sync:
   ```bash
   git remote add upstream https://github.com/ORIGINAL-OWNER/smart-image-optimizer.git
   ```

---

## Development Setup

### Requirements

| Tool | Version |
|---|---|
| PHP | `7.4+` |
| WordPress | `5.6+` |
| Composer | `2.x` (for linting tools) |
| Imagick **or** GD | with WebP support |

### Install the plugin locally

Symlink or clone the repository directly into your local WordPress install:

```bash
cd /path/to/wordpress/wp-content/plugins
git clone https://github.com/YOUR-Shayan-alinezhad/smart-image-optimizer.git
```

Then activate it from **Plugins** in the WordPress admin.

### Install dev tooling

```bash
composer install
```

### Run the linters

```bash
# PHP syntax check on every file
composer run lint

# WordPress Coding Standards
composer run phpcs

# Auto-fix what can be fixed
composer run phpcbf
```

---

## Coding Standards

We follow the [**WordPress PHP Coding Standards**](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).

### Key rules

- **Indentation:** real tabs, not spaces.
- **Yoda conditions:** `if ( 'value' === $var )`.
- **Spacing inside parentheses:** `function_name( $arg )`.
- **Prefixing:** every global function, constant and option uses the `sio_` / `SIO_` prefix.
- **Namespacing:** classes live under the `SmartImageOptimizer\` namespace.
- **Escaping:** always escape on output — `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.
- **Sanitizing:** always sanitize on input — `sanitize_text_field()`, `absint()`, etc.
- **Nonces & capabilities:** every form and AJAX handler must verify a nonce **and** check `current_user_can()`.
- **Translations:** wrap every user-facing string in `__()` / `_e()` / `esc_html__()` with the `smart-image-optimizer` text domain.
- **Direct access guard:** every PHP file begins with:
  ```php
  if ( ! defined( 'ABSPATH' ) ) {
      exit;
  }
  ```

### File naming

| Type | Pattern | Example |
|---|---|---|
| Class file | `class-{name}.php` | `class-optimizer.php` |
| Template | `{name}-page.php` | `settings-page.php` |
| Partial | `partials/{name}.php` | `partials/header.php` |

---

## Commit Messages

We use [**Conventional Commits**](https://www.conventionalcommits.org/).

```
<type>(<optional scope>): <short description>

<optional body>

<optional footer>
```

### Types

| Type | Use for |
|---|---|
| `feat` | A new feature |
| `fix` | A bug fix |
| `docs` | Documentation only |
| `style` | Formatting, no logic change |
| `refactor` | Code change that is neither a fix nor a feature |
| `perf` | Performance improvement |
| `test` | Adding or fixing tests |
| `build` | Build system or dependencies |
| `ci` | CI configuration |
| `chore` | Everything else |

### Examples

```
feat(optimizer): add AVIF output support
fix(bulk): prevent timeout on libraries with 10k+ images
docs(readme): clarify Imagick requirement
refactor(i18n): extract language switcher into its own partial
```

---

## Branching Strategy

| Branch | Purpose |
|---|---|
| `main` | Stable, released code. Protected. |
| `develop` | Integration branch for the next release. |
| `feature/*` | New features — `feature/avif-support` |
| `fix/*` | Bug fixes — `fix/bulk-timeout` |
| `docs/*` | Documentation — `docs/faq-update` |

Always branch from `develop` (or `main` if `develop` does not exist).

```bash
git checkout develop
git pull upstream develop
git checkout -b feature/my-feature
```

---

## Pull Request Process

1. **Sync with upstream** before you start and before you push.
2. **Keep PRs focused** — one logical change per PR.
3. **Test manually** on a real WordPress install with both Imagick and GD if possible.
4. **Update documentation** — `README.md`, `readme.txt` and `CHANGELOG.md` under `[Unreleased]`.
5. **Bump the version** only if a maintainer asks you to. Version lives in three places:
   - `smart-image-optimizer.php` header `Version:`
   - `SIO_VERSION` constant
   - `readme.txt` `Stable tag:`
6. **Fill in the PR template** completely.
7. **Ensure CI is green** before requesting review.

### Checklist before submitting

- [ ] Code follows WordPress Coding Standards
- [ ] All strings are translatable and escaped
- [ ] Nonces and capability checks are in place
- [ ] No PHP notices, warnings or errors with `WP_DEBUG` enabled
- [ ] Tested in both LTR (English) and RTL (Persian) modes
- [ ] `CHANGELOG.md` updated

---

## Reporting Bugs

Open an issue using the **Bug report** template and include:

- WordPress version
- PHP version
- Image library (Imagick / GD) and whether WebP is supported
- Active theme and other active plugins
- Steps to reproduce
- Relevant entries from the plugin's **Logs** page

---

## Translations

The plugin ships with a `.pot` file at `languages/smart-image-optimizer.pot`.

### Adding a new language

1. Copy the `.pot` file to `languages/smart-image-optimizer-{locale}.po` (e.g. `-ar.po`).
2. Translate the strings with [Poedit](https://poedit.net/) or any PO editor.
3. Generate the `.mo` file.
4. If the language is RTL, verify the layout against `assets/css/admin-rtl.css`.
5. Submit a PR with both `.po` and `.mo` files.

### Regenerating the POT file

```bash
wp i18n make-pot . languages/smart-image-optimizer.pot
```

---

## Questions?

Open a [Discussion](../../discussions) or visit [clonerr.ir](https://clonerr.ir).

Thank you for contributing! 🙏
