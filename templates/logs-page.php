<?php
/**
 * Logs page template.
 *
 * @package SmartImageOptimizer
 *
 * @var array $logs
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
			'sio_page_title' => __( 'Optimization Logs', 'smart-image-optimizer' ),
			'sio_page_icon'  => 'dashicons-media-text',
		)
	);
	?>

	<?php if ( isset( $_GET['cleared'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Logs cleared.', 'smart-image-optimizer' ); ?></p></div>
	<?php endif; ?>

	<div class="sio-card">
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sio-log-clear">
			<input type="hidden" name="action" value="sio_clear_logs" />
			<?php wp_nonce_field( 'sio_clear_logs' ); ?>
			<button type="submit" class="button"><?php esc_html_e( 'Clear Logs', 'smart-image-optimizer' ); ?></button>
		</form>

		<?php if ( empty( $logs ) ) : ?>
			<p><?php esc_html_e( 'No log entries yet.', 'smart-image-optimizer' ); ?></p>
		<?php else : ?>
			<table class="widefat striped sio-log-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time', 'smart-image-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Level', 'smart-image-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Message', 'smart-image-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Context', 'smart-image-optimizer' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $logs as $entry ) : ?>
						<tr>
							<td><?php echo esc_html( $entry['time'] ); ?></td>
							<td><span class="sio-level sio-level--<?php echo esc_attr( $entry['level'] ); ?>"><?php echo esc_html( $entry['level'] ); ?></span></td>
							<td><?php echo esc_html( $entry['message'] ); ?></td>
							<td>
								<?php
								if ( ! empty( $entry['context'] ) && is_array( $entry['context'] ) ) {
									$pairs = array();
									foreach ( $entry['context'] as $key => $value ) {
										$pairs[] = $key . ': ' . $value;
									}
									echo esc_html( implode( ', ', $pairs ) );
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<?php sio_render_partial( 'footer' ); ?>
</div>
