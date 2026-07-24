<?php
/**
 * Shared plugin page header with the Persian / English language switcher.
 *
 * @package SmartImageOptimizer
 * @author  Cloner (Shayan)
 * @link    https://clonerr.ir
 *
 * @var string $sio_page_title Page title.
 * @var string $sio_page_icon  Dashicon class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sio_page_title = isset( $sio_page_title ) ? $sio_page_title : __( 'Smart Image Optimizer & Auto WebP', 'smart-image-optimizer' );
$sio_page_icon  = isset( $sio_page_icon ) ? $sio_page_icon : 'dashicons-images-alt2';

$sio_i18n    = sio_i18n();
$sio_current = $sio_i18n ? $sio_i18n->stored() : 'fa';
?>
<div class="sio-topbar">
	<h1 class="sio-title">
		<span class="dashicons <?php echo esc_attr( $sio_page_icon ); ?>"></span>
		<?php echo esc_html( $sio_page_title ); ?>
	</h1>

	<?php if ( $sio_i18n ) : ?>
		<div class="sio-langbar" role="group" aria-label="<?php esc_attr_e( 'Interface Language', 'smart-image-optimizer' ); ?>">
			<span class="sio-langbar__label dashicons dashicons-translation" aria-hidden="true"></span>
			<a class="sio-langbtn <?php echo ( 'fa' === $sio_current ) ? 'is-active' : ''; ?>"
				href="<?php echo esc_url( $sio_i18n->switch_url( 'fa' ) ); ?>">فارسی</a>
			<a class="sio-langbtn <?php echo ( 'en' === $sio_current ) ? 'is-active' : ''; ?>"
				href="<?php echo esc_url( $sio_i18n->switch_url( 'en' ) ); ?>">English</a>
			<a class="sio-langbtn <?php echo ( 'auto' === $sio_current ) ? 'is-active' : ''; ?>"
				href="<?php echo esc_url( $sio_i18n->switch_url( 'auto' ) ); ?>"><?php esc_html_e( 'Follow site language', 'smart-image-optimizer' ); ?></a>
		</div>
	<?php endif; ?>
</div>
