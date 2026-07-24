<?php
/**
 * Bulk optimization page (under Media).
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Admin;

use SmartImageOptimizer\Settings;
use SmartImageOptimizer\Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the bulk optimizer screen and its assets.
 */
final class BulkPage {

	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Stats service.
	 *
	 * @var Stats
	 */
	private $stats;

	/**
	 * Hook suffix.
	 *
	 * @var string
	 */
	private $hook = '';

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings service.
	 * @param Stats    $stats    Stats service.
	 */
	public function __construct( Settings $settings, Stats $stats ) {
		$this->settings = $settings;
		$this->stats    = $stats;
	}

	/**
	 * Register hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
	}

	/**
	 * Add the Media > Bulk Optimize page.
	 */
	public function menu() {
		$this->hook = add_media_page(
			__( 'Bulk Optimize Images', 'smart-image-optimizer' ),
			__( 'Bulk Optimize', 'smart-image-optimizer' ),
			'manage_options',
			'sio-bulk',
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue assets + localize bulk data (lazy loaded, this page only).
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function assets( $hook ) {
		if ( $hook !== $this->hook ) {
			return;
		}

		sio_enqueue_admin_assets();

		wp_enqueue_script(
			'sio-bulk',
			SIO_PLUGIN_URL . 'assets/js/bulk.js',
			array(),
			SIO_VERSION,
			true
		);

		wp_localize_script(
			'sio-bulk',
			'SIO_Bulk',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'sio_bulk' ),
				'batchSize' => (int) $this->settings->get( 'batch_size', 3 ),
				'i18n'      => array(
					'scanning'   => __( 'Scanning media library…', 'smart-image-optimizer' ),
					'noImages'   => __( 'No images found to optimize.', 'smart-image-optimizer' ),
					'starting'   => __( 'Starting…', 'smart-image-optimizer' ),
					'processing' => __( 'Processing %1$d of %2$d…', 'smart-image-optimizer' ),
					'paused'     => __( 'Paused.', 'smart-image-optimizer' ),
					'resumed'    => __( 'Resuming…', 'smart-image-optimizer' ),
					'cancelled'  => __( 'Cancelled.', 'smart-image-optimizer' ),
					'done'       => __( 'All done! Optimized %1$d image(s), saved %2$s.', 'smart-image-optimizer' ),
					'error'      => __( 'Error', 'smart-image-optimizer' ),
					'skipped'    => __( 'Skipped', 'smart-image-optimizer' ),
					'eta'        => __( 'Estimated time remaining: %s', 'smart-image-optimizer' ),
					'calculating'=> __( 'calculating…', 'smart-image-optimizer' ),
				),
			)
		);
	}

	/**
	 * Render the bulk page template.
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'smart-image-optimizer' ) );
		}

		$totals = $this->stats->get_totals();
		include SIO_PLUGIN_DIR . 'templates/bulk-page.php';
	}
}
