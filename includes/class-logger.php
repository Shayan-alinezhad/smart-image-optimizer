<?php
/**
 * Lightweight logger backed by an autoloaded option.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores recent log entries in the options table (capped).
 */
final class Logger {

	/**
	 * Option key for the log store.
	 */
	const OPTION_LOG = 'sio_logs';

	/**
	 * Maximum number of retained entries.
	 */
	const MAX_ENTRIES = 500;

	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings service.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Write a log entry.
	 *
	 * @param string $level   info|warning|error.
	 * @param string $message Message.
	 * @param array  $context Optional scalar context.
	 */
	public function log( $level, $message, $context = array() ) {
		// Errors are always recorded; info/warning respect the logging toggle.
		if ( 'error' !== $level && ! $this->settings->is_enabled( 'enable_logging' ) ) {
			return;
		}

		$logs = get_option( self::OPTION_LOG, array() );
		if ( ! is_array( $logs ) ) {
			$logs = array();
		}

		$logs[] = array(
			'time'    => current_time( 'mysql' ),
			'level'   => sanitize_key( $level ),
			'message' => wp_strip_all_tags( (string) $message ),
			'context' => $this->sanitize_context( $context ),
		);

		if ( count( $logs ) > self::MAX_ENTRIES ) {
			$logs = array_slice( $logs, -self::MAX_ENTRIES );
		}

		update_option( self::OPTION_LOG, $logs, false );
	}

	/**
	 * Convenience: info.
	 *
	 * @param string $message Message.
	 * @param array  $context Context.
	 */
	public function info( $message, $context = array() ) {
		$this->log( 'info', $message, $context );
	}

	/**
	 * Convenience: warning.
	 *
	 * @param string $message Message.
	 * @param array  $context Context.
	 */
	public function warning( $message, $context = array() ) {
		$this->log( 'warning', $message, $context );
	}

	/**
	 * Convenience: error.
	 *
	 * @param string $message Message.
	 * @param array  $context Context.
	 */
	public function error( $message, $context = array() ) {
		$this->log( 'error', $message, $context );
	}

	/**
	 * Retrieve recent log entries (newest first).
	 *
	 * @param int $limit Max entries.
	 * @return array
	 */
	public function get_logs( $limit = 200 ) {
		$logs = get_option( self::OPTION_LOG, array() );
		if ( ! is_array( $logs ) ) {
			return array();
		}
		$logs = array_reverse( $logs );
		return array_slice( $logs, 0, max( 1, (int) $limit ) );
	}

	/**
	 * Clear all log entries.
	 *
	 * @return bool
	 */
	public function clear() {
		return update_option( self::OPTION_LOG, array(), false );
	}

	/**
	 * Sanitize a context array to scalar values only.
	 *
	 * @param mixed $context Context.
	 * @return array
	 */
	private function sanitize_context( $context ) {
		if ( ! is_array( $context ) ) {
			return array();
		}
		$out = array();
		foreach ( $context as $key => $value ) {
			if ( is_scalar( $value ) ) {
				$out[ sanitize_key( $key ) ] = is_string( $value ) ? wp_strip_all_tags( $value ) : $value;
			}
		}
		return $out;
	}
}
