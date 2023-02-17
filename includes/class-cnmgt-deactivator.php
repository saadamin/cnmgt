<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.saadamin.com
 * @since      1.0.0
 *
 * @package    Cnmgt
 * @subpackage Cnmgt/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cnmgt
 * @subpackage Cnmgt/includes
 * @author     Saad Amin <saadvi@gmail.com>
 */
class Cnmgt_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
		$table_name = $wpdb->prefix . "cnmgt";
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}

}
