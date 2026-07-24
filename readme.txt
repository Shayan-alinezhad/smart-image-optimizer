=== Cloner Smart Image Optimizer & Auto WebP ===
Contributors: cloner
Donate link: https://clonerr.ir
Tags: images, webp, optimize, compress, resize, media, performance, persian, farsi
Requires at least: 5.6
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically resize, compress and convert Media Library uploads to WebP. Bulk optimization, backups, statistics, logging, and a built-in Persian/English switcher. By Cloner - https://clonerr.ir

== Description ==

**Cloner Smart Image Optimizer & Auto WebP** optimizes every image the moment it is uploaded to the WordPress Media Library. It resizes oversized images, compresses them, strips unnecessary metadata and converts them to WebP using native WordPress image editors (Imagick with a GD fallback).

Developed and maintained by **Cloner** - [clonerr.ir](https://clonerr.ir).

**Key features**

* Automatic optimization pipeline on upload: detect, validate, fix orientation, resize, compress, convert to WebP, update metadata.
* Supported input formats: JPG, JPEG, PNG, BMP and GIF (first frame).
* Configurable WebP quality (default 85) with optional lossless mode (Imagick).
* Smart resizing with maximum width/height, aspect-ratio preservation and upscaling prevention.
* Metadata / EXIF stripping with optional ICC color-profile preservation.
* Optional backup of the original file so it can be restored at any time.
* Media Library column showing original size, optimized size, saved %, WebP status, resize status and optimization date.
* Per-image row actions: **Optimize now**, **Re-optimize** and **Restore original**.
* Bulk optimization screen under Media with progress bar, estimated time, and pause / resume / cancel controls.
* Dashboard widget with aggregate statistics.
* Detailed logging of started/finished optimizations, skipped files and errors.
* **Bilingual admin UI (Persian / English)** with a one-click language switcher on every plugin page, including full RTL styling for Persian.
* Optional "skip large files" guard to protect server memory on shared hosting.
* Safe by design: on any failure the original file is preserved and the error is logged.

== Installation ==

1. Upload the `smart-image-optimizer` folder to `/wp-content/plugins/`.
2. Activate the plugin through the “Plugins” menu in WordPress.
3. Go to **Cloner Image Optimizer > Settings** to configure options.
4. Use the language buttons at the top of any plugin page to switch between فارسی and English.
5. Optionally run **Media > Bulk Optimize** to process existing images.

== Frequently Asked Questions ==

= How do I change the plugin language? =
Every plugin admin page has a language switcher in the top-right corner with three buttons: فارسی, English, and "Follow site language". You can also set it in **Settings > General > Interface Language**. The switch affects only this plugin's screens, not the rest of your site.

= Does this require Imagick? =
No. The plugin uses `wp_get_image_editor()`, which prefers Imagick and falls back to GD. WebP conversion requires either extension to be compiled with WebP support.

= What happens to my original images? =
When “Keep Originals” is enabled, a copy is stored under `wp-content/uploads/sio-backups/` before optimization and can be restored from the Media Library row actions. When disabled, the original is removed after a successful WebP conversion.

= Are backups removed on uninstall? =
No. Options and optimization metadata are removed on uninstall, but backup files are intentionally left in place so you never lose data. Delete `uploads/sio-backups/` manually if you no longer need them.

= Where can I get support? =
Visit [clonerr.ir](https://clonerr.ir).

== Screenshots ==

1. Settings page with the Persian / English language switcher.
2. Bulk optimization screen with progress bar and estimated time.
3. Media Library optimization column.
4. Dashboard statistics widget.

== Changelog ==

= 1.1.0 =
* Added a full Persian translation and a one-click Persian/English language switcher on every plugin page.
* Added dedicated RTL stylesheet for the Persian interface.
* Added "Interface Language" setting (Persian / English / Follow site language).
* Added Media Library row actions: Optimize now, Re-optimize, Restore original.
* Added "Skip Large Files (MB)" safety setting.
* Added Cloner branding and clonerr.ir links across the admin UI, plugin header and plugin row meta.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.0 =
Adds a bilingual Persian/English admin interface with a language switcher, per-image row actions, and a large-file safety guard.
