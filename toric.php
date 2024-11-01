<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/alvinmuthui/
 * @since             1.0.0
 * @package           Toric
 *
 * @wordpress-plugin
 * Plugin Name:       Tori Codes
 * Plugin URI:        https://wordpress.org/plugins/tori-codes/
 * Description:       Allows you to create QR codes for your site.
 * Version:           1.0.1
 * Author:            Alvin Muthui
 * Author URI:        https://profiles.wordpress.org/alvinmuthui/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       toric
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TORIC_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-toric-activator.php
 */
function activate_toric() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-toric-activator.php';
	Toric_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-toric-deactivator.php
 */
function deactivate_toric() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-toric-deactivator.php';
	Toric_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_toric' );
register_deactivation_hook( __FILE__, 'deactivate_toric' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-toric.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_toric() {

	$plugin = new Toric();
	$plugin->run();
}
run_toric();
