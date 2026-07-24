<?php
/**
 * Detects new uploads and runs the optimizer automatically.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Media;

use SmartImageOptimizer\Settings;
use SmartImageOptimizer\Logger;
use SmartImageOptimizer\Image\Optimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooks into the WordPress media pipeline.
 */
final class UploadHandler {

	/**
	 * Re-entry guard keyed by attachment ID.
	 *
	 * @var array
	 */
	private static $processing = array();

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
	 * Constructor.
	 *
	 * @param Settings  $settings  Settings service.
	 * @param Optimizer $optimizer Optimizer service.
	 * @param Logger    $logger    Logger service.
	 */
	public function __construct( Settings $settings, Optimizer $optimizer, Logger $logger ) {
		$this->settings  = $settings;
		$this->optimizer = $optimizer;
		$this->logger    = $logger;
	}

	/**
	 * Register hooks.
	 */
	public function register() {
		// Light-touch detection log at the wp_handle_upload stage.
		add_filter( 'wp_handle_upload', array( $this, 'detect_upload' ), 10, 2 );

		// Main pipeline runs once metadata is first generated for the attachment.
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'on_generate_metadata' ), 20, 2 );
	}

	/**
	 * Log that a supported image upload was detected.
	 *
	 * @param array  $upload  Upload data (file, url, type).
	 * @param string $context Upload context.
	 * @return array Unmodified upload data.
	 */
	public function detect_upload( $upload, $context = 'upload' ) {
		if ( ! $this->settings->is_enabled( 'enable_plugin' ) ) {
			return $upload;
		}
		if ( isset( $upload['type'] ) && in_array( $upload['type'], (array) $this->settings->get( 'allowed_types' ), true ) ) {
			$this->logger->info(
				'Upload detected.',
				array(
					'type' => $upload['type'],
					'file' => isset( $upload['file'] ) ? wp_basename( $upload['file'] ) : '',
				)
			);
		}
		return $upload;
	}

	/**
	 * Optimize the attachment when its metadata is generated.
	 *
	 * @param array $metadata      Attachment metadata.
	 * @param int   $attachment_id Attachment ID.
	 * @return array Possibly refreshed metadata.
	 */
	public function on_generate_metadata( $metadata, $attachment_id ) {
		$attachment_id = (int) $attachment_id;

		if ( ! $this->settings->is_enabled( 'enable_plugin' ) ) {
			return $metadata;
		}

		// Prevent recursion: the optimizer regenerates metadata internally.
		if ( isset( self::$processing[ $attachment_id ] ) ) {
			return $metadata;
		}

		$mime = get_post_mime_type( $attachment_id );
		if ( ! in_array( $mime, (array) $this->settings->get( 'allowed_types' ), true ) ) {
			return $metadata;
		}

		self::$processing[ $attachment_id ] = true;
		$result                             = $this->optimizer->optimize_attachment( $attachment_id );
		unset( self::$processing[ $attachment_id ] );

		if ( is_wp_error( $result ) ) {
			// Original is preserved and the error is already logged.
			return $metadata;
		}

		$fresh = wp_get_attachment_metadata( $attachment_id );
		return is_array( $fresh ) && ! empty( $fresh ) ? $fresh : $metadata;
	}
}
