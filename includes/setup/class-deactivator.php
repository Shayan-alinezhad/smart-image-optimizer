<?php
/**
 * Deactivation routine.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Setup;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on plugin deactivation. Intentionally non-destructive.
 */
final class Deactivator {

	/**
	 * Clean up transient/scheduled state only. Settings and stats are kept.
	 */
	public static function deactivate() {
		// Nothing scheduled at the moment; hook kept for forward compatibility.
		do_action( 'sio_deactivated' );
	}
}
