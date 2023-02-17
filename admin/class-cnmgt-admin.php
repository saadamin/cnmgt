<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.saadamin.com
 * @since      1.0.0
 *
 * @package    Cnmgt
 * @subpackage Cnmgt/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cnmgt
 * @subpackage Cnmgt/admin
 * @author     Saad Amin <saadvi@gmail.com>
 */
class Cnmgt_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */


	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cnmgt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cnmgt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cnmgt-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'select2_css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cnmgt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cnmgt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cnmgt-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'select2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), $this->version, false );

	}
	public function load_menu()
	{
		// Menu hook
		global $cnmgt_hook;
    	// Add main page
		$cnmgt_hook = add_menu_page( 'Contact Management', 'Contact Management', 'manage_options', 'cnmgt',array( __CLASS__, 'show_all_people' ), 'dashicons-universal-access', 6 );
		$cnmgt_hook = add_submenu_page('cnmgt', 'All_Entries', 'Add People', 'manage_options', 'cnmgt_manage_users',  array( __CLASS__, 'manage_people' ));
	}
	public static function show_all_people() {
		return plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/cnmgt-admin-display.php';
	}
	public static function manage_people() {
		$countries = self::get_countries();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/cnmgt-manage-people.php';
	}
	private static function get_countries() {
		// Getting country list from restcountries.com
		$raw_response = wp_remote_get( 'https://restcountries.com/v2/all' );

		if ( !is_wp_error( $raw_response ) && ( wp_remote_retrieve_response_code( $raw_response ) == 200 ) ) {
			
			$response = wp_remote_retrieve_response_code( $raw_response );
		}
		return wp_remote_retrieve_body( $raw_response);
	}
}
