<?php
/**
 * Plugin Name: Branding
 * Plugin URI: 
 * Description: Well, g'day there! Lets work together to rebrand your copy of the using your logo, your icon and your brand name.
 * Version: 
 * Author: 
 * Author URI: 
 * Requires at least: 
 * Tested up to: 
 *
 * Text Domain: branding
 * Domain Path: /languages/
 *
 * @package Branding
 * @category Core
 * @author Matty
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Branding to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Branding
 */
function Branding() {
	return Branding::instance();
} // End Branding()

Branding();

/**
 * Main Branding Class
 *
 * @class Branding
 * @version	1.0.0
 * @since 1.0.0
 * @package	Branding
 * @author Matty
 */
final class Branding {
	/**
	 * Branding The single instance of Branding.
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
	 * The admin page slug.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin_page;

	/**
	 * The admin parent page.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin_parent_page;

	/**
	 * The instance of SF_Fields.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	private $_field_obj;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'framework-branding';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.1';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// We need to run this only once the theme is setup and ready.
		add_action( 'after_setup_theme', array( $this, 'init' ) );
	} // End __construct()

	/**
	 * Initialise the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init () {
		if ( is_admin() ) {
			// Register the admin screen.
			add_action( 'admin_menu', array( $this, 'register_admin_screen' ) );

			// Register the admin screen to be able to load the Framework's CSS and other assets.
			add_filter( 'sf_load_admin_css', array( $this, 'register_screen_id' ) );

			// If applicable, instantiate SF_Fields from the Framework.
			if ( defined( 'THEME_FRAMEWORK' ) && 'sf' == constant( 'THEME_FRAMEWORK' ) && class_exists( 'SF_Fields' ) ) {
				$this->_field_obj = new SF_Fields();
				$this->_field_obj->init( $this->get_settings_template() );
				$this->_field_obj->__set( 'token', 'sf' );
			}
			// Maybe override the Framework settings screen logo.
			add_filter( 'sf_branding_logo', array( $this, 'maybe_override_logo_image_url' ) );

			// Maybe override the Framework administration menu icon.
			add_filter( 'sf_branding_icon', array( $this, 'maybe_override_icon_url' ) );

			// Maybe override the Framework login screen logo.
			add_filter( 'sf_login_logo', array( $this, 'maybe_override_login_image_url' ) );

			// Maybe override the Framework administration menu label.
			add_action( 'admin_menu', array( $this, 'maybe_override_admin_menu_label' ) );
		}
	} // End init()

	/**
	 * Register the screen ID with the Framework's asset loader.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_screen_id ( $screens ) {
		if ( ! in_array( 'sf-branding', $screens ) ) {
			$screens[] = 'sf-branding';
		}
		return $screens;
	} // End register_screen_id()

	/**
	 * Register the admin screen within WordPress.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_admin_screen () {
		$this->admin_parent_page = 'themes.php';
		if ( defined( 'THEME_FRAMEWORK' ) && 'sf' == constant( 'THEME_FRAMEWORK' ) ) {
			$this->admin_parent_page = 'sf';
		}

		$this->admin_page = add_submenu_page( $this->admin_parent_page, __( 'Branding', 'framework-branding' ), __( 'Branding', 'framework-branding' ), 'manage_options', 'sf-branding', array( $this, 'admin_screen' ) );

		// Admin screen logic.
		add_action( 'load-' . $this->admin_page, array( $this, 'admin_screen_logic' ) );

		// Add contextual help tabs.
		add_action( 'load-' . $this->admin_page, array( $this, 'admin_screen_help' ) );

		// Make sure our data is added to the Framework settings exporter.
		add_filter( 'framework_export_query_inner', array( $this, 'add_exporter_data' ) );

		// Add admin notices.
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	} // End register_admin_screen()

	/**
	 * Load the admin screen markup.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_screen () {
?>
	<div class="wrap framework-branding-wrap">
<?php
		// If a WooThemes theme isn't activated, display a notice.
		if ( ! defined( 'THEME_FRAMEWORK' ) || 'sf' != constant( 'THEME_FRAMEWORK' ) ) {
			echo '<div class="error fade"><p>' . __( 'It appears your theme does not contain the Framework. In order to use the Framework Branding, please use a theme which makes use of the Framework.', 'framework-branding' ) . '</p></div>' . "\n";
		} else {
			// If this is an old version of the Framework, display a notice.
			if ( ! class_exists( 'SF_Fields' ) ) {
				echo '<div class="error fade"><p>' . __( 'It appears you\'re using an older version of the Framework. Framework Branding requires Framework 6.0 or higher.', 'framework-branding' ) . '</p></div>' . "\n";
			} else {
				// Otherwise, we're good to go!
				$hidden_fields = array( 'page' => 'sf-branding' );
				do_action( 'sf_screen_get_header', 'sf-branding', 'themes' );
				$this->_field_obj->__set( 'has_tabs', false );
				$this->_field_obj->__set( 'extra_hidden_fields', $hidden_fields );
				$this->_field_obj->render();
				do_action( 'sf_screen_get_footer', 'sf-branding', 'themes' );
			}
		}
?>
	</div><!--/.wrap-->
<?php
		// This must be present if using fields that require Javascript or styling.
		add_action( 'admin_footer', array( $this->_field_obj, 'maybe_enqueue_field_assets' ) );
	} // End admin_screen()

	/**
	 * Display admin notices for this settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_notices () {
		$notices = array();

		if ( isset( $_GET['page'] ) && 'sf-branding' == $_GET['page'] && isset( $_GET['updated'] ) && 'true' == $_GET['updated'] ) {
			$notices['settings-updated'] = array( 'type' => 'updated', 'message' => __( 'Settings saved.', 'framework-branding' ) );
		}

		if ( 0 < count( $notices ) ) {
			$html = '';
			foreach ( $notices as $k => $v ) {
				$html .= '<div id="' . esc_attr( $k ) . '" class="fade ' . esc_attr( $v['type'] ) . '">' . wpautop( '<strong>' . esc_html( $v['message'] ) . '</strong>' ) . '</div>' . "\n";
			}
			echo $html;
		}
	} // End admin_notices()

	/**
	 * Load contextual help for the admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  string Modified contextual help string.
	 */
	public function admin_screen_help () {
		$screen = get_current_screen();
		if ( $screen->id != $this->admin_page ) return;

		$overview =
			  '<p>' . __( 'Configure the branding settings and hit the "Save Changes" button. It\'s as easy as that!', 'framework-branding' ) . '</p>' .
			  '<p><strong>' . __( 'For more information:', 'framework-branding' ) . '</strong></p>' .
			  '<p>' . sprintf( __( '<a href="%s" target="_blank">WooThemes Help Desk</a>', 'framework-branding' ), 'http://support.woothemes.com/' ) . '</p>';

		$screen->add_help_tab( array( 'id' => 'framework_branding_overview', 'title' => __( 'Overview', 'framework-branding' ), 'content' => $overview ) );
	} // End admin_screen_help()

	/**
	 * Logic to run on the admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_screen_logic () {
		if ( ! empty( $_POST ) && check_admin_referer( $this->_field_obj->__get( 'token' ) . '_nonce', $this->_field_obj->__get( 'token' ) . '_nonce' ) ) {
			$data = $_POST;

			$page = 'sf-branding';
			if ( isset( $data['page'] ) ) {
				$page = $data['page'];
				unset( $data['page'] );
			}

			$data = $this->_field_obj->validate_fields( $data );

			if ( 0 < count( $data ) ) {
				foreach ( $data as $k => $v ) {
					update_option( $k, $v );
				}
			}

			// Keep track of the last username to edit the branding screen, so as least one user is never locked out. :)
			$user_id = get_current_user_id();
			update_option( 'framework_sf_last_branding_editor', intval( $user_id ) );

			// Redirect on settings save, and exit.
			$url = add_query_arg( 'page', $page );
			$url = add_query_arg( 'updated', 'true', $url );

			wp_safe_redirect( esc_url( $url ) );
			exit;
		}
	} // End admin_screen_logic()

	/**
	 * Maybe override the logo image URL.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_override_logo_image_url ( $url ) {
		$image_url = get_option( 'framework_sf_backend_header_image', '' );
		if ( '' != $image_url ) {
			$url = esc_url( $image_url );
		}
		return $url;
	} // End maybe_override_logo_image_url()

	/**
	 * Maybe override the login image URL.
	 * @access  public
	 * @since   1.0.3
	 * @return  array
	 */
	public function maybe_override_login_image_url ( $url ) {
		$image_url = get_option( 'framework_sf_custom_login_logo', '' );
		if ( '' != $image_url ) {
			$url = esc_url( $image_url );
		}
		return $url;
	} // End maybe_override_login_image_url()

	/**
	 * Maybe override the icon URL.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_override_icon_url ( $url ) {
		$image_url = get_option( 'framework_sf_backend_icon', '' );
		if ( '' != $image_url ) {
			$url = esc_url( $image_url );
		}
		return $url;
	} // End maybe_override_icon_url()

	/**
	 * Maybe override the menu label.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_override_admin_menu_label () {
		global $menu;
		$label = get_option( 'framework_sf_menu_label', '' );
		if ( '' != $label && 0 < count( (array)$menu ) ) {
			foreach ( $menu as $k => $v ) {
				if ( isset( $v[0] ) && isset( $v[2] ) && 'sf' == $v[2] ) {
					$menu[$k][0] = esc_html( $label );
				}
			}
		}
	} // End maybe_override_admin_menu_label()

	/**
	 * Return an array of the settings scafolding. The field types, names, etc.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function get_settings_template () {
		return array(
				// We must have a heading, so the fields can be assigned a section, and display correctly. :)
				'sf_branding_heading' => array(
										'name' => __( 'Branding', 'framework-branding' ),
										'std' => '',
										'id' => 'sf_branding_heading',
										'type' => 'heading'
										),
				'framework_sf_backend_header_image' => array(
										'name' => __( 'Your Logo Image', 'framework-branding' ),
										'desc' => __( 'Your logo image, for use on all Framework screens.', 'framework-branding' ),
										'std' => '',
										'id' => 'framework_sf_backend_header_image',
										'type' => 'upload'
										),
				'framework_sf_backend_icon' => array(
										'name' => __( 'Your Logo Icon', 'framework-branding' ),
										'desc' => __( 'Your logo icon, for the WordPress administration menu.', 'framework-branding' ),
										'std' => '',
										'id' => 'framework_sf_backend_icon',
										'type' => 'upload'
										),
				'framework_sf_custom_login_logo' => array(
										'name' => __( 'Your Login Logo Image', 'framework-branding' ),
										'desc' => __( 'Your logo image, for the WordPress administration login screen.', 'framework-branding' ),
										'std' => '',
										'id' => 'framework_sf_custom_login_logo',
										'type' => 'upload'
										),
				'framework_sf_menu_label' => array(
										'name' => __( 'Admin Menu Label', 'framework-branding' ),
										'desc' => sprintf( __( 'The label of the %1$s administration menu. Leave empty for the default menu label.', 'framework-branding' ), wp_get_theme()->__get( 'Name' ) ),
										'std' => '',
										'id' => 'framework_sf_menu_label',
										'type' => 'text'
										)
				);
	} // End get_settings_template()

	/**
	 * Main Branding Instance
	 *
	 * Ensures only one instance of Branding is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Branding()
	 * @return Main Branding instance
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
		load_plugin_textdomain( 'framework-branding', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
 	 * Add our saved data to the Framework data exporter.
 	 * @access  public
	 * @since   1.0.1
 	 * @param   string $data SQL query.
 	 * @return  string SQL query.
 	 */
	public function add_exporter_data ( $data ) {
		$option_keys = array(
								'framework_sf_last_branding_editor',
								'framework_sf_backend_header_image',
								'framework_sf_backend_icon',
								'framework_sf_custom_login_logo',
								'framework_sf_menu_label'
								);
		foreach ( $option_keys as $key ) {
			$data .= " OR option_name = '" . $key . "'";
		} // End For Loop
		return $data;
	} // End add_exporter_data()

} // End Class
?>