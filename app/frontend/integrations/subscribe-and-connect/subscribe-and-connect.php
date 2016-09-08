<?php
/**
 * Integrates this theme with the Subscribe & Connect by plugin
 * http://wordpress.org/plugins/subscribe-and-connect/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 */
function sf_subscribe_and_connect_scripts() {
	wp_register_style( 'sf-subscribe-and-connect-css', get_template_directory_uri() . '/app/frontend/integrations/subscribe-and-connect/css/subscribe-and-connect.css' );
	wp_enqueue_style( 'sf-subscribe-and-connect-css' );
}
add_action( 'wp_enqueue_scripts', 'sf_subscribe_and_connect_scripts' );