<?php
/**
 * Shared admin integration (action links + row meta).
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cross-cutting admin behaviour that is not tied to a single page.
 */
final class Admin {

	/**
	 * Register hooks.
	 */
	public function register() {
		add_filter( 'plugin_action_links_' . SIO_PLUGIN_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'row_meta' ), 10, 2 );
	}

	/**
	 * Add a Settings link on the plugins screen.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public function action_links( $links ) {
		$settings_url = admin_url( 'admin.php?page=sio-settings' );
		$link         = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $settings_url ),
			esc_html__( 'Settings', 'smart-image-optimizer' )
		);
		array_unshift( $links, $link );
		return $links;
	}

	/**
	 * Add helpful links under the plugin description.
	 *
	 * @param array  $meta Plugin meta links.
	 * @param string $file Plugin file.
	 * @return array
	 */
	public function row_meta( $meta, $file ) {
		if ( SIO_PLUGIN_BASENAME === $file ) {
			$meta[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( admin_url( 'upload.php?page=sio-bulk' ) ),
				esc_html__( 'Bulk Optimize', 'smart-image-optimizer' )
			);
			$meta[] = sprintf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( SIO_AUTHOR_URL ),
				esc_html__( 'Visit clonerr.ir', 'smart-image-optimizer' )
			);
			$meta[] = sprintf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( SIO_SUPPORT_URL ),
				esc_html__( 'Support & Documentation', 'smart-image-optimizer' )
			);
		}
		return $meta;
	}
}
