<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           MS_Registration
 *
 * @wordpress-plugin
 * Plugin Name:       MS Registration
 * Description:       Customize WordPress registration form
 * Version:           1.0.0
 * Author:            Miroslav Sapic
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ms-registration
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
define( 'MS_REGISTRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_ms_registration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ms-registration-activator.php';
	MS_Registration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_ms_registration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ms-registration-deactivator.php';
	MS_Registration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ms_registration' );
register_deactivation_hook( __FILE__, 'deactivate_ms_registration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ms-registration.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ms_registration() {

	$plugin = new MS_Registration();
	$plugin->run();

}
run_ms_registration();
