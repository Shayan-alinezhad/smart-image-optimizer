<?php
/**
 * Singleton bootstrap + lightweight service container.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer;

use SmartImageOptimizer\Image\Optimizer;
use SmartImageOptimizer\Image\Processor;
use SmartImageOptimizer\Media\UploadHandler;
use SmartImageOptimizer\Media\Columns;
use SmartImageOptimizer\Media\RowActions;
use SmartImageOptimizer\Admin\Admin;
use SmartImageOptimizer\Admin\SettingsPage;
use SmartImageOptimizer\Admin\BulkPage;
use SmartImageOptimizer\Admin\LogsPage;
use SmartImageOptimizer\Admin\DashboardWidget;
use SmartImageOptimizer\Admin\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin orchestrator.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Resolved services.
	 *
	 * @var array
	 */
	private $services = array();

	/**
	 * Whether boot() already ran.
	 *
	 * @var bool
	 */
	private $booted = false;

	/**
	 * Retrieve the singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Protected constructor (singleton).
	 */
	private function __construct() {}

	/**
	 * No cloning.
	 */
	private function __clone() {}

	/**
	 * No unserializing.
	 */
	public function __wakeup() {
		throw new \RuntimeException( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Wire up services and register hooks.
	 */
	public function boot() {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		// --- Core services (manual dependency injection). ---
		$settings  = new Settings();
		$logger    = new Logger( $settings );
		$stats     = new Stats();
		$processor = new Processor( $settings, $logger );
		$optimizer = new Optimizer( $settings, $processor, $logger, $stats );
		$i18n      = new I18n( $settings );

		$this->services = compact( 'settings', 'logger', 'stats', 'processor', 'optimizer', 'i18n' );

		// --- Internationalisation (text domain + Persian/English switcher). ---
		add_action( 'init', array( $this, 'load_textdomain' ) );
		$i18n->register();

		// --- Media + upload integration (runs on front and back end). ---
		( new UploadHandler( $settings, $optimizer, $logger ) )->register();
		( new Columns( $settings, $stats ) )->register();

		// --- Admin-only surfaces. ---
		if ( is_admin() ) {
			( new Admin() )->register();
			( new SettingsPage( $settings ) )->register();
			( new BulkPage( $settings, $stats ) )->register();
			( new LogsPage( $logger ) )->register();
			( new DashboardWidget( $stats ) )->register();
			( new RowActions( $optimizer, $stats ) )->register();
			( new Ajax( $settings, $optimizer, $logger, $stats ) )->register();
		}
	}

	/**
	 * Fetch a resolved service by key.
	 *
	 * @param string $key Service key.
	 * @return mixed|null
	 */
	public function get( $key ) {
		return isset( $this->services[ $key ] ) ? $this->services[ $key ] : null;
	}

	/**
	 * Load the plugin text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'smart-image-optimizer',
			false,
			dirname( SIO_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
