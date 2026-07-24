<div align="center">

### 🌐 زبان / Language

[🇮🇷 خواندن به فارسی](README.md)  ❘  **🇬🇧 English (current page)**

[![فارسی](https://img.shields.io/badge/فارسی-اینجا%20کلیک%20کنید-lightgrey?style=for-the-badge&logo=googletranslate&logoColor=white)](README.md)
[![English](https://img.shields.io/badge/English-Current%20Page-239120?style=for-the-badge)](README.en.md)

---

# 🖼️ Cloner Smart Image Optimizer &amp; Auto WebP

**Automatically resize, compress and convert every WordPress Media Library upload to WebP.**

[![WordPress](https://img.shields.io/badge/WordPress-5.6%2B-21759B?logo=wordpress&logoColor=white)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-blue.svg)](LICENSE)
[![Version](https://img.shields.io/badge/Version-1.1.0-success.svg)](CHANGELOG.md)
[![RTL Support](https://img.shields.io/badge/RTL-Persian%20%2F%20English-orange.svg)](#-multilingual-persian--english)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Configuration](#%EF%B8%8F-configuration) • [FAQ](#-faq) • [Contributing](#-contributing)

</div>

---

## 📖 Overview

**Cloner Smart Image Optimizer &amp; Auto WebP** optimizes every image the moment it is uploaded to the WordPress Media Library. It resizes oversized images, compresses them, strips unnecessary metadata, and converts them to **WebP** using native WordPress image editors (Imagick with an automatic GD fallback).

No external API. No monthly quota. No image ever leaves your server.

> Developed and maintained by **Cloner (Shayan)** — [clonerr.ir](https://clonerr.ir)

---

## ✨ Features

### Core optimization
- **Automatic pipeline on upload** — detect → validate → fix orientation → resize → compress → convert to WebP → update metadata.
- **Supported input formats** — JPG, JPEG, PNG, BMP and GIF (first frame).
- **Configurable WebP quality** (default `85`) with optional **lossless mode** (Imagick).
- **Smart resizing** — maximum width/height, aspect-ratio preservation, and upscaling prevention.
- **Metadata / EXIF stripping** with optional ICC color-profile preservation.
- **Safe by design** — on any failure the original file is preserved and the error is logged.

### Media Library integration
- **Optimization column** showing original size, optimized size, saved %, WebP status, resize status and date.
- **Per-image row actions** — `Optimize now`, `Re-optimize`, `Restore original`.
- **Optional original backup** stored in `wp-content/uploads/sio-backups/`, restorable at any time.

### Bulk &amp; reporting
- **Bulk optimization screen** under *Media* with a live progress bar, estimated time, and pause / resume / cancel controls.
- **Dashboard widget** with aggregate savings statistics.
- **Detailed logging** of started/finished optimizations, skipped files and errors.

### Safety
- **Skip large files (MB)** guard to protect server memory on shared hosting.
- Backups are **never** deleted on uninstall.

---

## 🌍 Multilingual (Persian / English)

The entire admin UI is bilingual with a **one-click language switcher** on every plugin page:

| Option | Behaviour |
|---|---|
| **فارسی** | Full Persian UI with a dedicated **RTL stylesheet** |
| **English** | Full English UI (LTR) |
| **Follow site language** | Inherits the WordPress site locale |

The switch affects **only this plugin's screens** — the rest of your site is untouched.

---

## 📋 Requirements

| Requirement | Minimum |
|---|---|
| WordPress | `5.6` |
| PHP | `7.4` |
| Image library | **Imagick** (recommended) or **GD** compiled with WebP support |
| Tested up to | WordPress `6.6` |

---

## 🚀 Installation

### Option 1 — Upload the ZIP (recommended)

1. Download the latest `smart-image-optimizer.zip` from the [**Releases**](../../releases) page.
2. In WordPress go to **Plugins → Add New → Upload Plugin**.
3. Choose the ZIP file and click **Install Now**.
4. Click **Activate**.

### Option 2 — Manual (FTP / cPanel)

1. Download and extract the ZIP.
2. Upload the `smart-image-optimizer` folder to `/wp-content/plugins/`.
3. Activate the plugin from **Plugins** in the WordPress admin.

### Option 3 — Git clone (for developers)

```bash
cd wp-content/plugins
git clone https://github.com/Shayan-alinezhad/smart-image-optimizer.git
```

---

## 🎯 Usage

### 1. Configure the plugin
Go to **Cloner Image Optimizer → Settings** and set your quality, max dimensions and backup preference.

### 2. Upload normally
Every new upload is optimized automatically. Nothing else to do.

### 3. Optimize existing images
Go to **Media → Bulk Optimize**, click **Start**, and watch the progress bar.

### 4. Fine-tune per image
In the Media Library, hover any row and use `Optimize now`, `Re-optimize` or `Restore original`.

---

## ⚙️ Configuration

| Setting | Default | Description |
|---|---|---|
| **WebP Quality** | `85` | 1–100. Higher = better quality, larger file. |
| **Lossless Mode** | `off` | Imagick only. Perfect quality, larger output. |
| **Max Width** | `2560` | Images wider than this are resized down. |
| **Max Height** | `2560` | Images taller than this are resized down. |
| **Keep Originals** | `on` | Backs up the source file before optimizing. |
| **Strip Metadata** | `on` | Removes EXIF/IPTC data. |
| **Preserve ICC Profile** | `on` | Keeps color accuracy when stripping metadata. |
| **Skip Large Files (MB)** | `0` | `0` = no limit. Protects low-memory hosts. |
| **Enable Logging** | `on` | Records every operation and error. |
| **Interface Language** | `Follow site` | فارسی / English / Follow site language. |

---

## 🗂️ Project structure

```
smart-image-optimizer/
├── smart-image-optimizer.php      # Plugin bootstrap + header
├── uninstall.php                  # Clean uninstall routine
├── readme.txt                     # WordPress.org readme
├── includes/
│   ├── class-autoloader.php       # PSR-4 style autoloader
│   ├── class-plugin.php           # Main singleton / boot
│   ├── class-settings.php         # Options API wrapper
│   ├── class-logger.php           # Logging engine
│   ├── class-stats.php            # Aggregate statistics
│   ├── class-i18n.php             # Translation + language switcher
│   ├── helpers.php                # Procedural helpers
│   ├── image/
│   │   ├── class-processor.php    # Orchestrates the pipeline
│   │   └── class-optimizer.php    # Resize / compress / WebP
│   ├── media/
│   │   ├── class-upload-handler.php
│   │   ├── class-columns.php
│   │   └── class-row-actions.php
│   ├── admin/
│   │   ├── class-admin.php
│   │   ├── class-settings-page.php
│   │   ├── class-bulk-page.php
│   │   ├── class-logs-page.php
│   │   ├── class-dashboard-widget.php
│   │   └── class-ajax.php
│   └── setup/
│       ├── class-activator.php
│       └── class-deactivator.php
├── templates/                     # Admin view templates
├── assets/
│   ├── css/  (admin.css, admin-rtl.css)
│   └── js/   (admin.js, bulk.js)
└── languages/                     # .pot + Persian translation
```

---

## ❓ FAQ

<details>
<summary><strong>Does this require Imagick?</strong></summary>
<br>
No. The plugin uses <code>wp_get_image_editor()</code>, which prefers Imagick and falls back to GD. WebP conversion requires either extension to be compiled with WebP support.
</details>

<details>
<summary><strong>What happens to my original images?</strong></summary>
<br>
When <em>Keep Originals</em> is enabled, a copy is stored under <code>wp-content/uploads/sio-backups/</code> before optimization and can be restored from the Media Library row actions. When disabled, the original is removed after a successful WebP conversion.
</details>

<details>
<summary><strong>Are backups removed on uninstall?</strong></summary>
<br>
No. Options and optimization metadata are removed on uninstall, but backup files are intentionally left in place so you never lose data. Delete <code>uploads/sio-backups/</code> manually if you no longer need them.
</details>

<details>
<summary><strong>How do I change the plugin language?</strong></summary>
<br>
Every plugin admin page has a language switcher in the top-right corner with three buttons: فارسی, English, and "Follow site language". You can also set it in <strong>Settings → General → Interface Language</strong>.
</details>

<details>
<summary><strong>Will it break my existing images?</strong></summary>
<br>
No. Every operation is wrapped in error handling — if anything fails, the original file is left untouched and the error is written to the log.
</details>

---

## 🤝 Contributing

Contributions are very welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request.

1. Fork the repository
2. Create your branch — `git checkout -b feature/amazing-feature`
3. Commit your changes — `git commit -m 'feat: add amazing feature'`
4. Push the branch — `git push origin feature/amazing-feature`
5. Open a Pull Request

---

## 🔒 Security

Found a vulnerability? Please **do not** open a public issue. See [SECURITY.md](SECURITY.md) for responsible disclosure instructions.

---

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for the full release history.

---

## 📄 License

This project is licensed under the **GNU General Public License v2.0 or later** — see the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Cloner (Shayan)**

- Website — [clonerr.ir](https://clonerr.ir)
- Support — [clonerr.ir/support](https://clonerr.ir/support)

---

<div align="center">

**If this plugin saved you bandwidth, please give it a ⭐**

Made with ❤️ by [Cloner](https://clonerr.ir)

</div>
