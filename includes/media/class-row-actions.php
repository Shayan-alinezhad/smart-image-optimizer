<?php
/**
 * Per-image row actions in the Media Library (optimize / restore).
 *
 * @package SmartImageOptimizer
 * @author  Cloner (Shayan)
 * @link    https://clonerr.ir
 */

namespace SmartImageOptimizer\Media;

use SmartImageOptimizer\Stats;
use SmartImageOptimizer\Image\Optimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds "Optimize now" and "Restore original" links to media rows.
 */
final class RowActions {

	/**
	 * Optimizer service.
	 *
	 * @var Optimizer
	 */
	private $optimizer;

	/**
	 * Stats service.
	 *
	 * @var Stats
	 */
	private $stats;

	/**
	 * Constructor.
	 *
	 * @param Optimizer $optimizer Optimizer service.
	 * @param Stats     $stats     Stats service.
	 */
	public function __construct( Optimizer $optimizer, Stats $stats ) {
		$this->optimizer = $optimizer;
		$this->stats     = $stats;
	}

	/**
	 * Register hooks.
	 */
	public function register() {
		add_filter( 'media_row_actions', array( $this, 'add_actions' ), 10, 2 );
		add_action( 'admin_post_sio_optimize_single', array( $this, 'handle_optimize' ) );
		add_action( 'admin_post_sio_restore_single', array( $this, 'handle_restore' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );
	}

	/**
	 * Append plugin row actions.
	 *
	 * @param array    $actions Existing actions.
	 * @param \WP_Post $post    Attachment post.
	 * @return array
	 */
	public function add_actions( $actions, $post ) {
		if ( ! current_user_can( 'manage_options' ) || ! wp_attachment_is_image( $post->ID ) ) {
			return $actions;
		}

		$is_optimized = $this->stats->is_optimized( $post->ID );

		$actions['sio_optimize'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $this->action_url( 'sio_optimize_single', $post->ID ) ),
			$is_optimized
				? esc_html__( 'Re-optimize', 'smart-image-optimizer' )
				: esc_html__( 'Optimize now', 'smart-image-optimizer' )
		);

		$backup = get_post_meta( $post->ID, Stats::META_BACKUP, true );
		if ( $backup && file_exists( $backup ) ) {
			$actions['sio_restore'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $this->action_url( 'sio_restore_single', $post->ID ) ),
				esc_html__( 'Restore original', 'smart-image-optimizer' )
			);
		}

		return $actions;
	}

	/**
	 * Build a nonced admin-post URL.
	 *
	 * @param string $action Action name.
	 * @param int    $id     Attachment ID.
	 * @return string
	 */
	private function action_url( $action, $id ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action'        => $action,
					'attachment_id' => (int) $id,
				),
				admin_url( 'admin-post.php' )
			),
			$action . '_' . (int) $id
		);
	}

	/**
	 * Validate the incoming request and return the attachment ID.
	 *
	 * @param string $action Action name.
	 * @return int
	 */
	private function validate( $action ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'smart-image-optimizer' ) );
		}

		$id = isset( $_GET['attachment_id'] ) ? absint( wp_unslash( $_GET['attachment_id'] ) ) : 0;
		check_admin_referer( $action . '_' . $id );

		return $id;
	}

	/**
	 * Redirect back to the media list with a status flag.
	 *
	 * @param string $status Status key.
	 */
	private function redirect_back( $status ) {
		$referer = wp_get_referer();
		if ( ! $referer ) {
			$referer = admin_url( 'upload.php' );
		}
		wp_safe_redirect( add_query_arg( 'sio_notice', $status, $referer ) );
		exit;
	}

	/**
	 * Handle a single-image optimize request.
	 */
	public function handle_optimize() {
		$id     = $this->validate( 'sio_optimize_single' );
		$result = $this->optimizer->optimize_attachment( $id, true );
		$this->redirect_back( is_wp_error( $result ) ? 'failed' : 'optimized' );
	}

	/**
	 * Handle a single-image restore request.
	 */
	public function handle_restore() {
		$id     = $this->validate( 'sio_restore_single' );
		$result = $this->optimizer->restore_original( $id );
		$this->redirect_back( is_wp_error( $result ) ? 'failed' : 'restored' );
	}

	/**
	 * Render admin notices after a row action.
	 */
	public function notices() {
		if ( ! isset( $_GET['sio_notice'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$notice = sanitize_key( wp_unslash( $_GET['sio_notice'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$messages = array(
			'optimized' => array( 'success', __( 'Image optimized successfully.', 'smart-image-optimizer' ) ),
			'restored'  => array( 'success', __( 'Original image restored.', 'smart-image-optimizer' ) ),
			'failed'    => array( 'error', __( 'The operation could not be completed. Check the logs for details.', 'smart-image-optimizer' ) ),
		);

		if ( ! isset( $messages[ $notice ] ) ) {
			return;
		}

		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $messages[ $notice ][0] ),
			esc_html( $messages[ $notice ][1] )
		);
	}
}
