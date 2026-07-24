<?php
/**
 * Plugin settings page.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Admin;

use SmartImageOptimizer\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the top-level menu and renders the settings form.
 */
final class SettingsPage {

	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Hook suffix for this page.
	 *
	 * @var string
	 */
	private $hook = '';

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings service.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
	}

	/**
	 * Register the top-level menu + Settings submenu.
	 */
	public function menu() {
		$this->hook = add_menu_page(
			__( 'Smart Image Optimizer & Auto WebP', 'smart-image-optimizer' ),
			__( 'Cloner Image Optimizer', 'smart-image-optimizer' ),
			'manage_options',
			'sio-settings',
			array( $this, 'render' ),
			'dashicons-images-alt2',
			80
		);

		add_submenu_page(
			'sio-settings',
			__( 'Settings', 'smart-image-optimizer' ),
			__( 'Settings', 'smart-image-optimizer' ),
			'manage_options',
			'sio-settings',
			array( $this, 'render' )
		);
	}

	/**
	 * Register the setting + sanitize callback (Options API).
	 */
	public function register_settings() {
		register_setting(
			Settings::OPTION_GROUP,
			Settings::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this->settings, 'sanitize' ),
				'default'           => $this->settings->defaults(),
			)
		);
	}

	/**
	 * Enqueue assets only on this page.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function assets( $hook ) {
		if ( $hook !== $this->hook ) {
			return;
		}
		sio_enqueue_admin_assets();
		wp_enqueue_script(
			'sio-admin',
			SIO_PLUGIN_URL . 'assets/js/admin.js',
			array(),
			SIO_VERSION,
			true
		);
	}

	/**
	 * Render the settings page template.
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'smart-image-optimizer' ) );
		}

		$settings       = $this->settings;
		$values         = $settings->all();
		$webp_supported = sio_webp_supported();
		$imagick        = sio_imagick_available();
		$gd             = sio_gd_available();

		include SIO_PLUGIN_DIR . 'templates/settings-page.php';
	}
}
