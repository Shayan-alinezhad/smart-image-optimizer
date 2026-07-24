<?php
/**
 * Plugin Name:       Cloner Smart Image Optimizer & Auto WebP
 * Plugin URI:        https://clonerr.ir/smart-image-optimizer
 * Description:       Automatically validates, resizes, compresses and converts uploaded images to WebP. Includes bulk optimization, media library stats, a dashboard widget, logging and a built-in Persian/English language switcher. Developed by Cloner (https://clonerr.ir).
 * Version:           1.1.0
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            Cloner (Shayan)
 * Author URI:        https://clonerr.ir
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-image-optimizer
 * Domain Path:       /languages
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Constants.
// -----------------------------------------------------------------------------
define( 'SIO_VERSION', '1.1.0' );
define( 'SIO_AUTHOR', 'Cloner' );
define( 'SIO_AUTHOR_URL', 'https://clonerr.ir' );
define( 'SIO_SUPPORT_URL', 'https://clonerr.ir/support' );
define( 'SIO_PLUGIN_FILE', __FILE__ );
define( 'SIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SIO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SIO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// -----------------------------------------------------------------------------
// Autoloader + procedural helpers.
// -----------------------------------------------------------------------------
require_once SIO_PLUGIN_DIR . 'includes/class-autoloader.php';
Autoloader::register();

require_once SIO_PLUGIN_DIR . 'includes/helpers.php';

// -----------------------------------------------------------------------------
// Lifecycle hooks.
// -----------------------------------------------------------------------------
register_activation_hook( __FILE__, array( __NAMESPACE__ . '\\Setup\\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\\Setup\\Deactivator', 'deactivate' ) );

/**
 * Boot the plugin once all other plugins are loaded.
 */
function sio_boot() {
	Plugin::instance()->boot();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\sio_boot' );
