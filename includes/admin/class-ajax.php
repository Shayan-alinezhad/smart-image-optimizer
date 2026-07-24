<?php
/**
 * AJAX handlers for bulk optimization, stats and log actions.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Admin;

use SmartImageOptimizer\Settings;
use SmartImageOptimizer\Logger;
use SmartImageOptimizer\Stats;
use SmartImageOptimizer\Image\Optimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and processes admin-ajax endpoints.
 */
final class Ajax {

	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Optimizer service.
	 *
	 * @var Optimizer
	 */
	private $optimizer;

	/**
	 * Logger service.
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Stats service.
	 *
	 * @var Stats
	 */
	private $stats;

	/**
	 * Constructor.
	 *
	 * @param Settings  $settings  Settings service.
	 * @param Optimizer $optimizer Optimizer service.
	 * @param Logger    $logger    Logger service.
	 * @param Stats     $stats     Stats service.
	 */
	public function __construct( Settings $settings, Optimizer $optimizer, Logger $logger, Stats $stats ) {
		$this->settings  = $settings;
		$this->optimizer = $optimizer;
		$this->logger    = $logger;
		$this->stats     = $stats;
	}

	/**
	 * Register hooks.
	 */
	public function register() {
		add_action( 'wp_ajax_sio_bulk_scan', array( $this, 'scan' ) );
		add_action( 'wp_ajax_sio_bulk_optimize', array( $this, 'optimize' ) );
		add_action( 'wp_ajax_sio_get_stats', array( $this, 'get_stats' ) );
		add_action( 'wp_ajax_sio_restore', array( $this, 'restore' ) );
	}

	/**
	 * Shared guard: verify nonce + capability.
	 */
	private function guard() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Insufficient permissions.', 'smart-image-optimizer' ) ),
				403
			);
		}
		check_ajax_referer( 'sio_bulk', 'nonce' );
	}

	/**
	 * Return the list of attachment IDs to process.
	 */
	public function scan() {
		$this->guard();

		$scope = isset( $_POST['scope'] ) ? sanitize_key( wp_unslash( $_POST['scope'] ) ) : 'all';
		$only_unoptimized = ( 'unoptimized' === $scope );

		$ids = $this->stats->get_image_ids( $only_unoptimized );

		wp_send_json_success(
			array(
				'ids'    => $ids,
				'total'  => count( $ids ),
				'totals' => $this->stats->get_totals(),
			)
		);
	}

	/**
	 * Optimize a single attachment (called repeatedly by the client loop).
	 */
	public function optimize() {
		$this->guard();

		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
		$force         = isset( $_POST['force'] ) ? (bool) absint( wp_unslash( $_POST['force'] ) ) : false;

		if ( $attachment_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'smart-image-optimizer' ) ), 400 );
		}

		$result = $this->optimizer->optimize_attachment( $attachment_id, $force );

		if ( is_wp_error( $result ) ) {
			$skipped_codes = array( 'sio_already', 'sio_unsupported', 'sio_disabled' );
			$status        = in_array( $result->get_error_code(), $skipped_codes, true ) ? 'skipped' : 'error';

			wp_send_json_success(
				array(
					'id'      => $attachment_id,
					'status'  => $status,
					'message' => $result->get_error_message(),
					'title'   => get_the_title( $attachment_id ),
				)
			);
		}

		$result['status'] = 'optimized';
		$result['title']  = get_the_title( $attachment_id );
		$result['human']  = array(
			'original'  => sio_format_bytes( $result['original_size'] ),
			'optimized' => sio_format_bytes( $result['optimized_size'] ),
			'saved'     => sio_format_bytes( $result['saved_bytes'] ),
		);

		wp_send_json_success( $result );
	}

	/**
	 * Return current aggregate totals.
	 */
	public function get_stats() {
		$this->guard();
		wp_send_json_success( $this->stats->get_totals() );
	}

	/**
	 * Restore an original from backup.
	 */
	public function restore() {
		$this->guard();

		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
		if ( $attachment_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'smart-image-optimizer' ) ), 400 );
		}

		$result = $this->optimizer->restore_original( $attachment_id );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		wp_send_json_success( array( 'message' => __( 'Original restored.', 'smart-image-optimizer' ) ) );
	}
}
