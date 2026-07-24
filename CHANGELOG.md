# Changelog

All notable changes to **Cloner Smart Image Optimizer & Auto WebP** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- AVIF output support.
- WP-CLI command for headless bulk optimization.
- Per-image-size (thumbnail/medium/large) quality overrides.

---

## [1.1.0] - 2025-01-01

### Added
- Full **Persian translation** and a one-click Persian/English language switcher on every plugin page.
- Dedicated **RTL stylesheet** (`assets/css/admin-rtl.css`) for the Persian interface.
- **Interface Language** setting (Persian / English / Follow site language).
- Media Library **row actions**: `Optimize now`, `Re-optimize`, `Restore original`.
- **Skip Large Files (MB)** safety setting to protect low-memory hosts.
- Cloner branding and `clonerr.ir` links across the admin UI, plugin header and plugin row meta.

---

## [1.0.0] - 2024-01-01

### Added
- Initial release.
- Automatic optimization pipeline on upload: detect, validate, fix orientation, resize, compress, convert to WebP, update metadata.
- Support for JPG, JPEG, PNG, BMP and GIF (first frame).
- Configurable WebP quality with optional lossless mode (Imagick).
- Smart resizing with max width/height, aspect-ratio preservation and upscaling prevention.
- Metadata / EXIF stripping with optional ICC color-profile preservation.
- Optional backup of original files with restore support.
- Media Library optimization column with size, savings and status.
- Bulk optimization screen with progress bar, ETA and pause/resume/cancel.
- Dashboard widget with aggregate statistics.
- Detailed logging of operations, skips and errors.

---

[Unreleased]: https://github.com/Shayan-alinezhad/smart-image-optimizer/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/Shayan-alinezhad/smart-image-optimizer/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/Shayan-alinezhad/smart-image-optimizer/releases/tag/v1.0.0
