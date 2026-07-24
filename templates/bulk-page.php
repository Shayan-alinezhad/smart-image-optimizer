<?php
/**
 * Bulk optimization page template.
 *
 * @package SmartImageOptimizer
 *
 * @var array $totals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="<?php echo esc_attr( sio_wrap_classes() ); ?>">
	<?php
	sio_render_partial(
		'header',
		array(
			'sio_page_title' => __( 'Bulk Optimize Images', 'smart-image-optimizer' ),
			'sio_page_icon'  => 'dashicons-images-alt2',
		)
	);
	?>

	<div class="sio-card">
		<div class="sio-stats-grid" id="sio-bulk-stats">
			<div class="sio-stat">
				<span class="sio-stat__num" data-stat="total_images"><?php echo esc_html( number_format_i18n( $totals['total_images'] ) ); ?></span>
				<span class="sio-stat__label"><?php esc_html_e( 'Total Images', 'smart-image-optimizer' ); ?></span>
			</div>
			<div class="sio-stat">
				<span class="sio-stat__num" data-stat="optimized_images"><?php echo esc_html( number_format_i18n( $totals['optimized_images'] ) ); ?></span>
				<span class="sio-stat__label"><?php esc_html_e( 'Optimized', 'smart-image-optimizer' ); ?></span>
			</div>
			<div class="sio-stat">
				<span class="sio-stat__num" data-stat="saved_total_human"><?php echo esc_html( sio_format_bytes( $totals['saved_total'] ) ); ?></span>
				<span class="sio-stat__label"><?php esc_html_e( 'Space Saved', 'smart-image-optimizer' ); ?></span>
			</div>
			<div class="sio-stat">
				<span class="sio-stat__num" data-stat="average_saved"><?php echo esc_html( number_format_i18n( $totals['average_saved'], 1 ) ); ?>%</span>
				<span class="sio-stat__label"><?php esc_html_e( 'Avg. Compression', 'smart-image-optimizer' ); ?></span>
			</div>
		</div>
	</div>

	<div class="sio-card">
		<h2><?php esc_html_e( 'Run Bulk Optimization', 'smart-image-optimizer' ); ?></h2>

		<p>
			<label>
				<input type="radio" name="sio-scope" value="unoptimized" checked="checked" />
				<?php esc_html_e( 'Only images that have not been optimized yet', 'smart-image-optimizer' ); ?>
			</label>
			<br />
			<label>
				<input type="radio" name="sio-scope" value="all" />
				<?php esc_html_e( 'All images (re-optimize everything)', 'smart-image-optimizer' ); ?>
			</label>
		</p>

		<div class="sio-actions">
			<button type="button" class="button button-primary" id="sio-start"><?php esc_html_e( 'Start Optimization', 'smart-image-optimizer' ); ?></button>
			<button type="button" class="button" id="sio-pause" disabled="disabled"><?php esc_html_e( 'Pause', 'smart-image-optimizer' ); ?></button>
			<button type="button" class="button" id="sio-resume" disabled="disabled"><?php esc_html_e( 'Resume', 'smart-image-optimizer' ); ?></button>
			<button type="button" class="button" id="sio-cancel" disabled="disabled"><?php esc_html_e( 'Cancel', 'smart-image-optimizer' ); ?></button>
		</div>

		<div class="sio-progress-wrap" id="sio-progress-wrap" hidden="hidden">
			<div class="sio-progress">
				<div class="sio-progress__bar" id="sio-progress-bar" style="width:0%"></div>
			</div>
			<div class="sio-progress__meta">
				<span id="sio-progress-count">0 / 0</span>
				<span id="sio-progress-eta"></span>
			</div>
			<p class="sio-status" id="sio-status" role="status" aria-live="polite"></p>
		</div>

		<div class="sio-log" id="sio-log" hidden="hidden"></div>
	</div>

	<?php sio_render_partial( 'footer' ); ?>
</div>
