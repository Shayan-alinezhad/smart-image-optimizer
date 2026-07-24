<?php
/**
 * Bilingual (Persian / English) runtime language switcher.
 *
 * Instead of relying on compiled .mo files, the plugin ships a PHP translation
 * map and swaps strings through the `gettext` filter. This makes the language
 * toggle instant and independent of the site-wide WordPress locale.
 *
 * @package SmartImageOptimizer
 * @author  Cloner (Shayan)
 * @link    https://clonerr.ir
 */

namespace SmartImageOptimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the admin UI language for the plugin only.
 */
final class I18n {

	/**
	 * Text domain handled by this switcher.
	 */
	const DOMAIN = 'smart-image-optimizer';

	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Lazily loaded translation map.
	 *
	 * @var array|null
	 */
	private $map = null;

	/**
	 * Resolved active language: 'fa' or 'en'.
	 *
	 * @var string
	 */
	private $active = 'fa';

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
		$this->active = $this->resolve_language();

		if ( 'fa' === $this->active ) {
			add_filter( 'gettext', array( $this, 'translate' ), 20, 3 );
			add_filter( 'gettext_with_context', array( $this, 'translate_with_context' ), 20, 4 );
			add_filter( 'ngettext', array( $this, 'translate_plural' ), 20, 5 );
		}

		add_action( 'admin_post_sio_switch_language', array( $this, 'handle_switch' ) );
	}

	/**
	 * Available language choices.
	 *
	 * @return array
	 */
	public static function choices() {
		return array(
			'fa'   => 'فارسی',
			'en'   => 'English',
			'auto' => __( 'Follow site language', 'smart-image-optimizer' ),
		);
	}

	/**
	 * Resolve the effective language from the stored setting.
	 *
	 * @return string 'fa' or 'en'.
	 */
	public function resolve_language() {
		$stored = $this->settings->get( 'admin_language', 'fa' );

		if ( 'auto' === $stored ) {
			$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			return ( 0 === strpos( (string) $locale, 'fa' ) ) ? 'fa' : 'en';
		}

		return in_array( $stored, array( 'fa', 'en' ), true ) ? $stored : 'fa';
	}

	/**
	 * The raw stored preference ('fa', 'en' or 'auto').
	 *
	 * @return string
	 */
	public function stored() {
		$stored = $this->settings->get( 'admin_language', 'fa' );
		return in_array( $stored, array( 'fa', 'en', 'auto' ), true ) ? $stored : 'fa';
	}

	/**
	 * Currently active language code.
	 *
	 * @return string
	 */
	public function current() {
		return $this->active;
	}

	/**
	 * Whether the plugin UI should render right-to-left.
	 *
	 * @return bool
	 */
	public function is_rtl() {
		return 'fa' === $this->active;
	}

	/**
	 * Load (once) the Persian translation map.
	 *
	 * @return array
	 */
	private function map() {
		if ( null === $this->map ) {
			$file      = SIO_PLUGIN_DIR . 'languages/fa.php';
			$loaded    = file_exists( $file ) ? include $file : array();
			$this->map = is_array( $loaded ) ? $loaded : array();

			/**
			 * Filter the Persian translation map.
			 *
			 * @param array $map Translation pairs.
			 */
			$this->map = apply_filters( 'sio_translation_map_fa', $this->map );
		}
		return $this->map;
	}

	/**
	 * Translate a plugin string.
	 *
	 * @param string $translation Current translation.
	 * @param string $text        Original text.
	 * @param string $domain      Text domain.
	 * @return string
	 */
	public function translate( $translation, $text, $domain ) {
		if ( self::DOMAIN !== $domain ) {
			return $translation;
		}
		$map = $this->map();
		return isset( $map[ $text ] ) ? $map[ $text ] : $translation;
	}

	/**
	 * Translate a string that has gettext context.
	 *
	 * @param string $translation Current translation.
	 * @param string $text        Original text.
	 * @param string $context     Gettext context.
	 * @param string $domain      Text domain.
	 * @return string
	 */
	public function translate_with_context( $translation, $text, $context, $domain ) {
		return $this->translate( $translation, $text, $domain );
	}

	/**
	 * Translate plural forms (Persian uses a single form here).
	 *
	 * @param string $translation Current translation.
	 * @param string $single      Singular text.
	 * @param string $plural      Plural text.
	 * @param int    $number      Count.
	 * @param string $domain      Text domain.
	 * @return string
	 */
	public function translate_plural( $translation, $single, $plural, $number, $domain ) {
		if ( self::DOMAIN !== $domain ) {
			return $translation;
		}
		$map = $this->map();
		$key = ( 1 === (int) $number ) ? $single : $plural;
		return isset( $map[ $key ] ) ? $map[ $key ] : $translation;
	}

	/**
	 * Handle the language toggle request.
	 */
	public function handle_switch() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'smart-image-optimizer' ) );
		}

		check_admin_referer( 'sio_switch_language' );

		$lang = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : 'fa';
		if ( ! in_array( $lang, array( 'fa', 'en', 'auto' ), true ) ) {
			$lang = 'fa';
		}

		$this->settings->update( array( 'admin_language' => $lang ) );

		$redirect = wp_get_referer();
		if ( ! $redirect ) {
			$redirect = admin_url( 'admin.php?page=sio-settings' );
		}

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Build a nonced URL that switches to a given language.
	 *
	 * @param string $lang Language code.
	 * @return string
	 */
	public function switch_url( $lang ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'sio_switch_language',
					'lang'   => $lang,
				),
				admin_url( 'admin-post.php' )
			),
			'sio_switch_language'
		);
	}
}
