<?php
/**
 * Integrates this theme with the WooSlider by plugin
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Do not load theme JS
 * @since	1.0
 * @return boolean
 */
function sf_unload_slider_js() {
	return false;
}
add_filter( 'sf_load_slider_js', 'sf_unload_slider_js' );

/**
 * Styles
 * @since	1.0
 */
function sf_wooslider_scripts() {
	// Add our own
	wp_register_style( 'sf-wooslider-css', esc_url( get_template_directory_uri() . '/app/frontend/integrations/wooslider/css/wooslider.css' ) );
	wp_enqueue_style( 'sf-wooslider-css' );
}
add_action( 'wp_enqueue_scripts', 'sf_wooslider_scripts', 10 );

/**
 * Add extra layout types
 * @since	1.0
 * @return array
 */
function sf_add_custom_slider_layouts( $layouts ) {
	$layouts['text-none']	= array( 'name' => __( 'None', 'sfwp-locale' ), 'callback' => 'method' );
	$layouts['text-full'] 	= array( 'name' => __( 'Full', 'sfwp-locale' ), 'callback' => 'method' );
	$layouts['text-center']	= array( 'name' => __( 'Center', 'sfwp-locale' ), 'callback' => 'method' );
	return apply_filters( 'sf_custom_slider_layouts', $layouts );
}
add_filter( 'wooslider_posts_layout_types', 'sf_add_custom_slider_layouts' );

/**
 * Add extra theme types
 * @since	1.0
 * @return array
 */
function sf_add_custom_slider_themes( $themes ) {
	$themes['magazine'] = array( 'name' => __( 'Magazine', 'sfwp-locale' ), 'stylesheet' => esc_url( get_template_directory_uri() . '/app/frontend/integrations/wooslider/css/magazine.css' ) );
	$themes['business'] = array( 'name' => __( 'Business', 'sfwp-locale' ), 'stylesheet' => esc_url( get_template_directory_uri() . '/app/frontend/integrations/wooslider/css/business.css' ) );
	return apply_filters( 'sf_custom_slider_themes', $themes );
}
add_filter( 'wooslider_slider_themes', 'sf_add_custom_slider_themes' );

/**
 * Add .entry and .col-full to slides without a featured image
 * @since	1.0
 * @return string
 */
function sf_add_custom_content_wrapper( $content ) {
	return '<div class="entry col-full">' . $content . '</div>';
}
add_filter( 'wooslider_slide_content_slides', 'sf_add_custom_content_wrapper' );

/**
 * Slider Magazine Excerpt Length
 * @since	1.0
 * @return string
 */
function sf_add_custom_excerpt_length( $excerpt ) {
	// Set default values
	$settings = array(
		'slider_magazine_excerpt' 			=> 'true',
		'slider_magazine_excerpt_length' 	=> '15'
	);
	// Compare default values against Theme Options
	$settings = sf_get_dynamic_values( $settings );
	if ( 'true' == $settings[ 'slider_magazine_excerpt' ] ) {
		$excerpt = sf_text_trim( $excerpt, $settings[ 'slider_magazine_excerpt_length' ] );
	}
	return $excerpt;
}
add_filter( 'wooslider_posts_excerpt', 'sf_add_custom_excerpt_length' );

/**
 * Get Global Settings
 * @since	1.0
 * @return array
 */
function sf_slider_get_global_settings() {

	// Set default values
	$global_settings = array(
		'slider_interval' 					=> '4',
		'slider_speed' 						=> '0.5',
		'slider_auto'						=> 'true',
		'slider_hover' 						=> 'true',
		'slider_effect' 					=> 'slide',
		'slider_pagination'					=> 'true'
	);

	return apply_filters( 'sf_slider_global_settings', $global_settings );

}

/**
 * Magazine Slider
 * @since	1.0
 * @return	void
 */
function sf_wooslider_magazine() {

	// Set default values
	$settings = array(
		'slider_magazine_tags' 				=> '',
		'slider_magazine_entries' 			=> '5',
		'slider_magazine_title' 			=> 'true',
		'slider_magazine_excerpt' 			=> 'true'
	);

	// Get slider global settings
	$global_settings = sf_slider_get_global_settings();

	// Merge global & slider specific options
 	$settings = array_merge( $settings, $global_settings );

	// Compare default values against Theme Options
	$settings = sf_get_dynamic_values( $settings );

	// Translate options into something WooSlider can read
	$slider_settings = apply_filters( 'sf_slider_magazine_template_settings', array(
		'slider_type' 						=> 'posts',
		'smoothheight' 						=> 'true',
		'direction_nav'						=> 'true',
		'control_nav'						=> $settings[ 'slider_pagination' ],
		'pause_on_hover' 					=> $settings[ 'slider_hover' ],
		'slider_animation' 					=> $settings[ 'slider_effect' ],
		'autoslide' 						=> $settings[ 'slider_auto' ],
		'slideshow_speed'					=> $settings[ 'slider_interval' ],
		'animation_duration' 				=> $settings[ 'slider_speed' ]
		)
	);

	$slides_args = apply_filters( 'sf_slider_magazine_template_args', array(
		'link_title'						=> 'true',
		'tag' 								=> $settings[ 'slider_magazine_tags' ],
		'display_title'						=> $settings[ 'slider_magazine_title' ],
		'display_excerpt'					=> $settings[ 'slider_magazine_excerpt' ],
		'limit'								=> $settings[ 'slider_magazine_entries' ],
		'thumbnails'						=> '',
		'theme'								=> 'magazine'
		)
	);

	// Fire WooSlider.
    wooslider( $slider_settings, $slides_args );

	// Exclude posts from loop
	$count = 0;
	$exclude_posts = new WP_Query( array( 'tag' => $settings[ 'slider_magazine_tags' ], 'posts_per_page' => $settings[ 'slider_magazine_entries' ] ) );
	if ( $exclude_posts->have_posts() ) {
		while ( $exclude_posts->have_posts() ) {
			global $post;
			$exclude_posts->the_post();
			$shownposts[ $count++ ] = $post->ID;
		}
	}
	if ( get_option( 'sf_exclude' ) != $shownposts ) { update_option( "sf_exclude", $shownposts ); }

}

/**
 * Business Slider
 * @since	1.0
 * @return	void
 */
function sf_wooslider_business() {

	global $post;

	// Set default values
	$settings = array(
		'slider_biz_number'		 			=> '5',
		'slider_biz_title' 					=> 'true',
		'slider_biz_slide_group'			=> '',
		'slider_biz_order'					=> 'DESC',
		'slider_biz_overlay'				=> 'bottom'
	);

	// Get slider global settings
	$global_settings = sf_slider_get_global_settings();

	// Merge global & slider specific options
 	$settings = array_merge( $settings, $global_settings );

	// Compare default values against Theme Options
	$settings = sf_get_dynamic_values( $settings );

	// Translate options into something WooSlider can read
	$slider_settings = apply_filters( 'sf_slider_business_template_settings', array(
		'slider_type' 						=> 'slides',
		'smoothheight' 						=> 'true',
		'direction_nav'						=> 'true',
		'control_nav'						=> $settings[ 'slider_pagination' ],
		'pause_on_hover' 					=> $settings[ 'slider_hover' ],
		'slider_animation' 					=> $settings[ 'slider_effect' ],
		'autoslide' 						=> $settings[ 'slider_auto' ],
		'slideshow_speed'					=> $settings[ 'slider_interval' ],
		'animation_duration' 				=> $settings[ 'slider_speed' ]
		)
	);

	// Setup the "Slide Group", if one is set.
	$slide_page = '';
	$slide_page_obj	= get_term( $settings[ 'slider_biz_slide_group' ], 'slide-page' );

	if ( is_object( $slide_page_obj ) ) {
		if ( isset( $slide_page_obj->slug ) ) {
			$slide_page = $slide_page_obj->slug;
		}
	}

	// Get "Slide Group" from Page
	if ( isset( $post->ID ) ) {
		$stored_slide_page = get_post_meta( $post->ID, '_slide-page', true );
		if ( '0' == $stored_slide_page ) {
			$slide_page = ( isset( $slide_page ) && ! empty( $slide_page ) ) ? $slide_page : '';
		}
		if ( '' != $stored_slide_page && '0' != $stored_slide_page ) {
			$slide_page_obj	= get_term( $stored_slide_page, 'slide-page' );
			if ( is_object( $slide_page_obj ) ) {
				if ( isset( $slide_page_obj->slug ) ) {
					$slide_page = $slide_page_obj->slug;
				}
			}
		}
	}

	$slides_args = apply_filters( 'sf_slider_business_template_args', array(
		'slide_page'						=> $slide_page,
		'display_title'						=> $settings[ 'slider_biz_title' ],
		'limit'								=> $settings[ 'slider_biz_number' ],
		'layout'							=> 'text-' . $settings[ 'slider_biz_overlay' ],
		'order'								=> $settings[ 'slider_biz_order' ],
		'imageslide'						=> 'true',
		'link_slide'						=> 'true',
		'theme'								=> 'business'
		)
	);

	// Fire WooSlider.
    wooslider( $slider_settings, $slides_args );

}