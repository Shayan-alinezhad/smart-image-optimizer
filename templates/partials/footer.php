<?php
/**
 * Shared Cloner branding footer for plugin admin pages.
 *
 * @package SmartImageOptimizer
 * @author  Cloner (Shayan)
 * @link    https://clonerr.ir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sio-brandbar">
	<div class="sio-brandbar__left">
		<span class="sio-brandbar__logo">Cloner</span>
		<span class="sio-brandbar__by"><?php esc_html_e( 'Developed by Cloner', 'smart-image-optimizer' ); ?></span>
	</div>
	<div class="sio-brandbar__right">
		<a href="<?php echo esc_url( SIO_AUTHOR_URL ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="dashicons dashicons-admin-site-alt3" aria-hidden="true"></span>
			clonerr.ir
		</a>
		<span class="sio-brandbar__sep">|</span>
		<span><?php esc_html_e( 'Version', 'smart-image-optimizer' ); ?> <?php echo esc_html( SIO_VERSION ); ?></span>
	</div>
</div>
