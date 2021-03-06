<?php
/**
 * Filters
 *
 * This is the filters class, containing all processing and setup functionality
 * for managing the metadata above and below blog post content.
 *
 * @package Framework
 * @subpackage Module
 *
 * CLASS INFORMATION
 *
 * Date Created: 2011-03-21.
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
 * - public $meta_areas
 *
 * - public $shortcodes
 *
 * - function __construct()
 * - function init()
 * - function _generate_sections_menu()
 * - function _generate_sections_html()
 * - function register_admin_screen()
 * - function admin_screen()
 * - function admin_screen_help()
 * - function _get_shortcode_reference()
 * - function admin_screen_logic()
 * - function enqueue_scripts()
 * - function create_filters()
 * - function setup_shortcodes()
 * - function add_exporter_data()
 */
class Meta_Manager {
	public $plugin_prefix;
	public $plugin_path;
	public $plugin_url;
	public $version;

	public $admin_page;
	public $meta_areas;

	public $shortcodes;

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
	} // End __construct()

	/**
	 * Initialise the plugin.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function init () {
		if ( is_admin() ) {
			// Register the admin screen.
			add_action( 'admin_menu', array( $this, 'register_admin_screen' ), 20 );

			// Execute certain code only on the specific admin screen.
			if ( is_admin( $this->admin_page ) ) {
				// Setup default shortcodes for reference.
				$this->setup_shortcodes();

				// Make sure our data is added to the Framework settings exporter.
				add_filter( 'sf_export_query_inner', array( $this, 'add_exporter_data' ) );
			}

			// Register the admin screen to be able to load the Framework's CSS and other assets.
			add_filter( 'sf_load_admin_css', array( $this, 'register_screen_id' ) );
		} // End IF Statement

		// Setup meta areas.
		$this->meta_areas = array();

		$this->meta_areas['post_meta'] = array(
								'sf_filter_post_meta' => array(
										'title' => __( 'Above post content', 'sfwp-locale' ),
										'default' => '<span class="small">' . __( 'By', 'sfwp-locale' ) . '</span> [post_author_posts_link] <span class="small">' . __( 'on', 'sfwp-locale' ) . '</span> [post_date] <span class="small">' . __( 'in', 'sfwp-locale' ) . '</span> [post_categories before=""] ' . '[post_comments]',
										'stored_value' => '',
										'description' => __( 'Data above the content of your blog posts.', 'sfwp-locale' )
									),
								'sf_post_more' => array(
										'title' => __( '"Read more" area below posts', 'sfwp-locale' ),
										'default' => '[view_full_article] [post_edit]',
										'stored_value' => '',
										'description' => __( 'Data below each blog post.', 'sfwp-locale' )
									)
							);

		// Allow child themes/plugins to filter here.
		$this->meta_areas = (array)apply_filters( 'sf_meta_manager_meta_areas', $this->meta_areas );

		// Stored data.
		$stored_values = get_option( $this->plugin_prefix . 'stored_meta' );

		// Assigned stored data to the appropriate meta_area.
		foreach ( $this->meta_areas as $id => $arr ) {
			foreach ( $this->meta_areas[$id] as $k => $v ) {
				if ( is_array( $stored_values ) && array_key_exists( $k, $stored_values ) ) {
					$this->meta_areas[$id][$k]['stored_value'] = $stored_values[$k];
				} else {
					$this->meta_areas[$id][$k]['stored_value'] = $this->meta_areas[$id][$k]['default'];
				}
			}
		}


		// Create the necessary filters.
		add_action( 'after_setup_theme', array( $this, 'create_filters' ), 10 );
	} // End init()

	/**
	 * Register the screen ID with the Framework's asset loader.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function register_screen_id ( $screens ) {
		if ( ! in_array( 'sf-meta-manager', $screens ) ) {
			$screens[] = 'sf-meta-manager';
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

		if ( 1 < count( array_keys( $this->meta_areas ) ) ) {
			$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
			foreach ( array_keys( $this->meta_areas ) as $k ) {
				$count++;
				$title = str_replace( '_', ' ', $k );

				$css_class = $k . ' general';

				if ( $count == 1 ) { $css_class .= ' current'; }

				$html .= '<li><a href="#' . esc_attr( $k ) . '" class="tab ' . esc_attr( $css_class ) . '">' . ucwords( $title ) . '</a>';
				if ( 1 < count( array_keys( $this->meta_areas ) ) && $count < count( array_keys( $this->meta_areas ) ) ) { $html .= ' | '; }
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

		if ( 0 < count( $this->meta_areas ) ) {
			foreach ( $this->meta_areas as $k => $v ) {

				$title = str_replace( '_', ' ', $k );

				$html .= '<div id="' . $k . '" class="content-section">' . "\n";
					$html .= '<h3 class="title">' . ucwords( $title ) . '</h3>' . "\n";
					if ( 0 < count( $v ) ) {
						$html .= '<table class="form-table">' . "\n";
						foreach ( $v as $i => $j ) {
							$html .= '<tr>' . "\n";
								$html .= '<th scope="row">' . $j['title'] . '</th>' . "\n";
								$html .= '<td>' . "\n";
									$html .= '<fieldset><legend class="screen-reader-text"><span>' . $j['title'] . '</span></legend>' . "\n";
									$html .= '<textarea id="' . esc_attr( $i ) . '" name="' . esc_attr( $i ) . '" rows="10" cols="50" class="large-text code">' . stripslashes( $j['stored_value'] ) . '</textarea>' . "\n";
									$html .= '</fieldset>' . "\n";
									if ( isset( $j['description'] ) ) {
										$html .= '<p class="description">' . "\n";
											$html .= $j['description'] . "\n";
										$html .= '</p><!--/.description-->' . "\n";
									}
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
			$this->admin_page = add_submenu_page( 'sf', __( 'Filters', 'sfwp-locale' ), __( 'Filters', 'sfwp-locale' ), 'manage_options', 'sf-meta-manager', array( $this, 'admin_screen' ) );

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
		do_action( 'sf_screen_get_header', 'sf-meta-manager', 'themes' );

		// Keep the screen XHTML separate and load it from that file.
		include_once( $this->plugin_path . '/screens/admin.php' );

		do_action( 'sf_screen_get_footer', 'sf-meta-manager', 'themes' );
	} // End admin_screen()

	/**
	 * Enqueue scripts for the admin screen.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'sf-hooks-tabs-navigation', $this->plugin_url . 'assets/js/tabs-navigation' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );
		//wp_register_script( 'sf-filters-tabs-navigation', $this->plugin_url . 'assets/js/tabs-navigation.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'sf-filters-tabs-navigation' );
	} // End enqueue_scripts()

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
		$screen->add_help_tab( array( 'id' => 'filters_shortcode_reference', 'title' => __( 'Shortcode Reference', 'sfwp-locale' ), 'content' => $this->_get_shortcode_reference() ) );
	} // End admin_screen_help()

	/**
	 * Generate and return HTML for a reference to the various supported shortcodes.
	 * @access  private
	 * @since	1.0
	 * @return  string Description and list of shortcodes.
	 */
	private function _get_shortcode_reference () {
		$html = '<p>' . __( 'Use these shortcodes to include dynamic data into your meta sections.', 'sfwp-locale' ) . '</p>' . "\n";
		if ( 0 < count( $this->shortcodes ) ) {
			$count = 0;
			foreach ( $this->shortcodes as $k => $v ) {
				$count++;
				if ( 1 < $count ) { $html .= '<br />'; }
				$html .= '<code>[' . $k . ']</code> - ' . $v . '' . "\n";
			}
		}
		return $html;
	} // End _get_shortcode_reference()

	/**
	 * Logic to run on the admin screen.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function admin_screen_logic () {
		$is_processed = false;
		// Save logic.
		if ( isset( $_POST['submit'] ) && check_admin_referer( 'meta-options-update' ) ) {
			$fields_to_skip = array( 'submit', '_wp_http_referer', '_wpnonce' );

			$posted_data = $_POST;

			foreach ( $posted_data as $k => $v ) {
				if ( in_array( $k, $fields_to_skip ) || ! $this->is_valid_meta_key( $k ) ) {
					unset( $posted_data[$k] );
				} else {
					$posted_data[$k] = wp_filter_post_kses( $v );
				}
			}

			if ( is_array( $posted_data ) ) {
				$is_updated = update_option( $this->plugin_prefix . 'stored_meta', $posted_data );

				// Redirect to make sure the latest changes are reflected.
				wp_safe_redirect( admin_url( 'admin.php?page=sf-meta-manager&updated=true' ) );
				exit;
			}
			$is_processed = true;
		}
	} // End admin_screen_logic()

 	/**
 	 * Create filters using the saved content.
 	 * @access  public
	 * @since	1.0
 	 * @return  void
 	 */
	public function create_filters () {
		if ( ! is_admin() ) {
			$stored_meta = get_option( $this->plugin_prefix . 'stored_meta' );

			// Create the filter functions.
			if ( is_array( $stored_meta ) && 0 < count( $stored_meta ) ) {
				foreach ( $stored_meta as $k => $v ) {
					if ( ! $this->is_valid_meta_key( $k ) ) {
						_doing_it_wrong( __METHOD__, 'Attempting to add invalid filter: ' . $k, 'sfwp-locale' );
						continue;
					}
					add_filter( $k, array( $this, 'do_filter' ), 12 );
				}
			}
		}
	} // End create_filters()

	/**
	 * Run the desired custom filter.
	 * @access public
	 * @param  mixed $value Filter value.
	 * @return mixed        Modified filter value.
	 */
	public function do_filter( $value ) {
		$current_filter = current_filter();

		// TODO: helper function for this would be nice
		$stored_meta = get_option( $this->plugin_prefix . 'stored_meta' );
		$new_value = isset( $stored_meta[ $current_filter ] ) ? $stored_meta[ $current_filter ] : '';

		$new_value = stripcslashes( $new_value );

		return wp_kses_post( $new_value );
	}

	/**
	 * Make sure a specified meta key is valid.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	private function is_valid_meta_key( $meta_key ) {
		$exists = false;
		if ( 0 < count( $this->meta_areas ) ) {
			foreach ( $this->meta_areas as $id => $meta_area ) {
				if ( array_key_exists( $meta_key, $meta_area ) ) {
					$exists = true;
					break;
				}
			}
		}
		return $exists;
	} // End is_valid_meta_key()

	/**
	 * Setup the shortcodes for reference.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function setup_shortcodes() {
		$this->shortcodes = array();
		$this->shortcodes['view_full_article'] = __( 'Link to read the full post.', 'sfwp-locale' );
		$this->shortcodes['post_date'] = __( 'The post date.', 'sfwp-locale' );
		$this->shortcodes['post_time'] = __( 'The post time.', 'sfwp-locale' );
		$this->shortcodes['post_author_link'] = __( 'The post author (link to the author\'s website).', 'sfwp-locale' );
		$this->shortcodes['post_author_posts_link'] = __( 'The post author (link to the author\'s posts archive).', 'sfwp-locale' );
		$this->shortcodes['post_comments'] = __( 'Comments for the post.', 'sfwp-locale' );
		$this->shortcodes['post_tags'] = __( 'Tags for the post.', 'sfwp-locale' );
		$this->shortcodes['post_categories'] = __( 'Categories for the post.', 'sfwp-locale' );
		$this->shortcodes['post_edit'] = __( '"Edit" link for the post.', 'sfwp-locale' );
	} // End setup_shortcodes()

 	/**
 	 * Add our saved data to the Framework data exporter.
 	 * @access  public
	 * @since	1.0
 	 * @param   string $data SQL query.
 	 * @return  string SQL query.
 	 */
	public function add_exporter_data ( $data ) {
		$data .= " OR option_name = '" . $this->plugin_prefix . "stored_meta" . "'";

		return $data;
	} // End add_exporter_data()
} // End Class
?>