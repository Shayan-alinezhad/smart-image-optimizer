<?php
/**
 * Persian (فارسی) translation map for Smart Image Optimizer & Auto WebP.
 *
 * Returns an associative array of original English string => Persian string.
 * Loaded by SmartImageOptimizer\I18n when the admin language is set to Persian.
 *
 * @package SmartImageOptimizer
 * @author  Cloner (Shayan)
 * @link    https://clonerr.ir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	// --- Plugin identity / menus -------------------------------------------
	'Smart Image Optimizer & Auto WebP'                                                                                        => 'بهینه‌ساز هوشمند تصاویر و تبدیل خودکار به WebP',
	'Image Optimizer'                                                                                                          => 'بهینه‌ساز تصویر',
	'Cloner Image Optimizer'                                                                                                   => 'بهینه‌ساز تصویر کلونر',
	'Settings'                                                                                                                 => 'تنظیمات',
	'Logs'                                                                                                                     => 'گزارش‌ها',
	'Bulk Optimize'                                                                                                            => 'بهینه‌سازی گروهی',
	'Bulk Optimize Images'                                                                                                     => 'بهینه‌سازی گروهی تصاویر',
	'Optimization'                                                                                                             => 'بهینه‌سازی',
	'Image Optimization'                                                                                                       => 'بهینه‌سازی تصاویر',
	'Optimization Logs'                                                                                                        => 'گزارش‌های بهینه‌سازی',

	// --- Language switcher --------------------------------------------------
	'Language'                                                                                                                 => 'زبان',
	'Interface Language'                                                                                                       => 'زبان رابط کاربری',
	'Follow site language'                                                                                                     => 'مطابق زبان سایت',
	'Switch to English'                                                                                                        => 'تغییر به انگلیسی',
	'Switch to Persian'                                                                                                        => 'تغییر به فارسی',
	'Change the language of this plugin\'s admin pages. This does not affect the rest of your site.'                           => 'زبان صفحات مدیریت این افزونه را تغییر می‌دهد. این تنظیم روی بقیهٔ سایت شما تأثیری ندارد.',

	// --- Media library column ----------------------------------------------
	'Not optimized'                                                                                                            => 'بهینه‌نشده',
	'Original'                                                                                                                 => 'حجم اصلی',
	'Optimized'                                                                                                                => 'حجم بهینه',
	'Saved'                                                                                                                    => 'صرفه‌جویی',
	'WebP'                                                                                                                     => 'WebP',
	'Resized'                                                                                                                  => 'تغییر اندازه',
	'Date'                                                                                                                     => 'تاریخ',
	'Yes'                                                                                                                      => 'بله',
	'No'                                                                                                                       => 'خیر',
	'Optimize now'                                                                                                             => 'بهینه‌سازی فوری',
	'Re-optimize'                                                                                                              => 'بهینه‌سازی مجدد',
	'Restore original'                                                                                                         => 'بازگردانی نسخهٔ اصلی',
	'Image optimized successfully.'                                                                                            => 'تصویر با موفقیت بهینه شد.',
	'Original image restored.'                                                                                                 => 'تصویر اصلی بازگردانی شد.',

	// --- Settings: General --------------------------------------------------
	'General'                                                                                                                  => 'عمومی',
	'Enable Plugin'                                                                                                            => 'فعال‌سازی افزونه',
	'Automatically optimize new uploads.'                                                                                      => 'تصاویر تازه بارگذاری‌شده به‌صورت خودکار بهینه شوند.',
	'Enable WebP'                                                                                                              => 'فعال‌سازی WebP',
	'Generate a WebP version of each uploaded image.'                                                                          => 'برای هر تصویر بارگذاری‌شده یک نسخهٔ WebP ساخته شود.',
	'Enable Resize'                                                                                                            => 'فعال‌سازی تغییر اندازه',
	'Downscale images that exceed the maximum dimensions.'                                                                     => 'تصاویری که از ابعاد بیشینه بزرگ‌تر هستند کوچک شوند.',
	'Keep Originals'                                                                                                           => 'نگهداری نسخهٔ اصلی',
	'Back up the original file before optimizing. Disable to delete originals after a successful WebP conversion.'             => 'پیش از بهینه‌سازی، از فایل اصلی نسخهٔ پشتیبان گرفته می‌شود. اگر غیرفعال کنید، پس از تبدیل موفق به WebP فایل اصلی حذف می‌شود.',
	'Overwrite Existing'                                                                                                       => 'بازنویسی تصاویر بهینه‌شده',
	'Re-optimize images that were already optimized.'                                                                          => 'تصاویری که قبلاً بهینه شده‌اند دوباره بهینه شوند.',

	// --- Settings: WebP & compression --------------------------------------
	'WebP & Compression'                                                                                                       => 'WebP و فشرده‌سازی',
	'Quality'                                                                                                                  => 'کیفیت',
	'WebP / JPEG quality. 85 is a good balance of size and clarity.'                                                           => 'کیفیت WebP و JPEG. مقدار ۸۵ تعادل خوبی میان حجم و وضوح تصویر ایجاد می‌کند.',
	'Lossless WebP'                                                                                                            => 'WebP بدون افت کیفیت',
	'Use lossless WebP encoding (larger files, requires Imagick).'                                                             => 'استفاده از رمزگذاری WebP بدون افت کیفیت (حجم بیشتر، نیازمند Imagick).',
	'Strip Metadata'                                                                                                           => 'حذف فراداده',
	'Remove unnecessary EXIF / metadata to reduce file size.'                                                                  => 'حذف اطلاعات EXIF و فرادادهٔ غیرضروری برای کاهش حجم فایل.',
	'Preserve Color Profile'                                                                                                   => 'حفظ پروفایل رنگ',
	'Keep the ICC color profile when stripping metadata (Imagick).'                                                            => 'هنگام حذف فراداده، پروفایل رنگ ICC حفظ شود (Imagick).',
	'Fix Orientation'                                                                                                          => 'اصلاح جهت تصویر',
	'Auto-rotate images based on EXIF orientation.'                                                                            => 'چرخش خودکار تصاویر بر اساس اطلاعات جهت EXIF.',

	// --- Settings: Resize ---------------------------------------------------
	'Resize'                                                                                                                   => 'تغییر اندازه',
	'Maximum Width (px)'                                                                                                       => 'بیشینه عرض (پیکسل)',
	'Maximum Height (px)'                                                                                                      => 'بیشینه ارتفاع (پیکسل)',
	'Maintain Aspect Ratio'                                                                                                    => 'حفظ نسبت ابعاد',
	'Prevent Upscaling'                                                                                                        => 'جلوگیری از بزرگ‌نمایی',
	'Only shrink images; never enlarge smaller ones.'                                                                          => 'فقط تصاویر بزرگ کوچک شوند؛ تصاویر کوچک هرگز بزرگ نشوند.',

	// --- Settings: Advanced -------------------------------------------------
	'Advanced'                                                                                                                 => 'پیشرفته',
	'Bulk Batch Size'                                                                                                          => 'اندازهٔ دستهٔ پردازش گروهی',
	'How many images to process per request during bulk optimization.'                                                         => 'در بهینه‌سازی گروهی، در هر درخواست چند تصویر پردازش شود.',
	'Enable Logging'                                                                                                           => 'فعال‌سازی گزارش‌گیری',
	'Record optimization activity. <a href="%s">View logs</a>.'                                                                => 'ثبت فعالیت‌های بهینه‌سازی. <a href="%s">مشاهدهٔ گزارش‌ها</a>.',
	'Skip Large Files (MB)'                                                                                                    => 'نادیده گرفتن فایل‌های بزرگ (مگابایت)',
	'Skip images larger than this size. Set to 0 to disable the limit.'                                                        => 'تصاویر بزرگ‌تر از این حجم پردازش نشوند. مقدار ۰ یعنی بدون محدودیت.',
	'Save Settings'                                                                                                            => 'ذخیرهٔ تنظیمات',
	'Settings saved.'                                                                                                          => 'تنظیمات ذخیره شد.',

	// --- Sidebar / capabilities --------------------------------------------
	'Bulk Optimization'                                                                                                        => 'بهینه‌سازی گروهی',
	'Optimize images that already exist in your media library.'                                                                => 'تصاویری که از قبل در کتابخانهٔ رسانه وجود دارند را بهینه کنید.',
	'Open Bulk Optimizer'                                                                                                      => 'باز کردن بهینه‌ساز گروهی',
	'Server Capabilities'                                                                                                      => 'قابلیت‌های سرور',
	'Imagick'                                                                                                                  => 'Imagick',
	'GD'                                                                                                                       => 'GD',
	'Available'                                                                                                                => 'در دسترس',
	'Missing'                                                                                                                  => 'موجود نیست',
	'WebP Encoding'                                                                                                            => 'رمزگذاری WebP',
	'Supported'                                                                                                                => 'پشتیبانی می‌شود',
	'Unsupported'                                                                                                              => 'پشتیبانی نمی‌شود',
	'WebP encoding is not available on this server. Images will still be resized and compressed, but not converted to WebP.'   => 'رمزگذاری WebP روی این سرور در دسترس نیست. تصاویر همچنان تغییر اندازه و فشرده می‌شوند، اما به WebP تبدیل نخواهند شد.',

	// --- Statistics ---------------------------------------------------------
	'Total Images'                                                                                                             => 'کل تصاویر',
	'Optimized Images'                                                                                                         => 'تصاویر بهینه‌شده',
	'Total Original Size'                                                                                                      => 'مجموع حجم اصلی',
	'Total Optimized Size'                                                                                                     => 'مجموع حجم بهینه',
	'Total Space Saved'                                                                                                        => 'مجموع فضای آزادشده',
	'Average Compression'                                                                                                      => 'میانگین فشرده‌سازی',
	'Space Saved'                                                                                                              => 'فضای آزادشده',
	'Avg. Compression'                                                                                                         => 'میانگین فشرده‌سازی',

	// --- Bulk page ----------------------------------------------------------
	'Run Bulk Optimization'                                                                                                    => 'اجرای بهینه‌سازی گروهی',
	'Only images that have not been optimized yet'                                                                             => 'فقط تصاویری که هنوز بهینه نشده‌اند',
	'All images (re-optimize everything)'                                                                                      => 'همهٔ تصاویر (بهینه‌سازی مجدد همه)',
	'Start Optimization'                                                                                                       => 'شروع بهینه‌سازی',
	'Pause'                                                                                                                    => 'توقف موقت',
	'Resume'                                                                                                                   => 'ادامه',
	'Cancel'                                                                                                                   => 'لغو',

	// --- Bulk JS strings ----------------------------------------------------
	'Scanning media library…'                                                                                                  => 'در حال بررسی کتابخانهٔ رسانه…',
	'No images found to optimize.'                                                                                             => 'تصویری برای بهینه‌سازی پیدا نشد.',
	'Starting…'                                                                                                                => 'در حال شروع…',
	'Processing %1$d of %2$d…'                                                                                                 => 'در حال پردازش %1$d از %2$d…',
	'Paused.'                                                                                                                  => 'متوقف شد.',
	'Resuming…'                                                                                                                => 'در حال ادامه…',
	'Cancelled.'                                                                                                               => 'لغو شد.',
	'All done! Optimized %1$d image(s), saved %2$s.'                                                                           => 'تمام شد! %1$d تصویر بهینه شد و %2$s فضا آزاد گردید.',
	'Error'                                                                                                                    => 'خطا',
	'Skipped'                                                                                                                  => 'رد شد',
	'Estimated time remaining: %s'                                                                                             => 'زمان تقریبی باقی‌مانده: %s',
	'calculating…'                                                                                                             => 'در حال محاسبه…',

	// --- Logs page ----------------------------------------------------------
	'Logs cleared.'                                                                                                            => 'گزارش‌ها پاک شد.',
	'Clear Logs'                                                                                                               => 'پاک کردن گزارش‌ها',
	'No log entries yet.'                                                                                                      => 'هنوز هیچ گزارشی ثبت نشده است.',
	'Time'                                                                                                                     => 'زمان',
	'Level'                                                                                                                    => 'سطح',
	'Message'                                                                                                                  => 'پیام',
	'Context'                                                                                                                  => 'جزئیات',

	// --- Errors / notices ---------------------------------------------------
	'You do not have permission to access this page.'                                                                          => 'شما اجازهٔ دسترسی به این صفحه را ندارید.',
	'You do not have permission to do this.'                                                                                   => 'شما اجازهٔ انجام این کار را ندارید.',
	'Insufficient permissions.'                                                                                                => 'سطح دسترسی کافی نیست.',
	'Invalid attachment ID.'                                                                                                   => 'شناسهٔ پیوست نامعتبر است.',
	'Original restored.'                                                                                                       => 'نسخهٔ اصلی بازگردانی شد.',
	'No backup is available for this image.'                                                                                   => 'برای این تصویر نسخهٔ پشتیبانی موجود نیست.',
	'Could not restore the original file.'                                                                                     => 'بازگردانی فایل اصلی ممکن نشد.',
	'The plugin is disabled.'                                                                                                  => 'افزونه غیرفعال است.',
	'Attachment is not an image.'                                                                                              => 'این پیوست یک تصویر نیست.',
	'Unsupported image type.'                                                                                                  => 'نوع تصویر پشتیبانی نمی‌شود.',
	'Image already optimized.'                                                                                                 => 'این تصویر قبلاً بهینه شده است.',
	'Attachment file not found.'                                                                                               => 'فایل پیوست پیدا نشد.',
	'Could not read image dimensions.'                                                                                         => 'خواندن ابعاد تصویر ممکن نشد.',
	'Source file not found or unreadable.'                                                                                     => 'فایل مبدأ پیدا نشد یا قابل خواندن نیست.',
	'WebP file was not created.'                                                                                               => 'فایل WebP ساخته نشد.',
	'Optimized file was not created.'                                                                                          => 'فایل بهینه‌شده ساخته نشد.',
	'BMP support requires the GD extension.'                                                                                   => 'پشتیبانی از BMP نیازمند افزونهٔ GD است.',
	'Could not read the BMP image.'                                                                                            => 'خواندن تصویر BMP ممکن نشد.',
	'Could not create a temporary BMP conversion.'                                                                             => 'ساخت فایل موقت تبدیل BMP ممکن نشد.',
	'File is larger than the configured limit.'                                                                                => 'حجم فایل از محدودیت تعیین‌شده بیشتر است.',

	// --- Branding -----------------------------------------------------------
	'Developed by Cloner'                                                                                                      => 'توسعه‌یافته توسط کلونر',
	'Visit clonerr.ir'                                                                                                         => 'مشاهدهٔ clonerr.ir',
	'Support & Documentation'                                                                                                  => 'پشتیبانی و مستندات',
	'Version'                                                                                                                  => 'نسخه',
);
