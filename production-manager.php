<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.protosstudio.com
 * @since             1.0.0
 * @package           Production_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Production Manager
 * Plugin URI:        www.plugins.protosstudio.com
 * Description:       Production Manager using Woocommerce Vouchers.
 * Version:           1.0.0
 * Author:            Samuel Bohl
 * Author URI:        www.samuelbohl.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       production-manager
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
define( 'PRODUCTION_MANAGER', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-production-manager-activator.php
 */
function activate_production_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-production-manager-activator.php';
	Production_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-production-manager-deactivator.php
 */
function deactivate_production_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-production-manager-deactivator.php';
	Production_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_production_manager' );
register_deactivation_hook( __FILE__, 'deactivate_production_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-production-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_production_manager() {

	$plugin = new Production_Manager();
	$plugin->run();

}
run_production_manager();
