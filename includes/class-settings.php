<?php
/**
 * Settings store built on the WordPress Options API.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads, writes and sanitizes plugin settings.
 */
final class Settings {

	/**
	 * Option key in wp_options.
	 */
	const OPTION_KEY = 'sio_settings';

	/**
	 * Settings group used by register_setting().
	 */
	const OPTION_GROUP = 'sio_settings_group';

	/**
	 * Default values.
	 *
	 * @var array
	 */
	private $defaults;

	/**
	 * Current (merged) values.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->defaults = array(
			'enable_plugin'      => 1,
			'enable_resize'      => 1,
			'enable_webp'        => 1,
			'keep_originals'     => 1,
			'overwrite_existing' => 0,
			'quality'            => 85,
			'lossless'           => 0,
			'max_width'          => 1920,
			'max_height'         => 1920,
			'maintain_aspect'    => 1,
			'prevent_upscaling'  => 1,
			'strip_metadata'     => 1,
			'preserve_icc'       => 1,
			'fix_orientation'    => 1,
			'enable_logging'     => 1,
			'batch_size'         => 3,
			'skip_large_mb'      => 0,
			'admin_language'     => 'fa',
			'allowed_types'      => array( 'image/jpeg', 'image/png', 'image/bmp', 'image/gif' ),
		);

		/**
		 * Filter the default settings.
		 *
		 * @param array $defaults Default settings.
		 */
		$this->defaults = apply_filters( 'sio_default_settings', $this->defaults );

		$stored        = get_option( self::OPTION_KEY, array() );
		$this->options = wp_parse_args( is_array( $stored ) ? $stored : array(), $this->defaults );
	}

	/**
	 * Get all merged settings.
	 *
	 * @return array
	 */
	public function all() {
		return $this->options;
	}

	/**
	 * Get the default settings.
	 *
	 * @return array
	 */
	public function defaults() {
		return $this->defaults;
	}

	/**
	 * Get a single setting value.
	 *
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Optional fallback.
	 * @return mixed
	 */
	public function get( $key, $fallback = null ) {
		if ( array_key_exists( $key, $this->options ) ) {
			return $this->options[ $key ];
		}
		if ( null !== $fallback ) {
			return $fallback;
		}
		return isset( $this->defaults[ $key ] ) ? $this->defaults[ $key ] : null;
	}

	/**
	 * Boolean helper.
	 *
	 * @param string $key Setting key.
	 * @return bool
	 */
	public function is_enabled( $key ) {
		return (bool) $this->get( $key );
	}

	/**
	 * Update and persist settings.
	 *
	 * @param array $values Partial or full settings.
	 * @return bool
	 */
	public function update( array $values ) {
		$this->options = wp_parse_args( $values, $this->options );
		return update_option( self::OPTION_KEY, $this->options );
	}

	/**
	 * Sanitize callback for register_setting().
	 *
	 * @param mixed $input Raw input.
	 * @return array
	 */
	public function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$clean = array();

		$booleans = array(
			'enable_plugin',
			'enable_resize',
			'enable_webp',
			'keep_originals',
			'overwrite_existing',
			'lossless',
			'maintain_aspect',
			'prevent_upscaling',
			'strip_metadata',
			'preserve_icc',
			'fix_orientation',
			'enable_logging',
		);
		foreach ( $booleans as $key ) {
			$clean[ $key ] = ( ! empty( $input[ $key ] ) ) ? 1 : 0;
		}

		$quality          = isset( $input['quality'] ) ? absint( $input['quality'] ) : 85;
		$clean['quality'] = max( 1, min( 100, $quality ) );

		$clean['max_width']  = isset( $input['max_width'] ) ? absint( $input['max_width'] ) : 1920;
		$clean['max_height'] = isset( $input['max_height'] ) ? absint( $input['max_height'] ) : 1920;
		if ( $clean['max_width'] < 1 ) {
			$clean['max_width'] = 1920;
		}
		if ( $clean['max_height'] < 1 ) {
			$clean['max_height'] = 1920;
		}

		$clean['batch_size'] = isset( $input['batch_size'] ) ? absint( $input['batch_size'] ) : 3;
		$clean['batch_size'] = max( 1, min( 50, $clean['batch_size'] ) );

		$clean['skip_large_mb'] = isset( $input['skip_large_mb'] ) ? absint( $input['skip_large_mb'] ) : 0;
		$clean['skip_large_mb'] = min( 512, $clean['skip_large_mb'] );

		// Admin UI language (Persian / English / follow site).
		$language                = isset( $input['admin_language'] ) ? sanitize_key( $input['admin_language'] ) : 'fa';
		$clean['admin_language'] = in_array( $language, array( 'fa', 'en', 'auto' ), true ) ? $language : 'fa';

		// Allowed types are fixed to the supported formats.
		$clean['allowed_types'] = $this->defaults['allowed_types'];

		add_settings_error(
			self::OPTION_KEY,
			'sio_settings_saved',
			__( 'Settings saved.', 'smart-image-optimizer' ),
			'updated'
		);

		/**
		 * Filter the sanitized settings before saving.
		 *
		 * @param array $clean Clean settings.
		 * @param array $input Raw input.
		 */
		return apply_filters( 'sio_sanitize_settings', $clean, $input );
	}
}
