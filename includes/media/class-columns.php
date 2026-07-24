<?php
/**
 * Media library column + attachment detail integration.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Media;

use SmartImageOptimizer\Settings;
use SmartImageOptimizer\Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds optimization details to the Media Library list view.
 */
final class Columns {

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
		add_filter( 'manage_media_columns', array( $this, 'add_column' ) );
		add_action( 'manage_media_custom_column', array( $this, 'render_column' ), 10, 2 );
	}

	/**
	 * Register the custom column.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function add_column( $columns ) {
		$columns['sio_optimization'] = __( 'Optimization', 'smart-image-optimizer' );
		return $columns;
	}

	/**
	 * Render the custom column contents.
	 *
	 * @param string $column_name   Column key.
	 * @param int    $attachment_id Attachment ID.
	 */
	public function render_column( $column_name, $attachment_id ) {
		if ( 'sio_optimization' !== $column_name ) {
			return;
		}

		$attachment_id = (int) $attachment_id;

		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			echo '<span class="sio-col sio-col--na">&mdash;</span>';
			return;
		}

		$data = $this->stats->get_attachment_stats( $attachment_id );

		if ( 'optimized' !== $data['status'] ) {
			printf(
				'<span class="sio-badge sio-badge--pending">%s</span>',
				esc_html__( 'Not optimized', 'smart-image-optimizer' )
			);
			return;
		}

		$webp_label   = ( 'yes' === $data['webp'] ) ? __( 'Yes', 'smart-image-optimizer' ) : __( 'No', 'smart-image-optimizer' );
		$resize_label = ( 'yes' === $data['resized'] ) ? __( 'Yes', 'smart-image-optimizer' ) : __( 'No', 'smart-image-optimizer' );
		$date_display = $data['date'] ? mysql2date( get_option( 'date_format' ), $data['date'] ) : '';

		echo '<div class="sio-col">';
		printf(
			'<div class="sio-col__row"><span>%1$s</span><strong>%2$s</strong></div>',
			esc_html__( 'Original', 'smart-image-optimizer' ),
			esc_html( sio_format_bytes( $data['original'] ) )
		);
		printf(
			'<div class="sio-col__row"><span>%1$s</span><strong>%2$s</strong></div>',
			esc_html__( 'Optimized', 'smart-image-optimizer' ),
			esc_html( sio_format_bytes( $data['optimized'] ) )
		);
		printf(
			'<div class="sio-col__row"><span>%1$s</span><strong class="sio-saved">%2$s%%</strong></div>',
			esc_html__( 'Saved', 'smart-image-optimizer' ),
			esc_html( number_format_i18n( (float) $data['percent'], 1 ) )
		);
		printf(
			'<div class="sio-col__row"><span>%1$s</span><strong>%2$s</strong></div>',
			esc_html__( 'WebP', 'smart-image-optimizer' ),
			esc_html( $webp_label )
		);
		printf(
			'<div class="sio-col__row"><span>%1$s</span><strong>%2$s</strong></div>',
			esc_html__( 'Resized', 'smart-image-optimizer' ),
			esc_html( $resize_label )
		);
		if ( $date_display ) {
			printf(
				'<div class="sio-col__row"><span>%1$s</span><strong>%2$s</strong></div>',
				esc_html__( 'Date', 'smart-image-optimizer' ),
				esc_html( $date_display )
			);
		}
		echo '</div>';
	}
}
