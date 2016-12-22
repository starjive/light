<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

$sf_options = get_option( 'sf_options' ); 

// Check for and enqueue custom styles, if necessary.
add_action( 'sf_wp_head_before', 'sf_enqueue_custom_styling', 9 );

// Add layout to body_class output
add_filter( 'body_class','sf_layout_body_class', 10 );

// WooSlider Setup
add_action( 'sf_head', 'sf_slider', 10 );

// Navigation
add_action( 'sf_header_after', 'sf_nav', 10 );

// Primary Menu
add_action( 'sf_nav_inside', 'sf_nav_primary', 10 );

// Subscribe links in navigation
add_action( 'sf_nav_inside', 'sf_nav_subscribe', 25 );

// Search in navigation
add_action( 'sf_nav_inside', 'sf_nav_search', 25 );

// Side Navigation wrappers
add_action( 'sf_nav_inside', 'sf_nav_sidenav_start', 15 );
add_action( 'sf_nav_inside', 'sf_nav_sidenav_end', 30 );

// Conditionals
add_action( 'sf_head', 'sf_conditionals', 10 );

// Author Box
add_action( 'wp_head', 'sf_author', 10 );

// Single post navigation
add_action( 'sf_post_after', 'sf_postnav', 10 );

// Add Google Fonts output to HEAD
add_action( 'wp_head', 'sf_google_webfonts', 10 );

// Breadcrumbs
if ( isset( $sf_options['sf_breadcrumbs_show'] ) && $sf_options['sf_breadcrumbs_show'] == 'true' ) {
	add_action( 'sf_loop_before', 'sf_breadcrumbs', 10 );
}

// Subscribe & Connect
add_action( 'wp_head', 'sf_subscribe_connect_action', 10 );

// Optional Top Navigation (WP Menus)
add_action( 'sf_top', 'sf_top_navigation', 10 );

// Remove the banner warning about static home page
if ( is_admin() && current_user_can( 'manage_options' ) && ( 0 < intval( get_option( 'page_on_front' ) ) ) ) {
	remove_action( 'sf_container_inside', 'sf_add_static_front_page_banner' );
}


	/**
	 * Theme Setup
	 *
	 * This is the general theme setup, where we add_theme_support(), create global variables
	 * and setup default generic filters and actions to be used across our theme.
	 *
	 * Sets up theme defaults and registers support for various WordPress features and plugins.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook. The init hook is too late for some features, such as indicating
	 * support for post thumbnails.
	 *
	 * To override sf_setup() in a child theme, add your own sf_setup to your child theme's functions.php file.
	 *
	 * @uses add_theme_support() To add support for various features / plugins.
	 * @uses add_editor_style() To style the visual editor.
	 */
	if ( ! function_exists( 'sf_setup' ) ) {
		function sf_setup () {
	
			// Editor Styles
			if ( '' != locate_template( 'editor-style.css' ) ) {
				add_editor_style();
			}
	
			// This theme uses post thumbnails
			add_theme_support( 'post-thumbnails' );
	
			// Add default posts and comments RSS feed links to head
			add_theme_support( 'automatic-feed-links' );
	
			// Plugin Support
			add_theme_support( 'archives-by-woothemes' );
			add_theme_support( 'features-by-woothemes' );
			add_theme_support( 'our-team-by-woothemes' );
			add_theme_support( 'projects-by-woothemes' );
			add_theme_support( 'sensei' );
			add_theme_support( 'tesitmonials-by-woothemes' );
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wooslider' );
	
			// Custom Background
			add_theme_support( 'custom-background', apply_filters( 'sf_custom_background_args', array(
				'wp-head-callback' 		=> 'sf_custom_background_cb',
				'default-color'         => 'ffffff',
				) )
			);
	
			// Custom Header
			add_theme_support( 'custom-header', apply_filters( 'sf_custom_header_args', array(
				'default-text-color'     => 'fff',
				'width'                  => $custom_header_width,
				'height'                 => 200,
				'flex-height'            => true,
				'wp-head-callback'       => 'sf_custom_header_style',
				'admin-head-callback'    => 'sf_custom_admin_header_style',
				'admin-preview-callback' => 'sf_custom_admin_header_image',
				) )
			);
	
			// Menu Locations
			if ( function_exists( 'wp_nav_menu') ) {
				add_theme_support( 'nav-menus' );
				register_nav_menus(
					array(
						'primary-menu' 	=> __( 'Primary Menu', 'sfwp-locale' )
						)
					);
				register_nav_menus(
					array(
						'top-menu' 		=> __( 'Top Menu', 'sfwp-locale' )
						)
					);
			}
	
			// Set the content width based on the theme's design and stylesheet.
			if ( ! isset( $content_width ) ) {
				$content_width = 640;
			}
	
		} // End sf_setup()
	}
	add_action( 'after_setup_theme', 'sf_setup' );


	/**
	 * Custom Background Callback.
	 *
	 * Duplicated from wp-includes/theme.php until there's a better way to change the selector.
	 *
	 * @see _custom_background_cb()
	 * @since	1.0
	 */
	if ( ! function_exists( 'sf_custom_background_cb' ) ) {
		function sf_custom_background_cb() {
			// $background is the saved custom image, or the default image.
			$background 	= set_url_scheme( get_background_image() );
	
			// $color is the saved custom color.
			// A default has to be specified in style.css. It will not be printed here.
			$color 			= get_background_color();
	
			if ( $color === get_theme_support( 'custom-background', 'default-color' ) ) {
				$color = false;
			}
	
			if ( ! $background && ! $color )
				return;
	
			$style = $color ? "background-color: #$color;" : '';
	
			if ( $background ) {
				$image 		= " background-image: url('$background');";
				$repeat 	= get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
	
				if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) ) {
					$repeat = 'repeat';
				}
	
				$repeat 	= " background-repeat: $repeat;";
				$position 	= get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
	
				if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) ) {
					$position = 'left';
				}
	
				$position 	= " background-position: top $position;";
				$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
	
				if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) ) {
					$attachment = 'scroll';
				}
	
				$attachment = " background-attachment: $attachment;";
	
				$style .= $image . $repeat . $position . $attachment;
			}
			?>
			<style type="text/css" id="custom-background-css">
			body.custom-background { <?php echo trim( $style ); ?> }
			</style>
			<?php
		} // sf_custom_background_cb()
	}
	
	/**
	 * Styles the header image and text displayed on the blog
	 *
	 * @since	1.0
	 */
	if ( ! function_exists( 'sf_custom_header_style' ) ) {
		function sf_custom_header_style() {
			$text_color = get_header_textcolor();
	
			// If no custom color for text is set, let's bail.
			if ( display_header_text() && $text_color === get_theme_support( 'custom-header', 'default-text-color' ) )
				return;
	
			// If we get this far, we have custom styles.
			?>
			<style type="text/css" id="sf-header-css">
			<?php
				// Has the text been hidden?
				if ( ! display_header_text() ) :
			?>
				.site-title,
				.site-description {
					clip: rect(1px 1px 1px 1px); /* IE7 */
					clip: rect(1px, 1px, 1px, 1px);
					position: absolute;
				}
			<?php
				// If the user has set a custom color for the text, use that.
				elseif ( $text_color != get_theme_support( 'custom-header', 'default-text-color' ) ) :
			?>
				#logo .site-title a {
					color: #<?php echo esc_attr( $text_color ); ?>;
				}
			<?php endif; ?>
			</style>
			<?php
		} // sf_custom_header_style()
	}

/**
 * Style the header image displayed on the Appearance > Header screen.
 *
 * @since	1.0
 */
if ( ! function_exists( 'sf_custom_admin_header_style' ) ) {
	function sf_custom_admin_header_style() {
	?>
		<style type="text/css" id="sf-admin-header-css">
		.appearance_page_custom-header #headimg {
			border: none;
			max-width: 980px;
			min-height: 48px;
			padding: 40px 0;
		}
		#headimg h1 {
			font: bold 28px/1.2em "Helvetica Neue", Helvetica, sans-serif;
			color: #000;
			display: block;
			line-height: inherit;
			margin-bottom: 5px;
			font-weight: bold;
		}
		#headimg h1 a {
			font: bold 40px/1em "Helvetica Neue", Helvetica, sans-serif;
			color: #222222;
			text-decoration: none;
		}
		#headimg img {
			vertical-align: middle;
		}
		</style>
	<?php
	} // sf_custom_admin_header_style()
}

/**
 * Create the custom header image markup displayed on the Appearance > Header screen.
 *
 * @since	1.0
 */
if ( ! function_exists( 'sf_custom_admin_header_image' ) ) {
	function sf_custom_admin_header_image() {
	?>
		<div id="headimg" <?php if ( get_header_image() ) : ?>style="background-image:url(<?php echo header_image(); ?>);"<?php endif; ?>>
			<h1 class="displaying-header-text"><a id="name"<?php echo sprintf( ' style="color:#%s;"', get_header_textcolor() ); ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		</div>
	<?php
	} // sf_custom_admin_header_image()
}

/**
 * Output custom header image as a background.
 *
 * @since	1.0
 */

add_action( 'sf_wp_head_before', 'sf_custom_header_bg_output', 10 );

if ( ! function_exists( 'sf_custom_header_bg_output' ) ) {
	function sf_custom_header_bg_output() {
		if ( get_header_image() ) {
		?>
		<style type="text/css" id="sf-header-bg-css">
		#header { background-image:url(<?php echo header_image(); ?>); }
		</style>
		<?php
		}
	} // sf_custom_header_bg_output()
}


/*-----------------------------------------------------------------------------------*/
/* Conditionals */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_conditionals' ) ) {
	function sf_conditionals () {

		// Post More
		if ( ! is_singular() && ! is_404() || is_page_template( 'templates/template-blog.php' ) || is_page_template( 'templates/template-magazine.php' ) || is_page_template( 'templates/template-widgets.php' ) ) {
			add_action( 'sf_post_inside_after', 'sf_post_more' );
		}

	} // End sf_conditionals()
}

/*-----------------------------------------------------------------------------------*/
/* Load style.css in the <head> */
/*-----------------------------------------------------------------------------------*/

if ( ! is_admin() ) { add_action( 'wp_enqueue_scripts', 'sf_load_frontend_css', 4 ); }

if ( ! function_exists( 'sf_load_frontend_css' ) ) {
function sf_load_frontend_css () {

	// Assign the version to a var
	$theme 				= wp_get_theme();
	$sf_version 	= $theme['Version'];

	if ( is_rtl() ) {
		wp_register_style( 'theme-stylesheet', get_template_directory_uri() . '/app/frontend/assets/css/style.css', array(), $sf_version, 'all' );
		wp_enqueue_style( 'theme-stylesheet' );
		wp_register_style( 'theme-stylesheet-rtl', get_template_directory_uri() . '/app/frontend/assets/css/rtl.css', array(), $sf_version, 'all' );
		wp_enqueue_style( 'theme-stylesheet-rtl' );
	} else {
		wp_register_style( 'theme-stylesheet', get_template_directory_uri() . '/app/frontend/assets/css/style.css', array(), $sf_version, 'all' );
		wp_enqueue_style( 'theme-stylesheet' );
	}

} // End sf_load_frontend_css()
}

/*-----------------------------------------------------------------------------------*/
/* Load responsive <meta> tags in the <head> */
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_head', 'sf_load_responsive_meta_tags', 1 );

if ( ! function_exists( 'sf_load_responsive_meta_tags' ) ) {
function sf_load_responsive_meta_tags () {
	$html = '';

	/* Remove this if not responsive design */
	$html .= "\n" . '<!--  Small-screen viewport scale -->' . "\n";
	//$html .= '<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />' . "\n";
	//$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />' . "\n";
	$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />' . "\n";

	echo $html;
} // End sf_load_responsive_meta_tags()
}

/*-----------------------------------------------------------------------------------*/
/* // Add custom styling */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_custom_styling' ) ) {
function sf_custom_styling() {
	global $sf_options;

	$output = '';

	// Logo
	if ( isset( $sf_options['sf_logo'] ) && $sf_options['sf_logo'] ) $output .= '#logo .site-title, #logo .site-description { display:none; }' . "\n";

	// Check if we want to generate the custom styling or not.
	if ( ! isset( $sf_options['sf_style_disable'] ) ) {
		$sf_options['sf_style_disable'] = 'false';
	}

	if ( 'true' == $sf_options['sf_style_disable'] ) {
		// We still output the CSS related to the custom logo, if one exists.
		if ( '' != $output ) { echo $output; }
		return;
	}

	// Init options
	$options_init = array( 'sf_style_bg', 'sf_style_bg_image', 'sf_style_bg_image_repeat', 'sf_style_bg_image_pos', 'sf_style_bg_image_attach', 'sf_border_top', 'sf_style_border',
						   'sf_link_color', 'sf_link_hover_color', 'sf_button_color', 'sf_button_hover_color', 'sf_header_bg', 'sf_header_bg_image', 'sf_header_bg_image_repeat',
						   'sf_layout_content_padding_top', 'sf_layout_content_padding_right', 'sf_layout_content_padding_bottom', 'sf_layout_content_padding_left',
						   'sf_layout_content_mobile_padding_top', 'sf_layout_content_mobile_padding_right', 'sf_layout_content_mobile_padding_bottom', 'sf_layout_content_mobile_padding_left',
						   'sf_header_border', 'sf_header_margin_top', 'sf_header_margin_bottom', 'sf_header_padding_top', 'sf_header_padding_right', 'sf_header_padding_bottom', 'sf_header_padding_left',
						   
						   'sf_logo_width', 'sf_logo_margin_top', 'sf_logo_margin_bottom', 'sf_logo_color', 'sf_logo_hover_color',
						   
						   'sf_font_logo', 'sf_font_desc', 'sf_layout_boxed', 'sf_style_box_bg', 'sf_box_margin_top', 'sf_box_margin_bottom',
						   'sf_box_border_tb', 'sf_box_border_lr', 'sf_box_border_radius', 'sf_box_shadow', 'sf_full_header_full_width_bg', 'sf_full_header_bg_image',
						   'sf_full_header_bg_image_repeat', 'sf_nav_bg', 'sf_nav_divider_border', 'sf_nav_border_top', 'sf_nav_border_bot', 'sf_foot_full_width_widget_bg',
						   'sf_footer_full_width_bg', 'sf_footer_border_top', 'sf_font_text', 'sf_font_h1', 'sf_font_h2', 'sf_font_h3', 'sf_font_h4', 'sf_font_h5', 'sf_font_h6',
						   'sf_font_post_title', 'sf_font_post_meta', 'sf_font_post_text', 'sf_font_post_more', 'sf_post_more_border_top', 'sf_post_more_border_bottom',
						   'sf_post_comments_bg', 'sf_post_author_border_top', 'sf_post_author_border_bottom', 'sf_post_author_border_lr', 'sf_post_author_border_radius',
						   'sf_post_author_bg', 'sf_pagenav_font', 'sf_pagenav_bg', 'sf_pagenav_border_top', 'sf_pagenav_border_bottom', 'sf_widget_font_title',
						   'sf_widget_font_text', 'sf_widget_padding_tb', 'sf_widget_padding_lr', 'sf_footer_widget_bg', 'sf_footer_widget_border_top', 'sf_footer_widget_border_bottom', 'sf_widget_bg', 'sf_widget_border', 'sf_widget_title_border', 'sf_widget_border_radius',
						   'sf_widget_tabs_bg', 'sf_widget_tabs_bg_inside', 'sf_widget_tabs_font', 'sf_widget_tabs_font_meta', 'sf_nav_bg', 'sf_nav_font', 'sf_nav_hover', 'sf_nav_hover_bg',
						   'sf_nav_divider_border', 'sf_nav_dropdown_border', 'sf_nav_border_lr', 'sf_nav_border_radius', 'sf_nav_border_top', 'sf_nav_border_bot', 'sf_nav_margin_top',
						   'sf_nav_margin_bottom', 'sf_top_nav_bg', 'sf_top_nav_hover', 'sf_top_nav_hover_bg', 'sf_top_nav_font', 'sf_footer_font', 'sf_footer_link_color', 'sf_footer_link_hover_color', 'sf_footer_bg', 'sf_footer_border_top',
						   'sf_footer_border_bottom', 'sf_footer_border_lr', 'sf_footer_border_radius', 'sf_slider_magazine_font_title', 'sf_slider_magazine_font_excerpt', 'sf_magazine_grid_font_post_title',
						   'sf_slider_biz_font_title', 'sf_slider_biz_font_excerpt', 'sf_slider_biz_overlay', 'sf_archive_header_font', 'sf_archive_header_border_bottom'
						);

	foreach ( $options_init as $option ) {
		if ( isset( $sf_options[ $option ] ) ) {
			${ $option } = $sf_options[ $option ];
		} else {
			${ $option } = false;
		}
	}

		// Layout styling
		$body = '';
		if ($sf_style_bg)
			$body .= 'background-color:'.$sf_style_bg.';';
		if ($sf_style_bg_image)
			$body .= 'background-image:url('.$sf_style_bg_image.');';
		if ($sf_style_bg_image_repeat)
			$body .= 'background-repeat:'.$sf_style_bg_image_repeat.';';
		if ($sf_style_bg_image_pos)
			$body .= 'background-position:'.$sf_style_bg_image_pos.';';
		if ($sf_style_bg_image_attach)
			$body .= 'background-attachment:'.$sf_style_bg_image_attach.';';
		if ($sf_border_top && $sf_border_top['width'] >= 0)
			$body .= 'border-top:'.$sf_border_top["width"].'px '.$sf_border_top["style"].' '.$sf_border_top["color"].';';

		if ( $body != '' )
			$output .= 'body {'. $body . '}'. "\n";

		if ( $sf_style_border )
			$output .= 'hr, .entry img, img.thumbnail, .entry .wp-caption, #footer-widgets, #comments, #comments .comment.thread-even, #comments ul.children li, .entry h1{border-color:'. $sf_style_border . '}'. "\n";

		// Layout content
		if ( $sf_layout_content_padding_top <> '' || $sf_layout_content_padding_right <> '' || $sf_layout_content_padding_bottom <> '' || $sf_layout_content_padding_left <> '' )
			$output .= '@media only screen and (min-width: 768px) { .boxed-layout #header, .boxed-layout #content, .boxed-layout #footer-widgets, .boxed-layout #footer, .full-width #header, .full-width #content, .full-width #footer-widgets, .full-width #footer { padding-left: '.$sf_layout_content_padding_left.'em; padding-right: '.$sf_layout_content_padding_right.'em; } #content { padding-top: '.$sf_layout_content_padding_top.'em; padding-bottom: '.$sf_layout_content_padding_bottom.'em; } }'. "\n";
		if ( $sf_layout_content_mobile_padding_top <> '' || $sf_layout_content_mobile_padding_right <> '' || $sf_layout_content_mobile_padding_bottom <> '' || $sf_layout_content_mobile_padding_left <> '' )
			$output .= '@media only screen and (max-width: 767px) { .boxed-layout #header, .boxed-layout #content, .boxed-layout #footer-widgets, .boxed-layout #footer, .full-width #header, .full-width #content, .full-width #footer-widgets, .full-width #footer, #content { padding-left: '.$sf_layout_content_mobile_padding_left.'em; padding-right: '.$sf_layout_content_mobile_padding_right.'em; } #content { padding-top: '.$sf_layout_content_mobile_padding_top.'em; padding-bottom: '.$sf_layout_content_mobile_padding_bottom.'em; } }'. "\n";	

		// General styling
		if ($sf_link_color)
			$output .= 'a:link, a:visited, #loopedSlider a.flex-prev:hover, #loopedSlider a.flex-next:hover {color:'.$sf_link_color.'} .quantity .plus, .quantity .minus {background-color: ' . $sf_link_color . ';}' . "\n";
		if ($sf_link_hover_color)
			$output .= 'a:hover, .post-more a:hover, .post-meta a:hover, .post p.tags a:hover {color:'.$sf_link_hover_color.'}' . "\n";
		if ($sf_button_color)
			$output .= 'body #wrapper .button, body #wrapper #content .button, body #wrapper #content .button:visited, body #wrapper #content .reply a, body #wrapper #content #respond .form-submit input#submit, input[type=submit], body #wrapper #searchsubmit, #navigation ul.cart .button, body #wrapper .sf-sc-button {border: none; background:'.$sf_button_color.'}' . "\n";
		if ($sf_button_hover_color)
			$output .= 'body #wrapper .button:hover, body #wrapper #content .button:hover, body #wrapper #content .reply a:hover, body #wrapper #content #respond .form-submit input#submit:hover, input[type=submit]:hover, body #wrapper #searchsubmit:hover, #navigation ul.cart .button:hover, body #wrapper .sf-sc-button:hover {border: none; background:'.$sf_button_hover_color.'}' . "\n";
		
		// Header styling
		$header_css = '';
		if ( $sf_header_bg )
			$header_css .= 'background-color:'.$sf_header_bg.';';
		if ( $sf_header_bg_image )
			$header_css .= 'background-image:url('.$sf_header_bg_image.');';
		if ( $sf_header_bg_image_repeat )
			$header_css .= 'background-repeat:'.$sf_header_bg_image_repeat.';background-position:left top;';
		if ( $sf_header_margin_top <> '' || $sf_header_margin_bottom <> '' )
			$header_css .= 'margin-top:'.$sf_header_margin_top.'px;margin-bottom:'.$sf_header_margin_bottom.'px;';
		if ( $sf_header_padding_top <> '' || $sf_header_padding_bottom <> '' )
			$header_css .= 'padding-top:'.$sf_header_padding_top.'px;padding-bottom:'.$sf_header_padding_bottom.'px;';
		if ( $sf_header_border && $sf_header_border['width'] >= 0)
			$header_css .= 'border:'.$sf_header_border["width"].'px '.$sf_header_border["style"].' '.$sf_header_border["color"].';';
		if ( $header_css != '' )
			$output .= '#header {'. $header_css . '}'. "\n";

		if ( $sf_header_padding_left <> '' )
			$output .= '#logo {padding-left:'.$sf_header_padding_left.'px;}';
		if ( $sf_header_padding_right <> '' )
			$output .= '.header-widget {padding-right:'.$sf_header_padding_right.'px;}'. "\n";
		if ( $sf_font_logo )
			$output .= '#logo .site-title a {' . sf_generate_font_css( $sf_font_logo ) . '}' . "\n";
		if ( $sf_font_desc )
			$output .= '#logo .site-description {' . sf_generate_font_css( $sf_font_desc ) . '}' . "\n";

		// Boxed styling
		$wrapper = '';
		if ($sf_layout_boxed == "true") {
			//$wrapper .= 'margin:0 auto;padding:0 0 20px 0;width:'.get_option('sf_layout_width').';';
			if ( get_option('sf_layout_width') == '940px' )
				//$wrapper .= 'padding-left:20px; padding-right:20px;';
				$wrapper .= '';
			else
				//$wrapper .= 'padding-left:30px; padding-right:30px;';
				$wrapper .= '';
		}
		if ($sf_layout_boxed == "true" && $sf_style_box_bg)
			$wrapper .= 'background-color:'.$sf_style_box_bg.';';
		if ($sf_layout_boxed == "true" && ($sf_box_margin_top || $sf_box_margin_bottom) )
			$wrapper .= 'margin-top:'.$sf_box_margin_top.'px;margin-bottom:'.$sf_box_margin_bottom.'px;';
		if ($sf_layout_boxed == "true" && $sf_box_border_tb["width"] > 0 )
			$wrapper .= 'border-top:'.$sf_box_border_tb["width"].'px '.$sf_box_border_tb["style"].' '.$sf_box_border_tb["color"].';border-bottom:'.$sf_box_border_tb["width"].'px '.$sf_box_border_tb["style"].' '.$sf_box_border_tb["color"].';';
		if ($sf_layout_boxed == "true" && $sf_box_border_lr["width"] > 0 )
			$wrapper .= 'border-left:'.$sf_box_border_lr["width"].'px '.$sf_box_border_lr["style"].' '.$sf_box_border_lr["color"].';border-right:'.$sf_box_border_lr["width"].'px '.$sf_box_border_lr["style"].' '.$sf_box_border_lr["color"].';';
		if ( $sf_layout_boxed == "true" && $sf_box_border_radius )
			$wrapper .= 'border-radius:'.$sf_box_border_radius.';';
		if ( $sf_layout_boxed == "true" && $sf_box_shadow == "true" )
			$wrapper .= 'box-shadow: 0px 1px 5px rgba(0,0,0,.1);';

		if ( $wrapper != '' )
			$output .= '#inner-wrapper {'. $wrapper . '} .col-full { width: auto; } @media only screen and (max-width:767px) { #inner-wrapper { margin:0; border-radius:none; border: none; } } '. "\n";

		// Full width layout
		if ( $sf_layout_boxed != "true" && (isset( $sf_options['sf_header_full_width'] ) && ( $sf_options['sf_header_full_width']  == 'true'  )  ||  isset( $sf_options['sf_footer_full_width'] ) && ( $sf_options['sf_footer_full_width'] == 'true' ) ) ) {

			if ( isset( $sf_options['sf_header_full_width'] ) && $sf_options['sf_header_full_width'] == 'true' ) {

				if ( $sf_full_header_full_width_bg )
					$output .= '#header-container{background-color:' . $sf_full_header_full_width_bg . ';}';

				if ( $sf_full_header_bg_image )
					$output .= '#header-container{background-image:url('.$sf_full_header_bg_image.');background-repeat:'.$sf_full_header_bg_image_repeat.';background-position:top center;}';

				if ( $sf_nav_bg )
					$output .= '#nav-container{background:' . $sf_nav_bg . ';}';

				if ( $sf_nav_border_top && $sf_nav_border_top["width"] >= 0 )
					$output .= '#nav-container{border-top:'.$sf_nav_border_top["width"].'px '.$sf_nav_border_top["style"].' '.$sf_nav_border_top["color"].';border-bottom:'.$sf_nav_border_bot["width"].'px '.$sf_nav_border_bot["style"].' '.$sf_nav_border_bot["color"].';border-left:none;border-right:none;}';

				if ( $sf_nav_divider_border && $sf_nav_divider_border["width"] >= 0 )
					$output .= '#nav-container #navigation ul#main-nav > li:first-child{border-left: '.$sf_nav_divider_border["width"].'px '.$sf_nav_divider_border["style"].' '.$sf_nav_divider_border["color"].';}';

			}

			if ( isset( $sf_options['sf_footer_full_width'] ) && ( 'true' == $sf_options['sf_footer_full_width'] ) ) {

				if ( $sf_foot_full_width_widget_bg )
					$output .= '#footer-widgets-container{background-color:' . $sf_foot_full_width_widget_bg . '}#footer-widgets{border:none;}';

				if ( $sf_footer_full_width_bg )
					$output .= '#footer-container{background-color:' . $sf_footer_full_width_bg . '}';

				if ( $sf_footer_border_top && $sf_footer_border_top["width"] >= 0 )
					$output .= '#footer-container{border-top:'.$sf_footer_border_top["width"].'px '.$sf_footer_border_top["style"].' '.$sf_footer_border_top["color"].';}#footer {border-width: 0 !important;}';

			}
			$output .= "\n";

		}

		// General Typography
		if ( $sf_font_text )
			$output .= 'body, p { ' . sf_generate_font_css( $sf_font_text, 1.5 ) . ' }' . "\n";
		if ( $sf_font_h1 )
			$output .= 'h1 { ' . sf_generate_font_css( $sf_font_h1, 1.2 ) . ' }';
		if ( $sf_font_h2 )
			$output .= 'h2 { ' . sf_generate_font_css( $sf_font_h2, 1.2 ) . ' }';
		if ( $sf_font_h3 )
			$output .= 'h3 { ' . sf_generate_font_css( $sf_font_h3, 1.2 ) . ' }';
		if ( $sf_font_h4 )
			$output .= 'h4 { ' . sf_generate_font_css( $sf_font_h4, 1.2 ) . ' }';
		if ( $sf_font_h5 )
			$output .= 'h5 { ' . sf_generate_font_css( $sf_font_h5, 1.2 ) . ' }';
		if ( $sf_font_h6 )
			$output .= 'h6 { ' . sf_generate_font_css( $sf_font_h6, 1.2 ) . ' }' . "\n";

		// Post Styling
		if ( $sf_font_post_title )
			$output .= '.page-title, .post .title, .page .title {'.sf_generate_font_css( $sf_font_post_title, 1.1 ).'}' . "\n";
			$output .= '.post .title a:link, .post .title a:visited, .page .title a:link, .page .title a:visited {color:'.$sf_font_post_title["color"].'}' . "\n";
		if ( $sf_font_post_meta )
			$output .= '.post-meta { ' . sf_generate_font_css( $sf_font_post_meta, 1.5 ) . ' }' . "\n";
		if ( $sf_font_post_text )
			$output .= '.entry, .entry p{ ' . sf_generate_font_css( $sf_font_post_text, 1.5 ) . ' }' . "\n";
		$post_more_border = '';
		if ( $sf_font_post_more )
			$post_more_border .= 'font:'.$sf_font_post_more["style"].' '.$sf_font_post_more["size"].$sf_font_post_more["unit"].'/1.5em '.stripslashes($sf_font_post_more["face"]).';color:'.$sf_font_post_more["color"].';';
		if ( $sf_post_more_border_top )
			$post_more_border .= 'border-top:'.$sf_post_more_border_top["width"].'px '.$sf_post_more_border_top["style"].' '.$sf_post_more_border_top["color"].';';
		if ( $sf_post_more_border_bottom )
			$post_more_border .= 'border-bottom:'.$sf_post_more_border_bottom["width"].'px '.$sf_post_more_border_bottom["style"].' '.$sf_post_more_border_bottom["color"].';';
		if ( $post_more_border )
		$output .= '.post-more {'.$post_more_border .'}' . "\n";

		$post_author = '';
		if ( $sf_post_author_border_top )
			$post_author .= 'border-top:'.$sf_post_author_border_top["width"].'px '.$sf_post_author_border_top["style"].' '.$sf_post_author_border_top["color"].';';
		if ( $sf_post_author_border_bottom )
			$post_author .= 'border-bottom:'.$sf_post_author_border_bottom["width"].'px '.$sf_post_author_border_bottom["style"].' '.$sf_post_author_border_bottom["color"].';';
		if ( $sf_post_author_border_lr )
			$post_author .= 'border-left:'.$sf_post_author_border_lr["width"].'px '.$sf_post_author_border_lr["style"].' '.$sf_post_author_border_lr["color"].';border-right:'.$sf_post_author_border_lr["width"].'px '.$sf_post_author_border_lr["style"].' '.$sf_post_author_border_lr["color"].';';
		if ( $sf_post_author_border_radius )
			$post_author .= 'border-radius:'.$sf_post_author_border_radius.';-moz-border-radius:'.$sf_post_author_border_radius.';-webkit-border-radius:'.$sf_post_author_border_radius.';';
		if ( $sf_post_author_bg )
			$post_author .= 'background-color:'.$sf_post_author_bg;

		if ( $post_author )
			$output .= '#post-author, #connect {'.$post_author .'}' . "\n";

		if ( $sf_post_comments_bg )
			$output .= '#comments .comment.thread-even {background-color:'.$sf_post_comments_bg.';}' . "\n";

		// Page Nav Styling
		$pagenav_css = '';
		if ( $sf_pagenav_bg )
			$pagenav_css .= 'background-color:'.$sf_pagenav_bg.';';
		if ( $sf_pagenav_border_top && $sf_pagenav_border_top["width"] > 0 )
			$pagenav_css .= 'border-top:'.$sf_pagenav_border_top["width"].'px '.$sf_pagenav_border_top["style"].' '.$sf_pagenav_border_top["color"].';';
		if ( $sf_pagenav_border_bottom && $sf_pagenav_border_bottom["width"] > 0 )
			$pagenav_css .= 'border-bottom:'.$sf_pagenav_border_bottom["width"].'px '.$sf_pagenav_border_bottom["style"].' '.$sf_pagenav_border_bottom["color"].';';
		if ( $pagenav_css != '' )
			$output .= '.nav-entries, .sf-pagination {'. $pagenav_css . ' padding: 12px 0px; }'. "\n";
		if ( $sf_pagenav_font ) {
			$output .= '.nav-entries a, .sf-pagination { ' . sf_generate_font_css( $sf_pagenav_font ) . ' }' . "\n";
			$output .= '.sf-pagination a, .sf-pagination a:hover {color:'.$sf_pagenav_font["color"].'!important}' . "\n";
		}

		// Widget Styling
		$h3_css = '';
		if ( $sf_widget_font_title )
			$h3_css .= 'font:'.$sf_widget_font_title["style"].' '.$sf_widget_font_title["size"].$sf_widget_font_title["unit"].'/1.2em '.stripslashes($sf_widget_font_title["face"]).';color:'.$sf_widget_font_title["color"].';';
		if ( $sf_widget_title_border )
			$h3_css .= 'border-bottom:'.$sf_widget_title_border["width"].'px '.$sf_widget_title_border["style"].' '.$sf_widget_title_border["color"].';';
		if ( isset( $sf_widget_title_border["width"] ) AND $sf_widget_title_border["width"] == 0 )
			$h3_css .= 'margin-bottom:0;';

		if ( $h3_css != '' )
			$output .= '.widget h3 {'. $h3_css . '}'. "\n";

		if ( $sf_widget_title_border )
			$output .= '.widget_recent_comments li, #twitter li { border-color: '.$sf_widget_title_border["color"].';}'. "\n";

		if ( $sf_widget_font_text )
			$output .= '.widget p, .widget .textwidget { ' . sf_generate_font_css( $sf_widget_font_text, 1.5 ) . ' }' . "\n";

		$widget_css = '';
		if ( $sf_widget_font_text )
			$widget_css .= 'font:'.$sf_widget_font_text["style"].' '.$sf_widget_font_text["size"].$sf_widget_font_text["unit"].'/1.5em '.stripslashes($sf_widget_font_text["face"]).';color:'.$sf_widget_font_text["color"].';';
		if ( $sf_widget_padding_tb || $sf_widget_padding_lr )
			$widget_css .= 'padding:'.$sf_widget_padding_tb.'px '.$sf_widget_padding_lr.'px;';
		if ( $sf_widget_bg )
			$widget_css .= 'background-color:'.$sf_widget_bg.';';
		if ( $sf_widget_border["width"] > 0 )
			$widget_css .= 'border:'.$sf_widget_border["width"].'px '.$sf_widget_border["style"].' '.$sf_widget_border["color"].';';
		if ( $sf_widget_border_radius )
			$widget_css .= 'border-radius:'.$sf_widget_border_radius.';-moz-border-radius:'.$sf_widget_border_radius.';-webkit-border-radius:'.$sf_widget_border_radius.';';

		if ( $widget_css != '' )
			$output .= '.widget {'. $widget_css . '}'. "\n";

		if ( $sf_widget_border["width"] > 0 )
			$output .= '#tabs {border:'.$sf_widget_border["width"].'px '.$sf_widget_border["style"].' '.$sf_widget_border["color"].';}'. "\n";

		if ( $sf_footer_widget_bg )
			$output .= '#footer-widgets {background-color:'.$sf_footer_widget_bg.'}' . "\n";
			
		if ( $sf_footer_widget_border_top )
			$output .= '#footer-widgets {border-top:'.$sf_footer_widget_border_top["width"].'px '.$sf_footer_widget_border_top["style"].' '.$sf_footer_widget_border_top["color"].'}' . "\n";
			
		if ( $sf_footer_widget_border_bottom )
			$output .= '#footer-widgets {border-bottom:'.$sf_footer_widget_border_bottom["width"].'px '.$sf_footer_widget_border_bottom["style"].' '.$sf_footer_widget_border_bottom["color"].'}' . "\n";

		// Logotype
		if ( $sf_logo_width )
			$output .= '#logo .logotype { width:'.$sf_logo_width.'px; }' . "\n";
		if ( $sf_logo_margin_top <> '' || $sf_logo_margin_bottom <> '' )
			$output .= '#logo .logotype { margin-top:'.$sf_logo_margin_top.'em;margin-bottom:'.$sf_logo_margin_bottom.'em; }' . "\n";
		if ( $sf_nav_hover )
			$output .= '#logo .logotype path { fill:'.$sf_logo_color.'; }' . "\n";
		if ( $sf_nav_hover )
			$output .= '#logo .logotype:hover path { fill:'.$sf_logo_hover_color.'; }' . "\n";

		// Navigation
		global $is_IE;
		if ( !$is_IE )
			$output .= '@media only screen and (min-width:768px) {' . "\n";
		if ( $sf_nav_font )
			$output .= 'ul.nav li a, #navigation ul.rss a, #navigation ul.cart a.cart-contents, #navigation .cart-contents #navigation ul.rss, #navigation ul.nav-search, #navigation ul.nav-search a { ' . sf_generate_font_css( $sf_nav_font, 1.2 ) . ' } #navigation ul.rss li a:before, #navigation ul.nav-search a.search-contents:before { color:' . $sf_nav_font['color'] . ';}' . "\n";
		if ( $sf_nav_hover )
			$output .= '#navigation ul.nav > li a:hover, #navigation ul.nav > li:hover a, #navigation ul.nav li ul li a, #navigation ul.cart > li:hover > a, #navigation ul.cart > li > ul > div, #navigation ul.cart > li > ul > div p, #navigation ul.cart > li > ul span, #navigation ul.cart .cart_list a, #navigation ul.nav li.current_page_item a, #navigation ul.nav li.current_page_parent a, #navigation ul.nav li.current-menu-ancestor a, #navigation ul.nav li.current-cat a, #navigation ul.nav li.current-menu-item a { color:'.$sf_nav_hover.'!important; }' . "\n";
		if ( $sf_nav_hover_bg )
			$output .= '#navigation ul.nav > li a:hover, #navigation ul.nav > li:hover, #navigation ul.nav li ul, #navigation ul.cart li:hover a.cart-contents, #navigation ul.nav-search li:hover a.search-contents, #navigation ul.nav-search a.search-contents + ul, #navigation ul.cart a.cart-contents + ul, #navigation ul.nav li.current_page_item a, #navigation ul.nav li.current_page_parent a, #navigation ul.nav li.current-menu-ancestor a, #navigation ul.nav li.current-cat a, #navigation ul.nav li.current-menu-item a{background-color:'.$sf_nav_hover_bg.'!important}' . "\n";

		if ( $sf_nav_dropdown_border && $sf_nav_dropdown_border["width"] >= 0 ) {
			$output .= '#navigation ul.nav li ul, #navigation ul.cart > li > ul > div  { border: '.$sf_nav_dropdown_border["width"].'px '.$sf_nav_dropdown_border["style"].' '.$sf_nav_dropdown_border["color"].'; }' . "\n";
			if ($sf_nav_dropdown_border["width"] == 0) {
				$output .= '#navigation ul.nav > li:hover > ul  { left: 0; }' . "\n";
			}
		}

		if ( $sf_nav_divider_border && $sf_nav_divider_border["width"] >= 0 ) {
			$output .= '#navigation ul.nav > li  { border-right: '.$sf_nav_divider_border["width"].'px '.$sf_nav_divider_border["style"].' '.$sf_nav_divider_border["color"].'; }';
			if ($sf_nav_divider_border["width"] == 0) {
				$output .= '#navigation ul.nav > li:hover > ul  { left: 0; }' . "\n";
			}
		}

		$navigation_css = '';
		if ( $sf_nav_bg )
			$navigation_css .= 'background:'.$sf_nav_bg.';';
		if ( $sf_nav_border_top && $sf_nav_border_top["width"] >= 0 )
			$navigation_css .= 'border-top:'.$sf_nav_border_top["width"].'px '.$sf_nav_border_top["style"].' '.$sf_nav_border_top["color"].';border-bottom:'.$sf_nav_border_bot["width"].'px '.$sf_nav_border_bot["style"].' '.$sf_nav_border_bot["color"].';border-left:'.$sf_nav_border_lr["width"].'px '.$sf_nav_border_lr["style"].' '.$sf_nav_border_lr["color"].';border-right:'.$sf_nav_border_lr["width"].'px '.$sf_nav_border_lr["style"].' '.$sf_nav_border_lr["color"].';';
		if ( $sf_nav_border_bot && $sf_nav_border_bot["width"] == 0 )
			$output .= '#navigation { box-shadow: none; -moz-box-shadow: none; -webkit-box-shadow: none; }';
		if ( $sf_nav_border_radius )
			$navigation_css .= 'border-radius:'.$sf_nav_border_radius.'; -moz-border-radius:'.$sf_nav_border_radius.'; -webkit-border-radius:'.$sf_nav_border_radius.';';

		if ( $sf_nav_border_radius )
			$output .= '#navigation ul li:first-child, #navigation ul li:first-child a { border-radius:'.$sf_nav_border_radius.' 0 0 '.$sf_nav_border_radius.'; -moz-border-radius:'.$sf_nav_border_radius.' 0 0 '.$sf_nav_border_radius.'; -webkit-border-radius:'.$sf_nav_border_radius.' 0 0 '.$sf_nav_border_radius.'; }' . "\n";

		if ( '' != $sf_nav_margin_top  || '' != $sf_nav_margin_bottom ) {
			if ( isset( $sf_options[ 'sf_header_full_width' ] ) && 'true' == $sf_options[ 'sf_header_full_width' ]  ) {
				$navigation_css .= 'margin-top:0;margin-bottom:0;';
				$output .= '#nav-container { margin-top:'.$sf_nav_margin_top.'px;margin-bottom:'.$sf_nav_margin_bottom.'px; }';
			} else {
				$navigation_css .= 'margin-top:'.$sf_nav_margin_top.'px;margin-bottom:'.$sf_nav_margin_bottom.'px;';
			}
		}

		if ( $navigation_css != '' )
			$output .= '#navigation {'. $navigation_css . '}'. "\n";

		if ( $sf_top_nav_bg )
			$output .= '#top, #top ul.nav li ul li a:hover { background:'.$sf_top_nav_bg.';}'. "\n";

		if ( $sf_top_nav_hover )
			$output .= '#top ul.nav li a:hover, #top ul.nav li.current_page_item a, #top ul.nav li.current_page_parent a,#top ul.nav li.current-menu-ancestor a,#top ul.nav li.current-cat a,#top ul.nav li.current-menu-item a,#top ul.nav li.sfHover, #top ul.nav li ul, #top ul.nav > li:hover a, #top ul.nav li ul li a { color:'.$sf_top_nav_hover.'!important;}'. "\n";

		if ( $sf_top_nav_hover_bg )
			$output .= '#top ul.nav li a:hover, #top ul.nav li.current_page_item a, #top ul.nav li.current_page_parent a,#top ul.nav li.current-menu-ancestor a,#top ul.nav li.current-cat a,#top ul.nav li.current-menu-item a,#top ul.nav li.sfHover, #top ul.nav li ul, #top ul.nav > li:hover { background:'.$sf_top_nav_hover_bg.';}'. "\n";

		if ( $sf_top_nav_font ) {
			$output .= '#top ul.nav li a { ' . sf_generate_font_css( $sf_top_nav_font, 1.6 ) . ' }' . "\n";
			if ( isset( $sf_top_nav_font['color'] ) && strlen( $sf_top_nav_font['color'] ) == 7 ) {
				$output .= '#top ul.nav li.parent > a:after { border-top-color:'. esc_attr( $sf_top_nav_font['color'] ) .';}'. "\n";
			}
		}
		if ( !$is_IE )
			$output .= '}' . "\n";

		// Footer
		if ( $sf_footer_font )
			$output .= '#footer, #footer p { ' . sf_generate_font_css( $sf_footer_font, 1.4 ) . ' }' . "\n";
		if ( $sf_footer_link_color )
			$output .= '#footer a:link, #footer a:visited  {color:'.$sf_footer_link_color.'}' . "\n";
		if ( $sf_footer_link_hover_color )
			$output .= '#footer a:hover {color:'.$sf_footer_link_hover_color.'}' . "\n";
		$footer_css = '';
		if ( $sf_footer_bg )
			$footer_css .= 'background-color:'.$sf_footer_bg.';';
		if ( $sf_footer_border_top )
			$footer_css .= 'border-top:'.$sf_footer_border_top["width"].'px '.$sf_footer_border_top["style"].' '.$sf_footer_border_top["color"].';';
		if ( $sf_footer_border_bottom )
			$footer_css .= 'border-bottom:'.$sf_footer_border_bottom["width"].'px '.$sf_footer_border_bottom["style"].' '.$sf_footer_border_bottom["color"].';';
		if ( $sf_footer_border_lr )
			$footer_css .= 'border-left:'.$sf_footer_border_lr["width"].'px '.$sf_footer_border_lr["style"].' '.$sf_footer_border_lr["color"].';border-right:'.$sf_footer_border_lr["width"].'px '.$sf_footer_border_lr["style"].' '.$sf_footer_border_lr["color"].';';
		if ( $sf_footer_border_radius )
			$footer_css .= 'border-radius:'.$sf_footer_border_radius.'; -moz-border-radius:'.$sf_footer_border_radius.'; -webkit-border-radius:'.$sf_footer_border_radius.';';

		if ( $footer_css != '' )
			$output .= '#footer {'. $footer_css . '}' . "\n";

		// Magazine Template
		if ( $sf_slider_magazine_font_title ) {
			$output .= '.magazine #loopedSlider .content h2.title a { ' . sf_generate_font_css( $sf_slider_magazine_font_title ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-magazine .slide-title a { ' . sf_generate_font_css( $sf_slider_magazine_font_title ) . ' }'. "\n";
		}
		if ( $sf_slider_magazine_font_excerpt ) {
			$output .= '.magazine #loopedSlider .content .excerpt p { ' . sf_generate_font_css( $sf_slider_magazine_font_excerpt, 1.5 ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-magazine .slide-content p, .wooslider-theme-magazine .slide-excerpt p { ' . sf_generate_font_css( $sf_slider_magazine_font_excerpt, 1.5 ) . ' }'. "\n";

		}
		if ( $sf_magazine_grid_font_post_title ) {
			$output .= '.magazine .block .post .title a {' . sf_generate_font_css( $sf_magazine_grid_font_post_title, 1.2 ) . ' }'. "\n";
		}

		// Business Template
		if ( $sf_slider_biz_font_title ) {
			$output .= '#loopedSlider.business-slider .content h2 { ' . sf_generate_font_css( $sf_slider_biz_font_title ) . ' }'. "\n";
			$output .= '#loopedSlider.business-slider .content h2.title a { ' . sf_generate_font_css( $sf_slider_biz_font_title ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-business .has-featured-image .slide-title { ' . sf_generate_font_css( $sf_slider_biz_font_title ) . ' }'. "\n";
			$output .= '.wooslider-theme-business .has-featured-image .slide-title a { ' . sf_generate_font_css( $sf_slider_biz_font_title ) . ' }'. "\n";
		}
		if ( $sf_slider_biz_font_excerpt ) {
			$output .= '#wrapper #loopedSlider.business-slider .content p { ' . sf_generate_font_css( $sf_slider_biz_font_excerpt, 1.5 ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-business .has-featured-image .slide-content p { ' . sf_generate_font_css( $sf_slider_biz_font_excerpt, 1.5 ) . ' }'. "\n";
			$output .= '.wooslider-theme-business .has-featured-image .slide-excerpt p { ' . sf_generate_font_css( $sf_slider_biz_font_excerpt, 1.5 ) . ' }'. "\n";

		}

		// Slider overlay
		if ( $sf_slider_biz_overlay == 'left' || $sf_slider_biz_overlay == 'right' || $sf_slider_biz_overlay == 'center' || $sf_slider_biz_overlay == 'full' || $sf_slider_biz_overlay == 'none' ) {
			$output .= '@media only screen and (min-width:768px) {' . "\n";
			if ( $sf_slider_biz_overlay && $sf_slider_biz_overlay == 'left' )
				$output .= '#wrapper #loopedSlider.business-slider .content { width: 40%; top: 2.5em; bottom: inherit; left:0; right: inherit; text-align: left; }'. "\n";
			if ( $sf_slider_biz_overlay && $sf_slider_biz_overlay == 'right' )
				$output .= '#wrapper #loopedSlider.business-slider .content { width: 40%; top: 2.5em; bottom: inherit; right:0; left: inherit; text-align: right; }'. "\n";
			if ( $sf_slider_biz_overlay && $sf_slider_biz_overlay == 'center' )
				$output .= '#wrapper #loopedSlider.business-slider .content { width: 50%; top: 20%; bottom: inherit; }'. "\n";
			if ( $sf_slider_biz_overlay && $sf_slider_biz_overlay == 'full' )
				$output .= '#wrapper #loopedSlider.business-slider .content { top: 0; padding-top: 7%; }'. "\n";
			if ( $sf_slider_biz_overlay && $sf_slider_biz_overlay == 'none' )
				$output .= '#wrapper #loopedSlider.business-slider .content { background: none; width: 50%; top: 20%; bottom: inherit; }'. "\n";
			$output .= '}' . "\n";
		}

		// Archive Header
		if ( $sf_archive_header_font )
			$output .= '.archive_header { ' . sf_generate_font_css( $sf_archive_header_font ) . ' }'. "\n";
		if ( $sf_archive_header_border_bottom )
			$output .= '.archive_header {border-bottom:'.$sf_archive_header_border_bottom["width"].'px '.$sf_archive_header_border_bottom["style"].' '.$sf_archive_header_border_bottom["color"].';}'. "\n";
		if ( isset( $sf_options['sf_archive_header_disable_rss'] ) && $sf_options['sf_archive_header_disable_rss'] == "true" )
			$output .= '.archive_header .catrss { display:none; }' . "\n";

	// Output styles
	if (isset($output)) {
		// $output = "\n<!-- Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n<!-- /Woo Custom Styling -->\n\n";
		echo $output;
	}
} // End sf_custom_styling()
}

// Returns proper font css output
if ( ! function_exists( 'sf_generate_font_css' ) ) {
	function sf_generate_font_css( $option, $em = '1' ) {

		// Test if font-face is a Google font
		global $google_fonts;
		foreach ( $google_fonts as $google_font ) {

			// Add single quotation marks to font name and default arial sans-serif ending
			if ( $option['face'] == $google_font['name'] )
				$option['face'] = "'" . $option['face'] . "', arial, sans-serif";

		} // END foreach

		if ( !@$option['style'] && !@$option['size'] && !@$option['unit'] && !@$option['color'] )
			return 'font-family: '.stripslashes(str_replace( '&quot;', '', $option['face'] )).';';
		else
			return 'font:'.$option['style'].' '.$option['size'].$option['unit'].'/'.$em.'em '.stripslashes(str_replace( '&quot;', '', $option['face'] )).';color:'.$option['color'].';';
	} // End sf_generate_font_css()
}

/*-----------------------------------------------------------------------------------*/
/* Determine what layout to use */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_get_layout' ) ) {
	function sf_get_layout() {

		global $post, $wp_query, $sf_options;

		// Reset the query
		if ( is_main_query() ) {
			wp_reset_query();
		}

		// Set default global layout
		$layout = 'two-col-left';
		if ( '' != get_option( 'sf_layout' ) ) {
			$layout = get_option( 'sf_layout' );
		}

		// Single post layout
		if ( is_singular() ) {
			// Get layout setting from single post Custom Settings panel
			if ( '' != get_post_meta( $post->ID, 'layout', true ) ) {
				$layout = get_post_meta( $post->ID, 'layout', true );

			} elseif ( 'project' == get_post_type() ) {
				if ( '' != get_option( 'sf_projects_layout_single' ) ) {
					$layout = get_option( 'sf_projects_layout_single' );
				} else {
					$layout = get_option( 'sf_layout' );
				}
			}
		}

		// Projects gallery layout option.
		if ( is_tax( 'project-category' ) || is_post_type_archive( 'project' ) ) {
			if ( '' != get_option( 'sf_projects_layout' ) ) {
				$layout = get_option( 'sf_projects_layout' );
			} else {
				$layout = get_option( 'sf_layout' );
			}
		}

		// WooCommerce Layout
		if ( is_woocommerce_activated() && is_woocommerce() ) {
			// Set defaul layout
			if ( '' != get_option( 'sf_woocommerce_layout' ) ) {
				$layout = get_option( 'sf_woocommerce_layout' );
			}
			// WooCommerce single post/page
			if ( is_singular() ) {
				// Get layout setting from single post Custom Settings panel
				if ( '' != get_post_meta( $post->ID, 'layout', true ) ) {
					$layout = get_post_meta( $post->ID, 'layout', true );
				}
			}
		}

		// Blog Page - Get layout setting from single post Custom Settings panel
		if ( is_home() ) {
		  	if ( '' != get_post_meta( $post->ID, 'layout', true ) ) {
				$layout = get_post_meta( $post->ID, 'layout', true );
			}
		}

		return $layout;

	} // End sf_get_layout()
}

/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_layout_body_class' ) ) {
	function sf_layout_body_class( $classes ) {
		global $post, $wp_query;

		// Specify site width
		$width = intval( str_replace( 'px', '', get_option( 'sf_layout_width', '960' ) ) );

		// Add classes to body_class() output
		$classes[] = sf_get_layout();
		$classes[] = 'width-' . $width;
		$classes[] = sf_get_layout() . '-' . $width;
		return $classes;
	} // End sf_layout_body_class()
}

/*-----------------------------------------------------------------------------------*/
/* Slider Setup */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_slider' ) ) {
	function sf_slider( $load_slider_js = false ) {
		global $sf_options;

		$load_slider_js = false;

		if ( ( is_page_template( 'templates/template-business.php' ) && isset( $sf_options['sf_slider_biz'] ) && $sf_options['sf_slider_biz'] == 'true' ) ||
			 ( is_page_template( 'templates/template-magazine.php' ) && isset( $sf_options['sf_slider_magazine'] ) && $sf_options['sf_slider_magazine'] == 'true' ) ||
			   is_page_template( 'templates/template-widgets.php' ) ||
			   is_active_sidebar( 'homepage' ) ) { $load_slider_js = true; }

		// Allow child themes/plugins to load the slider JavaScript when they need it.
		$load_slider_js = (bool)apply_filters( 'sf_load_slider_js', $load_slider_js );


		if ( $load_slider_js != false ) {

		// Default slider settings.
		$defaults = array(
							'autoStart' => 0,
							'hoverPause' => 'false',
							'containerClick' => 'false',
							'slideSpeed' => 600,
							'canAutoStart' => 'false',
							'next' => 'next',
							'prev' => 'previous',
							'container' => 'slides',
							'generatePagination' => 'false',
							'crossfade' => 'true',
							'fadeSpeed' => 600,
							'effect' => 'slide'
						 );

		// Dynamic settings from the "Theme Options" screen.
		$args = array();

		if ( isset( $sf_options['sf_slider_pagination'] ) && $sf_options['sf_slider_pagination'] == 'true' ) { $args['generatePagination'] = 'true'; }
		if ( isset( $sf_options['sf_slider_effect'] ) && $sf_options['sf_slider_effect'] != '' ) { $args['effect'] = $sf_options['sf_slider_effect']; }
		if ( isset( $sf_options['sf_slider_hover'] ) && $sf_options['sf_slider_hover'] == 'true' ) { $args['hoverPause'] = 'true'; }
		if ( isset( $sf_options['sf_slider_containerclick'] ) && $sf_options['sf_slider_containerclick'] == 'true' ) { $args['containerClick'] = 'true'; }
		if ( isset( $sf_options['sf_slider_speed'] ) && $sf_options['sf_slider_speed'] != '' ) { $args['slideSpeed'] = $sf_options['sf_slider_speed'] * 1000; }
		if ( isset( $sf_options['sf_slider_speed'] ) && $sf_options['sf_slider_speed'] != '' ) { $args['fadeSpeed'] = $sf_options['sf_slider_speed'] * 1000; }
		if ( isset( $sf_options['sf_slider_auto'] ) && $sf_options['sf_slider_auto'] == 'true' ) {
			$args['canAutoStart'] = 'true';
			$args['autoStart'] = $sf_options['sf_slider_interval'] * 1000;
		}

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		// Allow child themes/plugins to filter these arguments.
		$args = apply_filters( 'sf_slider_args', $args );

	?>
	<!-- Slider Setup -->
	<script type="text/javascript">
	jQuery(window).load(function() {
		var args = {};
		args.useCSS = false;
		<?php if ( $args['effect'] == 'fade' ) { ?>args.animation = 'fade';
		<?php } else { ?>args.animation = 'slide';<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( $args['canAutoStart'] == 'true' ) { ?>args.slideshow = true;
		<?php } else { ?>args.slideshow = false;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( intval( $args['autoStart'] ) > 0 ) { ?>args.slideshowSpeed = <?php echo intval( $args['autoStart'] ) ?>;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( intval( $args['slideSpeed'] ) >= 0 ) { ?>args.animationSpeed = <?php echo intval( $args['slideSpeed'] ) ?>;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( $args['generatePagination'] == 'true' ) { ?>args.controlNav = true;
		<?php } else { ?>args.controlNav = false;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( $args['hoverPause'] == 'true' ) { ?>args.pauseOnHover = true;
		<?php } else { ?>args.pauseOnHover = false;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( apply_filters( 'sf_slider_autoheight', true ) ) { ?>args.smoothHeight = true;<?php } ?>
		<?php echo "\n"; ?>
		args.manualControls = '.pagination-wrap .flex-control-nav > li';
		<?php echo "\n"; ?>
		args.start = function ( slider ) {
			slider.next( '.slider-pagination' ).fadeIn();
		}
		args.prevText = '<span class="fa fa-angle-left"></span>';
		args.nextText = '<span class="fa fa-angle-right"></span>';

		jQuery( '.sf-slideshow' ).each( function ( i ) {
			jQuery( this ).flexslider( args );
		});
	});
	</script>
	<!-- /Woo Slider Setup -->
	<?php
		}
	} // End sf_slider()
}

/*-----------------------------------------------------------------------------------*/
/* Slider Magazine */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_slider_magazine' ) ) {
	function sf_slider_magazine( $args = null, $tags = null ) {
		global $sf_options, $wp_query;

		// Exit if this isn't the first page in the loop
		if ( is_paged() ) return;

		// If WooSlider is enabled, let's use it instead
		if ( class_exists( 'WooSlider' ) ) {
			if ( version_compare( get_option( 'wooslider-version' ), '2.0.2' ) >= 0 ) {
				echo '<div class="wooslider-slider-magazine">';
				sf_wooslider_magazine();
				echo '</div><!-- /.wooslider-slider-magazine -->';
				return;
			}
		}

		// This is where our output will be added.
		$html = '';

		// Default slider settings.
		$defaults = array(
							'id' => 'loopedSlider',
							'echo' => true,
							'excerpt_length' => '15',
							'pagination' => false,
							'width' => '960',
							'order' => 'ASC',
							'posts_per_page' => '5'
						 );

		// Setup width of slider and images
		$width = '623';

		if ( 'one-col' == sf_get_layout() && isset( $sf_options['sf_layout_width'] ) && '' != $sf_options['sf_layout_width'] ) {
			$width = intval( str_replace( 'px', '', $sf_options['sf_layout_width'] ) );
		}

		// Setup slider tags array
		$slider_tags = array();
		if ( is_array( $tags ) && ( 0 < count( $tags ) ) ) {
			$slider_tags = $tags;
		}

		if ( ! is_array( $tags ) && '' != $tags && ! is_null( $tags ) ) {
			$slider_tags = explode( ',', $tags );
		}

		if ( 0 >= count( $slider_tags ) ) {
			$slider_tags = explode( ',', $sf_options['sf_slider_magazine_tags'] ); // Tags to be shown
		}

		if ( 0 < count( $slider_tags ) ) {
			foreach ( $slider_tags as $tags ) {
				$tag = get_term_by( 'name', trim($tags), 'post_tag', 'ARRAY_A' );
				if ( $tag['term_id'] > 0 )
					$tag_array[] = $tag['term_id'];
			}
		}

		if ( empty( $tag_array ) ) {
			echo do_shortcode( '[box type="alert"]' . __( 'Setup slider by adding <strong>Post Tags</strong> in <em>Magazine Template > Posts Slider</em>.', 'sfwp-locale' ) . '[/box]' );
			return;
		}

		// Setup the slider CSS class.
		$slider_css = '';

		if ( isset( $sf_options['sf_slider_pagination'] ) && $sf_options['sf_slider_pagination'] == 'true' ) {
			$slider_css = ' class="magazine-slider has-pagination sf-slideshow"';
		} else {
			$slider_css = ' class="magazine-slider sf-slideshow"';
		}

		// Setup the number of posts to show.
		$posts_per_page = $sf_options['sf_slider_magazine_entries'];
		if ( $posts_per_page != '' ) { $defaults['posts_per_page'] = $posts_per_page; }

		// Setup the excerpt length.
		$excerpt_length = $sf_options['sf_slider_magazine_excerpt_length'];
		if ( $excerpt_length != '' ) { $defaults['excerpt_length'] = $excerpt_length; }

		if ( $width > 0 && ( isset( $args['width'] ) || empty( $args['width'] ) ) ) { $defaults['width'] = $width; }

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		if ( ( ( isset($args['width']) ) && ( ( $args['width'] <= 0 ) || ( $args['width'] == '')  ) ) || ( !isset($args['width']) ) ) {	$args['width'] = '100'; }

		// Allow child themes/plugins to filter these arguments.
		$args = apply_filters( 'sf_magazine_slider_args', $args );

		// Begin setting up HTML output.
		$image_args = 'width=' . $args['width'] . '&link=img&return=true&noheight=true';

		if ( apply_filters( 'sf_slider_autoheight', true ) ) {
			$html .= '<div id="' . $args['id'] . '"' . $slider_css . ' style="height:auto;">' . "\n";

	    } else {
			$html .= '<div id="' . $args['id'] . '"' . $slider_css . ' style="max-height:' . apply_filters( 'sf_slider_height', 350 ) . 'px;">' . "\n";
			$image_args .= '&height=' . apply_filters( 'sf_slider_height', 350 );
		}

	$saved = $wp_query; $query = new WP_Query( array( 'tag__in' => $tag_array, 'posts_per_page' => $args['posts_per_page'] ) );

	if ( $query->have_posts() ) : $count = 0;

			if ( apply_filters( 'sf_slider_autoheight', true ) ) {
				$html .= '<ul class="slides">' . "\n";
			} else {
				$html .= '<ul class="slides" style="max-height:' . apply_filters( 'sf_slider_height', 350 ) . 'px;">' . "\n";
			}

	        while ( $query->have_posts() ) : $query->the_post(); global $post; $shownposts[$count] = $post->ID; $count++;

	           $styles = 'width: ' . $args['width'] . 'px;';
				if ( $count >= 2 ) { $styles .= ' display:none;'; } else { $styles = ''; }

				$url = get_permalink( $post->ID );

	            $html .= '<li id="slide-' . esc_attr( $post->ID ) . '" class="slide slide-number-' . esc_attr( $count ) . '" style="' . $styles . '">' . "\n";
					$html .= '<a href="' . $url . '" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">' . sf_image( $image_args ) . '</a>' . "\n";
	                $html .= '<div class="content">' . "\n";
	                if ( $sf_options['sf_slider_magazine_title'] == 'true' ) {
	                	$html .= '<h2 class="title"><a href="' . $url . '" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">' . get_the_title( $post->ID ) . '</a></h2>'; }

	                if ( $sf_options['sf_slider_magazine_excerpt'] == 'true' ) {
	                	$excerpt = sf_text_trim( get_the_excerpt(), $excerpt_length );
	                	if ( '' != $excerpt )
	                		$html .= '<div class="excerpt"><p>' . $excerpt . '</p></div>' . "\n";
	                }

	                $html .= '</div>' . "\n";

	            $html .= '</li>' . "\n";

	       endwhile;
		endif; $wp_query = $saved;

	    $html .= '</ul><!-- /.slides -->' . "\n";
	    $html .= '</div>' . "\n";

	if ( isset( $sf_options['sf_slider_pagination'] ) && $sf_options['sf_slider_pagination'] == 'true' ) {
		$html .= '<div class="pagination-wrap slider-pagination"><ol class="flex-control-nav flex-control-paging">';
		for ( $i = 0; $i < $count; $i++ ) {
			$html .= '<li><a>' . ( $i + 1 ) . '</a></li>';
		}
		$html .= '</ol></div>';
	}

    	if ( get_option( 'sf_exclude' ) != $shownposts ) { update_option( "sf_exclude", $shownposts ); }

		if ( $args['echo'] ) {
			echo $html;
		}

		return $html;
	} // End sf_slider_magazine()
}

/*-----------------------------------------------------------------------------------*/
/* Get Slides */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_slider_get_slides' ) ) {
	function sf_slider_get_slides( $args ) {
		$defaults = array( 'posts_per_page' => '5', 'order' => 'DESC', 'slide_page_terms' => '',  'use_slide_page' => false );
		$args = wp_parse_args( (array)$args, $defaults );
		$query_args = array( 'post_type' => 'slide', 'suppress_filters' => false );
		if ( in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ) ) ) {
			$query_args['order'] = strtoupper( $args['order'] );
		}
		if ( 0 < intval( $args['posts_per_page'] ) ) {
			$query_args['posts_per_page'] = intval( $args['posts_per_page'] );
		}
		if ( false != $args['use_slide_page'] ) {
			$slide_type = 'slug';
			if ( is_numeric( $args['slide_page_terms'] ) ) $slide_type = 'id';
			$query_args['tax_query'] = array(
											array( 'taxonomy' => 'slide-page', 'field' => 'id', 'terms' => intval( $args['slide_page_terms']) )
											);
		}

		$slides = false;

		$query = get_posts( $query_args );

		if ( ! is_wp_error( $query ) && ( 0 < count( $query ) ) ) {
			$slides = $query;
		}

		return $slides;
	} // End sf_slider_get_slides()
}

/*-----------------------------------------------------------------------------------*/
/* Slider Business */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_slider_biz' ) ) {
	function sf_slider_biz( $args = null ) {

		global $sf_options, $post;

		// Exit if this isn't the first page in the loop
		if ( is_paged() ) return;

		// If WooSlider is enabled, let's use it instead
		if ( class_exists( 'WooSlider' ) ) {
			if ( version_compare( get_option( 'wooslider-version' ), '2.0.2' ) >= 0 ) {
				echo '<div class="wooslider-slider-business">';
				sf_wooslider_business();
				echo '</div><!-- /.wooslider-slider-business -->';
				return;
			}
		}

		$options = sf_get_dynamic_values( array( 'slider_biz_slide_group' => '0' ) );

		// Default slider settings.
		$defaults = array(
							'id' => 'loopedSlider',
							'pagination' => false,
							'width' => '960',
							'order' => 'ASC',
							'posts_per_page' => '5',
							'slide_page' => $options['slider_biz_slide_group'],
							'use_slide_page' => false
						 );

		if ( '0' != $defaults['slide_page'] ) $defaults['use_slide_page'] = true;

		// Setup the "Slide Group", if one is set.
		if ( isset( $post->ID ) ) {
			$slide_page = '0';
			$stored_slide_page = get_post_meta( $post->ID, '_slide-page', true );

			if ( $stored_slide_page != '' && '0' != $stored_slide_page ) {
				$slide_page = $stored_slide_page;
				$defaults['use_slide_page'] = true; // Instruct the slider to apply the necessary conditional.
				$defaults['slide_page'] = $slide_page;
			}
		}

		// Setup width of slider and images.
		if ( isset( $sf_options['sf_slider_biz_full'] ) && 'true' == $sf_options['sf_slider_biz_full'] ) {
			$width = '1600';
		} else {
			$layout = sf_get_layout();
			$layout_width = get_option('sf_layout_width');

			$width = intval( $layout_width );
		}

		// Setup the number of posts to show.
		$posts_per_page = $sf_options['sf_slider_biz_number'];
		if ( $posts_per_page != '' ) { $defaults['posts_per_page'] = $posts_per_page; }

		// Setup the order of posts.
		$post_order = $sf_options['sf_slider_biz_order'];
		if ( $post_order != '' ) { $defaults['order'] = $post_order; }

		if ( ( 0 < $width ) && !isset( $args['width'] ) ) { $defaults['width'] = $width; }

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		if ( ( ( isset( $args['width'] ) ) && ( ( $args['width'] <= 0 ) || ( $args['width'] == '' )  ) ) || ( ! isset( $args['width'] ) ) ) {	$args['width'] = '100'; }

		// Allow child themes/plugins to filter these arguments.
		$args = apply_filters( 'sf_biz_slider_args', $args );

		// Disable auto image functionality
		$auto_img = false;
		if ( get_option( 'sf_auto_img' ) == 'true' ) {
			update_option( 'sf_auto_img', 'false' );
			$auto_img = true;
		}

		// Disable placeholder image functionality
		$placeholder_img = get_option( 'sf_default_image' );
		if ( $placeholder_img ) {
			update_option( 'sf_default_image', '' );
		}

		// Setup the slider CSS class.
		$slider_css = '';
		if ( isset( $sf_options['sf_slider_pagination'] ) && $sf_options['sf_slider_pagination'] == 'true' ) {
			$slider_css = 'business-slider has-pagination sf-slideshow';
		} else {
			$slider_css = 'business-slider sf-slideshow';
		}

		// Setup the slider height.
		if ( apply_filters( 'sf_slider_autoheight', true ) ) {
			$slider_height = 'height:auto';
	    } else {
			$slider_height = apply_filters( 'sf_slider_height', 350 );
		}

		// Slide Styles
		$slide_styles = 'width: ' . $args['width'] . 'px;';

		$query_args = array(
						'posts_per_page' => $posts_per_page,
						'order' => $post_order,
						'use_slide_page' => $args['use_slide_page'],
						'slide_page_terms' => $args['slide_page']
					);

		// Retrieve the slides, based on the query arguments.
		$slides = sf_slider_get_slides( $query_args );

		if ( false == $slides ) {
			echo do_shortcode( '[box type="alert"]' . __( 'Please add some slider posts via Slides > Add New', 'sfwp-locale' ) . '[/box]');
			return;
		}

		if ( ( count( $slides ) < 1 ) ) {
			echo do_shortcode( '[box type="alert"]' . __( 'Please note that this slider requires 2 or more slides in order to function. Please add another slide.', 'sfwp-locale' ) . '[/box]');
			return;
		}

		$view_args = array(
					'id' => $args['id'],
					'width' => $args['width'],
					'height' => $slider_height,
					'container_css' => $slider_css,
					'slide_styles' => $slide_styles
				);

		// Allow child themes/plugins to filter these arguments.
		$view_args = apply_filters( 'sf_slider_biz_view_args', $view_args );

		// Display slider
		sf_slider_biz_view( $view_args, $slides );

		// Enable auto img functionality
		if ( $auto_img )
			update_option( 'sf_auto_img', 'true' );

		// Enable placeholder functionality
		if ( '' != $placeholder_img )
			update_option( 'sf_default_image', $placeholder_img );

	} // End sf_slider_biz()
}

/*-----------------------------------------------------------------------------------*/
/* Business Slider View */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_slider_biz_view' ) ) {
	function sf_slider_biz_view( $args = null, $slides = null ) {

		global $sf_options, $post;

		// Default slider settings.
		$defaults = array(
							'id' => 'loopedSlider',
							'width' => '960',
							'container_css' => '',
							'slide_styles' => ''
						);

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		// Init slide count
		$count = 0;

	?>

	<?php do_action('sf_biz_slider_before'); ?>

	<div id="<?php echo esc_attr( $args['id'] ); ?>"<?php if ( '' != $args['container_css'] ): ?> class="<?php echo esc_attr( $args['container_css'] ); ?>"<?php endif; ?><?php if ( !apply_filters( 'sf_slider_autoheight', true ) ): ?> style="height: <?php echo apply_filters( 'sf_slider_height', 350 ); ?>px;"<?php endif; ?>>

		<ul class="slides"<?php if ( !apply_filters( 'sf_slider_autoheight', true ) ): ?> style="height: <?php echo apply_filters( 'sf_slider_height', 350 ); ?>px;"<?php endif; ?>>
			<?php $original_slide_styles = $args['slide_styles']; ?>
			<?php foreach ( $slides as $k => $post ) { setup_postdata( $post ); $count++; ?>

			<?php
				// Slide Styles
				if ( $count >= 2 ) { $args['slide_styles'] = $original_slide_styles . ' display:none;'; } else { $args['slide_styles'] = ''; }
			?>

			<li id="slide-<?php echo esc_attr( $post->ID ); ?>" class="slide slide-number-<?php echo esc_attr( $count ); ?>" <?php if ( '' != $args['slide_styles'] ): ?>style="<?php echo esc_attr( $args['slide_styles'] ); ?>"<?php endif; ?>>

				<?php
					$type = sf_image('return=true');
					if ( $type ):
						$url = get_post_meta( $post->ID, 'url', true );
				?>

					<?php if ( '' != $url ): ?><a href="<?php echo esc_url( $url ); ?>" title="<?php the_title_attribute(); ?>"><?php endif; ?>
					<?php sf_image( 'width=' . $args['width'] . '&link=img&noheight=true' ); ?>
					<?php if ( '' != $url ): ?></a><?php endif; ?>

					<?php if ( 'true' == $sf_options['sf_slider_biz_title'] || '' != get_the_content() ): ?>
					<div class="content">

						<?php if ( 'true' == $sf_options['sf_slider_biz_title'] ): ?>
						<div class="title">
							<h2 class="title">
								<?php if ( '' != $url ): ?><a href="<?php echo esc_url( $url ); ?>" title="<?php the_title_attribute(); ?>"><?php endif; ?>
								<?php the_title(); ?>
								<?php if ( '' != $url ): ?></a><?php endif; ?>
							</h2>
						</div>
						<?php endif; ?>

						<?php
							$content = '';
							if ( '' != $post->post_excerpt ) {
								$content = $post->post_excerpt;
							} else {
								$content = $post->post_content;
							}
							$content = do_shortcode( $content );
							$content = wpautop( $content );
						?>

						<?php if ( '' != $content ): ?>
						<div class="excerpt">
							<?php echo $content; ?>
						</div><!-- /.excerpt -->
						<?php endif; ?>

					</div><!-- /.content -->
					<?php endif; ?>

				<?php else: ?>

					<section class="entry col-full">
						<?php the_content(); ?>
					</section>

				<?php endif; ?>

			</li><!-- /.slide-number-<?php echo esc_attr( $count ); ?> -->

			<?php } // End foreach ?>

			<?php wp_reset_postdata();  ?>

		</ul><!-- /.slides -->

	</div><!-- /#<?php echo $args['id']; ?> -->

	<?php if ( isset( $sf_options['sf_slider_pagination'] ) && $sf_options['sf_slider_pagination'] == 'true' ) : ?>
		<div class="pagination-wrap slider-pagination">
			<ol class="flex-control-nav flex-control-paging">
				<?php for ( $i = 0; $i < $count; $i++ ): ?>
					<li><a><?php echo ( $i + 1 ) ?></a></li>
				<?php endfor; ?>
			</ol>
		</div>
	<?php endif; ?>

	<?php do_action('sf_biz_slider_after'); ?>

<?php
	} // End sf_slider_biz_view()
}

/*-----------------------------------------------------------------------------------*/
/* Navigation */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_nav' ) ) {
function sf_nav() {
	global $sf_options;
	sf_nav_before();
?>
<nav id="navigation" class="col-full" role="navigation">

	<?php
		$menu_class = 'menus';
		$number_icons = 0;

		$icons = array(
			'sf_nav_rss',
			'sf_nav_search',
			'sf_header_cart_link'
		);

		foreach ( $icons as $icon ) {
			if ( isset( $sf_options[ $icon ] ) && 'true' == $sf_options[ $icon ] ) {
				$number_icons++;
			}
		}

		if ( isset( $sf_options[ 'sf_subscribe_email' ] ) && '' != $sf_options[ 'sf_subscribe_email' ] ) {
			$number_icons++;
		}

		if ( 0 < $number_icons ) {
			$menu_class .= ' nav-icons nav-icons-' . $number_icons;

			if ( isset( $sf_options[ 'sf_header_cart_link' ] ) && 'true' == $sf_options['sf_header_cart_link'] ) {
				if ( isset( $sf_options[ 'sf_header_cart_total' ] ) && 'true' == $sf_options[ 'sf_header_cart_total' ] ) {
					$menu_class .= ' cart-extended';
				}
			}
		}
	?>

	<section class="<?php echo $menu_class; ?>">

	<?php sf_nav_inside(); ?>

	</section><!-- /.menus -->

	<a href="#top" class="nav-close"><span><?php _e( 'Return to Content', 'sfwp-locale' ); ?></span></a>

</nav>
<?php
	sf_nav_after();
} // End sf_nav()
}

/*-----------------------------------------------------------------------------------*/
/* Primary menu */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_nav_primary' ) ) {
function sf_nav_primary() {
?>
	<a href="<?php echo home_url(); ?>" class="nav-home"><span><?php _e( 'Home', 'sfwp-locale' ); ?></span></a>

	<?php
	if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {
		echo '<h3>' . sf_get_menu_name( 'primary-menu' ) . '</h3>';
		wp_nav_menu( array( 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
	} else {
	?>
		<ul id="main-nav" class="nav fl">
			<?php
			if ( get_option( 'sf_custom_nav_menu' ) == 'true' ) {
				if ( function_exists( 'sf_custom_navigation_output' ) ) { sf_custom_navigation_output( 'name=Woo Menu 1' ); }
			} else { ?>

				<?php if ( is_page() ) { $highlight = 'page_item'; } else { $highlight = 'page_item current_page_item'; } ?>
				<li class="<?php echo esc_attr( $highlight ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Home', 'sfwp-locale' ); ?></a></li>
				<?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>
			<?php } ?>
		</ul><!-- /#nav -->
	<?php }

} // End sf_nav_primary()
}


/*-----------------------------------------------------------------------------------*/
/* Add Side Navigation wrappers */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_nav_sidenav_start' ) ) {
function sf_nav_sidenav_start() {
?>
	<div class="side-nav">
<?php
} // End sf_nav_sidenav_start()
}

if ( ! function_exists( 'sf_nav_sidenav_end' ) ) {
function sf_nav_sidenav_end() {
?>
	</div><!-- /#side-nav -->
<?php
} // End sf_nav_sidenav_start()
}

/*-----------------------------------------------------------------------------------*/
/* Add subscription links to the navigation bar */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_nav_subscribe' ) ) {
function sf_nav_subscribe() {
	global $sf_options;
	$class = '';
	if ( isset( $sf_options['sf_header_cart_link'] ) && 'true' == $sf_options['sf_header_cart_link'] )
		$class = ' cart-enabled';

	if ( ( isset( $sf_options['sf_nav_rss'] ) ) && ( $sf_options['sf_nav_rss'] == 'true' ) || ( isset( $sf_options['sf_subscribe_email'] ) ) && ( $sf_options['sf_subscribe_email'] ) ) { ?>
	<ul class="rss fr<?php echo $class; ?>">
		<?php if ( ( isset( $sf_options['sf_subscribe_email'] ) ) && ( $sf_options['sf_subscribe_email'] ) ) { ?>
		<li class="sub-email"><a href="<?php echo esc_url( $sf_options['sf_subscribe_email'] ); ?>"></a></li>
		<?php } ?>
		<?php if ( isset( $sf_options['sf_nav_rss'] ) && ( $sf_options['sf_nav_rss'] == 'true' ) ) { ?>
		<li class="sub-rss"><a href="<?php if ( isset($sf_options['sf_feed_url']) ) { echo esc_url( $sf_options['sf_feed_url'] ); } else { echo esc_url( get_bloginfo_rss( 'rss2_url' ) ); } ?>"></a></li>
		<?php } ?>
	</ul>
	<?php }
} // End sf_nav_subscribe()
}

/*-----------------------------------------------------------------------------------*/
/* Add Search to the navigation bar */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_nav_search' ) ) {
function sf_nav_search() {
	global $sf_options;
?>
	<?php if ( apply_filters( 'sf_nav_search', true ) && ( isset( $sf_options['sf_nav_search'] ) && 'true' == $sf_options['sf_nav_search'] ) ) { ?>
	<ul class="nav-search">
		<li>
			<a class="search-contents" href="#"></a>
			<ul>
				<li>
					<?php
						$args = array(
							'title' => ''
						);

						if ( isset( $sf_options['sf_header_search_scope'] ) && 'products' == $sf_options['sf_header_search_scope'] ) {
							the_widget( 'WC_Widget_Product_Search', $args );
						} else {
							the_widget( 'WP_Widget_Search', $args );
						}
					?>
				</li>
			</ul>
		</li>
	</ul>
	<?php } ?>
<?php
} // End sf_nav_search
}

/*-----------------------------------------------------------------------------------*/
/* Post More  */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_post_more' ) ) {
function sf_post_more() {
	if ( get_option( 'sf_disable_post_more' ) != 'true' ) {

	$html = '';

	if ( get_option('sf_post_content') == 'excerpt' ) { $html .= '[view_full_article]'; }

	$comm = get_option('sf_comments');
	if ( 'post' == $comm || 'both' == $comm ) {
		$html .= '[post_comments]';
	}

	$html = apply_filters( 'sf_post_more', $html );

		if ( $html != '' ) {
?>
	<div class="post-more">
		<?php
			echo $html;
		?>
	</div>
<?php
		}
	}
} // End sf_post_more()
}


/*-----------------------------------------------------------------------------------*/
/* Author Box */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_author' ) ) {
function sf_author () {
	// Author box single post page
	if ( is_single() && get_option( 'sf_disable_post_author' ) != 'true' ) { add_action( 'sf_post_inside_after', 'sf_author_box', 10 ); }
	// Author box author page
	if ( is_author() ) { add_action( 'sf_loop_before', 'sf_author_box', 10 ); }
} // End sf_author()
}


/*-----------------------------------------------------------------------------------*/
/* Single Post Author */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_author_box' ) ) {
function sf_author_box () {
	global $post;
	$author_id=$post->post_author;

	// Adjust the arrow, if is_rtl().
	$arrow = '&rarr;';
	if ( is_rtl() ) $arrow = '&larr;';
?>
<aside id="post-author">
	<div class="profile-image"><?php echo get_avatar( $author_id, '80' ); ?></div>
	<div class="profile-content">
		<h4><?php printf( esc_attr__( 'About %s', 'sfwp-locale' ), get_the_author_meta( 'display_name', $author_id ) ); ?></h4>
		<?php echo get_the_author_meta( 'description', $author_id ); ?>
		<?php if ( is_singular() ) { ?>
		<div class="profile-link">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ); ?>">
				<?php printf( __( 'View all posts by %s %s', 'sfwp-locale' ), get_the_author_meta( 'display_name', $author_id ), '<span class="meta-nav">' . $arrow . '</span>' ); ?>
			</a>
		</div><!--#profile-link-->
		<?php } ?>
	</div>
	<div class="fix"></div>
</aside>
<?php
} // End sf_author_box()
}


/*-----------------------------------------------------------------------------------*/
/* Yoast Breadcrumbs */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( '_dep_sf_breadcrumbs' ) ) {
function _dep_sf_breadcrumbs() {
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div id="breadcrumb"><p>', '</p></div>' );
	}
} // End _dep_sf_breadcrumbs()
}


/*-----------------------------------------------------------------------------------*/
/* Subscribe & Connect  */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_subscribe_connect_action' ) ) {
function sf_subscribe_connect_action() {
	if ( is_single() && 'true' == get_option( 'sf_connect' ) ) { add_action('sf_post_inside_after', 'sf_subscribe_connect'); }
} // End sf_subscribe_connect_action()
}


/*-----------------------------------------------------------------------------------*/
/* Optional Top Navigation (WP Menus)  */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_top_navigation' ) ) {
function sf_top_navigation() {
	if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) {
?>
	<div id="top">
		<div class="col-full">
			<?php
				echo '<h3 class="top-menu">' . sf_get_menu_name( 'top-menu' ) . '</h3>';
				wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav top-navigation fl', 'theme_location' => 'top-menu' ) );
			?>
		</div>
	</div><!-- /#top -->
<?php
	}
} // End sf_top_navigation()
}

/*-----------------------------------------------------------------------------------*/
/* Footer Widgetized Areas  */
/*-----------------------------------------------------------------------------------*/

add_action( 'sf_footer_top', 'sf_footer_sidebars', 30 );

if ( ! function_exists( 'sf_footer_sidebars' ) ) {
function sf_footer_sidebars() {
	$settings = sf_get_dynamic_values( array( 'biz_disable_footer_widgets' => '', 'footer_sidebars' => '4' ) );

	$footer_sidebar_total = 4;
	$has_footer_sidebars = false;

	// Check if we have footer sidebars to display.
	for ( $i = 1; $i <= $footer_sidebar_total; $i++ ) {
		if ( sf_active_sidebar( 'footer-' . $i ) && ( $has_footer_sidebars == false ) ) {
			$has_footer_sidebars = true;
		}
	}

	// If footer sidebars are available, we're on the "Business" page template and we want to disable them, do so.
	if ( $has_footer_sidebars && is_page_template( 'templates/template-business.php' ) && ( 'true' == $settings['biz_disable_footer_widgets'] ) ) {
		$has_footer_sidebars = false;
	}

	$total = $settings['footer_sidebars'];
	if ( '0' == $settings['footer_sidebars'] ) { $total = 0; } // Make sure the footer widgets don't display if the "none" option is set under "Theme Options".

	// Lastly, we display the sidebars.
	if ( $has_footer_sidebars &&  $total > 0 ) {
?>
<section id="footer-widgets" class="col-full col-<?php echo esc_attr( intval( $total ) ); ?>">
	<?php $i = 0; while ( $i < intval( $total ) ) { $i++; ?>
		<?php if ( sf_active_sidebar( 'footer-' . $i ) ) { ?>
	<div class="block footer-widget-<?php echo $i; ?>">
    	<?php sf_sidebar( 'footer-' . $i ); ?>
	</div>
        <?php } ?>
	<?php } // End WHILE Loop ?>
	<div class="fix"></div>
</section><!--/#footer-widgets-->
<?php

	} // End IF Statement
} // End sf_footer_sidebars()
}

/*-----------------------------------------------------------------------------------*/
/* Add customisable footer areas */
/*-----------------------------------------------------------------------------------*/

/**
 * Add customisable footer areas.
 *
 * @subpackage Actions
 */

if ( ! function_exists( 'sf_footer_left' ) ) {
function sf_footer_left () {
	$settings = sf_get_dynamic_values( array( 'footer_left' => 'true', 'footer_left_text' => '[site_copyright]' ) );

	sf_do_atomic( 'sf_footer_left_before' );

	$html = '';

	if( 'true' == $settings['footer_left'] ) {
		$html .= '<p>' . stripslashes( $settings['footer_left_text'] ) . '</p>';
	} else {
		$html .= '[site_copyright]';
	}

	$html = apply_filters( 'sf_footer_left', $html );

	echo $html;

	sf_do_atomic( 'sf_footer_left_after' );
} // End sf_footer_left()
}

if ( ! function_exists( 'sf_footer_right' ) ) {
function sf_footer_right () {
	$settings = sf_get_dynamic_values( array( 'footer_right' => 'true', 'footer_right_text' => '[site_credit]' ) );

	sf_do_atomic( 'sf_footer_right_before' );

	$html = '';

	if( 'true' == $settings['footer_right'] ) {
		$html .= '<p>' . stripslashes( $settings['footer_right_text'] ) . '</p>';
	} else {
		$html .= '[site_credit]';
	}

	$html = apply_filters( 'sf_footer_right', $html );

	echo $html;

	sf_do_atomic( 'sf_footer_right_after' );
} // End sf_footer_right()
}

/*-----------------------------------------------------------------------------------*/
/* Add customisable post meta */
/*-----------------------------------------------------------------------------------*/

/**
 * Add customisable post meta.
 *
 * Add customisable post meta, using shortcodes,
 * to be added/modified where necessary.
 *
 * @subpackage Actions
 */

if ( ! function_exists( 'sf_post_meta' ) ) {
function sf_post_meta() {

	if ( is_page() && !( is_page_template( 'templates/template-blog.php' ) || is_page_template( 'templates/template-magazine.php' ) ) ) {
		return;
	}

	$post_info = '<span class="small">' . __( 'By', 'sfwp-locale' ) . '</span> [post_author_posts_link] <span class="small">' . _x( 'on', 'post datetime', 'sfwp-locale' ) . '</span> [post_date] <span class="small">' . __( 'in', 'sfwp-locale' ) . '</span> [post_categories before=""] ';
printf( '<div class="post-meta">%s</div>' . "\n", apply_filters( 'sf_filter_post_meta', $post_info ) );

} // End sf_post_meta()
}

/*-----------------------------------------------------------------------------------*/
/* Add Post Thumbnail to Single posts on Archives */
/*-----------------------------------------------------------------------------------*/

/**
 * Add Post Thumbnail to Single posts on Archives
 *
 * Add code to the sf_post_inside_before() hook.
 *
 * @subpackage Actions
 */

 add_action( 'sf_post_inside_before', 'sf_display_post_image', 10 );

if ( ! function_exists( 'sf_display_post_image' ) ) {
function sf_display_post_image() {
	$display_image = false;
	$options = sf_get_dynamic_values( array( 'thumb_w' => '100', 'thumb_h' => '100', 'thumb_align' => 'alignleft', 'single_w' => '100', 'single_h' => '100', 'thumb_align_single' => 'alignright', 'thumb_single' => 'false' ) );
	$width = $options['thumb_w'];
	$height = $options['thumb_h'];
	$align = $options['thumb_align'];

	if ( is_single() && ( 'true' == $options['thumb_single'] ) ) {
		$width = $options['single_w'];
		$height = $options['single_h'];
		$align = $options['thumb_align_single'];
		$display_image = true;
	}

	if ( true == $display_image && ! sf_embed( '' ) ) { sf_image( 'width=' . esc_attr( $width ) . '&height=' . esc_attr( $height ) . '&class=thumbnail ' . esc_attr( $align ) ); }
} // End sf_display_post_image()
}

/*-----------------------------------------------------------------------------------*/
/* Post Inside After */
/*-----------------------------------------------------------------------------------*/
/**
 * Post Inside After
 *
 * Add code to the sf_post_inside_after() hook.
 *
 * @subpackage Actions
 */

 add_action( 'sf_post_inside_after_singular-post', 'sf_post_inside_after_default', 10 );

if ( ! function_exists( 'sf_post_inside_after_default' ) ) {
function sf_post_inside_after_default() {

	$post_info ='[post_tags before=""]';
	printf( '<div class="post-utility">%s</div>' . "\n", apply_filters( 'sf_post_inside_after_default', $post_info ) );

} // End sf_post_inside_after_default()
}

/*-----------------------------------------------------------------------------------*/
/* Modify the default "comment" form field. */
/*-----------------------------------------------------------------------------------*/
/**
 * Modify the default "comment" form field.
 *
 * @subpackage Filters
 */

  add_filter( 'comment_form_field_comment', 'sf_comment_form_comment', 10 );

if ( ! function_exists( 'sf_comment_form_comment' ) ) {
function sf_comment_form_comment ( $field ) {
	$field = str_replace( '<label ', '<label class="hide" ', $field );
	$field = str_replace( 'cols="45"', 'cols="50"', $field );
	$field = str_replace( 'rows="8"', 'rows="10"', $field );

	return $field;
} // End sf_comment_form_comment()
}

/*-----------------------------------------------------------------------------------*/
/* Add theme default comment form fields. */
/*-----------------------------------------------------------------------------------*/
/**
 * Add theme default comment form fields.
 *
 * @subpackage Filters
 */

add_filter( 'comment_form_default_fields', 'sf_comment_form_fields', 10 );

if ( ! function_exists( 'sf_comment_form_fields' ) ) {
function sf_comment_form_fields ( $fields ) {
	$commenter = wp_get_current_commenter();

$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );

	$fields =  array(
	'author' => '<p class="comment-form-author"><input id="author" name="author" type="text" class="txt" tabindex="1" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
				'<label for="author">' . __( 'Name', 'sfwp-locale' ) . ( $req ? ' <span class="required">(' . __( 'required', 'sfwp-locale' ) . ')</span>' : '' ) . '</label> ' . '</p>',
	'email'  => '<p class="comment-form-email"><input id="email" name="email" type="text" class="txt" tabindex="2" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
				'<label for="email">' . __( 'Email (will not be published)', 'sfwp-locale' ) . ( $req ? ' <span class="required">(' . __( 'required', 'sfwp-locale' ) . ')</span>' : '' ) . '</label> ' . '</p>',
	'url'    => '<p class="comment-form-url"><input id="url" name="url" type="text" class="txt" tabindex="3" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
	            '<label for="url">' . __( 'Website', 'sfwp-locale' ) . '</label></p>',
);

	return $fields;
} // End sf_comment_form_fields()
}

/*-----------------------------------------------------------------------------------*/
/* Add theme default comment form arguments. */
/*-----------------------------------------------------------------------------------*/
/**
 * Add theme default comment form arguments.
 *
 * @subpackage Filters
 */

add_filter( 'comment_form_defaults', 'sf_comment_form_args', 10 );

if ( ! function_exists( 'sf_comment_form_args' ) ) {
	function sf_comment_form_args ( $args ) {
		// Add tabindex of "field count + 1" to the comment textarea. This lets us cater for additional fields and have a dynamic tab index.
		$tabindex = count( $args['fields'] ) + 1;
		$args['comment_field'] = str_replace( '<textarea ', '<textarea tabindex="' . $tabindex . '" ', $args['comment_field'] );

		// Adjust tabindex for "submit" button.
		$tabindex++;

		$args['label_submit'] = __( 'Submit Comment', 'sfwp-locale' );
		$args['comment_notes_before'] = '';
		$args['comment_notes_after'] = '';
		$args['cancel_reply_link'] = __( 'Click here to cancel reply.', 'sfwp-locale' );

		return $args;
	} // End sf_comment_form_args()
}

/*-----------------------------------------------------------------------------------*/
/* Activate shortcode compatibility in our new custom areas. */
/*-----------------------------------------------------------------------------------*/
/**
 * Activate shortcode compatibility in our new custom areas.
 *
 * @subpackage Filters
 */
 	$sections = array( 'sf_filter_post_meta', 'sf_post_inside_after_default', 'sf_post_more', 'sf_footer_left', 'sf_footer_right' );

 	foreach ( $sections as $s ) { add_filter( $s, 'do_shortcode', 20 ); }

/*-----------------------------------------------------------------------------------*/
/* sf_feedburner_link() */
/*-----------------------------------------------------------------------------------*/
/**
 * sf_feedburner_link()
 *
 * Replace the default RSS feed link with the Feedburner URL, if one
 * has been provided by the user.
 *
 * @subpackage Filters
 */

add_filter( 'feed_link', 'sf_feedburner_link', 10 );

if ( ! function_exists( 'sf_feedburner_link' ) ) {
function sf_feedburner_link ( $output, $feed = null ) {
	global $sf_options;

	$default = get_default_feed();

	if ( ! $feed ) $feed = $default;

	if ( isset( $sf_options['sf_feed_url'] ) && $sf_options['sf_feed_url'] && ( $feed == $default ) && ( ! stristr( $output, 'comments' ) ) ) $output = $sf_options['sf_feed_url'];

	return esc_url( $output );
} // End sf_feedburner_link()
}

/*-----------------------------------------------------------------------------------*/
/* Enqueue dynamic CSS */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_enqueue_custom_styling' ) ) {
function sf_enqueue_custom_styling () {
	echo "\n" . '<!-- Custom CSS Styling -->' . "\n";
	echo '<style type="text/css">' . "\n";
	sf_custom_styling();
	echo '</style>' . "\n";
} // End sf_enqueue_custom_styling()
}


	/**
	 * Load site width CSS in the header
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_load_site_width_css' ) ) {
		function sf_load_site_width_css () {
			$settings = sf_get_dynamic_values( array( 'layout_width' => 960 ) );
			$layout_width = intval( $settings['layout_width'] );
			if ( 0 < $layout_width && 960 != $layout_width ) { /* Has legitimate width */ } else { return; } // Use default width from stylesheet
		?>
		
		<!-- Adjust the website width -->
		<style type="text/css">
			.col-full, #wrapper { max-width: <?php echo intval( $layout_width ); ?>px !important; }
		</style>
		
		<?php
		} // End sf_load_site_width_css()
	}
	add_action( 'wp_head', 'sf_load_site_width_css', 9 );


	/**
	 * Load the layout width CSS without a media query wrapping it.
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_load_site_width_css_nomedia' ) ) {
	
		function sf_load_site_width_css_nomedia () {
			$settings = sf_get_dynamic_values( array( 'layout_width' => 960 ) );
			$layout_width = intval( $settings['layout_width'] );
			if ( 0 < $layout_width ) { /* Has legitimate width */ } else { $layout_width = 960; } // Default Width
		?>
		<style type="text/css">
		.col-full, #wrapper { width: <?php echo intval( $layout_width ); ?>px; max-width: <?php echo intval( $layout_width ); ?>px; }
		#inner-wrapper { padding: 0; }
		body.full-width #header, #nav-container, body.full-width #content, body.full-width #footer-widgets, body.full-width #footer { padding-left: 0; padding-right: 0; }
		body.full-width #content { width: auto; padding: 0 1em;}</style>
		<?php
		} // End sf_load_site_width_css_nomedia()
	}

	/**
	 * Adjust the homepage query, if using the "Magazine" page template as the homepage.
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_modify_magazine_homepage_query' ) ) {
		function sf_modify_magazine_homepage_query ( $q ) {
			if ( ! is_admin() && $q->is_main_query() && ( 0 < $q->query_vars['page_id'] ) && ( $q->query_vars['page_id'] == get_option( 'page_on_front' ) ) && ( 'template-magazine.php' == get_post_meta( intval( $q->query_vars['page_id'] ), '_wp_page_template', true ) ) ) {
		
				$settings = sf_get_dynamic_values( array( 'magazine_limit' => get_option( 'posts_per_page' ) ) );
		
				$q->set( 'posts_per_page', intval( $settings['magazine_limit'] ) );
		
				if ( isset( $q->query_vars['page'] ) ) {
					$q->set( 'paged', intval( $q->query_vars['page'] ) );
				}
		
				$q->parse_query();
			}
			return $q;
		} // End sf_modify_magazine_homepage_query()
	}
	add_filter( 'pre_get_posts', 'sf_modify_magazine_homepage_query' );


	/**
	 * Full width header
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_full_width_header' ) ) {
		function sf_full_width_header() {
			$settings = sf_get_dynamic_values( array( 'header_full_width' => '', 'layout_boxed' => '' ) );
		
			if ( 'true' == $settings['layout_boxed'] ) return;
			if ( 'true' != $settings['header_full_width'] ) return;
		
		
			// Add header container
			add_action( 'sf_header_before', 'sf_header_container_start' );
			add_action( 'sf_header_after', 'sf_header_container_end', 8 );
		
			// Add navigation container
			add_action( 'sf_nav_before', 'sf_nav_container_start' );
			add_action( 'sf_nav_after', 'sf_nav_container_end' );
		} // End sf_full_width_header()
	}
	add_action( 'get_header', 'sf_full_width_header', 10 );


	/**
	 * Full width footer
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_full_width_footer' ) ) {
		function sf_full_width_footer() {
			$settings = sf_get_dynamic_values( array( 'footer_full_width' => '', 'layout_boxed' => '' ) );
		
			if ( 'true' == $settings['layout_boxed'] ) return;
			if ( 'true' != $settings['footer_full_width'] ) return;
		
			// Add footer widget container
			add_action( 'sf_footer_top', 'sf_footer_widgets_container_start', 8 );
			add_action( 'sf_footer_before', 'sf_footer_widgets_container_end' );
		
			// Add footer container
			add_action( 'sf_footer_before', 'sf_footer_container_start' );
			add_action( 'sf_footer_after', 'sf_footer_container_end' );
		} // End sf_full_width_footer()
	}
	add_action( 'get_header', 'sf_full_width_footer', 10 );


	/**
	 * Full width markup functions
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_header_container_start' ) ) {
		function sf_header_container_start () {
		?>
			<!--#header-container-->
			<div id="header-container">
		<?php
		} // End sf_header_container_start()
	}

	if ( ! function_exists( 'sf_header_container_end' ) ) {
		function sf_header_container_end () {
		?>
			</div><!--/#header-container-->
		<?php
		} // End sf_header_container_end()
	}

	if ( ! function_exists( 'sf_nav_container_start' ) ) {
		function sf_nav_container_start () {
		?>
			<!--#nav-container-->
			<div id="nav-container">
		<?php
		} // End sf_nav_container_start()
	}

	if ( ! function_exists( 'sf_nav_container_end' ) ) {
		function sf_nav_container_end () {
		?>
			</div><!--/#nav-container-->
		<?php
		} // End sf_nav_container_end()
	}

	if ( ! function_exists( 'sf_footer_widgets_container_start' ) ) {
		function sf_footer_widgets_container_start () {
		?>
			<!--#footer-widgets-container-->
			<div id="footer-widgets-container">
		<?php
		} // End sf_footer_widgets_container_start()
	}

	if ( ! function_exists( 'sf_footer_widgets_container_end' ) ) {
		function sf_footer_widgets_container_end () {
		?>
			</div><!--/#footer_widgets_container_end-->
		<?php
		}
	}

	if ( ! function_exists( 'sf_footer_container_start' ) ) {
		function sf_footer_container_start () { ?>
			<!--#footer_container_start-->
			<div id="footer-container">
		<?php
		} // End sf_footer_container_start()
	}

	if ( ! function_exists( 'sf_footer_container_end' ) ) {
		function sf_footer_container_end () { ?>
			</div><!--/#footer_container_end-->
		<?php
		} // End sf_footer_container_end()
	}


	/**
	 * Full width body classes
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_add_full_width_class' ) ) {
		function sf_add_full_width_class ( $classes ) {
			$settings = sf_get_dynamic_values( array( 'header_full_width' => 'false', 'footer_full_width' => 'false', 'layout_boxed' => '', 'slider_biz_full' => 'false' ) );
			if ( 'true' == $settings['layout_boxed'] ) return $classes; // Don't add the full width CSS classes if boxed layout is enabled.
		
			if ( 'true' == $settings['header_full_width'] || 'true' == $settings['footer_full_width'] ) {
		
				$classes[] = 'full-width';
		
				if ( 'true' == $settings['header_full_width'] ) {
					$classes[] = 'full-header';
				}
		
				if ( 'true' == $settings['footer_full_width'] ) {
					$classes[] = 'full-footer';
				}
			}
		
			if ( 'true' == $settings['slider_biz_full'] && is_page_template( 'templates/template-business.php' ) ) {
				if ( !in_array( 'full-width', $classes ) ) {
					$classes[] = 'full-width';
				}
		
				$classes[] = 'full-slider';
			}
		
			return $classes;
		} // End sf_add_full_width_class()
	}
	add_filter( 'body_class', 'sf_add_full_width_class', 10 );


	/**
	 * Optionally load custom logo
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_logo' ) ) {
		function sf_logo () {
			$settings = sf_get_dynamic_values( array( 'logo' => '' ) );
			// Setup the tag to be used for the header area (`h1` on the front page and `span` on all others).
			$heading_tag = 'span';
			if ( is_home() || is_front_page() ) { $heading_tag = 'h1'; }
		
			// Get our website's name, description and URL. We use them several times below so lets get them once.
			$site_title = get_bloginfo( 'name' );
			$site_url = home_url( '/' );
			$site_description = get_bloginfo( 'description' );
		?>
		<div id="logo">
		<?php
			// Website heading/logo and description text.
			$logo_url = apply_filters( 'sf_logo_img', $settings['logo'] );
			if ( ( '' != $logo_url ) ) {
				if ( is_ssl() ) $logo_url = str_replace( 'http://', 'https://', $logo_url );
		
				echo '<a href="' . esc_url( $site_url ) . '" title="' . esc_attr( $site_description ) . '"><img class="logotype" src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $site_title ) . '" /></a>' . "\n";
			} // End IF Statement
		
			echo '<' . $heading_tag . ' class="site-title"><a href="' . esc_url( $site_url ) . '">' . $site_title . '</a></' . $heading_tag . '>' . "\n";
			if ( $site_description ) { echo '<span class="site-description">' . $site_description . '</span>' . "\n"; }
		?>
		</div>
		<?php
		} // End sf_logo()
	}
	add_action( 'sf_header_inside', 'sf_logo', 10 );


	/**
	 * Optionally load the mobile navigation toggle
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_nav_toggle' ) ) {
		function sf_nav_toggle () {
		?>
		<h3 class="nav-toggle icon"><a href="#navigation"><?php _e( 'Navigation', 'sfwp-locale' ); ?></a></h3>
		<?php
		} // End sf_nav_toggle()
	}
	add_action( 'sf_header_before', 'sf_nav_toggle', 20 );


	/**
	 * Widgetized header area, add the code inside the header area
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_header_widgetized' ) ) {
		function sf_header_widgetized() {
			if ( sf_active_sidebar( 'header' ) ) {
		?>
			<div class="header-widget">
				<?php sf_sidebar( 'header' ) ?>
			</div>
		<?php
			}
		}
	}
	add_action( 'sf_header_inside', 'sf_header_widgetized' );


	/**
	 * Remove customizer options.
	 * @since	1.0
	 * @param	object $wp_customize
	 */
	function wp_remove_customize_page(){
		global $submenu;
		unset($submenu['themes.php'][6]);
	}
	if ( isset( $sf_options[ 'sf_wp_remove_customize_page' ] ) && $sf_options[ 'sf_wp_remove_customize_page' ] == 'true' ) {
		add_action( 'admin_menu', 'wp_remove_customize_page');
	}	


	/**
	 * Remove items from WordPress Toolbar.
	 * @since	1.0
	 * @param	object $wp_admin_bar
	 */
	function wp_remove_toolbar_items( $wp_admin_bar ) {
		$wp_admin_bar->remove_menu( 'wp-logo' );		// Remove the WordPress logo
		$wp_admin_bar->remove_menu( 'customize' );		// Remove the Customize
		$wp_admin_bar->remove_menu( 'comments' );		// Remove the comments link
	}
	if ( isset( $sf_options[ 'sf_wp_remove_toolbar_items' ] ) && $sf_options[ 'sf_wp_remove_toolbar_items' ] == 'true' ) {
		add_action( 'admin_bar_menu', 'wp_remove_toolbar_items', 999 );
	}


	/**
	 * Remove Emojis.
	 * @since	1.0
	 * @return	void
	 */
	if ( isset( $sf_options[ 'sf_wp_remove_dns_prefetch' ] ) && $sf_options[ 'sf_wp_remove_dns_prefetch' ] == 'true' ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}


	/**
	 * Remove DNS Prefetch.
	 * @since	1.0
	 * @return	void
	 */
	if ( isset( $sf_options[ 'sf_wp_remove_emojis' ] ) && $sf_options[ 'sf_wp_remove_emojis' ] == 'true' ) {
		remove_action( 'wp_head', 'wp_resource_hints', 2 );
	}


	/**
	 * Remove REST API.
	 * @since	1.0
	 * @return	void
	 */
	if ( isset( $sf_options[ 'sf_wp_remove_rest_api' ] ) && $sf_options[ 'sf_wp_remove_rest_api' ] == 'true' ) {
		// Disable REST API link tag
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		// Disable oEmbed Discovery Links
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
		// Disable REST API link in HTTP headers
		remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
	}


	/**
	 * Remove canonical URLs.
	 * @since	1.0
	 * @return	void
	 */
	if ( isset( $sf_options[ 'sf_wp_remove_canonical_urls' ] ) && $sf_options[ 'sf_wp_remove_canonical_urls' ] == 'true' ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}


	/**
	 * Remove Shortlink.
	 * @since	1.0
	 * @return	void
	 */
	if ( isset( $sf_options[ 'sf_wp_remove_shortlink' ] ) && $sf_options[ 'sf_wp_remove_shortlink' ] == 'true' ) {
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	}


	/**
	 * Remove Really Simple Discovery.
	 * @since	1.0
	 * @return	void
	 */
	if ( isset( $sf_options[ 'sf_wp_remove_rsd' ] ) && $sf_options[ 'sf_wp_remove_rsd' ] == 'true' ) {
		remove_action ('wp_head', 'rsd_link');
	}


	/**
	 * Remove Windows Live Writer.
	 * @since	1.0
	 * @return	void
	 */
	if ( isset( $sf_options[ 'sf_wp_remove_wlw' ] ) && $sf_options[ 'sf_wp_remove_wlw' ] == 'true' ) {
		remove_action( 'wp_head', 'wlwmanifest_link' );
	}


/*
Customizing WooCommerce
**************************************************

Shop Page
	Product columns
	1 2 3 4
	Products per page
	1-24
	Display product image				Checked
	Display product title				Checked
	Display rating						Checked
	Display price						Checked
	Display add to cart button			Checked		Fixed

Product Page
	Display product title				Checked
	Display product image				Checked
	Display gallery images				Checked
	Display product ratings				Checked
	Display product description tab		Checked
	Display reviews tab					Checked
	Display upsell products				Checked
	Display related products			Checked		Fixed

Cart Page
	Display coupon field				Checked
	Display upsell products				Checked

Checkout Page
	Display coupon field				Checked
	Distraction free checkout			
*/

	/*
	 * WooCommerce Product Data Tabs
	 */
	if ( isset( $sf_options[ 'sf_wc_remove_product_tabs' ] ) && $sf_options[ 'sf_wc_remove_product_tabs' ] == 'true' ) {
		add_filter( 'woocommerce_product_tabs', 'sf_wc_remove_product_tabs', 98 );
	}


	/*
	 * WooCommerce Related Product
	 */
	if ( isset( $sf_options[ 'sf_wc_remove_related_products' ] ) && $sf_options[ 'sf_wc_remove_related_products' ] == 'true' ) {
		add_filter( 'woocommerce_related_products_args', 'sf_wc_remove_related_products', 10 ); 
	}
	
	/*
	 * WooCommerce Add to Cart
	 */
	if ( isset( $sf_options[ 'sf_wc_remove_add_to_cart' ] ) && $sf_options[ 'sf_wc_remove_add_to_cart' ] == 'true' ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}


	/*
	 * WooCommerce Orderby Dropdown
	 */
	if ( isset( $sf_options[ 'sf_wc_remove_orderby_dropdown' ] ) && $sf_options[ 'sf_wc_remove_orderby_dropdown' ] == 'true' ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}


	/*
	 * WooCommerce Showing Results
	 */
	if ( isset( $sf_options[ 'sf_wc_remove_showing_results' ] ) && $sf_options[ 'sf_wc_remove_showing_results' ] == 'true' ) {
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	}


	/*
	 * WooCommerce Product Meta
	 */
	if ( isset( $sf_options[ 'sf_wc_remove_meta' ] ) && $sf_options[ 'sf_wc_remove_meta' ] == 'true' ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	}


	/*
	 * WooCommerce Product Data Tabs
	 * Remove product data tabs from singel product page
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_wc_remove_product_tabs' ) ) {
		function sf_wc_remove_product_tabs( $tabs ) {
			unset( $tabs['description'] );  			// Remove the description tab
			unset( $tabs['reviews'] ); 					// Remove the reviews tab
			unset( $tabs['additional_information'] );  	// Remove the additional information tab
			return $tabs;
		}
	}


	/*
	 * WooCommerce Product Related
	 * Remove product related from singel product page 
	 *
	 * @since	1.0
	 * @return	void
	 */
	 if ( ! function_exists( 'sf_wc_remove_related_products' ) ) {
		function sf_wc_remove_related_products( $args ) {
			return array();
		}
	}

?>