<?php
/**
 * Dashboard widget template.
 *
 * @package SmartImageOptimizer
 *
 * @var array $totals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sio-widget">
	<ul class="sio-widget__list">
		<li>
			<span><?php esc_html_e( 'Total Images', 'smart-image-optimizer' ); ?></span>
			<strong><?php echo esc_html( number_format_i18n( $totals['total_images'] ) ); ?></strong>
		</li>
		<li>
			<span><?php esc_html_e( 'Optimized Images', 'smart-image-optimizer' ); ?></span>
			<strong><?php echo esc_html( number_format_i18n( $totals['optimized_images'] ) ); ?></strong>
		</li>
		<li>
			<span><?php esc_html_e( 'Total Original Size', 'smart-image-optimizer' ); ?></span>
			<strong><?php echo esc_html( sio_format_bytes( $totals['original_total'] ) ); ?></strong>
		</li>
		<li>
			<span><?php esc_html_e( 'Total Optimized Size', 'smart-image-optimizer' ); ?></span>
			<strong><?php echo esc_html( sio_format_bytes( $totals['optimized_total'] ) ); ?></strong>
		</li>
		<li>
			<span><?php esc_html_e( 'Total Space Saved', 'smart-image-optimizer' ); ?></span>
			<strong class="sio-saved"><?php echo esc_html( sio_format_bytes( $totals['saved_total'] ) ); ?></strong>
		</li>
		<li>
			<span><?php esc_html_e( 'Average Compression', 'smart-image-optimizer' ); ?></span>
			<strong class="sio-saved"><?php echo esc_html( number_format_i18n( $totals['average_saved'], 1 ) ); ?>%</strong>
		</li>
	</ul>
	<p class="sio-widget__foot">
		<a href="<?php echo esc_url( admin_url( 'upload.php?page=sio-bulk' ) ); ?>"><?php esc_html_e( 'Open Bulk Optimizer', 'smart-image-optimizer' ); ?></a>
		<span class="sio-brandbar__sep">|</span>
		<a href="<?php echo esc_url( SIO_AUTHOR_URL ); ?>" target="_blank" rel="noopener noreferrer">clonerr.ir</a>
	</p>
</div>
