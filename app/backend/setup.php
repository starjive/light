<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

	/**
	 * Setup.
	 *
	 * All functionality used for setting up the Framework.
	 *
	 *
	 * TABLE OF CONTENTS
	 *
	 * - Redirect to the Framework options screen on theme activation.
	 * - Flush the WordPress rewrite rules to refresh permalinks with updated rewrite rules.
	 * - Optionally add markup in the header of the WordPress admin.
	 * - Output the default Framework "head" markup in the "head" section.
	 * 		- Output alternative stylesheet
	 *   	- Output custom favicon
	 *    	- Load textdomains
	 *     	- Output CSS from standarized styling options
	 * - Output the alternative stylesheet.
	 * - Output the favicon HTML.
	 * - Load the theme's textdomain, as well as an optional child theme textdomain.
	 * - Output CSS from standardized theme options.
	 * - Add theme support for post thumbnails, and integrate the size settings.
	 * - Enqueue the comment reply JavaScript on singular entry screens.
	 *
	 * @category 	Backend
	 * @since		1.0
	 */
	define( 'THEME_FRAMEWORK', 'sf' );


	/**
	 * Redirect to the Framework theme options screen on theme activation.
	 * Hooked onto "sf_theme_activate" at priority 10.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_themeoptions_redirect' ) ) {
		function sf_themeoptions_redirect () {
			header( 'Location: ' . admin_url( 'admin.php?page=sf-about&activated=true' ) );
		} // End sf_themeoptions_redirect()
	}
	add_action( 'sf_theme_activate', 'sf_themeoptions_redirect', 10 );


	/**
	 * Flush the WordPress rewrite rules to refresh permalinks with updated rewrite rules.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_flush_rewriterules' ) ) {
		function sf_flush_rewriterules () {
			flush_rewrite_rules();
		} // End sf_flush_rewriterules()
	}


	/**
	 * Add default options and show Options Panel after activate
	 * @since	1.0
	 */
	global $pagenow;
	if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
		// Call action that sets.
		add_action( 'admin_head','sf_option_setup' );
		// Flush rewrite rules.
		add_action( 'admin_head', 'sf_flush_rewriterules', 9 );
		// Custom action for theme-setup (redirect is at priority 10).
		do_action( 'sf_theme_activate' );
	}


	/**
	 * Update theme options in database with options as stored in theme.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_option_setup' ) ) {
		function sf_option_setup () {
			//Update EMPTY options
			$sf_array = array();
			add_option( 'sf_options', $sf_array );

			$sf_array = SF()->settings->get_all();

			// Allow child themes/plugins to filter here.
			$sf_array = apply_filters( 'sf_options_array', $sf_array );
			update_option( 'sf_options', $sf_array );
		} // End sf_option_setup()
	}


	/**
	 * Optionally add markup in the header of the WordPress admin.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_admin_head' ) ) {
		function sf_admin_head() {} // End sf_admin_head()
		}
	add_action( 'admin_head', 'sf_admin_head', 10 );


	/**
	 * Output the default Framework "head" markup in the "head" section.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_wp_head' ) ) {
		function sf_wp_head() {
			do_action( 'sf_wp_head_before' );
			// Output alternative stylesheet
			if ( function_exists( 'sf_output_alt_stylesheet' ) )
				sf_output_alt_stylesheet();
			// Output custom favicon
			if ( function_exists( 'sf_output_custom_favicon' ) )
				sf_output_custom_favicon();
			// Output custom touch icon
			if ( function_exists( 'sf_output_custom_touch_icon' ) )
				sf_output_custom_touch_icon();
			// Output CSS from standarized styling options
			if ( function_exists( 'sf_head_css' ) )
				sf_head_css();
			// Output fontawesome.css
			if ( function_exists( 'sf_output_fontawesome_css' ) )
				sf_output_fontawesome_css();
			do_action( 'sf_wp_head_after' );
		} // End sf_wp_head()
	}
	add_action( 'wp_head', 'sf_wp_head', 10 );


	/**
	 * Output the alternative stylesheet.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_output_alt_stylesheet' ) ) {
		function sf_output_alt_stylesheet() {
			$style = '';
		
			if ( isset( $_REQUEST['style'] ) ) {
				$requested_style = esc_attr( strtolower( strip_tags( trim( $_REQUEST['style'] ) ) ) );
				$style = $requested_style;
			}
		
			echo "\n" . '<!-- Alt Stylesheet -->' . "\n";
			// If we're using the query variable, be sure to check for /css/layout.css as well.
			if ( $style != '' ) {
				if ( strtolower( $style ) == 'default' ) {
					if ( file_exists( get_template_directory() . '/css/layout.css' ) ) {
						echo '<link href="' . esc_url( get_template_directory_uri() . '/css/layout.css' ) . '" rel="stylesheet" type="text/css" />' . "\n";
					}
					echo '<link href="' . esc_url( get_stylesheet_uri() ) . '" rel="stylesheet" type="text/css" />' . "\n";
				} else {
					echo '<link href="' . esc_url( get_template_directory_uri() . '/styles/' . $style . '.css' ) . '" rel="stylesheet" type="text/css" />' . "\n";
				}
			} else {
				$style = get_option( 'sf_alt_stylesheet' );
				$style = esc_attr( strtolower( strip_tags( trim( $style ) ) ) );
				if( $style != '' ) {
					echo '<link href="'. esc_url( get_template_directory_uri() . '/styles/'. $style ) . '" rel="stylesheet" type="text/css" />' . "\n";
				} else {
					echo '<link href="'. esc_url( get_template_directory_uri() . '/styles/default.css' ) . '" rel="stylesheet" type="text/css" />' . "\n";
				}
			}
		} // End sf_output_alt_stylesheet()
	}


	/**
	 * Output the favicon HTML.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_output_custom_favicon' ) ) {
		function sf_output_custom_favicon () {
			$favicon = get_option( 'sf_custom_favicon', '' );
			if ( is_ssl() ) $favicon = str_replace( 'http://', 'https://', $favicon );
			$favicon = apply_filters( 'sf_custom_favicon', $favicon );
			if( '' != $favicon ) echo "\n" . '<!-- Custom Favicon -->' . "\n" . '<link rel="shortcut icon" href="' .  esc_url( $favicon )  . '">' . "\n";
		} // End sf_output_custom_favicon()
	}


	/**
	 * Output the touch icon HTML.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_output_custom_touch_icon' ) ) {
		function sf_output_custom_touch_icon () {
			$touchicon = get_option( 'sf_custom_touch_icon', '' );
			if ( is_ssl() ) $touchicon = str_replace( 'http://', 'https://', $touchicon );
			$touchicon = apply_filters( 'sf_custom_touch_icon', $touchicon );
			if( '' != $touchicon ) echo "\n"
			. '<!-- Custom Touch Icon -->' . "\n"
			. '<link rel="apple-touch-icon" href="' .  esc_url( $touchicon )  . '">' . "\n"
			. '<link rel="apple-touch-icon" sizes="76x76" href="' .  esc_url( $touchicon )  . '">' . "\n"
			. '<link rel="apple-touch-icon" sizes="120x120" href="' .  esc_url( $touchicon )  . '">' . "\n"
			. '<link rel="apple-touch-icon" sizes="152x152" href="' .  esc_url( $touchicon )  . '">' . "\n"
			. '<link rel="apple-touch-icon-precomposed" sizes="180x180" href="' .  esc_url( $touchicon )  . '">' . "\n";
		} // End sf_output_custom_touch_icon()
	}


	/**
	 * Load the theme's textdomain, as well as an optional child theme textdomain.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_load_textdomain' ) ) {
		function sf_load_textdomain () {
			load_theme_textdomain( 'sfwp-locale' );
			load_theme_textdomain( 'sfwp-locale', get_template_directory() . '/app/languages' );
			if ( function_exists( 'load_child_theme_textdomain' ) )
				load_child_theme_textdomain( 'sfwp-locale' );
		} // End sf_load_textdomain()
	}
	add_action( 'after_setup_theme', 'sf_load_textdomain', 10 );


	/**
	 * Output CSS from standardized theme options.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_head_css' ) ) {
		function sf_head_css () {
			$output = '';
			$text_title = get_option( 'sf_texttitle' );
			$tagline = get_option( 'sf_tagline' );
			$custom_css = get_option( 'sf_custom_css' );
			$template = get_option( 'sf_template' );
			if ( is_array( $template ) ) {
				foreach( $template as $option ) {
					if( isset( $option['id'] ) ) {
						if( $option['id'] == 'sf_texttitle' ) {
							// Add CSS to output
							if ( $text_title == 'true' ) {
								$output .= '#logo img { display:none; } .site-title { display:block !important; }' . "\n";
								if ( $tagline == 'false' )
									$output .= '.site-description { display:none !important; }' . "\n";
								else
									$output .= '.site-description { display:block !important; }' . "\n";
							}
						}
					}
				}
			}

			if ( '' != $custom_css ) {
				// Prepare the custom CSS code for output.
				$custom_css = strip_tags( $custom_css );
				$custom_css = html_entity_decode( $custom_css );
				$output .= $custom_css . "\n";
			}

			// Output styles
			if ( $output != '' ) {
				$output = strip_tags($output);
				echo '<!-- Options Panel Custom CSS -->' . "\n";
				$output = "<style type=\"text/css\">\n" . $output . "</style>\n\n";
				echo stripslashes( $output );
			}
		} // End sf_head_css()
	}


	/**
	 * Output the HTML for the CDN "font-awesome.min.css" file.
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_output_fontawesome_css' ) ) {
		function sf_output_fontawesome_css() {
			// If "fontawesome.css" exists in the parent theme, load it.
				echo "\n" . '<!-- Font Awesome -->' . "\n" . '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" type="text/css" />' . "\n";
		} // End sf_output_fontawesome_css()
	}


	/**
	 * Add theme support for post thumbnails, and integrate the size settings.
	 * @since	1.0
	 */
	if( function_exists( 'add_theme_support' ) ) {
		if( get_option( 'sf_post_image_support' ) == 'true' ) {
			add_theme_support( 'post-thumbnails' );
			// set height, width and crop if dynamic resize functionality isn't enabled
			if ( get_option( 'sf_pis_resize' ) != 'true' ) {
				$thumb_width = get_option( 'sf_thumb_w' );
				$thumb_height = get_option( 'sf_thumb_h' );
				$single_width = get_option( 'sf_single_w' );
				$single_height = get_option( 'sf_single_h' );
				$hard_crop = get_option( 'sf_pis_hard_crop' );
				if($hard_crop == 'true') { $hard_crop = true; } else { $hard_crop = false; }
				set_post_thumbnail_size( $thumb_width, $thumb_height, $hard_crop ); // Normal post thumbnails
				add_image_size( 'single-post-thumbnail', $single_width, $single_height, $hard_crop );
			}
		}
	}


	/**
	 * Enqueue the comment reply JavaScript on singular entry screens.
	 * @since	1.0
	 * @return	void
	 */
	add_action( 'get_header', 'sf_comment_reply', 10 );
	if ( ! function_exists( 'sf_comment_reply' ) ) {
		function sf_comment_reply() {
			if ( is_singular() && comments_open() ) wp_enqueue_script( 'comment-reply' );
		} // End sf_comment_reply()
	}

?>