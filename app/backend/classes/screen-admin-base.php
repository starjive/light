<?php
/**
 * Generic Admin Info Screen Base Class
 *
 * Shows a tools screen.
 *
 * @category	Admin
 * @version		1.0
 * @since		1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SF_Screen_Admin_Base {
	/**
	 * Array of data for the current theme.
	 * @access  protected
	 * @var     array
	 * @since	1.0
	 */
	protected $_theme_data = array();

	/**
	 * Instance of the WP_Theme class.
	 * @access  protected
	 * @var     StdClass
	 * @since	1.0
	 */
	protected $_theme_obj = null;

	/**
	 * Class constructor.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function __construct () {
		$this->set_theme_data();

		add_action( 'admin_init', array( $this, 'maybe_load_plugin_install_override' ) );
	} // End __construct()

	/**
	 * Set the theme data into a local property.
	 * @access  protected
	 * @since	1.0
	 * @return  void
	 */
	protected function set_theme_data () {
		$this->_theme_data = sf_get_theme_version_data();
		$this->_theme_obj  = wp_get_theme();
	} // End set_theme_data()

	/**
	 * Enqueue CSS styles.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function admin_css () {
		$stylesheet_url = get_template_directory_uri() . '/app/backend/assets/css/activation.css';
		wp_enqueue_style( 'sf-activation', $stylesheet_url, array(), '6.0.0', 'all' );
	} // End admin_css()

	/**
	 * Conditionally load the Helper plugin install override, and remove the override used by plugins.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function maybe_load_plugin_install_override () {
		// If the Helper isn't installed, add a filter to ensure we can install it.
		if ( ! $this->_is_sf_helper_installed() ) {
			add_filter( 'plugins_api', array( $this, 'sf_helper_install_info' ), 10, 3 );
		}
	} // End maybe_load_plugin_install_override()

	/**
	 * Remove the plugin overrides added by other plugins.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function maybe_remove_existing_plugin_install_overrides () {
		// This is the filter added by plugins. Remove it, so we only have a single trigger point (from the theme).
		remove_filter( 'plugins_api', 'sf_updater_install', 10, 3 );

		// This is the action added by plugins. Remove it, so we only have a single trigger point (from the theme).
		remove_action( 'admin_notices', 'sf_updater_notice', 10 );
	} // End maybe_remove_existing_plugin_install_overrides()

	/**
	 * Filter the download data for the Helper, within the WordPress plugin installation API.
	 * @access  public
	 * @since	1.0
	 * @param   object $api
	 * @param   string $action
	 * @param   object $args
	 * @return  object
	 */
	public function sf_helper_install_info ( $api, $action, $args ) {
		$download_url = 'http://amazonaws.com/downloads/sf-updater/sf-updater.zip';
		if ( 'plugin_information' != $action ||
			false !== $api ||
			! isset( $args->slug ) ||
			'sf-updater' != $args->slug
		) return $api;

		$api = new stdClass();
		$api->name = 'Helper';
		$api->version = '';
		$api->download_link = esc_url( $download_url );
		return $api;
	} // sf_helper_install_info()

	/**
	 * Check if the Helper is activated.
	 * @access  protected
	 * @since	1.0
	 * @return  boolean
	 */
	protected function _is_sf_helper_activated () {
		$response = false;
		$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
		if ( 0 < count( $active_plugins ) && in_array( 'sf-updater/sf-updater.php', $active_plugins ) ) $response = true;
		return $response;
	} // End _is_sf_helper_activated()

	/**
	 * Check if the Helper is installed.
	 * @access  protected
	 * @since	1.0
	 * @return  boolean
	 */
	protected function _is_sf_helper_installed () {
		$response = false;
		$plugins = get_plugins();
		if ( 0 < count( $plugins ) && in_array( 'sf-updater/sf-updater.php', array_keys( $plugins ) ) ) $response = true;
		return $response;
	} // End _is_sf_helper_installed()

	/**
	 * Checks for supported plugins for the "Getting Started" screen.
	 * @access  protected
	 * @since	1.0
	 * @return  void
	 */
	protected function _has_supported_plugins () {
		$all_plugins = get_plugins();
		$to_check = $this->_get_plugins_to_look_for();
		$ct = $this->_theme_obj;
		if ( 0 < count( $to_check ) ) {
			$html = '';
			$i = 1;
			foreach ( $to_check as $k => $v ) {
				if ( current_theme_supports( dirname( $k ) ) || ( isset( $v['global'] ) && false != $v['global'] ) ) {
						$url 		= '';
						$title 		= '';
						if ( $i % 2 == 0 ) {
				            $class 	= 'alternate';
						}
				        else {
				            $class 	= '';
				        }
						if ( ! in_array( $k, array_keys( $all_plugins ) ) ) {
							$slug 	= dirname( $k );
							$url 	= str_replace( '&amp;', '&', wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug ) );
							$title 	= __( 'Download', 'sfwp-locale' );
						} else {
							if ( ! is_plugin_active( $k ) ) {
								$url 	= wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $k, 'activate-plugin_' . $k );
								$title 	= __( 'Activate', 'sfwp-locale' );
							} else {
								$url 	= '';
								$title 	= '<span class="active">' . __( 'Activated', 'sfwp-locale' ) . '</span>';
							}
						}

						$actions = $title;
						if ( '' != $url ) {
							$actions = '<a href="' . esc_url( $url ) . '">' . $actions . '</a>' . "\n";
						}
						$html .= '<tr class="' . $class . '" id="' . esc_attr( dirname( $k ) ) . '">' . "\n";
						$html .= '<td class="plugin-name"><p><strong>' . $v['name'] . '</strong></p>' . '<small>' . $v['description'] . '</small>' . '</td>' . "\n";
						$html .= '<td class="actions"><p>' . $actions . '</p></td>' . "\n";
						$html .= '</tr>' . "\n";
				}
				$i++;
			}
			if ( ! empty( $html ) ) {
				$intro_text 	= '<h2>'  . sprintf( __( 'Extend %1$s %2$s', 'sfwp-locale' ), $ct->__get( 'Name' ), '<div class="dashicons dashicons-admin-plugins"></div>' ) .  '</h2>' . '<p>' . sprintf( __( '%1$s offers support for several plugins allowing you add advanced functionality at the click of a button.', 'sfwp-locale' ), $ct->__get( 'Name' ) ) . '</p>' . '<p>' . __( 'To install or activate them, use the actions below.', 'sfwp-locale' ) . "\n";
				$headings 		= '<thead><tr><th scope="col" id="name" class="manage-column column-name" colspan="2">' . __( 'Plugin', 'sfwp-locale' ) . '</th></tr></thead>';
				$html 			= $intro_text . '<table class="wp-list-table widefat sf-plugins" cellspacing="0">' . $headings . '<tbody id="the-list">' . $html . '</tbody></table>';

				echo $html;
			}
		}
	} // End _has_supported_plugins()

	/**
	 * Checks for supported plugins for the "Getting Started" screen, when on a multisite.
	 * @access  protected
	 * @since	1.0
	 * @return  void
	 */
	protected function _has_supported_plugins_multisite () {
		$all_plugins = get_plugins();
		$to_check = $this->_get_plugins_to_look_for();
		$ct = $this->_theme_obj;
		if ( 0 < count( $to_check ) ) {
			$html = '';
			$i = 1;
			foreach ( $to_check as $k => $v ) {
				if ( current_theme_supports( dirname( $k ) ) || ( isset( $v['global'] ) && false != $v['global'] ) ) {
						$url 		= '';
						$title 		= '';
						if ( $i % 2 == 0 ) {
				            $class 	= 'alternate';
						}
				        else {
				            $class 	= '';
				        }
						if ( ! in_array( $k, array_keys( $all_plugins ) ) ) {
							$url 	= '';
							$title 	= __( 'Not Active', 'sfwp-locale' );
						} else {
							if ( ! is_plugin_active( $k ) ) {
								$url 	= wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $k, 'activate-plugin_' . $k );
								$title 	= __( 'Activate', 'sfwp-locale' );
							} else {
								$url 	= '';
								$title 	= '<span class="active">' . __( 'Activated', 'sfwp-locale' ) . '</span>';
							}
						}

						$actions = $title;
						if ( '' != $url ) {
							$actions = '<a href="' . esc_url( $url ) . '" class="button">' . $actions . '</a>' . "\n";
						}
						$html .= '<tr class="' . $class . '" id="' . esc_attr( dirname( $k ) ) . '">' . "\n";
						$html .= '<td class="plugin-name"><strong>' . $v['name'] . '</strong>' . $v['description'] . '</td>' . "\n";
						$html .= '<td class="actions"><p>' . $actions . '</p></td>' . "\n";
						$html .= '</tr>' . "\n";
				}
				$i++;
			}
			if ( ! empty( $html ) ) {
				$intro_text 	= '<h2>'  . sprintf( __( 'Extend %1$s %2$s', 'sfwp-locale' ), $ct->__get( 'Name' ), '<div class="dashicons dashicons-admin-plugins"></div>' ) .  '</h2>' . '<p>' . sprintf( __( '%1$s offers support for several plugins allowing you add advanced functionality at the click of a button.', 'sfwp-locale' ), $ct->__get( 'Name' ) ) . '</p>' . '<p>' . __( 'To install or activate them, use the actions below.', 'sfwp-locale' ) . "\n";
				$headings 		= '<thead><tr><th scope="col" id="name" class="manage-column column-name" colspan="2">' . __( 'Plugin', 'sfwp-locale' ) . '</th></tr></thead>';
				$html 			= $intro_text . '<table class="wp-list-table widefat sf-plugins" cellspacing="0">' . $headings . '<tbody id="the-list">' . $html . '</tbody></table>';

				echo $html;
			}
		}
	} // End _has_supported_plugins_multisite()

	/**
	 * Return an array of plugins to look for on the current installation.
	 * @access  protected
	 * @since	1.0
	 * @return  array key => value pairs of the plugin slug => name.
	 */
	protected function _get_plugins_to_look_for () {
		return (array)apply_filters( 'sf_get_plugins_to_look_for', array(
					'woocommerce/woocommerce.php' => array( 'name' => __( 'WooCommerce', 'sfwp-locale' ), 'description' => __( 'An ecommerce toolkit to help you sell anything. Beautifully.', 'sfwp-locale' ), 'global' => false ),
					//'testimonials-by-woothemes/sf-testimonials.php' => array( 'name' => __( 'Testimonials', 'sfwp-locale' ), 'description' => __( 'Share customer testimonials with your visitors.', 'sfwp-locale' ), 'global' => false ),
					//'features-by-woothemes/sf-features.php' => array( 'name' => __( 'Features', 'sfwp-locale' ), 'description' => __( 'Display the features or services you offer.', 'sfwp-locale' ), 'global' => false ),
					//'our-team-by-woothemes/sf-our-team.php' => array( 'name' => __( 'Our Team', 'sfwp-locale' ), 'description' => __( 'Showcase your team members and the roles they play.', 'sfwp-locale' ), 'global' => false ),
					'projects-by-woothemes/projects.php' => array( 'name' => __( 'Projects', 'sfwp-locale' ), 'description' => __( 'Showcase your recent projects.', 'sfwp-locale' ), 'global' => false )
				) );
	} // End _get_plugins_to_look_for()
} // End Class
?>