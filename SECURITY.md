# Security Policy

## Supported Versions

| Version | Supported |
|---|---|
| 1.1.x   | :white_check_mark: |
| 1.0.x   | :x: |

Only the latest minor release receives security fixes. Please keep the plugin updated.

---

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, report them privately using one of these channels:

1. **GitHub Security Advisories** — use the *Security* tab → *Report a vulnerability* (preferred).
2. **Website** — [clonerr.ir/support](https://clonerr.ir/support)

### What to include

Please provide as much of the following as possible:

- Type of issue (e.g. path traversal, arbitrary file write, XSS, CSRF, privilege escalation)
- Full paths of the affected source file(s)
- The location of the affected code (tag / branch / commit or direct URL)
- Any special configuration required to reproduce
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code, if available
- The impact of the issue and how an attacker might exploit it

---

## Response Timeline

| Stage | Target |
|---|---|
| Initial acknowledgement | within **48 hours** |
| Triage & severity assessment | within **7 days** |
| Fix released (critical) | within **14 days** |
| Fix released (low/medium) | next scheduled release |
| Public disclosure | after a fix is available |

---

## Disclosure Policy

We follow **coordinated disclosure**:

1. You report the issue privately.
2. We confirm and work on a fix.
3. We release the fix and credit you in the changelog (unless you prefer to stay anonymous).
4. Details are published after users have had a reasonable window to update.

---

## Security Practices in This Plugin

This plugin follows WordPress security best practices:

- **Direct access prevention** — every PHP file exits if `ABSPATH` is undefined.
- **Capability checks** — all admin actions require `manage_options` or `upload_files` as appropriate.
- **Nonce verification** — every form submission and AJAX request is nonce-protected.
- **Input sanitization** — all user input is sanitized with the WordPress sanitization API.
- **Output escaping** — all output is escaped with `esc_html()`, `esc_attr()`, `esc_url()`.
- **Path validation** — file operations are constrained to the WordPress uploads directory.
- **MIME validation** — uploads are validated with `wp_check_filetype_and_ext()` before processing.
- **No external requests** — images are never sent to a third-party service.

---

Thank you for helping keep this project and its users safe. 🔒
