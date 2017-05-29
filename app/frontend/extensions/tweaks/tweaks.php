<?php
/**
 * Plugin Name: Tweaks
 * Plugin URI: 
 * Description: Hidey ho, neighborino! Lets add a few options back to the Framework, for a bit of extra fine tuning, shall we?
 * Version: 
 * Author: 
 * Author URI: 
 * Requires at least: 
 * Tested up to: 
 *
 * Text Domain: tweaks
 * Domain Path: /languages/
 *
 * @package Tweaks
 * @category Core
 * @author Matty
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Tweaks to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Tweaks
 */
function Tweaks() {
	return Tweaks::instance();
} // End Tweaks()

Tweaks();

/**
 * Main Tweaks Class
 *
 * @class Tweaks
 * @version	1.0.0
 * @since 1.0.0
 * @package	Tweaks
 * @author Matty
 */
final class Tweaks {
	/**
	 * Tweaks The single instance of Tweaks.
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
		$this->token 			= 'framework-tweaks';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.1';
		$this->_field_obj 		= null;

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// We need to run this only once the theme is setup and ready.
		add_action( 'after_setup_theme', array( $this, 'init_admin' ) );
		add_action( 'plugins_loaded', array( $this, 'init_frontend' ) );
		add_action( 'after_setup_theme', array( $this, 'init' ) );
	} // End __construct()

	/**
	 * Initialise the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init () {
		// Placeholders are in both the frontend and admin, so apply this globally.
		add_filter( 'sf_placeholder_image_url', array( $this, 'maybe_override_placeholder_image_url' ) );
		add_filter( 'sf_placeholder_image_path', array( $this, 'maybe_override_placeholder_image_path' ) );
	} // End init()

	/**
	 * Initialise the plugin admin.
	 * @access public
	 * @since  1.0.1
	 * @return void
	 */
	public function init_admin () {
		if ( is_admin() ) {
			// Register the admin screen.
			add_action( 'admin_menu', array( $this, 'register_admin_screen' ) );

			// Register the admin screen to be able to load the Framework's CSS and other assets.
			add_filter( 'sf_load_admin_css', array( $this, 'register_screen_id' ) );

			// Make sure we clean out the super user, when deleting the user from the database.
			// This has to be done on `delete_user` rather than `deleted_user`, as we still require the username and are only passed the user ID.
			add_action( 'delete_user', array( $this, 'maybe_clean_superuser_entry' ) );

			// If applicable, instantiate SF_Fields from the Framework.
			if ( defined( 'THEME_FRAMEWORK' ) && 'sf' == constant( 'THEME_FRAMEWORK' ) && class_exists( 'SF_Fields' ) ) {
				$this->_field_obj = new SF_Fields();
				$this->_field_obj->init( $this->get_settings_template() );
				$this->_field_obj->__set( 'token', 'sf' );
			}

			// If a super user is specified, apply the filter.
			add_filter( 'sf_super_user', array( $this, 'maybe_apply_super_user' ) );
		} // End If Statement
	} // End init_admin()

	/**
	 * Initialise the plugin frontend.
	 * @access public
	 * @since  1.0.1
	 * @return void
	 */
	public function init_frontend () {
		if ( !is_admin() ) {
			// Maybe disable the generator tag.
			if ( 'true' == get_option( 'framework_sf_disable_generator', 'false' ) ) {
				add_filter( 'sf_disable_generator_tags', '__return_true' );
			}
		} // End If Statement
	} // End init_frontend()

	/**
	 * Register the screen ID with the Framework's asset loader.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_screen_id ( $screens ) {
		if ( ! in_array( 'sf-tweaks', $screens ) ) {
			$screens[] = 'sf-tweaks';
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
		// Make sure only super users and the last editor are allowed in.
		if ( ! $this->can_user_view_tweaks() ) return;

		$this->admin_parent_page = 'themes.php';
		if ( defined( 'THEME_FRAMEWORK' ) && 'sf' == constant( 'THEME_FRAMEWORK' ) ) {
			$this->admin_parent_page = 'sf';
		}

		$this->admin_page = add_submenu_page( $this->admin_parent_page, __( 'Tweaks', 'framework-tweaks' ), __( 'Tweaks', 'framework-tweaks' ), 'manage_options', 'sf-tweaks', array( $this, 'admin_screen' ) );

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
	<div class="wrap framework-tweaks-wrap">
<?php
		// If a WooThemes theme isn't activated, display a notice.
		if ( ! defined( 'THEME_FRAMEWORK' ) || 'sf' != constant( 'THEME_FRAMEWORK' ) ) {
			echo '<div class="error fade"><p>' . __( 'It appears your theme does not contain the Framework. In order to use the Framework Tweaks, please use a theme which makes use of the Framework.', 'framework-tweaks' ) . '</p></div>' . "\n";
		} else {
			// If this is an old version of the Framework, display a notice.
			if ( ! class_exists( 'SF_Fields' ) ) {
				echo '<div class="error fade"><p>' . __( 'It appears you\'re using an older version of the Framework. Framework Tweaks requires Framework 6.0 or higher.', 'framework-tweaks' ) . '</p></div>' . "\n";
			} else {
				// Otherwise, we're good to go!
				$hidden_fields = array( 'page' => 'sf-tweaks' );
				do_action( 'sf_screen_get_header', 'sf-tweaks', 'themes' );
				$this->_field_obj->__set( 'has_tabs', false );
				$this->_field_obj->__set( 'extra_hidden_fields', $hidden_fields );
				$this->_field_obj->render();
				do_action( 'sf_screen_get_footer', 'sf-tweaks', 'themes' );
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

		if ( isset( $_GET['page'] ) && 'sf-tweaks' == $_GET['page'] && isset( $_GET['updated'] ) && 'true' == $_GET['updated'] ) {
			$notices['settings-updated'] = array( 'type' => 'updated', 'message' => __( 'Settings saved.', 'framework-tweaks' ) );
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
			  '<p>' . __( 'Configure the tweaks and hit the "Save Changes" button. It\'s as easy as that!', 'framework-tweaks' ) . '</p>' .
			  '<p><strong>' . __( 'For more information:', 'framework-tweaks' ) . '</strong></p>' .
			  '<p>' . sprintf( __( '<a href="%s" target="_blank">WooThemes Help Desk</a>', 'framework-tweaks' ), 'http://support.woothemes.com/' ) . '</p>';

		$screen->add_help_tab( array( 'id' => 'framework_tweaks_overview', 'title' => __( 'Overview', 'framework-tweaks' ), 'content' => $overview ) );
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

			$page = 'sf-tweaks';
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

			// Keep track of the last username to edit the tweaks screen, so as least one user is never locked out. :)
			$user_id = get_current_user_id();
			update_option( 'framework_sf_last_tweaks_editor', intval( $user_id ) );

			// Redirect on settings save, and exit.
			$url = add_query_arg( 'page', $page );
			$url = add_query_arg( 'updated', 'true', $url );

			wp_safe_redirect( esc_url( $url ) );
			exit;
		}
	} // End admin_screen_logic()

	/**
	 * If our super user is removed from the database, clear out the super user entry.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_clean_superuser_entry ( $user_id ) {
		$user = get_userdata( (int) $user_id );
		if ( is_a( $user, 'WP_User' ) && isset( $user->user_login ) ) {
			if ( $user->user_login == get_option( 'framework_sf_super_user', '' ) ) {
				update_option( 'framework_sf_super_user', '' );
			}
		}
	} // End maybe_clean_superuser_entry()

	/**
	 * Maybe apply the super user to the filter.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_apply_super_user ( $username ) {
		$super_username = get_option( 'framework_sf_super_user', '' );
		if ( '' == $super_username ) return $username;

		$user = get_user_by( 'login', $super_username );
		if ( is_a( $user, 'WP_User' ) && isset( $user->user_login ) ) {
			$username = $user->user_login;
		}
		return $username;
	} // End maybe_apply_super_user()

	/**
	 * Maybe override the placeholder image URL.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_override_placeholder_image_url ( $url ) {
		$placeholder_image_url = get_option( 'framework_sf_default_image', '' );
		if ( '' != $placeholder_image_url ) {
			$url = esc_url( $placeholder_image_url );
		}
		return $url;
	} // End maybe_override_placeholder_image_url()

	/**
	 * Maybe override the placeholder image path.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_override_placeholder_image_path ( $path ) {
		$placeholder_image_id = get_option( 'framework_sf_default_image-id', 0 );
		if ( 0 < intval( $placeholder_image_id ) ) {
			$file_path = get_attached_file( $placeholder_image_id );
			if ( '' != $file_path ) {
				$path = $file_path;
			}
		}
		return $path;
	} // End maybe_override_placeholder_image_path()

	/**
	 * Determine if the current user can view the tweaks admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function can_user_view_tweaks () {
		$response = false;
		$user_id = get_current_user_id();
		$super_username = get_option( 'framework_sf_super_user', '' );
		if ( '' == $super_username ) {
			$response = true;
		} else {
			if ( $user_id == (int) get_option( 'framework_sf_last_tweaks_editor', 0 ) ) {
				$response = true;
			}
			$super_user = get_user_by( 'login', $super_username );
			$current_user = get_userdata( $user_id );
			if ( is_a( $super_user, 'WP_User' ) && isset( $super_user->ID ) && is_a( $current_user, 'WP_User' ) && isset( $current_user->ID ) ) {
				if ( $super_user->ID == $current_user->ID ) {
					$response = true;
				}
			}
		}
		return $response;
	} // End can_user_view_tweaks()

	/**
	 * Return an array of the settings scafolding. The field types, names, etc.
	 * @access  public
	 * @since   1.0.0
	 * @return  array
	 */
	public function get_settings_template () {
		return array(
				// We must have a heading, so the fields can be assigned a section, and display correctly. :)
				'sf_tweaks_heading' => array(
										'name' => __( 'Tweaks', 'framework-tweaks' ),
										'std' => '',
										'id' => 'sf_tweaks_heading',
										'type' => 'heading'
										),
				'framework_sf_default_image' => array(
										'name' => __( 'Placeholder Image', 'framework-tweaks' ),
										'desc' => __( 'Specify a placeholder image to use within the sf_image() function.', 'framework-tweaks' ),
										'std' => '',
										'id' => 'framework_sf_default_image',
										'type' => 'upload'
										),
				'framework_sf_super_user' => array(
										'name' => __( 'Super User', 'framework-tweaks' ),
										'desc' => __( 'Enter your username to hide the "Framework" screen and features from other administrators.', 'framework-tweaks' ) . '<br />' . sprintf( __( 'This can be reset from the %s under %s.', 'framework-tweaks' ), '<a href="' . admin_url( 'options.php' ) . '">' . __( 'WordPress Options Screen', 'framework-tweaks' ) . '</a>', '<code>framework_sf_super_user</code>' ),
										'std' => '',
										'id' => 'framework_sf_super_user',
										'type' => 'text'
										),
				'framework_sf_disable_generator' => array(
										'desc' => __( 'Disable the "Generator" META tags', 'framework-tweaks' ) . '<p class="description">' . __( "Removes the meta tags which show the current theme and Framework version in your site's source code.", 'framework-tweaks' ) . '</p>',
										'std' => '',
										'id' => 'framework_sf_disable_generator',
										'type' => 'checkbox'
										),
				'framework_sf_disable_shortcodes' => array(
										'desc' => __( 'Disable the shortcodes stylesheet', 'framework-tweaks' ) . '<p class="description">' . __( "Removes the CSS styles for all WooThemes theme shortcodes.", 'framework-tweaks' ) . '</p>',
										'std' => '',
										'id' => 'framework_sf_disable_shortcodes',
										'type' => 'checkbox'
										),
				'framework_sf_move_tracking_code' => array(
										'desc' => __( 'Output the Tracking Code setting in the Header', 'framework-tweaks' ) . '<p class="description">' . __( "Moves the output of your theme's 'Tracking Code' setting from the footer to the header.", 'framework-tweaks' ) . '</p>',
										'std' => '',
										'id' => 'framework_sf_move_tracking_code',
										'type' => 'checkbox'
										)
				);
	} // End get_settings_template()

	/**
	 * Main Tweaks Instance
	 *
	 * Ensures only one instance of Tweaks is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Tweaks()
	 * @return Main Tweaks instance
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
		load_plugin_textdomain( 'framework-tweaks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
								'framework_sf_disable_generator',
								'framework_sf_default_image',
								'framework_sf_default_image-id',
								'framework_sf_super_user',
								'framework_sf_last_tweaks_editor',
								);
		foreach ( $option_keys as $key ) {
			$data .= " OR option_name = '" . $key . "'";
		} // End For Loop
		return $data;
	} // End add_exporter_data()
} // End Class
?>