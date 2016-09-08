<?php
/**
 * Integrates this theme with the Archives plugin
 * http://wordpress.org/plugins/archives-by-woothemes/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Support Declaration
 * @since	1.0
 * @return  void
 */
function sf_archives_support() {
	add_theme_support( 'archives-by-woothemes' );
}
add_action( 'after_setup_theme', 'sf_archives_support' );


/**
 * Styles
 * @since	1.0
 * @return  void
 */
function sf_archives_scripts() {
	wp_register_style( 'sf-archives-css', get_template_directory_uri() . '/app/frontend/integrations/archives/css/archives.css' );
	wp_enqueue_style( 'sf-archives-css' );
}
add_action( 'wp_enqueue_scripts', 'sf_archives_scripts', 10 );