<?php
/**
 * High-level optimization orchestrator for a single attachment.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Image;

use SmartImageOptimizer\Settings;
use SmartImageOptimizer\Logger;
use SmartImageOptimizer\Stats;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs the full pipeline: validate, resize, compress, WebP, save, metadata, backup.
 */
final class Optimizer {

	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Processor service.
	 *
	 * @var Processor
	 */
	private $processor;

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
	 * @param Processor $processor Processor service.
	 * @param Logger    $logger    Logger service.
	 * @param Stats     $stats     Stats service.
	 */
	public function __construct( Settings $settings, Processor $processor, Logger $logger, Stats $stats ) {
		$this->settings  = $settings;
		$this->processor = $processor;
		$this->logger    = $logger;
		$this->stats     = $stats;
	}

	/**
	 * Optimize an attachment by ID.
	 *
	 * @param int  $attachment_id Attachment ID.
	 * @param bool $force         Force re-optimization.
	 * @return array|WP_Error Result data or error.
	 */
	public function optimize_attachment( $attachment_id, $force = false ) {
		$attachment_id = (int) $attachment_id;

		if ( ! $this->settings->is_enabled( 'enable_plugin' ) ) {
			return new WP_Error( 'sio_disabled', __( 'The plugin is disabled.', 'smart-image-optimizer' ) );
		}

		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			return new WP_Error( 'sio_not_image', __( 'Attachment is not an image.', 'smart-image-optimizer' ) );
		}

		$mime = get_post_mime_type( $attachment_id );
		if ( ! $this->processor->is_supported( $mime ) ) {
			$this->logger->info(
				'Skipped file: unsupported type.',
				array(
					'id'   => $attachment_id,
					'mime' => $mime,
				)
			);
			return new WP_Error( 'sio_unsupported', __( 'Unsupported image type.', 'smart-image-optimizer' ) );
		}

		$already = $this->stats->is_optimized( $attachment_id );
		if ( $already && ! $force && ! $this->settings->is_enabled( 'overwrite_existing' ) ) {
			return new WP_Error( 'sio_already', __( 'Image already optimized.', 'smart-image-optimizer' ) );
		}

		$file = get_attached_file( $attachment_id );
		if ( ! $file || ! file_exists( $file ) ) {
			$this->logger->error( 'Source file missing.', array( 'id' => $attachment_id ) );
			return new WP_Error( 'sio_no_file', __( 'Attachment file not found.', 'smart-image-optimizer' ) );
		}

		// Optional guard: skip very large files to protect server memory.
		$limit_mb = (int) $this->settings->get( 'skip_large_mb', 0 );
		if ( $limit_mb > 0 ) {
			$size = (int) filesize( $file );
			if ( $size > ( $limit_mb * 1024 * 1024 ) ) {
				$this->logger->info(
					'Skipped file: larger than the configured limit.',
					array(
						'id'       => $attachment_id,
						'size'     => $size,
						'limit_mb' => $limit_mb,
					)
				);
				return new WP_Error( 'sio_too_large', __( 'File is larger than the configured limit.', 'smart-image-optimizer' ) );
			}
		}

		return $this->run_pipeline( $attachment_id, $file, $mime );
	}

	/**
	 * Execute the pipeline for a resolved file.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $file          Absolute file path.
	 * @param string $mime          Mime type.
	 * @return array|WP_Error
	 */
	private function run_pipeline( $attachment_id, $file, $mime ) {
		$this->logger->info(
			'Optimization started.',
			array(
				'id'   => $attachment_id,
				'file' => wp_basename( $file ),
			)
		);

		$processed = $this->processor->process( $file );
		if ( is_wp_error( $processed ) ) {
			$this->logger->error(
				'Processing failed: ' . $processed->get_error_message(),
				array( 'id' => $attachment_id )
			);
			return $processed; // Original untouched.
		}

		$editor        = $processed['editor'];
		$original_size = (int) $processed['original_size'];
		$resized       = (bool) $processed['resized'];
		$temp_path     = $processed['temp_path'];

		$do_webp = $this->settings->is_enabled( 'enable_webp' ) && $this->processor->webp_supported();

		// Backup the original before any destructive change.
		$backup_path = '';
		if ( $this->settings->is_enabled( 'keep_originals' ) ) {
			$backup_path = $this->backup_original( $attachment_id, $file );
		}

		$new_file    = $file;
		$new_mime    = $mime;
		$webp_status = 'no';

		if ( $do_webp ) {
			$dir         = dirname( $file );
			$name        = pathinfo( $file, PATHINFO_FILENAME );
			$destination = trailingslashit( $dir ) . $name . '.webp';

			$saved = $this->processor->save_webp( $editor, $destination );
			if ( is_wp_error( $saved ) ) {
				$this->logger->error(
					'WebP conversion failed: ' . $saved->get_error_message(),
					array( 'id' => $attachment_id )
				);
				$this->processor->cleanup_temp( $temp_path );
				return $saved; // Never lose user data: original untouched.
			}

			$new_file    = $saved['path'];
			$new_mime    = 'image/webp';
			$webp_status = 'yes';

			// Remove old generated sizes + original main file when the name changed.
			if ( $new_file !== $file ) {
				$this->delete_old_sizes( $attachment_id );
				if ( file_exists( $file ) ) {
					@unlink( $file );
				}
			}
		} else {
			// No WebP: compress + resize in place, keeping the original mime.
			$saved = $this->processor->save_as( $editor, $file, $mime );
			if ( is_wp_error( $saved ) ) {
				$this->logger->error(
					'Save failed: ' . $saved->get_error_message(),
					array( 'id' => $attachment_id )
				);
				$this->processor->cleanup_temp( $temp_path );
				return $saved;
			}
			$new_file = $saved['path'];
			$new_mime = ! empty( $saved['mime-type'] ) ? $saved['mime-type'] : $mime;

			// If BMP was normalised to PNG the filename changed; drop the old file.
			if ( $new_file !== $file && file_exists( $file ) ) {
				$this->delete_old_sizes( $attachment_id );
				@unlink( $file );
			}
		}

		$this->processor->cleanup_temp( $temp_path );

		clearstatcache( true, $new_file );
		$optimized_size = file_exists( $new_file ) ? (int) filesize( $new_file ) : $original_size;

		// Point the attachment at the new file and refresh metadata.
		update_attached_file( $attachment_id, $new_file );

		if ( $new_mime !== $mime ) {
			wp_update_post(
				array(
					'ID'             => $attachment_id,
					'post_mime_type' => $new_mime,
				)
			);
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		$metadata = wp_generate_attachment_metadata( $attachment_id, $new_file );
		if ( ! empty( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		$saved_bytes = max( 0, $original_size - $optimized_size );
		$percent     = $original_size > 0 ? round( ( $saved_bytes / $original_size ) * 100, 2 ) : 0.0;

		$this->stats->record(
			$attachment_id,
			array(
				Stats::META_ORIGINAL  => $original_size,
				Stats::META_OPTIMIZED => $optimized_size,
				Stats::META_SAVED     => $saved_bytes,
				Stats::META_PERCENT   => $percent,
				Stats::META_WEBP      => $webp_status,
				Stats::META_RESIZED   => $resized ? 'yes' : 'no',
				Stats::META_DATE      => current_time( 'mysql' ),
				Stats::META_STATUS    => 'optimized',
				Stats::META_WEBP_PATH => $do_webp ? $new_file : '',
				Stats::META_BACKUP    => $backup_path,
			)
		);

		$this->logger->info(
			'Optimization finished.',
			array(
				'id'      => $attachment_id,
				'saved'   => $saved_bytes,
				'percent' => $percent,
				'webp'    => $webp_status,
				'resized' => $resized ? 'yes' : 'no',
			)
		);

		$result = array(
			'id'             => $attachment_id,
			'original_size'  => $original_size,
			'optimized_size' => $optimized_size,
			'saved_bytes'    => $saved_bytes,
			'percent'        => $percent,
			'webp'           => $webp_status,
			'resized'        => $resized ? 'yes' : 'no',
		);

		/**
		 * Fires after an attachment has been optimized.
		 *
		 * @param int   $attachment_id Attachment ID.
		 * @param array $result        Result data.
		 */
		do_action( 'sio_image_optimized', $attachment_id, $result );

		return $result;
	}

	/**
	 * Restore a previously backed-up original.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return bool|WP_Error
	 */
	public function restore_original( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		$backup        = get_post_meta( $attachment_id, Stats::META_BACKUP, true );

		if ( ! $backup || ! file_exists( $backup ) ) {
			return new WP_Error( 'sio_no_backup', __( 'No backup is available for this image.', 'smart-image-optimizer' ) );
		}

		$current = get_attached_file( $attachment_id );
		$dir     = dirname( $current );
		$target  = trailingslashit( $dir ) . wp_basename( $backup );

		if ( ! @copy( $backup, $target ) ) {
			return new WP_Error( 'sio_restore_failed', __( 'Could not restore the original file.', 'smart-image-optimizer' ) );
		}

		// Remove the optimized webp main file if it differs.
		if ( $current && $current !== $target && file_exists( $current ) ) {
			$this->delete_old_sizes( $attachment_id );
			@unlink( $current );
		}

		$type = wp_check_filetype( $target );
		update_attached_file( $attachment_id, $target );
		if ( ! empty( $type['type'] ) ) {
			wp_update_post(
				array(
					'ID'             => $attachment_id,
					'post_mime_type' => $type['type'],
				)
			);
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		$metadata = wp_generate_attachment_metadata( $attachment_id, $target );
		if ( ! empty( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		// Clear optimization meta.
		foreach ( array(
			Stats::META_STATUS,
			Stats::META_ORIGINAL,
			Stats::META_OPTIMIZED,
			Stats::META_SAVED,
			Stats::META_PERCENT,
			Stats::META_WEBP,
			Stats::META_RESIZED,
			Stats::META_DATE,
			Stats::META_WEBP_PATH,
		) as $meta_key ) {
			delete_post_meta( $attachment_id, $meta_key );
		}

		$this->logger->info( 'Original restored.', array( 'id' => $attachment_id ) );
		return true;
	}

	/**
	 * Copy the original file into the backup directory, mirroring its path.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $file          Absolute file path.
	 * @return string Backup path, or empty string on failure.
	 */
	private function backup_original( $attachment_id, $file ) {
		$uploads     = wp_get_upload_dir();
		$backup_root = trailingslashit( $uploads['basedir'] ) . 'sio-backups';

		if ( ! file_exists( $backup_root ) ) {
			wp_mkdir_p( $backup_root );
			@file_put_contents( trailingslashit( $backup_root ) . 'index.php', "<?php\n// Silence is golden.\n" );
			@file_put_contents( trailingslashit( $backup_root ) . '.htaccess', "Deny from all\n" );
		}

		$relative = ltrim( str_replace( $uploads['basedir'], '', $file ), '/\\\\' );
		$dest     = trailingslashit( $backup_root ) . $relative;
		wp_mkdir_p( dirname( $dest ) );

		if ( @copy( $file, $dest ) ) {
			return $dest;
		}

		$this->logger->warning( 'Backup failed.', array( 'id' => $attachment_id ) );
		return '';
	}

	/**
	 * Delete previously generated intermediate size files.
	 *
	 * @param int $attachment_id Attachment ID.
	 */
	private function delete_old_sizes( $attachment_id ) {
		$metadata = wp_get_attachment_metadata( $attachment_id );
		if ( empty( $metadata['sizes'] ) || ! is_array( $metadata['sizes'] ) ) {
			return;
		}
		$file = get_attached_file( $attachment_id );
		$dir  = trailingslashit( dirname( $file ) );
		foreach ( $metadata['sizes'] as $size ) {
			if ( ! empty( $size['file'] ) ) {
				$path = $dir . $size['file'];
				if ( file_exists( $path ) ) {
					@unlink( $path );
				}
			}
		}
	}
}
