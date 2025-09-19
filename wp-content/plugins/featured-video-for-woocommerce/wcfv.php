<?php
/*
 * Plugin Name:       Featured Video for WooCommerce
 * Description:       Easily add featured videos to your WooCommerce products to increase engagement and conversions.
 * Version:           2.1.2
 * Tested up to: 			6.8
 * Author:            Foad Adeli
 * Author URI:        https://foadadeli.ir
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wcfv
 * Domain Path:       /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'WCFV_BASENAME', plugin_basename(__FILE__) );


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WCFV_VERSION', '2.1.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wcfv-activator.php
 */
function wcfv_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcfv-activator.php';
	Wcfv_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wcfv-deactivator.php
 */
function wcfv_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcfv-deactivator.php';
	Wcfv_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wcfv_activate' );
register_deactivation_hook( __FILE__, 'wcfv_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wcfv.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wcfv_run() {

	$plugin = new Wcfv();
	$plugin->run();

}
wcfv_run();
