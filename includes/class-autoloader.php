<?php
/**
 * PSR-4 style autoloader mapping the SmartImageOptimizer namespace to
 * WordPress-flavoured class filenames under /includes.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Simple, dependency free autoloader.
 */
final class Autoloader {

	/**
	 * Register the autoloader with the SPL stack.
	 */
	public static function register() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Resolve a fully-qualified class name to a file and require it.
	 *
	 * Example: SmartImageOptimizer\Image\Processor
	 *          => includes/image/class-processor.php
	 *          SmartImageOptimizer\Admin\SettingsPage
	 *          => includes/admin/class-settings-page.php
	 *
	 * @param string $class Fully-qualified class name.
	 */
	public static function autoload( $class ) {
		$prefix = __NAMESPACE__ . '\\';
		$len    = strlen( $prefix );

		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			return;
		}

		$relative = substr( $class, $len );
		$parts    = explode( '\\', $relative );
		$class_nm = array_pop( $parts );

		$file = 'class-' . self::kebab( $class_nm ) . '.php';

		$sub = '';
		if ( ! empty( $parts ) ) {
			$sub = implode( '/', array_map( array( __CLASS__, 'kebab' ), $parts ) ) . '/';
		}

		$path = SIO_PLUGIN_DIR . 'includes/' . $sub . $file;

		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}

	/**
	 * Convert a CamelCase identifier to kebab-case.
	 *
	 * @param string $string Identifier.
	 * @return string
	 */
	private static function kebab( $string ) {
		$string = preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $string );
		return strtolower( $string );
	}
}
