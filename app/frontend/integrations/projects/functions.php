<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 * @since	1.0
 * @return  void
 */
function sf_projects_scripts() {
	wp_register_style( 'sf-projects-css', get_template_directory_uri() . '/app/frontend/integrations/projects/css/projects.css' );
	wp_enqueue_style( 'sf-projects-css' );
}

/**
 * Support Declaration
 * @since	1.0
 * @return  void
 */
function sf_projects_support() {
	add_theme_support( 'projects-by-woothemes' );
}

/**
 * Custom Body Class
 * @since	1.0
 * @return  array
 */
function sf_simplify_layout_body_class( $classes ) {
	$settings = sf_get_dynamic_values( array( 'simplify_layout' => 'false' ) );
	if ( 'true' == $settings['simplify_layout'] ) {
		$classes[] = 'simplify-layout';
	}
	// return the $classes array
	return $classes;
}

/**
 * Old Portfolio Layout
 * @since	1.0
 * @return  void
 */
function sf_projects_maybe_remove_description() {
	$settings = sf_get_dynamic_values( array( 'simplify_layout' => 'false' ) );
	if ( 'false' == $settings['simplify_layout'] ) return;
	remove_action( 'projects_after_loop_item', 'projects_template_short_description', 10 );
}