<?php
/**
 * Uninstall routine for Smart Image Optimizer & Auto WebP.
 *
 * Removes all plugin options and per-attachment optimization metadata.
 * Backup files in uploads/sio-backups are intentionally left in place so that
 * users can restore originals manually; this is documented in readme.txt.
 *
 * @package SmartImageOptimizer
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Remove options.
delete_option( 'sio_settings' );
delete_option( 'sio_logs' );
delete_option( 'sio_version' );

// Multisite: clean up per-site options.
if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $site_ids as $site_id ) {
		switch_to_blog( $site_id );
		delete_option( 'sio_settings' );
		delete_option( 'sio_logs' );
		delete_option( 'sio_version' );
		restore_current_blog();
	}
}

// Remove all plugin post meta (prepared query).
$meta_keys = array(
	'_sio_status',
	'_sio_original_size',
	'_sio_optimized_size',
	'_sio_saved_bytes',
	'_sio_saved_percent',
	'_sio_webp_status',
	'_sio_resize_status',
	'_sio_optimized_date',
	'_sio_webp_path',
	'_sio_backup_path',
);

foreach ( $meta_keys as $meta_key ) {
	$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => $meta_key ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
}
