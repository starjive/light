<?php
/**
 * Plugin Name: Help Me Save
 * Plugin URI: http://dwainm.com
 * Description: Hey ! I'll make sure that you're prompted before leaving any unsaved changes in you theme settings.
 * Version: 1.0.0
 * Author: Dwainm
 * Author URI: http://dwainm.com/
 * Requires at least: 3.9.1
 * Tested up to: 3.9.1
 *
 * Text Domain: framework-help-me-save
 * Domain Path: /languages/
 *
 * @package framework-help-me-save
 * @category Core
 * @author Dwain Maralack
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of framework-help-me-save to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object framework-help-me-save
 */
function framework_help_me_save() {
	return Wooframework_help_me_save::instance();
} // End framework-help-me-save()

framework_help_me_save();

/**
 * Main framework-help-me-save Class
 *
 * @class framework-help-me-save
 * @version	1.0.0
 * @since 1.0.0
 * @package	framework-help-me-save
 * @author Matty
 */
final class Wooframework_help_me_save {
	/**
	 * framework-help-me-save The single instance of framework-help-me-save.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'framework-help-me-save';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// load plugins scripts on the WF page
		add_action( 'admin_enqueue_scripts' ,  array( $this, 'load_plugin_js') );	

	} // End __construct()

	/**
	 * Main framework-help-me-save Instance
	 *
	 * Ensures only one instance of framework-help-me-save is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see framework-help-me-save()
	 * @return Main framework-help-me-save instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'framework-help-me-save', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_js ($hook_sufix) {
		if( 'toplevel_page_sfthemes' == $hook_sufix ){
			// get script url
			$script_url = $this->plugin_url.'plugin.js' ; 

			//load the script for all Framework settings tabs
			wp_enqueue_script( 'framework-help-me-save',  $script_url , array('underscore', 'jquery', 'backbone' ) , $this->version , true );
		}
	} // End _log_version_number()


} // End Class
?>
