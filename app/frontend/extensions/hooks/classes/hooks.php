<?php
/**
 * Hooks
 *
 * This is the hooks class, containing all processing and setup functionality
 * for inputting custom content at the various Framework hook calls.
 *
 * @package Framework
 * @subpackage Module
 *
 *
 * CLASS INFORMATION
 *
 * Date Created: 2011-04-19.
 * Last Modified: 2013-06-26.
 * Author: Matty.
 * Since: 4.0.0
 *
 *
 * TABLE OF CONTENTS
 *
 * - public $plugin_prefix
 * - public $plugin_path
 * - public $plugin_url
 * - public $version
 *
 * - public $admin_page
 * - public $hooks
 * - public $hook_titles
 *
 * - public $stored_data
 *
 * - function Hook_Manager () (Constructor)
 * - function init ()
 * - function _generate_sections_menu ()
 * - function _generate_sections_html ()
 * - function register_admin_screen ()
 * - function admin_screen ()
 * - function admin_screen_help ()
 * - function enqueue_scripts ()
 * - function enqueue_styles ()
 * - function create_hooks ()
 * - function execute_hook ()
 * - function setup_hook_data ()
 * - function setup_hook_titles ()
 * - function add_exporter_data ()
 *
 */
class Hook_Manager {
	public $plugin_prefix;
	public $plugin_path;
	public $plugin_url;
	public $version;

	public $admin_page;
	public $hooks;
	public $hook_titles;

	public $stored_data;

	/**
	 * Class Constructor.
	 * @access  public
	 * @since	1.0
	 * @param   string $plugin_prefix Prefix to use in this class.
	 * @param   string $plugin_path   The path to this plugin.
	 * @param   string $plugin_url    The URL to this plugin.
	 * @param   string $version       Version number.
	 */
	public function __construct ( $plugin_prefix, $plugin_path, $plugin_url, $version ) {
		$this->plugin_prefix = $plugin_prefix;
		$this->plugin_path = $plugin_path;
		$this->plugin_url = $plugin_url;
		$this->version = $version;

		$this->init();
	} // End Constructor

	/**
	 * Initialise the plugin.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function init () {
		// Create the necessary filters.
		add_action( 'after_setup_theme', array( $this, 'create_hooks' ), 10 );

		if ( is_admin() ) {
			// Setup hook areas.
			$this->setup_hook_data();

			// Register the admin screen.
			add_action( 'admin_menu', array( $this, 'register_admin_screen' ), 20 );

			// Make sure our data is added to the Framework settings exporter.
			add_filter( 'sf_export_query_inner', array( $this, 'add_exporter_data' ) );

			// Register the admin screen to be able to load the Framework's CSS and other assets.
			add_filter( 'sf_load_admin_css', array( $this, 'register_screen_id' ) );
		}
	} // End init()

	/**
	 * Register the screen ID with the Framework's asset loader.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function register_screen_id ( $screens ) {
		if ( ! in_array( 'sf-hook-manager', $screens ) ) {
			$screens[] = 'sf-hook-manager';
		}
		return $screens;
	} // End register_screen_id()

	/**
	 * Generate an unordered list of links to each section.
	 * @access  private
	 * @since	1.0
	 * @return  string Rendered menu HTML.
	 */
	private function _generate_sections_menu () {
		$html = '';
		$count = 0;

		if ( 1 < count( array_keys( $this->hooks ) ) ) {
			$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
			foreach ( array_keys( $this->hooks ) as $k ) {
				$count++;
				if ( in_array( $k, array_keys( $this->hook_titles ) ) ) {
					$title = $this->hook_titles[$k];
				} else {
					$title = ucwords( str_replace( '_', ' ', $k ) );
				}

				$css_class = $k . ' general';

				if ( $count == 1 ) { $css_class .= ' current'; }

				$html .= '<li><a href="#' . esc_attr( $k ) . '" class="tab ' . esc_attr( $css_class ) . '">' . $title . '</a>';
				if ( 1 < count( array_keys( $this->hooks ) ) && $count < count( array_keys( $this->hooks ) ) ) { $html .= ' | '; }
				$html .= '</li>' . "\n";
			}
			$html .= '</ul><div class="clear"></div>' . "\n";
		}

		echo $html;
	} // End _generate_sections_menu()

	/**
	 * Generate the HTML for the various sections.
	 * @access  private
	 * @since	1.0
	 * @return  string Rendered HTML.
	 */
	private function _generate_sections_html () {
		$html = '';

		if ( 0 < count( $this->hooks ) ) {
			foreach ( $this->hooks as $k => $v ) {
				if ( in_array( $k, array_keys( $this->hook_titles ) ) ) {
					$title = $this->hook_titles[$k];
				} else {
					$title = ucwords( str_replace( '_', ' ', $k ) );
				}

				$html .= '<div id="' . $k . '" class="content-section">' . "\n";
					$html .= '<h3 class="title">' . ucwords( $title ) . '</h3>' . "\n";
					if ( 0 < count( $v ) ) {
						$html .= '<table class="form-table">' . "\n";
						foreach ( $v as $i => $j ) {
							$html .= '<tr>' . "\n";
								$html .= '<th scope="row">' . $j['description'] . '</th>' . "\n";
								$html .= '<td>' . "\n";
									$html .= '<fieldset><legend class="screen-reader-text"><span>' . $j['description'] . '</span></legend>' . "\n";
									$html .= '<textarea id="' . esc_attr( $i ) . '-content" name="' . esc_attr( $i ) . '[content]" rows="5" cols="50" class="large-text code">' . stripslashes( $j['content'] ) . '</textarea>' . "\n";
									$html .= '<label for="' . esc_attr( $i ) . '[shortcodes]"><input id="' . esc_attr( $i ) . '-shortcodes" name="' . esc_attr( $i ) . '[shortcodes]" type="checkbox" value="1"' . checked( $j['shortcodes'], 1, false ) . ' /> ' . __( 'Execute Shortcodes on this Hook', 'sfwp-locale' ) . '</label>' . "\n";
									$html .= '</fieldset>' . "\n";
								$html .= '</td>' . "\n";
							$html .= '</tr>' . "\n";
						}
						$html .= '</table>' . "\n";
					}
				$html .= '</div>' . "\n";
			}
		}

		echo $html;
	} // End _generate_sections_html()

	/**
	 * Register the admin screen within WordPress.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function register_admin_screen () {
		if ( function_exists( 'add_submenu_page' ) ) {
			$this->admin_page = add_submenu_page('sf', __( 'Hooks', 'sfwp-locale' ), __( 'Hooks', 'sfwp-locale' ), 'manage_options', 'sf-hook-manager', array( &$this, 'admin_screen' ) );

			// Admin screen logic.
			add_action( 'load-' . $this->admin_page, array( $this, 'admin_screen_logic' ) );

			// Add contextual help tabs.
			add_action( 'load-' . $this->admin_page, array( $this, 'admin_screen_help' ) );

			// Add JavaScripts.
			add_action( 'load-' . $this->admin_page, array( $this, 'enqueue_scripts' ) );
		}
	} // End register_admin_screen()

	/**
	 * Load the admin screen markup.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function admin_screen () {
		do_action( 'sf_screen_get_header', 'sf-hook-manager', 'themes' );

		// Keep the screen XHTML separate and load it from that file.
		include_once( $this->plugin_path . '/screens/admin.php' );

		do_action( 'sf_screen_get_footer', 'sf-hook-manager', 'themes' );
	} // End admin_screen()

	/**
	 * Load contextual help for the admin screen.
	 * @access  public
	 * @since	1.0
	 * @return  string Modified contextual help string.
	 */
	public function admin_screen_help () {
		$screen = get_current_screen();
		if ( $screen->id != $this->admin_page ) return;

		$overview =
			  '<p>' . __( 'Fill in the area you\'d like to customise and hit the "Save Changes" button. It\'s as easy as that!', 'sfwp-locale' ) . '</p>' .
			  '<p><strong>' . __( 'For more information:', 'sfwp-locale' ) . '</strong></p>' .
			  '<p>' . sprintf( __( '<a href="%s" target="_blank">Help Desk</a>', 'sfwp-locale' ), 'http://support.woothemes.com/' ) . '</p>';

		$screen->add_help_tab( array( 'id' => 'filters_overview', 'title' => __( 'Overview', 'sfwp-locale' ), 'content' => $overview ) );
	} // End admin_screen_help()

	/**
	 * Logic to run on the admin screen.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function admin_screen_logic () {
		$is_processed = false;

		// Save logic.
		if ( isset( $_POST['submit'] ) && check_admin_referer( 'hooks-options-update' ) ) {
			$fields_to_skip = array( 'submit', '_wp_http_referer', '_wpnonce' );

			$posted_data = $_POST;

			foreach ( $posted_data as $k => $v ) {
				// Remove the fields we want to skip.
				if ( in_array( $k, $fields_to_skip ) ) {
					unset( $posted_data[$k] );
				} else {
					// Make sure the "shortcodes" and "php" keys are available, even if not posted.
					if ( ! array_key_exists( 'shortcodes', $v ) ) { $posted_data[$k]['shortcodes'] = 0; }
					if ( ! array_key_exists( 'php', $v ) ) { $posted_data[$k]['php'] = 0; }
				}
			}

			if ( is_array( $posted_data ) ) {
				$is_updated = update_option( $this->plugin_prefix . 'stored_hooks', $posted_data );

				// Redirect to make sure the latest changes are reflected.
				wp_safe_redirect( admin_url( 'admin.php?page=sf-hook-manager&updated=true' ) );
				exit;
			}
			$is_processed = true;
		}
	} // End admin_screen_logic()

	/**
	 * Enqueue scripts for the admin screen.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'sf-hooks-tabs-navigation', $this->plugin_url . 'assets/js/tabs-navigation' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );
		//wp_register_script( 'sf-hooks-tabs-navigation', $this->plugin_url . 'assets/js/tabs-navigation.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'sf-hooks-tabs-navigation' );
	} // End enqueue_scripts()

	/**
	 * Create the hooks using the saved content.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function create_hooks () {
		if ( ! is_admin() ) {
			$stored_hooks = get_option( $this->plugin_prefix . 'stored_hooks' );

			// Create the hooks, using an internal function to create the hook data.
			if ( is_array( $stored_hooks ) ) {

				$this->stored_data = $stored_hooks; // Store this data locally to avoid a second query in $this->execute_hook().

				foreach ( $stored_hooks as $k => $v ) {
					add_action($k, array( $this, 'execute_hook' ) );
				}
			}
		}
	} // End create_hooks()

	/**
	 * Executes the necessary hooks.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function execute_hook () {
		$hook = current_filter();
		$content = $this->stored_data[$hook]['content'];

		if( ! $hook || ! $content ) return;

		// Moved stripslashes here so that the do_shortcode function will accept parameters
		$content = stripslashes( $content );

		// If we are being instructed to execute shortcodes, execute them.
		if ( array_key_exists( 'shortcodes', $this->stored_data[$hook] ) && $this->stored_data[$hook]['shortcodes'] ) {
			$content = do_shortcode( $content );
		}

		echo $content;
	} // End execute_hook()

 	/**
 	 * Sets up the default and saved data for the various hook areas.
 	 * @access  public
 	 * @since	1.0
 	 * @return  void
 	 */
	public function setup_hook_data () {
		// Stored data.
		$stored_values = get_option( $this->plugin_prefix . 'stored_hooks' );

		$this->hooks = array();

		// Header Hooks
		$this->hooks['header'] = array(
								'sf_top' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed below the opening <code>&lt;body&gt;</code> tag.', 'sfwp-locale' )
									),
								'sf_header_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the opening <code>#header</code> DIV tag.', 'sfwp-locale' )
									)
,
								'sf_header_inside' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the top, inside the <code>#header</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_header_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the closing <code>#header</code> DIV tag.', 'sfwp-locale' )
									)
							);

		// Navigation Hooks
		$this->hooks['nav'] = array(
								'sf_nav_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the opening <code>#navigation</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_nav_inside' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the top, inside the <code>#navigation</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_nav_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the closing <code>#navigation</code> DIV tag.', 'sfwp-locale' )
									)
							);

		// Main Content Area Hooks
		$this->hooks['main'] = array(
								'sf_content_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the opening <code>#content</code> DIV tag.', 'sfwp-locale' )
									)
,
								'sf_main_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the opening <code>#main</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_loop_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the WordPress Loop.', 'sfwp-locale' )
									),
								'loop_start' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the start of the WordPress Loop.', 'sfwp-locale' )
									),
								'loop_end' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the end of the WordPress Loop.', 'sfwp-locale' )
									),
								'sf_loop_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the WordPress Loop.', 'sfwp-locale' )
									),
								'sf_main_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the closing <code>#main</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_content_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the closing <code>#content</code> DIV tag.', 'sfwp-locale' )
									)
							);

		// Post Hooks
		$this->hooks['post'] = array(
								'sf_post_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before each post.', 'sfwp-locale' )
									)
,
								'sf_post_inside_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the top, inside each post\'s DIV tag.', 'sfwp-locale' )
									),
								'sf_post_inside_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the bottom, inside each post\'s DIV tag.', 'sfwp-locale' )
									),
								'sf_post_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after each post.', 'sfwp-locale' )
									)
							);
		// Page Hooks
		$this->hooks['page'] = array(
								'sf_post_before_singular-page' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before each single page.', 'sfwp-locale' )
									),
								'sf_post_inside_before_singular-page' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the top, inside each single page\'s DIV tag.', 'sfwp-locale' )
									),
								'sf_post_inside_after_singular-page' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the bottom, inside each single page\'s DIV tag.', 'sfwp-locale' )
									),
								'sf_post_after_singular-page' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after each single page.', 'sfwp-locale' )
									)
							);
		// Sidebar Hooks
		$this->hooks['sidebars'] = array(
								'sf_sidebar_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before each sidebar.', 'sfwp-locale' )
									)
,
								'sf_sidebar_inside_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the top, inside each sidebar\'s DIV tag.', 'sfwp-locale' )
									),
								'sf_sidebar_inside_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the bottom, inside each sidebar\'s DIV tag.', 'sfwp-locale' )
									),
								'sf_sidebar_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after each sidebar.', 'sfwp-locale' )
									)
							);

		// Footer Hooks
		$this->hooks['footer'] = array(
								'sf_footer_top' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the top of the <code>footer.php</code> file.', 'sfwp-locale' )
									),
								'sf_footer_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the opening <code>#footer</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_footer_inside' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed at the top, inside the <code>#footer</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_footer_left_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the opening <code>#footer .col-left</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_footer_left_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the ending <code>#footer .col-left</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_footer_right_before' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the opening <code>#footer .col-right</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_footer_right_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the ending <code>#footer .col-right</code> DIV tag.', 'sfwp-locale' )
									),
								'sf_footer_after' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed after the closing <code>#footer</code> DIV tag.', 'sfwp-locale' )
									)
							);

		// WordPress Native Hooks
		$this->hooks['wordpress'] = array(
								'wp_head' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the closing <code>&lt;/head&gt;</code> tag.', 'sfwp-locale' )
									),
								'wp_footer' => array(
										'content' => '',
										'shortcodes' => 0,
										'php' => '',
										'description' => __( 'Executed before the closing <code>&lt;/body&gt;</code> tag.', 'sfwp-locale' )
									)
							);

		// Allow child themes/plugins to add their own hook sections.
		$this->hooks = apply_filters( 'sf_hook_manager_hooks', $this->hooks );

		// Assigned stored data to the appropriate hook area.
		foreach ( $this->hooks as $id => $arr ) {
			foreach ( $this->hooks[$id] as $k => $v ) {
				if ( is_array( $stored_values ) && array_key_exists( $k, $stored_values ) ) {
					if ( is_array( $stored_values[$k] ) ) {
						foreach ( $stored_values[$k] as $i => $j ) {
							$this->hooks[$id][$k][$i] = $j;
						}
					}
				}
			}
		}

		// Setup custom titles for certain hooks sections.
		$this->_setup_hook_titles();
	} // End setup_hook_data()

 	/**
 	 * Setup custom titles for certain sections.
 	 * @access  private
 	 * @since	1.0
 	 * @return  void
 	 */
	private function _setup_hook_titles () {
		$this->hook_titles = array();
		$this->hook_titles['nav'] = __( 'Navigation', 'sfwp-locale' );
		$this->hook_titles['wordpress'] = __( 'WordPress', 'sfwp-locale' );
	} // End _setup_hook_titles()

	/**
 	 * Add our saved data to the Framework data exporter.
 	 * @access  public
	 * @since	1.0
 	 * @param   string $data SQL query.
 	 * @return  string SQL query.
 	 */
	public function add_exporter_data ( $data ) {
		$data .= " OR option_name = '" . $this->plugin_prefix . "stored_hooks" . "'";
		return $data;
	} // End add_exporter_data()
} // End Class
?>