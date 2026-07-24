<?php
/**
 * Logs viewer page.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Admin;

use SmartImageOptimizer\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays recent log entries and allows clearing them.
 */
final class LogsPage {

	/**
	 * Logger service.
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Hook suffix.
	 *
	 * @var string
	 */
	private $hook = '';

	/**
	 * Constructor.
	 *
	 * @param Logger $logger Logger service.
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Register hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_post_sio_clear_logs', array( $this, 'handle_clear' ) );
	}

	/**
	 * Add the Logs submenu.
	 */
	public function menu() {
		$this->hook = add_submenu_page(
			'sio-settings',
			__( 'Logs', 'smart-image-optimizer' ),
			__( 'Logs', 'smart-image-optimizer' ),
			'manage_options',
			'sio-logs',
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue the shared stylesheet on this page only.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function assets( $hook ) {
		if ( $hook !== $this->hook ) {
			return;
		}
		sio_enqueue_admin_assets();
	}

	/**
	 * Handle the clear-logs form submission.
	 */
	public function handle_clear() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'smart-image-optimizer' ) );
		}
		check_admin_referer( 'sio_clear_logs' );
		$this->logger->clear();
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'sio-logs',
					'cleared' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Render the logs template.
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'smart-image-optimizer' ) );
		}
		$logs = $this->logger->get_logs( 200 );
		include SIO_PLUGIN_DIR . 'templates/logs-page.php';
	}
}
