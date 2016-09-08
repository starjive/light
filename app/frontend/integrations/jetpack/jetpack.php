<?php
/**
 * Integrates this theme with the Jetpack plugin
 * http://wordpress.org/plugins/jetpack/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 * @since	1.0
 * @return	void
 */
function sf_jetpack_scripts() {
	// Add our own
	wp_register_style( 'sf-jetpack', esc_url( get_template_directory_uri() . '/app/frontend/integrations/jetpack/css/jetpack.css' ) );
	wp_enqueue_style( 'sf-jetpack' );
}
add_action( 'wp_enqueue_scripts', 'sf_jetpack_scripts', 10 );

/**
 * Declare support for Jetpack Infinite Scroll
 * @since	1.0
 * @return	void
 */
function sf_infinite_scroll_init() {
	add_theme_support( 'infinite-scroll', array(
		'container'			=> 'main',
		'footer_widgets'	=> array( 'footer-1', 'footer-2', 'footer-3', 'footer-4' ),
		'footer'			=> 'inner-wrapper',
	) );
}
add_action( 'after_setup_theme', 'sf_infinite_scroll_init' );

/**
 * Add support for Jet Infinite Scroll in the blog post archive
 * @since	1.0
 * @return	void
 */
function sf_infinite_scroll_archive_support() {
	$supported = current_theme_supports( 'infinite-scroll' ) && ( is_home() || is_archive() || is_search() );
	return $supported;
}
add_filter( 'infinite_scroll_archive_supported', 'sf_infinite_scroll_archive_support' );