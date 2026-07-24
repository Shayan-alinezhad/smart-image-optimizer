<?php
/**
 * Statistics + per-attachment optimization metadata.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads and writes optimization meta and computes aggregate totals.
 */
final class Stats {

	const META_ORIGINAL  = '_sio_original_size';
	const META_OPTIMIZED = '_sio_optimized_size';
	const META_SAVED     = '_sio_saved_bytes';
	const META_PERCENT   = '_sio_saved_percent';
	const META_WEBP      = '_sio_webp_status';
	const META_RESIZED   = '_sio_resize_status';
	const META_DATE      = '_sio_optimized_date';
	const META_STATUS    = '_sio_status';
	const META_WEBP_PATH = '_sio_webp_path';
	const META_BACKUP    = '_sio_backup_path';

	/**
	 * Supported source mime types for querying.
	 *
	 * @var array
	 */
	private $mimes = array( 'image/jpeg', 'image/png', 'image/bmp', 'image/gif', 'image/webp' );

	/**
	 * Persist a set of meta values for an attachment.
	 *
	 * @param int   $attachment_id Attachment ID.
	 * @param array $data          Meta key => value.
	 */
	public function record( $attachment_id, array $data ) {
		$attachment_id = (int) $attachment_id;
		foreach ( $data as $key => $value ) {
			update_post_meta( $attachment_id, $key, $value );
		}
	}

	/**
	 * Whether an attachment has already been optimized.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return bool
	 */
	public function is_optimized( $attachment_id ) {
		return 'optimized' === (string) get_post_meta( (int) $attachment_id, self::META_STATUS, true );
	}

	/**
	 * Per-attachment optimization summary for display.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return array
	 */
	public function get_attachment_stats( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		return array(
			'status'    => (string) get_post_meta( $attachment_id, self::META_STATUS, true ),
			'original'  => (int) get_post_meta( $attachment_id, self::META_ORIGINAL, true ),
			'optimized' => (int) get_post_meta( $attachment_id, self::META_OPTIMIZED, true ),
			'saved'     => (int) get_post_meta( $attachment_id, self::META_SAVED, true ),
			'percent'   => (float) get_post_meta( $attachment_id, self::META_PERCENT, true ),
			'webp'      => (string) get_post_meta( $attachment_id, self::META_WEBP, true ),
			'resized'   => (string) get_post_meta( $attachment_id, self::META_RESIZED, true ),
			'date'      => (string) get_post_meta( $attachment_id, self::META_DATE, true ),
			'backup'    => (string) get_post_meta( $attachment_id, self::META_BACKUP, true ),
		);
	}

	/**
	 * Aggregate totals across the whole media library.
	 *
	 * @return array
	 */
	public function get_totals() {
		global $wpdb;

		$total_images = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_mime_type LIKE %s",
				'attachment',
				$wpdb->esc_like( 'image/' ) . '%'
			)
		);

		$optimized_images = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
				self::META_STATUS,
				'optimized'
			)
		);

		$original_total = (float) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = %s",
				self::META_ORIGINAL
			)
		);

		$optimized_total = (float) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = %s",
				self::META_OPTIMIZED
			)
		);

		$saved   = max( 0, $original_total - $optimized_total );
		$average = $original_total > 0 ? ( $saved / $original_total ) * 100 : 0;

		return array(
			'total_images'     => $total_images,
			'optimized_images' => $optimized_images,
			'unoptimized'      => max( 0, $total_images - $optimized_images ),
			'original_total'   => (int) $original_total,
			'optimized_total'  => (int) $optimized_total,
			'saved_total'      => (int) $saved,
			'average_saved'    => round( $average, 2 ),
		);
	}

	/**
	 * Get attachment IDs, optionally only those not yet optimized.
	 *
	 * @param bool $only_unoptimized Restrict to pending images.
	 * @return int[]
	 */
	public function get_image_ids( $only_unoptimized = false ) {
		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => array( 'image/jpeg', 'image/png', 'image/bmp', 'image/gif' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		);

		if ( $only_unoptimized ) {
			$args['meta_query'] = array(
				array(
					'key'     => self::META_STATUS,
					'compare' => 'NOT EXISTS',
				),
			);
		}

		$query = new \WP_Query( $args );
		return array_map( 'intval', $query->posts );
	}
}
