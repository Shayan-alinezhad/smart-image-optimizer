<?php
/**
 * Procedural helper functions.
 *
 * @package SmartImageOptimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'sio_format_bytes' ) ) {
	/**
	 * Format a byte count into a human readable string.
	 *
	 * @param int $bytes     Number of bytes.
	 * @param int $precision Decimal precision.
	 * @return string
	 */
	function sio_format_bytes( $bytes, $precision = 2 ) {
		$bytes = (float) $bytes;
		if ( $bytes <= 0 ) {
			return '0 B';
		}
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$pow   = (int) floor( log( $bytes, 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );
		$bytes /= pow( 1024, $pow );
		return round( $bytes, $precision ) . ' ' . $units[ $pow ];
	}
}

if ( ! function_exists( 'sio_imagick_available' ) ) {
	/**
	 * Whether the Imagick extension is available.
	 *
	 * @return bool
	 */
	function sio_imagick_available() {
		return extension_loaded( 'imagick' ) && class_exists( 'Imagick' );
	}
}

if ( ! function_exists( 'sio_gd_available' ) ) {
	/**
	 * Whether the GD extension is available.
	 *
	 * @return bool
	 */
	function sio_gd_available() {
		return extension_loaded( 'gd' ) && function_exists( 'gd_info' );
	}
}

if ( ! function_exists( 'sio_webp_supported' ) ) {
	/**
	 * Whether the server can encode WebP with either engine.
	 *
	 * @return bool
	 */
	function sio_webp_supported() {
		if ( sio_imagick_available() ) {
			try {
				$formats = \Imagick::queryFormats( 'WEBP' );
				if ( ! empty( $formats ) ) {
					return true;
				}
			} catch ( \Exception $e ) {
				return false;
			}
		}
		return function_exists( 'imagewebp' );
	}
}

if ( ! function_exists( 'sio_i18n' ) ) {
	/**
	 * Get the language switcher service.
	 *
	 * @return \SmartImageOptimizer\I18n|null
	 */
	function sio_i18n() {
		return \SmartImageOptimizer\Plugin::instance()->get( 'i18n' );
	}
}

if ( ! function_exists( 'sio_is_rtl_ui' ) ) {
	/**
	 * Whether the plugin admin UI should render right-to-left (Persian).
	 *
	 * @return bool
	 */
	function sio_is_rtl_ui() {
		$i18n = sio_i18n();
		return $i18n ? $i18n->is_rtl() : false;
	}
}

if ( ! function_exists( 'sio_wrap_classes' ) ) {
	/**
	 * Build the CSS classes for a plugin admin page wrapper.
	 *
	 * @param string $extra Additional class names.
	 * @return string
	 */
	function sio_wrap_classes( $extra = '' ) {
		$classes = array( 'wrap', 'sio-wrap' );
		if ( sio_is_rtl_ui() ) {
			$classes[] = 'sio-rtl';
		}
		if ( $extra ) {
			$classes[] = $extra;
		}
		return implode( ' ', $classes );
	}
}

if ( ! function_exists( 'sio_enqueue_admin_assets' ) ) {
	/**
	 * Enqueue the shared plugin admin stylesheet (plus RTL when Persian).
	 */
	function sio_enqueue_admin_assets() {
		wp_enqueue_style( 'dashicons' );

		wp_enqueue_style(
			'sio-admin',
			SIO_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			SIO_VERSION
		);

		if ( sio_is_rtl_ui() ) {
			wp_enqueue_style(
				'sio-admin-rtl',
				SIO_PLUGIN_URL . 'assets/css/admin-rtl.css',
				array( 'sio-admin' ),
				SIO_VERSION
			);
		}
	}
}

if ( ! function_exists( 'sio_render_partial' ) ) {
	/**
	 * Render a template partial (header / footer).
	 *
	 * @param string $name Partial file name without extension.
	 * @param array  $args Variables to expose to the partial.
	 */
	function sio_render_partial( $name, array $args = array() ) {
		$file = SIO_PLUGIN_DIR . 'templates/partials/' . sanitize_file_name( $name ) . '.php';
		if ( ! file_exists( $file ) ) {
			return;
		}
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( $args, EXTR_SKIP );
		include $file;
	}
}
