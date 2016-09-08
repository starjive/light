<?php
/**
 * Integrates this theme with the Gravity Forms plugin
 * http://www.gravityforms.com/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 */
function sf_gravity_forms_scripts() {
	wp_register_style( 'sf-gravity-forms', get_template_directory_uri() . '/app/frontend/integrations/gravity-forms/css/gravity-forms.css' );
	wp_enqueue_style( 'sf-gravity-forms' );
}
add_action( 'wp_enqueue_scripts', 'sf_gravity_forms_scripts', 50 );