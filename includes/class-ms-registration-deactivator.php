<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    MS_Registration
 * @subpackage MS_Registration/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    MS_Registration
 * @subpackage MS_Registration/includes
 * @author     Miroslav Sapic <sapic.miroslav@gmail.com>
 */
class MS_Registration_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wp_rewrite;
		delete_option("ms_config");
		remove_action('generate_rewrite_rules', 'ms_generate_rewrite_rules');
		$wp_rewrite->flush_rules();
	}

}
