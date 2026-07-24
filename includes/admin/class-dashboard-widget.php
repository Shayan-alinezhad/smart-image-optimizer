<?php
/**
 * Dashboard statistics widget.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Admin;

use SmartImageOptimizer\Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers a dashboard widget summarising optimization stats.
 */
final class DashboardWidget {

	/**
	 * Stats service.
	 *
	 * @var Stats
	 */
	private $stats;

	/**
	 * Constructor.
	 *
	 * @param Stats $stats Stats service.
	 */
	public function __construct( Stats $stats ) {
		$this->stats = $stats;
	}

	/**
	 * Register hooks.
	 */
	public function register() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );
	}

	/**
	 * Add the dashboard widget.
	 */
	public function add_widget() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		wp_add_dashboard_widget(
			'sio_stats_widget',
			__( 'Image Optimization', 'smart-image-optimizer' ),
			array( $this, 'render' )
		);
	}

	/**
	 * Render the widget.
	 */
	public function render() {
		$totals = $this->stats->get_totals();
		include SIO_PLUGIN_DIR . 'templates/dashboard-widget.php';
	}
}
