<?php
/**
 * Activation routine.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Setup;

use SmartImageOptimizer\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on plugin activation.
 */
final class Activator {

	/**
	 * Seed default options and store the version.
	 */
	public static function activate() {
		$existing = get_option( Settings::OPTION_KEY, null );
		if ( null === $existing ) {
			$settings = new Settings();
			add_option( Settings::OPTION_KEY, $settings->defaults() );
		}

		if ( false === get_option( 'sio_logs', false ) ) {
			add_option( 'sio_logs', array(), '', false );
		}

		update_option( 'sio_version', SIO_VERSION );
	}
}
