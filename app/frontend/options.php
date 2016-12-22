<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

if (!function_exists( 'sf_options')) {
	function sf_options() {
	
		// Theme Variables
		$themename = 'Light';
		$themeslug = 'light-framework';
	
		// Standard Variables
		$shortname = 'sf';
		$manualurl = 'http://docs.starjive.com/document/'.$themeslug.'/documentation/';
	
		// Access the WordPress Categories via an Array
		$sf_categories = array();
		$sf_categories_obj = get_categories( 'hide_empty=0' );
		foreach ($sf_categories_obj as $sf_cat) {
			$sf_categories[$sf_cat->cat_ID] = $sf_cat->cat_name;
		}
		$categories_tmp = array_unshift($sf_categories, 'Select a category:' );
	
		// Access the WordPress Pages via an Array
		$sf_pages = array();
		$sf_pages_obj = get_pages( 'sort_column=post_parent, menu_order' );
		foreach ($sf_pages_obj as $sf_page) {
			$sf_pages[$sf_page->ID] = $sf_page->post_name;
		}
		$sf_pages_tmp = array_unshift($sf_pages, 'Select a page:' );
	
		// Stylesheets Reader
		$alt_stylesheet_path = get_template_directory() . '/styles/';
		$alt_stylesheets = array();
	
		if ( is_dir($alt_stylesheet_path) ) {
			if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) {
				while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
					if(stristr($alt_stylesheet_file, '.css') !== false) {
						$alt_stylesheets[] = $alt_stylesheet_file;
					}
				}
			}
		}
	
		// More Options
		$options_pixels = array();
		$other_entries = array( __( 'Select a number:', 'sfwp-locale' ), '0' );
		$other_entries_2 = array( __( 'Select a number:', 'sfwp-locale' ) );
		$total_possible_numbers = intval( apply_filters( 'sf_total_possible_numbers', 20 ) );
		for ( $i = 0; $i <= $total_possible_numbers; $i++ ) {
			$options_pixels[] = $i . 'px';
			$other_entries[] = $i;
			$other_entries_2[] = $i;
		}
	
		$options_image_link_to = array( 'image' => __( 'The Image', 'sfwp-locale' ), 'post' => __( 'The Post', 'sfwp-locale' ) );
	
		// Setup an array of slide-page terms for a dropdown.
		$slide_groups = array();
	
		if ( taxonomy_exists( 'slide-page' ) ) {
			$args = array( 'echo' => 0, 'hierarchical' => 1, 'taxonomy' => 'slide-page' );
			$cats_dropdown = wp_dropdown_categories( $args );
			$cats = array();
		
			// Quick string hack to make sure we get the pages with the indents.
			$cats_dropdown = str_replace( '<select name="cat" id="cat" class="postform" >', '', $cats_dropdown );
			$cats_dropdown = str_replace( '</select>', '', $cats_dropdown );
			$cats_split = explode( '</option>', $cats_dropdown );
		
			$cats[] = __( 'Select a Slide Group:', 'sfwp-locale' );
		
			foreach ( $cats_split as $k => $v ) {
				$id = '';
				// Get the ID value.
				preg_match( '/value="(.*?)"/i', $v, $matches );
		
				if ( isset( $matches[1] ) ) {
					$id = $matches[1];
					$cats[$id] = trim( strip_tags( $v ) );
				}
			}
			$slide_groups = $cats;
		}
		
		// Layout directory for the images.
		$img_layouts_dir =  get_template_directory_uri() . '/app/backend/assets/images/layouts/';


		/**
		 * General Settings
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options = array();
		
		$options[] = array( 'name' => __( 'General Settings', 'sfwp-locale' ),
							'icon' => 'general',
							'type' => 'heading' );
		
		$options[] = array( 'name' => __( 'Quick Start', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Theme Stylesheet', 'sfwp-locale' ),
							'desc' => __( 'Select your themes alternative color scheme.', 'sfwp-locale' ),
							'id' => $shortname.'_alt_stylesheet',
							'std' => 'default.css',
							'type' => 'select',
							'options' => $alt_stylesheets );
							
		$options[] = array( 'name' => __( 'Logotype', 'sfwp-locale' ),
							'desc' => __( 'Upload a logotype for your theme, or specify an image URL directly.', 'sfwp-locale' ),
							'id' => $shortname.'_logo',
							'std' => '',
							'type' => 'upload' );
		
		$options[] = array( 'name' => __( 'Logotype Width', 'sfwp-locale' ),
							'desc' => 'Set the width (in px) that you would like your logotype to be (recommended max-width is 500px)',
							'id' => $shortname.'_logo_width',
							'std' => '280', 'min' => '100', 'max' => '500', 'increment' => '10',
							'type' => 'slider' );

		$options[] = array( 'name' => __( 'Logotype Margin', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 20 for the desired top and bottom margin.', 'sfwp-locale' ),
							//'id' => $shortname.'_logo_margin',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_logo_margin_top',
										'type' => 'text',
										'std' => '0',
										'meta' => __( 'Top', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_logo_margin_bottom',
										'type' => 'text',
										'std' => '0',
										'meta' => __( 'Bottom', 'sfwp-locale' ) )
							  ));
		
		$options[] = array( 'name' => __( 'Logotype Color (SVG)', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color to fill your svg logotype add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_logo_color',
							'std' => '#231f20',
							'type' => 'color' );

		$options[] = array( 'name' => __( 'Logotype Hover Color (SVG)', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom hover color to fill your svg logotype add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_logo_hover_color',
							'std' => '#373737',
							'type' => 'color' );
											
		$options[] = array( 'name' => __( 'Favicon', 'sfwp-locale' ),
							'desc' => __( 'Upload a 16x16 pixel ico/png/gif image that will represent your website\'s favicon, or specify an image URL directly.', 'sfwp-locale' ),
							'id' => $shortname.'_custom_favicon',
							'std' => '',
							'type' => 'upload' );
		
		$options[] = array( 'name' => __( 'Touch Icon', 'sfwp-locale' ),
							'desc' => __( 'Upload a 180x180 pixel png image that will represent your website\'s touch icon, or specify an image URL directly.', 'sfwp-locale' ),
							'id' => $shortname.'_custom_touch_icon',
							'std' => '',
							'type' => 'upload' );
							
		$options[] = array( 'name' => __( 'Tracking Code', 'sfwp-locale' ),
							'desc' => __( 'Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'sfwp-locale' ),
							'id' => $shortname.'_google_analytics',
							'std' => '',
							'type' => 'textarea' );
							
		$options[] = array( 'name' => __( 'Tracking Code Output', 'sfwp-locale' ),
							'desc' => __( 'Moves the output of your themes Tracking Code setting from the footer to the header.', 'sfwp-locale' ),
							'id' => $shortname.'_move_tracking_code',
							'std' => '',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Subscription Settings', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'RSS URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your preferred RSS URL. (Feedburner or other)', 'sfwp-locale' ),
							'id' => $shortname.'_feed_url',
							'std' => '',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Display Options', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Custom CSS', 'sfwp-locale' ),
							'desc' => __( 'Quickly add some CSS to your theme by adding it to this block.', 'sfwp-locale' ),
							'id' => $shortname.'_custom_css',
							'std' => '',
							'type' => 'textarea' );
		
		$options[] = array( 'name' => __( 'Post/Page Comments', 'sfwp-locale' ),
							'desc' => __( 'Select if you want to comments on posts and/or pages.', 'sfwp-locale' ),
							'id' => $shortname.'_comments',
							'type' => 'select2',
							'options' => array( 'post' => __( 'Posts Only', 'sfwp-locale' ), 'page' => __( 'Pages Only', 'sfwp-locale' ), 'both' => __( 'Pages / Posts', 'sfwp-locale' ), 'none' => __( 'None', 'sfwp-locale' ) ) );
		
		$options[] = array( 'name' => __( 'Post Content', 'sfwp-locale' ),
							'desc' => __( 'Select if you want to show the full content or the excerpt on posts.', 'sfwp-locale' ),
							'id' => $shortname.'_post_content',
							'type' => 'select2',
							'options' => array( 'excerpt' => __( 'The Excerpt', 'sfwp-locale' ), 'content' => __( 'Full Content', 'sfwp-locale' ) ) );
		
		$options[] = array( 'name' => __( 'Display Breadcrumbs', 'sfwp-locale' ),
							'desc' => __( 'Display dynamic breadcrumbs on each page of your website.', 'sfwp-locale' ),
							'id' => $shortname.'_breadcrumbs_show',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Pagination Style', 'sfwp-locale' ),
							'desc' => __( 'Select the style of pagination you would like to use on the blog.', 'sfwp-locale' ),
							'id' => $shortname.'_pagination_type',
							'type' => 'select2',
							'options' => array( 'paginated_links' => __( 'Numbers', 'sfwp-locale' ), 'simple' => __( 'Next/Previous', 'sfwp-locale' ) ) );

		$options[] = array( 'name' => __( 'Meta', 'sfwp-locale' ),
							'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Meta tags', 'sfwp-locale' ),
							'desc' => __( 'Disables the meta tags which show the current Theme and Framework version in your site source code.', 'sfwp-locale' ),
							'id' => $shortname.'_disable_generator',
							'std' => 'true',
							'type' => 'checkbox' );


		/**
		 * Styling
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Styling', 'sfwp-locale' ),
							'icon' => 'styling',
							'type' => 'heading' );
		
		$options[] = array( 'name' => __( 'Base Styling', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Disable ALL Custom Styling', 'sfwp-locale' ),
							'desc' => __( 'Disable output of all custom styling (CSS) from the theme options and use default styles from the stylesheet.', 'sfwp-locale' ),
							'id' => $shortname.'_style_disable',
							'std' => 'false',
							'type' => 'checkbox' );
		/*
		$options[] = array( 'name' => __( 'Background Options', 'sfwp-locale' ),
							'desc' => '',
							'id' => $shortname.'_background_notice',
							'std' => sprintf( __( 'Background options can also be set in <a href="%s">Appearance > Customize</a>. The options on that page <strong>override</strong> the background options below.', 'sfwp-locale' ), admin_url( 'customize.php' ) ),
							'type' => 'info' );
		*/
		
		$options[] = array( 'name' =>  __( 'Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_style_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Background Image', 'sfwp-locale' ),
							'desc' => __( 'Upload a background image, or specify the image address of your image. (http://yoursite.com/image.png)', 'sfwp-locale' ),
							'id' => $shortname.'_style_bg_image',
							'std' => '',
							'type' => 'upload' );
		
		$options[] = array( 'name' => __( 'Background Image Repeat', 'sfwp-locale' ),
							'desc' => __( 'Select how you want your background image to display.', 'sfwp-locale' ),
							'id' => $shortname.'_style_bg_image_repeat',
							'type' => 'select',
							'options' => array( 'No Repeat' => 'no-repeat', 'Repeat' => 'repeat', 'Repeat Horizontally' => 'repeat-x', 'Repeat Vertically' => 'repeat-y' ) );
		
		$options[] = array( 'name' => __( 'Background image position', 'sfwp-locale' ),
							'desc' => __( 'Select how you would like to position the background', 'sfwp-locale' ),
							'id' => $shortname.'_style_bg_image_pos',
							'std' => 'top left',
							'type' => 'select',
							'options' => array( 'top left', 'top center', 'top right', 'center left', 'center center', 'center right', 'bottom left', 'bottom center', 'bottom right' ) );
									
		$options[] = array( 'name' => __( 'Background Attachment', 'sfwp-locale' ),
							'desc' => __( 'Select whether the background should be fixed or move when the user scrolls', 'sfwp-locale' ),
							'id' => $shortname.'_style_bg_image_attach',
							'std' => 'scroll',
							'type' => 'select',
							'options' => array( 'scroll', 'fixed' ) );
		
		$options[] = array( 'name' => __( 'Top Border', 'sfwp-locale' ),
							'desc' => __( 'Select border properties.', 'sfwp-locale' ),
							'id' => $shortname.'_border_top',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#000000' ),
							'type' => 'border' );
		/*
		$options[] = array( 'name' => __( 'Links', 'sfwp-locale' ),
							'type' => 'subheading' );
		*/
		$options[] = array( 'name' => __( 'Link Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_link_color',
							'std' => '#0278c0',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Link Hover Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_link_hover_color',
							'std' => '#dd3333',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Button Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_button_color',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Button Hover Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_button_hover_color',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'General Border Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_style_border',
							'std' => '',
							'type' => 'color' );


		/**
		 * Misc Typography
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Misc Typography', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Misc Typography', 'sfwp-locale' ),
							'desc' => '',
							'id' => $shortname.'_general_font_notice',
							'std' => __( 'The misc typography options below only control typography not covered by other typography options. You can control specific typography on post title, post content, widget titles etc. in the other sections in the options panel.', 'sfwp-locale' ),
							'type' => 'info' );
		
		$options[] = array( 'name' => __( 'General Text Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for general text.', 'sfwp-locale' ),
							'id' => $shortname.'_font_text',
							'std' => array( 'size' => '14', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#555555'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'H1 Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for header H1.', 'sfwp-locale' ),
							'id' => $shortname.'_font_h1',
							'std' => array( 'size' => '28', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'H2 Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for header H2.', 'sfwp-locale' ),
							'id' => $shortname.'_font_h2',
							'std' => array( 'size' => '24', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'H3 Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for header H3.', 'sfwp-locale' ),
							'id' => $shortname.'_font_h3',
							'std' => array( 'size' => '20', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'H4 Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for header H4.', 'sfwp-locale' ),
							'id' => $shortname.'_font_h4',
							'std' => array( 'size' => '16', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'H5 Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for header H5.', 'sfwp-locale' ),
							'id' => $shortname.'_font_h5',
							'std' => array( 'size' => '14', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'H6 Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for header H6.', 'sfwp-locale' ),
							'id' => $shortname.'_font_h6',
							'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );


		/**
		 * Layout
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Layout', 'sfwp-locale' ),
							'icon' => 'styling',
							'type' => 'heading' );
		
		$options[] = array( 'name' => __( 'Layout General Settings', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Site Width', 'sfwp-locale' ),
									'desc' => 'Set the width (in px) that you would like your content column to be (recommended max-width is 1600px)',
									'id' => $shortname.'_layout_width',
									'std' => '960', 'min' => '600', 'max' => '1600', 'increment' => '10',
									'type' => 'slider' );
		
		$options[] = array( 'name' => __( 'Main Layout', 'sfwp-locale' ),
								'desc' => __( 'Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'sfwp-locale' ),
								'id' => $shortname.'_layout',
								'std' => 'two-col-left',
								'type' => 'images',
								'options' => array( 'one-col' => $img_layouts_dir . 'layout-main-1c.png',
									'two-col-left' => $img_layouts_dir . 'layout-main-2cl.png',
									'two-col-right' => $img_layouts_dir . 'layout-main-2cr.png',
									'three-col-left' => $img_layouts_dir . 'layout-main-3cl.png',
									'three-col-middle' => $img_layouts_dir . 'layout-main-3cm.png',
									'three-col-right' => $img_layouts_dir . 'layout-main-3cr.png')
								);
		
		$options[] = array( 'name' => __( 'Main Content Padding', 'sfwp-locale' ),
							'desc' => __( 'Enter an value (in em) for the desired padding.', 'sfwp-locale' ),
							'id' => $shortname.'_layout_content_padding',
							'std' => '',
							'type' => array(
											array( 'id' => $shortname.'_layout_content_padding_top',
													'type' => 'text',
													'std' => '2',
													'meta' => __( 'Top', 'sfwp-locale' ) ),
													
											array( 'id' => $shortname.'_layout_content_padding_right',
													'type' => 'text',
													'std' => '2',
													'meta' => __( 'Right', 'sfwp-locale' ) ),
													
											array( 'id' => $shortname.'_layout_content_padding_bottom',
													'type' => 'text',
													'std' => '2',
													'meta' => __( 'Bottom', 'sfwp-locale' ) ),
													
											array( 'id' => $shortname.'_layout_content_padding_left',
													'type' => 'text',
													'std' => '2',
													'meta' => __( 'Left', 'sfwp-locale' ) )
										  ));
		
		$options[] = array( 'name' => __( 'Main Content Mobile Padding', 'sfwp-locale' ),
							'desc' => __( 'Enter an value (in em) for the desired padding.', 'sfwp-locale' ),
							'id' => $shortname.'_layout_content_mobile_padding',
							'std' => '',
							'type' => array(
											array( 'id' => $shortname.'_layout_content_mobile_padding_top',
													'type' => 'text',
													'std' => '1.1',
													'meta' => __( 'Top', 'sfwp-locale' ) ),
													
											array( 'id' => $shortname.'_layout_content_mobile_padding_right',
													'type' => 'text',
													'std' => '1.1',
													'meta' => __( 'Right', 'sfwp-locale' ) ),
													
											array( 'id' => $shortname.'_layout_content_mobile_padding_bottom',
													'type' => 'text',
													'std' => '1.1',
													'meta' => __( 'Bottom', 'sfwp-locale' ) ),
													
											array( 'id' => $shortname.'_layout_content_mobile_padding_left',
													'type' => 'text',
													'std' => '1.1',
													'meta' => __( 'Left', 'sfwp-locale' ) )
										  ));
										  
		$options[] = array( 'name' => __( 'Footer Widget Areas', 'sfwp-locale' ),
							'desc' => __( 'Select how many footer widget areas you want to display.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_sidebars',
							'std' => '4',
							'type' => 'images',
							'options' => array( '0' => $img_layouts_dir . 'layout-footer-widgets-0.png',
								'1' => $img_layouts_dir . 'layout-footer-widgets-1.png',
								'2' => $img_layouts_dir . 'layout-footer-widgets-2.png',
								'3' => $img_layouts_dir . 'layout-footer-widgets-3.png',
								'4' => $img_layouts_dir . 'layout-footer-widgets-4.png')
							);


		/**
		 * Boxed Layout
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Boxed Layout', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Boxed Layout Style', 'sfwp-locale' ),
							'desc' => __( 'Enable the boxed layout style.', 'sfwp-locale' ),
							'id' => $shortname.'_layout_boxed',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Box Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the boxed background or add a hex color code e.g. #ffffff', 'sfwp-locale' ),
							'id' => $shortname.'_style_box_bg',
							'std' => '#ffffff',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Box Margin', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 20 for the desired top and bottom margin.', 'sfwp-locale' ),
							'id' => $shortname.'_box_margin',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_box_margin_top',
										'type' => 'text',
										'std' => '0',
										'meta' => __( 'Top', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_box_margin_bottom',
										'type' => 'text',
										'std' => '0',
										'meta' => __( 'Bottom', 'sfwp-locale' ) )
							  ));
		
		$options[] = array( 'name' => __( 'Box Border Top/Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the boxed layout.', 'sfwp-locale' ),
							'id' => $shortname.'_box_border_tb',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Box Border Left/Right', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the boxed layout.', 'sfwp-locale' ),
							'id' => $shortname.'_box_border_lr',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Box Rounded Corners', 'sfwp-locale' ),
							'desc' => __( 'Set amount of pixels for border radius (rounded corners). Will only show in CSS3 compatible browser.', 'sfwp-locale' ),
							'id' => $shortname.'_box_border_radius',
							'type' => 'select',
							'std' => '0px',
							'options' => $options_pixels);
		
		$options[] = array( 'name' => __( 'Box Shadow', 'sfwp-locale' ),
							'desc' => __( 'Enable box shadow. Will only show in CSS3 compatible browser.', 'sfwp-locale' ),
							'id' => $shortname.'_box_shadow',
							'std' => 'true',
							'type' => 'checkbox' );


		/**
		 * Full width Layout
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Full Width Layout', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Full Width Layout', 'sfwp-locale' ),
							'desc' => '',
							'id' => $shortname.'_full_width_notice',
							'std' => __( 'Below you can enable full width header and footer areas and set the background. You can set the styling options for the full width navigation under the Primary Navigation options. Please note that Boxed Layout must be disabled.', 'sfwp-locale' ),
							'type' => 'info' );
		
		$options[] = array( 'name' => __( 'Enable Full Width Header', 'sfwp-locale' ),
							'desc' => __( 'Set header container to display full width.', 'sfwp-locale' ),
							'id' => $shortname.'_header_full_width',
							'std' => 'true',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Header Background Color', 'sfwp-locale' ),
							'desc' => __( 'Select the background color you want for your full width header.', 'sfwp-locale' ),
							'id' => $shortname.'_full_header_full_width_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Header Background Image', 'sfwp-locale' ),
							'desc' => __( 'Upload a background image, or specify the image address of your image (http://yoursite.com/image.png). <br/>Image should be same width as your site width.', 'sfwp-locale' ),
							'id' => $shortname.'_full_header_bg_image',
							'std' => '',
							'type' => 'upload' );
		
		$options[] = array( 'name' => __( 'Header Background Image Repeat', 'sfwp-locale' ),
							'desc' => __( 'Select how you want your background image to display.', 'sfwp-locale' ),
							'id' => $shortname.'_full_header_bg_image_repeat',
							'type' => 'select',
							'options' => array( 'No Repeat' => 'no-repeat', 'Repeat' => 'repeat', 'Repeat Horizontally' => 'repeat-x', 'Repeat Vertically' => 'repeat-y' ) );
		
		$options[] = array( 'name' => __( 'Enable Full Width Footer', 'sfwp-locale' ),
							'desc' => __( 'Set footer widget area and footer container to display full width.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_full_width',
							'std' => 'true',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Footer Widget Area Background Color', 'sfwp-locale' ),
							'desc' => __( 'Select the background color you want for your full width widget area.', 'sfwp-locale' ),
							'id' => $shortname.'_foot_full_width_widget_bg',
							'std' => '#f0f0f0',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Footer Background Color', 'sfwp-locale' ),
							'desc' => __( 'Select the background color you want for your full width footer.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_full_width_bg',
							'std' => '#222222',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Header &amp; Footer', 'sfwp-locale' ),
							'icon' => 'styling',
							'type' => 'heading' );


		/**
		 * Header Styling
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Header', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Header Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for header background or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_header_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Header Background Image', 'sfwp-locale' ),
							'desc' => __( 'Upload a background image, or specify the image address of your image (http://yoursite.com/image.png). <br/>Image should be same width as your site width.', 'sfwp-locale' ),
							'id' => $shortname.'_header_bg_image',
							'std' => '',
							'type' => 'upload' );
		
		$options[] = array( 'name' => __( 'Header Background Image Repeat', 'sfwp-locale' ),
							'desc' => __( 'Select how you want your background image to display.', 'sfwp-locale' ),
							'id' => $shortname.'_header_bg_image_repeat',
							'type' => 'select',
							'options' => array( 'No Repeat' => 'no-repeat', 'Repeat' => 'repeat', 'Repeat Horizontally' => 'repeat-x', 'Repeat Vertically' => 'repeat-y' ) );
		
		$options[] = array( 'name' => __( 'Header Border', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties.', 'sfwp-locale' ),
							'id' => $shortname.'_header_border',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => ''),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Header Margin', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 20 for the desired margin.', 'sfwp-locale' ),
							'id' => $shortname.'_header_margin_tb',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_header_margin_top',
									'type' => 'text',
									'std' => '0',
									'meta' => __( 'Top', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_header_margin_bottom',
									'type' => 'text',
									'std' => '0',
									'meta' => __( 'Bottom', 'sfwp-locale' ) )
							  ));
		
		$options[] = array( 'name' => __( 'Header Padding', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 20 for the desired padding.', 'sfwp-locale' ),
							'id' => $shortname.'_header_padding_tb',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_header_padding_top',
									'type' => 'text',
									'std' => '40',
									'meta' => __( 'Top', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_header_padding_right',
									'type' => 'text',
									'std' => '0',
									'meta' => __( 'Right', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_header_padding_bottom',
									'type' => 'text',
									'std' => '40',
									'meta' => __( 'Bottom', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_header_padding_left',
									'type' => 'text',
									'std' => '',
									'meta' => __( 'Left', 'sfwp-locale' ) )
							  ));
		
		$options[] = array( 'name' => __( 'Site Title', 'sfwp-locale' ),
							'desc' => __( 'Select typography for site title.', 'sfwp-locale' ),
							'id' => $shortname.'_font_logo',
							'std' => array( 'size' => '40', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Site Description', 'sfwp-locale' ),
							'desc' => __( 'Select typography properties.', 'sfwp-locale' ),
							'id' => $shortname.'_font_desc',
							'std' => array( 'size' => '13', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#999999'),
							'type' => 'typography' );


		/**
		 * Footer
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Footer', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Footer Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for footer.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_font',
							'std' => array( 'size' => '13', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#999999'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Footer Link Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for footer links or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_footer_link_color',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Footer Link Hover Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for footer links hover or add a hex color code e.g. #0278c0', 'sfwp-locale' ),
							'id' => $shortname.'_footer_link_hover_color',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Footer Background', 'sfwp-locale' ),
							'desc' => __( 'Select the background color you want for your footer.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Footer Border Top', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_border_top',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Footer Border Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_border_bottom',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => ''),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Footer Border Left/Right', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_border_lr',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => ''),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Footer Rounded Corners', 'sfwp-locale' ),
							'desc' => __( 'Set amount of pixels for border radius (rounded corners). Will only show in CSS3 compatible browser.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_border_radius',
							'type' => 'select',
							'options' => $options_pixels);
		
		$options[] = array( 'name' => __( 'Enable Custom Footer (Left)', 'sfwp-locale' ),
							'desc' => __( 'Activate to add the custom text below to the theme footer.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_left',
							'class' => 'collapsed',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Custom Text (Left)', 'sfwp-locale' ),
							'desc' => __( 'Custom HTML and Text that will appear in the footer of your theme.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_left_text',
							'class' => 'hidden last',
							'std' => '<p></p>',
							'type' => 'textarea' );
		
		$options[] = array( 'name' => __( 'Enable Custom Footer (Right)', 'sfwp-locale' ),
							'desc' => __( 'Activate to add the custom text below to the theme footer.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_right',
							'class' => 'collapsed',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Custom Text (Right)', 'sfwp-locale' ),
							'desc' => __( 'Custom HTML and Text that will appear in the footer of your theme.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_right_text',
							'class' => 'hidden last',
							'std' => '<p></p>',
							'type' => 'textarea' );
		
		$options[] = array( 'name' => __( 'Navigation', 'sfwp-locale' ),
							'icon' => 'styling',
							'type' => 'heading' );


		/**
		 * Top Navigation
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Top Navigation', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Top Navigation - Background Color', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Pick a custom color for the top navigation background or add a hex color code e.g. #000.<br />Top Navigation can be added with <a href="%s">WP Menus</a>', 'sfwp-locale' ), admin_url( 'nav-menus.php' ) ),
							'id' => $shortname.'_top_nav_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Top Navigation - Hover / Sub Menu Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the top navigation hover text color or add a hex color code e.g. #000', 'sfwp-locale' ),
							'id' => $shortname.'_top_nav_hover',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Top Navigation - Hover / Sub Menu Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the top navigation hover background color or add a hex color code e.g. #000', 'sfwp-locale' ),
							'id' => $shortname.'_top_nav_hover_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Top Navigation Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_top_nav_font',
							'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#ddd'),
							'type' => 'typography' );


		/**
		 * Primary Navigation
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Primary Navigation', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the navigation background or add a hex color code e.g. #cccccc', 'sfwp-locale' ),
							'id' => $shortname.'_nav_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Navigation Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_font',
							'std' => array( 'size' => '14', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => '', 'color' => '#666666'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Hover / Sub Menu Text Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the navigation hover / sub menu text color or add a hex color code e.g. #eeeeee', 'sfwp-locale' ),
							'id' => $shortname.'_nav_hover',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Hover / Sub Menu Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the navigation hover / sub menu background color or add a hex color code e.g. #eeeeee', 'sfwp-locale' ),
							'id' => $shortname.'_nav_hover_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Divider', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the menu items dividers.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_divider_border',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Dropdown menu border', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the navigation dropdown menu.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_dropdown_border',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Border Top', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_border_top',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Border Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_border_bot',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Border Left/Right', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_border_lr',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Navigation Rounded Corners', 'sfwp-locale' ),
							'desc' => __( 'Set amount of pixels for border radius (rounded corners). Will only show in CSS3 compatible browser.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_border_radius',
							'type' => 'select',
							'std' => '0px',
							'options' => $options_pixels);
		
		$options[] = array( 'name' => __( 'Navigation Margin Top/Bottom', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 20 for the desired header margin.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_margin_tb',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_nav_margin_top',
									'type' => 'text',
									'std' => '',
									'meta' => __( 'Top', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_nav_margin_bottom',
									'type' => 'text',
									'std' => '',
									'meta' => __( 'Bottom', 'sfwp-locale' ) )
								));

		$options[] = array( 'name' => __( 'Enable Subscribe Icon', 'sfwp-locale' ),
							'desc' => __( 'Enable the Subscribe to RSS icon in right navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_rss',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Enable E-mail Icon', 'sfwp-locale' ),
							'desc' => __( 'Enter an URL for the mail icon in the right navigation', 'sfwp-locale' ),
							'id' => $shortname.'_subscribe_email',
							'std' => '',
							'type' => 'text' );

		$options[] = array( 'name' => __( 'Enable Search', 'sfwp-locale' ),
							'desc' => __( 'Enable Search in the right navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_nav_search',
							'std' => 'false',
							'type' => 'checkbox' );


		/**
		 * Post Styling
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Posts', 'sfwp-locale' ),
							'icon' => 'styling',
							'type' => 'heading' );

		$options[] = array( 'name' => __( 'Posts / Pages', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Post/Page Title Font Style', 'sfwp-locale' ),
							'desc' => __( 'Specify typography for post/page title text.', 'sfwp-locale' ),
							'id' => $shortname.'_font_post_title',
							'std' => array( 'size' => '28', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Post Meta Font Style', 'sfwp-locale' ),
							'desc' => __( 'Specify typography for post meta.', 'sfwp-locale' ),
							'id' => $shortname.'_font_post_meta',
							'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#999999'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Post/Page Text Font Style', 'sfwp-locale' ),
							'desc' => __( 'Specify typography for post/page content text.', 'sfwp-locale' ),
							'id' => $shortname.'_font_post_text',
							'std' => array( 'size' => '15', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => '300', 'color' => '#555555'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Post More (bottom) Font Style', 'sfwp-locale' ),
							'desc' => __( 'Specify typography for post bottom text.', 'sfwp-locale' ),
							'id' => $shortname.'_font_post_more',
							'std' => array( 'size' => '13', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => ''),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Post More (bottom) Border Top', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for post more section.', 'sfwp-locale' ),
							'id' => $shortname.'_post_more_border_top',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Post More (bottom) Border Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for post more section.', 'sfwp-locale' ),
							'id' => $shortname.'_post_more_border_bottom',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Post Author Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom background color for the post author section or add a hex color code e.g. #fafafa', 'sfwp-locale' ),
							'id' => $shortname.'_post_author_bg',
							'std' => '#fafafa',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Post Author Border Top', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for post author section.', 'sfwp-locale' ),
							'id' => $shortname.'_post_author_border_top',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Post Author Border Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for post author section.', 'sfwp-locale' ),
							'id' => $shortname.'_post_author_border_bottom',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Post Author Border Left/Right', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for the navigation.', 'sfwp-locale' ),
							'id' => $shortname.'_post_author_border_lr',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Post Author Rounded Corners', 'sfwp-locale' ),
							'desc' => __( 'Set amount of pixels for border radius (rounded corners). Will only show in CSS3 compatible browser.', 'sfwp-locale' ),
							'id' => $shortname.'_post_author_border_radius',
							'type' => 'select',
							'std' => '5px',
							'options' => $options_pixels);
		
		$options[] = array( 'name' => __( 'Disable Post Author', 'sfwp-locale' ),
							'desc' => __( 'Disable post author below post?', 'sfwp-locale' ),
							'id' => $shortname.'_disable_post_author',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Comments Background Color (even threads)', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom background color for the post comments even threads or add a hex color code e.g. #fafafa', 'sfwp-locale' ),
							'id' => $shortname.'_post_comments_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Page Navigation Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for Page Navigation text.', 'sfwp-locale' ),
							'id' => $shortname.'_pagenav_font',
							'std' => array( 'size' => '13', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#888'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Page Navigation Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the Page Navigation background or add a hex color code e.g. #fafafa', 'sfwp-locale' ),
							'id' => $shortname.'_pagenav_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Page Navigation Border Top', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for Page Navigation section.', 'sfwp-locale' ),
							'id' => $shortname.'_pagenav_border_top',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Page Navigation Border Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for Page Navigation section.', 'sfwp-locale' ),
							'id' => $shortname.'_pagenav_border_bottom',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Archives', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Archive Header Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for Archive header.', 'sfwp-locale' ),
							'id' => $shortname.'_archive_header_font',
							'std' => array( 'size' => '18', 'unit' => 'px', 'face' => 'Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Archive Header Border Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for Archive header', 'sfwp-locale' ),
							'id' => $shortname.'_archive_header_border_bottom',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Disable Archive Header RSS link', 'sfwp-locale' ),
							'desc' => __( 'Disable RSS link in Archive header', 'sfwp-locale' ),
							'id' => $shortname.'_archive_header_disable_rss',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Widgets', 'sfwp-locale' ),
							'icon' => 'styling',
							'type' => 'heading' );
		
		$options[] = array( 'name' => __( 'Widget Area Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the background or add a hex color code e.g. #cccccc', 'sfwp-locale' ),
							'id' => $shortname.'_footer_widget_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Widget Area Border Top', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_widget_border_top',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Widget Area Border Bottom', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties.', 'sfwp-locale' ),
							'id' => $shortname.'_footer_widget_border_bottom',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
						
		$options[] = array( 'name' => __( 'Widget Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the background or add a hex color code e.g. #cccccc', 'sfwp-locale' ),
							'id' => $shortname.'_widget_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Widget Border', 'sfwp-locale' ),
							'desc' => __( 'Specify border properties for widgets.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_border',
							'std' => array( 'width' => '0', 'style' => 'solid', 'color' => '#dbdbdb'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Widget Padding', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 20 for the desired widget padding.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_padding',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_widget_padding_tb',
									'type' => 'text',
									'std' => '',
									'meta' => __( 'Top/Bottom', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_widget_padding_lr',
									'type' => 'text',
									'std' => '',
									'meta' => __( 'Left/Right', 'sfwp-locale' ) )
							  ));
		
		$options[] = array( 'name' => __( 'Widget Title', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for the widget title.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_font_title',
							'std' => array( 'size' => '14', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#555555'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Widget Title Bottom Border', 'sfwp-locale' ),
							'desc' => __( 'Specify border property for the widget title.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_title_border',
							'std' => array( 'width' => '1', 'style' => 'solid', 'color' => '#e6e6e6'),
							'type' => 'border' );
		
		$options[] = array( 'name' => __( 'Widget Text', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for the widget text.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_font_text',
							'std' => array( 'size' => '13', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#555555'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Widget Rounded Corners', 'sfwp-locale' ),
							'desc' => __( 'Set amount of pixels for border radius (rounded corners). Will only show in CSS3 compatible browser.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_border_radius',
							'type' => 'select',
							'options' => $options_pixels);
		
		$options[] = array( 'name' => __( 'Tabs Widget Background color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the tabs widget or add a hex color code e.g. #cccccc', 'sfwp-locale' ),
							'id' => $shortname.'_widget_tabs_bg',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Tabs Widget Inside Background Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for the tabs widget or add a hex color code e.g. #cccccc', 'sfwp-locale' ),
							'id' => $shortname.'_widget_tabs_bg_inside',
							'std' => '',
							'type' => 'color' );
		
		$options[] = array( 'name' => __( 'Tabs Widget Title', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for the widget text.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_tabs_font',
							'std' => array( 'size' => '12', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#555555'),
							'type' => 'typography' );
		
		$options[] = array( 'name' => __( 'Tabs Widget Meta / Tabber Font', 'sfwp-locale' ),
							'desc' => __( 'Select the typography you want for the widget text.', 'sfwp-locale' ),
							'id' => $shortname.'_widget_tabs_font_meta',
							'std' => array( 'size' => '11', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'thin', 'color' => '#999999'),
							'type' => 'typography' );


		/**
		 * Template: Magazine
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Magazine Template', 'sfwp-locale' ),
							'icon' => 'layout',
							'type' => 'heading' );
	
	
		$options[] = array( 'name' => __( 'Posts Slider', 'sfwp-locale' ),
						'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Magazine Page Template', 'sfwp-locale' ),
							'desc' => '',
							'id' => $shortname.'_sf_magazine_notice',
							'std' => sprintf( __( 'Below you can control settings for the Magazine page template. Please refer to <a href="%s">documentation</a> on how to setup the page template.' ), 'http://starjive.com/wordpress/themes/light-framework/documentation/' ),
							'type' => 'info' );
	
		$options[] = array( 'name' => __( 'Enable Featured Slider', 'sfwp-locale' ),
							'desc' => __( 'Enable the featured slider on the "Magazine" page template.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine',
							'std' => 'false',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Post Tag(s)', 'sfwp-locale' ),
							'desc' => __( 'Add comma separated list for the tags that you would like to have displayed in the featured slider on the "Magazine" page template. For example, if you add "tag1, tag3" here, then all posts tagged with either "tag1" or "tag3" will be shown in the featured area. These posts will be excluded from normal posts below slider.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_tags',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Number Of Posts To Display', 'sfwp-locale' ),
							'desc' => __( 'Select the number of entries that should appear in the Featured Slider.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_entries',
							'std' => '3',
							'type' => 'select',
							'options' => $other_entries_2);
	
		$options[] = array( 'name' => __( 'Exclude Posts', 'sfwp-locale' ),
							'desc' => __( 'Exclude the slider posts from the posts grid below slider.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_exclude',
							'std' => 'true',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Display The Post Titles', 'sfwp-locale' ),
							'desc' => __( 'Show the post title in the "Posts" slider.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_title',
							'std' => 'true',
							'class' => 'collapsed',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Title Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for title.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_font_title',
							'std' => array( 'size' => '24', 'unit' => 'px', 'face' => 'Arial, sans-serif', 'style' => 'bold', 'color' => '#ffffff'),
							'class' => 'hidden last',
							'type' => 'typography' );
	
		$options[] = array( 'name' => __( 'Display The Post Excerpts', 'sfwp-locale' ),
							'desc' => __( 'Show the post excerpt in the "Posts" slider.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_excerpt',
							'std' => 'true',
							'class' => 'collapsed',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Excerpt Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for excerpt text.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_font_excerpt',
							'std' => array( 'size' => '13', 'unit' => 'px', 'face' => 'Arial, sans-serif', 'style' => 'thin', 'color' => '#cccccc'),
							'class' => 'hidden',
							'type' => 'typography' );
	
		$options[] = array( 'name' => __( 'Excerpt Length', 'sfwp-locale' ),
							'desc' => __( 'Total number of words to show in the excerpt.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_magazine_excerpt_length',
							'std' => '15',
							'class' => 'hidden last',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Featured Posts', 'sfwp-locale' ),
							'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Number Of Featured Posts', 'sfwp-locale' ),
							'desc' => __( 'Select how many featured (full width) posts you would like to show before your two-column posts. Set total number of posts in Settings > Reading.', 'sfwp-locale' ),
							'id' => $shortname.'_magazine_feat_posts',
							'type' => 'select',
							'options' => $other_entries);
	
		$options[] = array( 'name' => __( 'Post Content for "Featured" Posts', 'sfwp-locale' ),
							'desc' => __( 'Select if you want to show the full content or the excerpt on posts in the "Featured" section.', 'sfwp-locale' ),
							'id' => $shortname.'_magazine_featured_post_content',
							'std' => 'excerpt',
							'type' => 'select2',
							'options' => array( 'excerpt' => __( 'The Excerpt', 'sfwp-locale' ), 'content' => __( 'Full Content', 'sfwp-locale' ) ) );
	
		$options[] = array( 'name' => __( 'Featured Image Dimensions', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 250 for the image size.', 'sfwp-locale' ),
							'id' => $shortname.'_image_dimensions',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_magazine_f_w',
									'type' => 'text',
									'std' => 100,
									'meta' => __( 'Width', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_magazine_f_h',
									'type' => 'text',
									'std' => 100,
									'meta' => __( 'Height', 'sfwp-locale' ) )
							  ));
	
		$options[] = array( 'name' => __( 'Featured Post Image Alignment', 'sfwp-locale' ),
							'desc' => __( 'Select how to align your featured post images.', 'sfwp-locale' ),
							'id' => $shortname.'_magazine_f_align',
							'std' => 'alignleft',
							'type' => 'radio',
							'options' => array( 'alignleft' => 'Left', 'alignright' => 'Right', 'aligncenter' => 'Center' ) );
	
		$options[] = array( 'name' => __( 'Posts Grid', 'sfwp-locale' ),
							'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Post Content for "Grid" Posts', 'sfwp-locale' ),
							'desc' => __( 'Select if you want to show the full content or the excerpt on posts in the "Grid" section.', 'sfwp-locale' ),
							'id' => $shortname.'_magazine_grid_post_content',
							'std' => 'excerpt',
							'type' => 'select2',
							'options' => array( 'excerpt' => __( 'The Excerpt', 'sfwp-locale' ), 'content' => __( 'Full Content', 'sfwp-locale' ) ) );
	
		$options[] = array( 'name' => __( 'Post Title Font Style', 'sfwp-locale' ),
							'desc' => __( 'Specify typography for post title.', 'sfwp-locale' ),
							'id' => $shortname.'_magazine_grid_font_post_title',
							'std' => array( 'size' => '18', 'unit' => 'px', 'face' => 'Helvetica, Arial, sans-serif', 'style' => 'bold', 'color' => '#222222'),
							'type' => 'typography' );
	
		$options[] = array( 'name' => __( 'Post Image Dimensions', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 250 for the image size.', 'sfwp-locale' ),
							'id' => $shortname.'_image_dimensions',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_magazine_b_w',
									'type' => 'text',
									'std' => 100,
									'meta' => __( 'Width', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_magazine_b_h',
									'type' => 'text',
									'std' => 100,
									'meta' => __( 'Height', 'sfwp-locale' ) )
							  ));
	
		$options[] = array( 'name' => __( 'Post Image Alignment', 'sfwp-locale' ),
							'desc' => __( 'Select how to align your normal post images.', 'sfwp-locale' ),
							'id' => $shortname.'_magazine_b_align',
							'std' => 'alignleft',
							'type' => 'radio',
							'options' => array( 'alignleft' => 'Left', 'alignright' => 'Right', 'aligncenter' => 'Center' ) );


		/**
		 * Template: Business
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Business Template', 'sfwp-locale' ),
							'icon' => 'layout',
							'type' => 'heading' );
	
		$options[] = array( 'name' => __( '"Business" Setup', 'sfwp-locale' ),
						'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Business Page Template', 'sfwp-locale' ),
							'desc' => '',
							'id' => $shortname.'_sf_biz_notice',
							'std' => sprintf( __( 'Below you can control settings for the Business page template. Please refer to <a href="%s">documentation</a> on how to setup the page template. You can add slider posts with the <strong><em>Slides</em></strong> custom post type.' ), 'http://starjive.com/wordpress/themes/light-framework/documentation/' ),
							'type' => 'info' );
	
		$options[] = array( 'name' => __( 'Disable Footer Widgets', 'sfwp-locale' ),
								'desc' => __( 'Disable the footer widgets on this template.', 'sfwp-locale' ),
								'id' => $shortname.'_biz_disable_footer_widgets',
								'std' => 'false',
								'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Disable Slides Admin Menu', 'sfwp-locale' ),
						'desc' => __( 'Disable the slides admin menu functionality.', 'sfwp-locale' ),
						'id' => $shortname.'_biz_slides_disable',
						'std' => 'false',
						'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Featured Slider', 'sfwp-locale' ),
						'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Featured Slider', 'sfwp-locale' ),
							'desc' => __( 'Enable the featured slider.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz',
							'std' => 'false',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Full Width Slider', 'sfwp-locale' ),
							'desc' => __( 'Enable the slider to be full width.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_full',
							'std' => 'false',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Number of Slides', 'sfwp-locale' ),
							'desc' => __( 'Select how many slides you would like to show in the slider.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_number',
							'std' => '10',
							'type' => 'select',
							'options' => $other_entries_2);
	
		if ( ! empty( $slide_groups ) ) {
			$options[] = array( 'name' => __( 'Slide Group', 'sfwp-locale' ),
							'desc' => __( 'Optionally choose to display only slides from a specific slide group.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_slide_group',
							'std' => '0',
							'type' => 'select2',
							'options' => $slide_groups );
		}
	
		$options[] = array( 'name' => __( 'Display Order', 'sfwp-locale' ),
							'desc' => __( 'Select the order in which you want to show your slides.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_order',
							'type' => 'select2',
							'std' => 'desc',
							'options' => array( 'desc' => __( 'Newest first', 'sfwp-locale' ), 'ASC' => __( 'Oldest first', 'sfwp-locale' ) ) );
	
		$options[] = array( 'name' => __( 'Featured Slider Title', 'sfwp-locale' ),
							'desc' => __( 'Show the page title in slider when using Featured Image as background image.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_title',
							'std' => 'true',
							'class' => 'collapsed',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Featured Slider Title Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for title (when using image background).', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_font_title',
							'std' => array( 'size' => '24', 'unit' => 'px', 'face' => 'Arial, sans-serif', 'style' => 'bold', 'color' => '#ffffff'),
							'class' => 'hidden last',
							'type' => 'typography' );
	
		$options[] = array( 'name' => __( 'Featured Slider Content Font Style', 'sfwp-locale' ),
							'desc' => __( 'Select typography for content text (when using image background).', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_font_excerpt',
							'std' => array( 'size' => '13', 'unit' => 'px', 'face' => 'Arial, sans-serif', 'style' => 'thin', 'color' => '#cccccc'),
							'type' => 'typography' );
	
		$options[] = array( 'name' => __( 'Featured Slider Content Overlay', 'sfwp-locale' ),
							'desc' => __( 'Select the position of the slider content overlay which is shown when using a featured image in the slide post.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_biz_overlay',
							'type' => 'select2',
							'std' => 'bottom',
							'options' => array( 'none' => __( 'None', 'sfwp-locale' ), 'left' => __( 'Left', 'sfwp-locale' ), 'right' => __( 'Right', 'sfwp-locale' ), 'bottom' => __( 'Bottom', 'sfwp-locale' ), 'center' => __( 'Center', 'sfwp-locale' ), 'full' => __( 'Full', 'sfwp-locale' ) ) );


		/**
		 * Slider Settings
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Slider Settings', 'sfwp-locale' ),
							'icon' => 'slider',
							'type' => 'heading' );
	
		$options[] = array( 'name' => __( 'Slider Settings', 'sfwp-locale' ),
							'desc' => '',
							'id' => $shortname.'_sf_slider_notice',
							'std' => __( 'Below you can control the generic slider settings which will apply to both Business and Magazine templates.', 'sfwp-locale' ),
							'type' => 'info' );
	
		$options[] = array( 'name' => __( 'Auto Start', 'sfwp-locale' ),
							'desc' => __( 'Set the slider to start sliding automatically. Adjust the speed of sliding underneath.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_auto',
							'std' => 'true',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Hover Pause', 'sfwp-locale' ),
							'desc' => __( 'Hovering over slideshow will pause it', 'sfwp-locale' ),
							'id' => $shortname.'_slider_hover',
							'std' => 'false',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Animation Speed', 'sfwp-locale' ),
							'desc' => __( 'The time in <b>seconds</b> the animation between frames will take e.g. 0.6', 'sfwp-locale' ),
							'id' => $shortname.'_slider_speed',
							'std' => 0.6,
							'type' => 'select',
							'options' => array( '0.0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0' ) );
	
		$options[] = array( 'name' => __( 'Auto Slide Interval', 'sfwp-locale' ),
							'desc' => __( 'The time in <b>seconds</b> each slide pauses for, before sliding to the next. Only when using Auto Start option above.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_interval',
							'std' => '4',
							'type' => 'select',
							'options' => array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ) );
	
		$options[] = array( 'name' => __( 'Features Slider Effect', 'sfwp-locale' ),
							'desc' => __( 'Select the effect used when transitioning between posts (default: <strong>slide</strong>).', 'sfwp-locale' ),
							'id' => $shortname.'_slider_effect',
							'type' => 'select2',
							'std' => 'slide',
							'options' => array( 'slide' => __( 'Slide', 'sfwp-locale' ), 'fade' => __( 'Fade', 'sfwp-locale' )
							) );
	
		$options[] = array( 'name' => __( 'Slider Pagination', 'sfwp-locale' ),
							'desc' => __( 'Enable/disable the display of pagination in the sliders.', 'sfwp-locale' ),
							'id' => $shortname.'_slider_pagination',
							'std' => 'true',
							'type' => 'checkbox' );


		/**
		 * Media Settings
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Media Settings', 'sfwp-locale' ),
							'icon' => 'image',
							'type' => 'heading' );

		$options[] = array( 'name' => __( 'Image Placeholder', 'sfwp-locale' ),
							'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Image Placeholder', 'sfwp-locale' ),
							'desc' => __( 'Specify  a default image placeholder for your thumbnails, to use within the sf_image() function.', 'sfwp-locale' ),
							'id' => $shortname.'_default_image',
							'std' => '',
							'type' => 'upload' );
		
		$options[] = array( 'name' => __( 'Resizer Settings', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'WP Post Thumbnail', 'sfwp-locale' ),
							'desc' => __( 'Use WordPress post thumbnail to assign a post thumbnail. Will enable the <strong>Featured Image panel</strong> in your post sidebar where you can assign a post thumbnail.', 'sfwp-locale' ),
							'id' => $shortname.'_post_image_support',
							'std' => 'true',
							'class' => 'collapsed',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'WP Post Thumbnail - Dynamic Image Resizing', 'sfwp-locale' ),
							'desc' => __( 'The post thumbnail will be dynamically resized using native WP resize functionality. <em>(Requires PHP 5.2+)</em>', 'sfwp-locale' ),
							'id' => $shortname.'_pis_resize',
							'std' => 'true',
							'class' => 'hidden',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'WP Post Thumbnail - Hard Crop', 'sfwp-locale' ),
							'desc' => __( 'The post thumbnail will be cropped to match the target aspect ratio (only used if <em>Dynamic Image Resizing</em> is enabled).', 'sfwp-locale' ),
							'id' => $shortname.'_pis_hard_crop',
							'std' => 'true',
							'class' => 'hidden last',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Automatic Image Thumbnail', 'sfwp-locale' ),
							'desc' => __( 'If no thumbnail is specified then the first uploaded image in the post is used.', 'sfwp-locale' ),
							'id' => $shortname.'_auto_img',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Thumbnail Settings', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Thumbnail Dimensions', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 250 for the desired size which will be used when dynamically creating the images.', 'sfwp-locale' ),
							'id' => $shortname.'_image_dimensions',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_thumb_w',
									'type' => 'text',
									'std' => '100',
									'meta' => __( 'Width', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_thumb_h',
									'type' => 'text',
									'std' => 100,
									'meta' => __( 'Height', 'sfwp-locale' ) )
							  ));
		
		$options[] = array( 'name' => __( 'Thumbnail Alignment', 'sfwp-locale' ),
							'desc' => __( 'Select how to align your thumbnails with posts.', 'sfwp-locale' ),
							'id' => $shortname.'_thumb_align',
							'std' => 'alignleft',
							'type' => 'radio',
							'options' => array( 'alignleft' => 'Left', 'alignright' => 'Right', 'aligncenter' => 'Center' ) );
		
		$options[] = array( 'name' => __( 'Single Post - Show Thumbnail', 'sfwp-locale' ),
							'desc' => __( 'Show the thumbnail in the single post page.', 'sfwp-locale' ),
							'id' => $shortname.'_thumb_single',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Single Post - Thumbnail Dimensions', 'sfwp-locale' ),
							'desc' => __( 'Enter an integer value i.e. 250 for the image size.', 'sfwp-locale' ),
							'id' => $shortname.'_image_dimensions',
							'std' => '',
							'type' => array(
								array( 'id' => $shortname.'_single_w',
									'type' => 'text',
									'std' => 200,
									'meta' => __( 'Width', 'sfwp-locale' ) ),
								array( 'id' => $shortname.'_single_h',
									'type' => 'text',
									'std' => 200,
									'meta' => __( 'Height', 'sfwp-locale' ) )
							  ));
		
		$options[] = array( 'name' => __( 'Single Post - Thumbnail Alignment', 'sfwp-locale' ),
							'desc' => __( 'Select how to align your thumbnails with single posts.', 'sfwp-locale' ),
							'id' => $shortname.'_thumb_align_single',
							'std' => 'alignright',
							'type' => 'radio',
							'options' => array( 'alignleft' => 'Left', 'alignright' => 'Right', 'aligncenter' => 'Center' ) );
		
		
		$options[] = array( 'name' => __( 'Add Featured Image to RSS feed', 'sfwp-locale' ),
							'desc' => __( 'Add the featured image to your RSS feed', 'sfwp-locale' ),
							'id' => $shortname.'_rss_thumb',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Enable Lightbox', 'sfwp-locale' ),
							'desc' => __( 'Enable the PrettyPhoto lightbox script on images within your website\'s content.', 'sfwp-locale' ),
							'id' => $shortname.'_enable_lightbox',
							'std' => 'false',
							'type' => 'checkbox' );


		/**
		 * Subscribe & Connect Settings
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Subscribe & Connect', 'sfwp-locale' ),
							'type' => 'heading',
							'icon' => 'connect' );
	
		$options[] = array( 'name' => __( 'S&C Setup', 'sfwp-locale' ),
						'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Enable Subscribe & Connect - Single Post', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Enable the subscribe & connect area on single posts. You can also add this as a <a href="%s">widget</a> in your sidebar.', 'sfwp-locale' ), admin_url( 'widgets.php' ) ),
							'id' => $shortname.'_connect',
							'std' => 'true',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Subscribe Title', 'sfwp-locale' ),
							'desc' => __( 'Enter the title to show in your subscribe & connect area.', 'sfwp-locale' ),
							'id' => $shortname.'_connect_title',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Text', 'sfwp-locale' ),
							'desc' => __( 'Change the default text in this area.', 'sfwp-locale' ),
							'id' => $shortname.'_connect_content',
							'std' => '',
							'type' => 'textarea' );
	
		$options[] = array( 'name' => __( 'Enable Related Posts', 'sfwp-locale' ),
							'desc' => __( 'Enable related posts in the subscribe area. Uses posts with the same <strong>tags</strong> to find related posts. Note: Will not show in the Subscribe widget.', 'sfwp-locale' ),
							'id' => $shortname.'_connect_related',
							'std' => 'true',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Subscribe', 'sfwp-locale' ),
						'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Subscribe By E-mail ID (Feedburner)', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Enter your <a href="%s">Feedburner ID</a> for the e-mail subscription form.', 'sfwp-locale' ), 'http://starjive.com/wordpress/themes/light-framework/documentation/' ),
							'id' => $shortname.'_connect_newsletter_id',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Subscribe By E-mail to MailChimp', 'sfwp-locale' ),
							'desc' => sprintf( __( 'If you have a MailChimp account you can enter the <a href="%s" target="_blank">MailChimp List Subscribe URL</a> to allow your users to subscribe to a MailChimp List.', 'sfwp-locale' ), 'http://starjive.com/wordpress/themes/light-framework/documentation/' ),
							'id' => $shortname.'_connect_mailchimp_list_url',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Connect', 'sfwp-locale' ),
						'type' => 'subheading' );
	
		$options[] = array( 'name' => __( 'Enable RSS', 'sfwp-locale' ),
							'desc' => __( 'Enable the subscribe and RSS icon.', 'sfwp-locale' ),
							'id' => $shortname.'_connect_rss',
							'std' => 'true',
							'type' => 'checkbox' );
	
		$options[] = array( 'name' => __( 'Twitter URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your  <a href="http://www.twitter.com/">Twitter</a> URL e.g. http://www.twitter.com/starjive', 'sfwp-locale' ),
							'id' => $shortname.'_connect_twitter',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Facebook URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your  <a href="http://www.facebook.com/">Facebook</a> URL e.g. http://www.facebook.com/starjive', 'sfwp-locale' ),
							'id' => $shortname.'_connect_facebook',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'YouTube URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your  <a href="http://www.youtube.com/">YouTube</a> URL e.g. http://www.youtube.com/starjive', 'sfwp-locale' ),
							'id' => $shortname.'_connect_youtube',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Flickr URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your  <a href="http://www.flickr.com/">Flickr</a> URL e.g. http://www.flickr.com/starjive', 'sfwp-locale' ),
							'id' => $shortname.'_connect_flickr',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'LinkedIn URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your  <a href="http://www.www.linkedin.com.com/">LinkedIn</a> URL e.g. http://www.linkedin.com/in/starjive', 'sfwp-locale' ),
							'id' => $shortname.'_connect_linkedin',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Delicious URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your <a href="http://www.delicious.com/">Delicious</a> URL e.g. http://www.delicious.com/starjive', 'sfwp-locale' ),
							'id' => $shortname.'_connect_delicious',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Google+ URL', 'sfwp-locale' ),
							'desc' => __( 'Enter your <a href="http://plus.google.com/">Google+</a> URL e.g. https://plus.google.com/104560124403688998123/', 'sfwp-locale' ),
							'id' => $shortname.'_connect_googleplus',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Dribbble', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Enter your %1$s URL e.g. http://dribbble.com/starjive', 'sfwp-locale' ), '<a href="http://dribbble.com/">'.__( 'Dribbble', 'sfwp-locale' ).'</a>' ),
							'id' => $shortname.'_connect_dribbble',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Instagram', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Enter your %1$s URL e.g. http://instagram.com/starjive', 'sfwp-locale' ), '<a href="http://instagram.com">'.__( 'Instagram', 'sfwp-locale' ).'</a>' ),
							'id' => $shortname.'_connect_instagram',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Vimeo', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Enter your %1$s URL e.g. http://vimeo.com/starjive', 'sfwp-locale' ), '<a href="http://vimeo.com/">'.__( 'Vimeo', 'sfwp-locale' ).'</a>' ),
							'id' => $shortname.'_connect_vimeo',
							'std' => '',
							'type' => 'text' );
	
		$options[] = array( 'name' => __( 'Pinterest', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Enter your %1$s URL e.g. http://pinterest.com/starjive', 'sfwp-locale' ), '<a href="http://pinterest.com/">'.__( 'Pinterest', 'sfwp-locale' ).'</a>' ),
							'id' => $shortname.'_connect_pinterest',
							'std' => '',
							'type' => 'text' );


		/**
		 * Template: Contact Page
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Contact Page', 'sfwp-locale' ),
							'icon' => 'maps',
							'type' => 'heading' );
		
		$options[] = array( 'name' => __( 'Contact Information', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Contact Information Panel', 'sfwp-locale' ),
							'desc' => __( 'Enable the contact information panel on your contact page template.', 'sfwp-locale' ),
							'id' => $shortname.'_contact_panel',
							'std' => 'false',
							'class' => 'collapsed',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Location Name', 'sfwp-locale' ),
							'desc' => __( 'Enter the location name. Example: London Office', 'sfwp-locale' ),
							'id' => $shortname.'_contact_title',
							'std' => '',
							'class' => 'hidden',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Location Address', 'sfwp-locale' ),
							'desc' => __( 'Enter your company\'s address', 'sfwp-locale' ),
							'id' => $shortname.'_contact_address',
							'std' => '',
							'class' => 'hidden',
							'type' => 'textarea' );
		
		$options[] = array( 'name' => __( 'Telephone', 'sfwp-locale' ),
							'desc' => __( 'Enter your telephone number', 'sfwp-locale' ),
							'id' => $shortname.'_contact_number',
							'std' => '',
							'class' => 'hidden',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Fax', 'sfwp-locale' ),
							'desc' => __( 'Enter your fax number', 'sfwp-locale' ),
							'id' => $shortname.'_contact_fax',
							'std' => '',
							'class' => 'hidden last',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Contact Form E-Mail', 'sfwp-locale' ),
							'desc' => __( 'Enter your E-mail address to use on the "Contact Form" page Template.', 'sfwp-locale' ),
							'id' => $shortname.'_contactform_email',
							'std' => '',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Enable Subscribe and Connect', 'sfwp-locale' ),
							'desc' => __( 'Enable the subscribe and connect functionality on the contact page template', 'sfwp-locale' ),
							'id' => $shortname.'_contact_subscribe_and_connect',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Maps', 'sfwp-locale' ),
							'type' => 'subheading' );

		$options[] = array( 'name' => __( 'Google Maps API Key', 'sfwp-locale' ),
							'desc' => __( 'This API key can be obtained from the <a href="https://console.developers.google.com" target="_blank">Google Developers Console</a>. Our <a href="http://starjive.com/wordpress/themes/light-framework/documentation/creating-a-google-maps-api-key/" target="_blank">documentation</a> provides a full guide on how to obtain this.', 'sfwp-locale' ),
							'id' => $shortname.'_maps_api_key',
							'std' => '',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Contact Form Google Maps Coordinates', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Enter your Google Map coordinates to display a map on the Contact Form page template. You can get these details from %sGoogle Maps%s', 'sfwp-locale' ), '<a href="' . esc_url( 'http://itouchmap.com/latlong.html' ) . '" target="_blank">', '</a>' ),
							'id' => $shortname.'_contactform_map_coords',
							'std' => '',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Disable Mousescroll', 'sfwp-locale' ),
							'desc' => __( 'Turn off the mouse scroll action for all the Google Maps on the site. This could improve usability on your site.', 'sfwp-locale' ),
							'id' => $shortname.'_maps_scroll',
							'std' => '',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Map Height', 'sfwp-locale' ),
							'desc' => __( 'Height in pixels for the maps displayed on Single.php pages.', 'sfwp-locale' ),
							'id' => $shortname.'_maps_single_height',
							'std' => '250',
							'type' => 'text' );
		
		$options[] = array( 'name' => __( 'Default Map Zoom Level', 'sfwp-locale' ),
							'desc' => __( 'Set this to adjust the default in the post & page edit backend.', 'sfwp-locale' ),
							'id' => $shortname.'_maps_default_mapzoom',
							'std' => '9',
							'type' => 'select2',
							'options' => $other_entries);
		
		$options[] = array( 'name' => __( 'Default Map Type', 'sfwp-locale' ),
							'desc' => __( 'Set this to the default rendered in the post backend.', 'sfwp-locale' ),
							'id' => $shortname.'_maps_default_maptype',
							'std' => 'G_NORMAL_MAP',
							'type' => 'select2',
							'options' => array( 'G_NORMAL_MAP' => __( 'Normal', 'sfwp-locale' ),
								'G_SATELLITE_MAP' => __( 'Satellite', 'sfwp-locale' ),
								'G_HYBRID_MAP' => __( 'Hybrid', 'sfwp-locale' ),
								'G_PHYSICAL_MAP' => __( 'Terrain', 'sfwp-locale' )
							) );

		$options[] = array( 'name' => __( 'Default Map Maker Color', 'sfwp-locale' ),
							'desc' => __( 'Pick a custom color for map marker.', 'sfwp-locale' ),
							'id' => $shortname.'_cat_colors_pages',
							'type' => 'select2',
							'std' => 'red',
							'options' => array( 'black' => __( 'Black', 'sfwp-locale' ), 'blue' => __( 'Blue', 'sfwp-locale' ), 'green' => __( 'Green', 'sfwp-locale' ), 'pink' => __( 'Pink', 'sfwp-locale' ), 'purple' => __( 'Purple', 'sfwp-locale' ), 'red' => __( 'Red', 'sfwp-locale' ), 'teal' => __( 'Teal', 'sfwp-locale' ), 'white' => __( 'White', 'sfwp-locale' ), 'yellow' => __( 'Yellow', 'sfwp-locale' )
							) );

		$options[] = array( 'name' => __( 'Map Callout Text', 'sfwp-locale' ),
							'desc' => __( 'Text or HTML that will be output when you click on the map marker for your location.', 'sfwp-locale' ),
							'id' => $shortname.'_maps_callout_text',
							'std' => '',
							'type' => 'textarea' );




		/**
		 * WordPress - Backend & frontend changes.
		 *
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'WordPress', 'sfwp-locale' ),
							'icon' => 'wordpress',
							'type' => 'heading' );
							
		$options[] = array( 'name' => __( 'Login logo', 'sfwp-locale' ),
							'desc' => __( 'Change the logo image for the WordPress login page.', 'sfwp-locale' ),
							'id' => $shortname.'_custom_login_logo',
							'std' => '',
							'type' => 'upload' );
									
		$options[] = array( 'name' => __( 'WordPress Tweaks', 'sfwp-locale' ),
							'type' => 'subheading' );
		
		$options[] = array( 'name' => __( 'Customizer', 'sfwp-locale' ),
							'desc' => __( 'Remove customizer options in backend Appearance.', 'sfwp-locale' ),
							'id' => $shortname.'_wp_remove_customize_page',
							'std' => 'true',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Toolbar', 'sfwp-locale' ),
							'desc' => __( 'Remove items from frontend WordPress Toolbar.', 'sfwp-locale' ),
							'id' => $shortname.'_wp_remove_toolbar_items',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Emojis', 'sfwp-locale' ),
							'desc' => __( 'Remove the extra code bloat used to add support for emoji\'s in older browswers.', 'sfwp-locale' ),
							'id' => $shortname.'_wp_remove_emojis',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'DNS Prefetch', 'sfwp-locale' ),
							'desc' => __( 'Remove DNS Prefetch. DNS prefetching is an attempt to resolve domain names before a user tries to follow a link.', 'sfwp-locale' ),
							'id' => $shortname.'_wp_remove_dns_prefetch',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'REST API', 'sfwp-locale' ),
							'desc' => __( 'Remove api.w.org REST API from WordPress header.', 'sfwp-locale' ),
							'id' => $shortname.'_wp_remove_rest_api',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'canonical URLs', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Remove canonical URLs from WordPress header. Simple explanation of the purpose of canonical URLs <a href="%s" target="_blank">here</a>.', 'sfwp-locale' ), 'https://support.google.com/webmasters/answer/139066?hl=en' ),
							'id' => $shortname.'_wp_remove_canonical_urls',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Shortlink', 'sfwp-locale' ),
							'desc' => __( '', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Remove Shortlink from WordPress header. Shortlink is a shorten version of a web pages URL. This is where an SEO-friendly URL (that might have keywords in), is shorted to just a few characters after the domain name in URL. More about shortlinks over <a href="%s" target="_blank">here</a>.', 'sfwp-locale' ), 'http://microformats.org/wiki/rel-shortlink' ),
							'id' => $shortname.'_wp_remove_shortlink',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Really Simple Discovery', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Remove support for <a href="%s" target="_blank">Really Simple Discovery</a> (RSD). If you start having trouble with a 3rd party service (Example Flickr API) that updates your blog, don\'t remove it.', 'sfwp-locale' ), 'https://en.wikipedia.org/wiki/Really_Simple_Discovery' ),
							'id' => $shortname.'_wp_remove_rsd',
							'std' => 'false',
							'type' => 'checkbox' );
							
		$options[] = array( 'name' => __( 'Windows Live Writer', 'sfwp-locale' ),
							'desc' => __( '', 'sfwp-locale' ),
							'desc' => sprintf( __( 'Remove support for <a href="%s" target="_blank">Windows Live Writer</a> (WLW) from WordPress header.', 'sfwp-locale' ), 'https://en.wordpress.com/windows-live-writer/' ),
							'id' => $shortname.'_wp_remove_wlw',
							'std' => 'false',
							'type' => 'checkbox' );


		/**
		 * Cookies
		 * allows you to elegantly inform users that your site uses cookies and to comply with the EU cookie law regulations.
		 * @since	1.0
		 * @return	void
		 */
		$options[] = array( 'name' => __( 'Cookies', 'sfwp-locale' ),
							'icon' => 'cookies',
							'type' => 'heading' );
	
		$options[] = array( 'name' => __( 'Cookie', 'sfwp-locale' ),
							'desc' => '',
							'id' => $shortname.'_cookie_banner_notice',
							'std' => sprintf( __( 'Allows you to elegantly inform users that your site uses cookies and to comply with the cookie law regulations. Please refer to <a href="%s">documentation</a>.' ), 'http://starjive.com/wordpress/themes/light-framework/documentation/' ),
							'type' => 'info' );
									
		$options[] = array( 'name' => __( 'Notification', 'sfwp-locale' ),
							'desc' => __( 'Enable the cookie notification.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_banner_visibility',
							'std' => 'false',
							'type' => 'checkbox' );

		$options[] = array( 'name' => __( 'Message', 'sfwp-locale' ),
							'desc' => __( 'Enter the cookie message.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_message',
							'std' => __( 'We use cookies to ensure that we give you the best experience on our website. If you continue to use this site we will assume that you are happy with it.', 'sfwp-locale' ),
							'type' => 'textarea' );
							
		$options[] = array( 'name' => __( 'Accept text', 'sfwp-locale' ),
							'desc' => __( 'The text of the button to accept the usage of the cookies and make the notification disappear.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_accept_button',
							'std' => 'Okay, thanks',
							'class' => 'hidden',
							'type' => 'text' );
									
		$options[] = array( 'name' => __( 'Read more', 'sfwp-locale' ),
							'desc' => __( 'Enable the read more button.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_policy_button',
							'std' => 'false',
							'type' => 'checkbox' );
		
		$options[] = array( 'name' => __( 'Read more button', 'sfwp-locale' ),
							'desc' => __( 'The default text to use to link to a page providing further information about your cookie policy.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_policy_button_content',
							//'std' => 'Read more',
							'std' => __( 'Read more', 'sfwp-locale' ),
							'class' => 'hidden',
							'type' => 'text' );


		$options[] = array( 'name' => __( 'Page link', 'sfwp-locale' ),
							'desc' => __( 'The page containing further information about your cookie policy.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_policy_page_link',
							'type' => 'select2',
							'options' => array( '1' => __( 'Choose a page', 'sfwp-locale' ), '2' => __( 'Cookie page', 'sfwp-locale' ), '3' => __( 'Random page', 'sfwp-locale' ) )
							);

		$options[] = array( 'name' => __( 'Link target', 'sfwp-locale' ),
							'desc' => __( 'Select the link target for more info page.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_policy_link_target',
							'std' => '_self',
							'type' => 'select2',
							'options' => array( '_blank' => __( 'New page tab', 'sfwp-locale' ), '_self' => __( 'Current page tab', 'sfwp-locale' ) )
							);
							
		$options[] = array( 'name' => __( 'Cookie expiry', 'sfwp-locale' ),
							'desc' => __( 'The ammount of time that cookie should be stored for.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_expiry',
							'std' => '31536000',
							'type' => 'select2',
							'options' => array( '86400' => __( '1 day', 'sfwp-locale' ), '604800' => __( '1 week', 'sfwp-locale' ), '2592000' => __( '1 month', 'sfwp-locale' ), '7862400' => __( '3 months', 'sfwp-locale' ), '15811200' => __( '6 months', 'sfwp-locale' ), '31536000' => __( '1 year', 'sfwp-locale' ), '31337313373' => __( 'infinity', 'sfwp-locale' ) )
							);
							
		$options[] = array( 'name' => __( 'Cookie version', 'sfwp-locale' ),
							'desc' => __( 'A version number for the cookie - update this to invalidate the cookie and force all users to view the notification again.', 'sfwp-locale' ),
							'id' => $shortname.'_cookie_version',
							'std' => '1',
							'type' => 'text' );


		/**
		 * Plugin: WooCommerce
		 *
		 * @since	1.0
		 * @return	void
		 */
		if ( is_woocommerce_activated() ) {
		
			$options[] = array( 'name' => __( 'WooCommerce', 'sfwp-locale' ),
								'icon' => 'woocommerce',
								'type' => 'heading' );
		
			$options[] = array( 'name' => __( 'WooCommerce Page Layout', 'sfwp-locale' ),
								'desc' => __( 'Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'sfwp-locale' ),
								'id' => $shortname.'_woocommerce_layout',
								'std' => 'one-col',
								'type' => 'images',
								'options' => array( 'one-col' => $img_layouts_dir . 'layout-main-1c.png',
									'two-col-left' => $img_layouts_dir . 'layout-main-2cl.png',
									'two-col-right' => $img_layouts_dir . 'layout-main-2cr.png',
									'three-col-left' => $img_layouts_dir . 'layout-main-3cl.png',
									'three-col-middle' => $img_layouts_dir . 'layout-main-3cm.png',
									'three-col-right' => $img_layouts_dir . 'layout-main-3cr.png')
								);
			$options[] = array( 'name' => __( 'Search scope', 'sfwp-locale' ),
								'desc' => __( 'Select whether you want the search widget to search for products or posts', 'sfwp-locale' ),
								'id' => $shortname.'_header_search_scope',
								'type' => 'select2',
								'options' => array( 'products' => __( 'Products', 'sfwp-locale' ), 'posts' => __( 'Posts', 'sfwp-locale' ) ) );
		
			$options[] = array( 'name' => __( 'Custom Placeholder', 'sfwp-locale' ),
								'desc' => __( 'Upload a custom placeholder to be displayed when there is no product image.', 'sfwp-locale' ),
								'id' => $shortname.'_placeholder_url',
								'std' => '',
								'type' => 'upload' );
		
			$options[] = array( 'name' => __( 'Header Cart Link', 'sfwp-locale' ),
								'desc' => __( 'Display a link to the cart in the main navigation', 'sfwp-locale' ),
								'id' => $shortname.'_header_cart_link',
								'std' => 'true',
								'type' => 'checkbox' );
		
			$options[] = array( 'name' => __( 'Header Cart Totals', 'sfwp-locale' ),
								'desc' => __( 'Display item and amount totals in the cart in the main navigation', 'sfwp-locale' ),
								'id' => $shortname.'_header_cart_total',
								'std' => 'false',
								'type' => 'checkbox' );

			$options[] = array( 'name' => __( 'Shop page', 'sfwp-locale' ),
								'type' => 'subheading' );

			$options[] = array( 'name' => __( 'Add to Cart', 'sfwp-locale' ),
								'desc' => __( 'Removes "Add to Cart" button to "Go to Product" in the shop page', 'sfwp-locale' ),
								'id' => $shortname.'_wc_remove_add_to_cart',
								'std' => 'false',
								'type' => 'checkbox' );

			$options[] = array( 'name' => __( 'Orderby Dropdown', 'sfwp-locale' ),
								'desc' => __( 'Removes the Orderby Dropdown', 'sfwp-locale' ),
								'id' => $shortname.'_wc_remove_orderby_dropdown',
								'std' => 'false',
								'type' => 'checkbox' );

			$options[] = array( 'name' => __( 'Showing Results', 'sfwp-locale' ),
								'desc' => __( 'Removes showing results product page', 'sfwp-locale' ),
								'id' => $shortname.'_wc_remove_showing_results',
								'std' => 'false',
								'type' => 'checkbox' );

			$options[] = array( 'name' => __( 'Product page', 'sfwp-locale' ),
								'type' => 'subheading' );

			$options[] = array( 'name' => __( 'Meta', 'sfwp-locale' ),
								'desc' => __( 'Remove product meta single page', 'sfwp-locale' ),
								'id' => $shortname.'_wc_remove_meta',
								'std' => 'false',
								'type' => 'checkbox' );

			$options[] = array( 'name' => __( 'Product Tabs', 'sfwp-locale' ),
								'desc' => __( 'Remove product data tabs from singel product page', 'sfwp-locale' ),
								'id' => $shortname.'_wc_remove_product_tabs',
								'std' => 'false',
								'type' => 'checkbox' );

			$options[] = array( 'name' => __( 'Related Product', 'sfwp-locale' ),
								'desc' => __( 'Remove product related from singel product page', 'sfwp-locale' ),
								'id' => $shortname.'_wc_remove_related_products',
								'std' => 'false',
								'type' => 'checkbox' );
		}


		/**
		 * Plugin: Projects
		 *
		 * @since	1.0
		 * @return	void
		 */
		if ( class_exists( 'Projects' ) ) {
			$options[] = array( 'name' => __( 'Projects', 'sfwp-locale' ),
								'type' => 'heading' );
		
			$options[] = array( 'name' => __( 'Simplify Layout', 'sfwp-locale' ),
								'desc' => __( 'This enables simply slim simplifying layout.', 'sfwp-locale' ),
								'id' => $shortname.'_simplify_layout',
								'std' => 'false',
								'type' => 'checkbox' );
		
			$options[] = array( 'name' => __( 'Galleries Page Layout', 'sfwp-locale' ),
								'desc' => __( 'Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'sfwp-locale' ),
								'id' => $shortname.'_projects_layout',
								'std' => 'two-col-left',
								'type' => 'images',
								'options' => array( 'one-col' => $img_layouts_dir . 'layout-main-1c.png',
									'two-col-left' => $img_layouts_dir . 'layout-main-2cl.png',
									'two-col-right' => $img_layouts_dir . 'layout-main-2cr.png',
									'three-col-left' => $img_layouts_dir . 'layout-main-3cl.png',
									'three-col-middle' => $img_layouts_dir . 'layout-main-3cm.png',
									'three-col-right' => $img_layouts_dir . 'layout-main-3cr.png')
								);
		
			$options[] = array( 'name' => __( 'Single Project Layout', 'sfwp-locale' ),
								'desc' => __( 'Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'sfwp-locale' ),
								'id' => $shortname.'_projects_layout_single',
								'std' => 'two-col-left',
								'type' => 'images',
								'options' => array(
									'one-col' => $img_layouts_dir . 'layout-main-1c.png',
									'two-col-left' => $img_layouts_dir . 'layout-main-2cl.png',
									'two-col-right' => $img_layouts_dir . 'layout-main-2cr.png',
									'three-col-left' => $img_layouts_dir . 'layout-main-3cl.png',
									'three-col-middle' => $img_layouts_dir . 'layout-main-3cm.png',
									'three-col-right' => $img_layouts_dir . 'layout-main-3cr.png'
								) );
		
		}


		/**
		 * Add extra options through function.
		 *
		 * @since	1.0
		 * @return	void
		 */
		if ( function_exists( 'sf_options_add' ) )
			$options = sf_options_add( $options );
		
		if ( get_option( 'sf_template' ) != $options) update_option( 'sf_template', $options );
		if ( get_option( 'sf_themename' ) != $themename) update_option( 'sf_themename', $themename );
		if ( get_option( 'sf_shortname' ) != $shortname) update_option( 'sf_shortname', $shortname );
		if ( get_option( 'sf_manual' ) != $manualurl) update_option( 'sf_manual', $manualurl );
		
		// Metabox Options
		$sf_metaboxes = array();
		
		if( get_post_type() == 'post' || !get_post_type() ){
		
			$sf_metaboxes[] = array ( 'name' => 'layout', 'label' => __( 'Layout', 'sfwp-locale' ),
										'type' => 'images',
										'desc' => __( 'Select a specific layout for this post/page. Overrides default site layout.', 'sfwp-locale' ),
										'options' => array(
											'' => $img_layouts_dir . 'layout-off.png',
											'one-col' => $img_layouts_dir . 'layout-main-1c.png',
											'two-col-left' => $img_layouts_dir . 'layout-main-2cl.png',
											'two-col-right' => $img_layouts_dir . 'layout-main-2cr.png',
											'three-col-left' => $img_layouts_dir . 'layout-main-3cl.png',
											'three-col-middle' => $img_layouts_dir . 'layout-main-3cm.png',
											'three-col-right' => $img_layouts_dir . 'layout-main-3cr.png'
										));
		
			$sf_metaboxes[] = array ( 'name' => 'embed', 'label' => __( 'Embed', 'sfwp-locale' ),
										'type' => 'textarea',
										'desc' => __( 'Enter embed code for use on single posts and with the Video widget.', 'sfwp-locale' ) );
		} // End post
		
		if( get_post_type() == 'slide' || ! get_post_type() ) {
		
			$sf_metaboxes[] = array ( 'name' => 'url', 'label' => __( 'URL', 'sfwp-locale' ),
										'type' => 'text',
										'desc' => __( 'Enter URL if you want to add a link to the uploaded image. (optional)', 'sfwp-locale' ) );
		
		} // End slide
		
		// Page fields.
		if( get_post_type() == 'page' || ! get_post_type() ) {
		
			// Create an array of the available "Slide Groups".
			$slide_pages = array( '0' => __( 'All', 'sfwp-locale' )
								);
		
			$terms = get_terms( 'slide-page' );
		
			if ( is_array( $terms ) && ( count( $terms ) > 0 ) ) {
				foreach ( $terms as $k => $v ) {
					$slide_pages[ $v->term_id ] = $v->name;
				}
			}
		
			$sf_metaboxes[] = array ( 'name' => '_slide-page',
				'std' => '', 'label' => __( 'Slide Group', 'sfwp-locale' ),
				'type' => 'select2',
				'desc' => __( 'Optionally select a "Slide Group" to show slides from only that "Slide Group".', 'sfwp-locale' ),
				'options' => $slide_pages );
		
		} // End slide
		
		// Show layout option on all pages
		if ( get_post_type() != 'post' && get_post_type() != 'slide' ) {
		
			$sf_metaboxes[] = array ( 'name' => 'layout', 'label' => __( 'Layout', 'sfwp-locale' ),
				'type' => 'images',
				'desc' => __( 'Select a specific layout for this post/page. Overrides default site layout.', 'sfwp-locale' ),
				'options' => array(
					'' => $img_layouts_dir . 'layout-off.png',
					'one-col' => $img_layouts_dir . 'layout-main-1c.png',
					'two-col-left' => $img_layouts_dir . 'layout-main-2cl.png',
					'two-col-right' => $img_layouts_dir . 'layout-main-2cr.png',
					'three-col-left' => $img_layouts_dir . 'layout-main-3cl.png',
					'three-col-middle' => $img_layouts_dir . 'layout-main-3cm.png',
					'three-col-right' => $img_layouts_dir . 'layout-main-3cr.png'
				));
		}
		
		// Add extra metaboxes through function
		if ( function_exists( 'sf_metaboxes_add' ) )
			$sf_metaboxes = sf_metaboxes_add($sf_metaboxes);
		
		if ( get_option( 'sf_custom_template') != $sf_metaboxes) update_option( 'sf_custom_template',$sf_metaboxes);

	} // END sf_options()
} // END function_exists()

// Add options to admin_head
add_action( 'admin_head', 'sf_options' );

//Global options setup
add_action( 'init', 'sf_global_options' );
function sf_global_options(){
	// Populate option in array for use in theme
	global $sf_options;
	$sf_options = get_option( 'sf_options' );
}

?>