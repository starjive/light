<?php
/**
 * Contains checks to see if plugins are active and then loads logic accordingly
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Checks if plugins are activated and loads logic accordingly
 * @uses  class_exists() detect if a class exists
 * @uses  function_exists() detect if a function exists
 * @uses  defined() detect if a constant is defined
 */

/**
 * Testimonials by WooThemes
 * http://wordpress.org/plugins/testimonials-by-woothemes/
 */
if ( class_exists( 'Woothemes_Testimonials' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/testimonials/testimonials.php' );
}

/**
 * Our Team by WooThemes
 * http://wordpress.org/plugins/our-team-by-woothemes/
 */
if ( class_exists( 'Woothemes_Our_Team' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/our-team/our-team.php' );
}

/**
 * Projects by WooThemes
 * @link http://wordpress.org/plugins/projects-by-woothemes/
 */
if ( class_exists( 'Projects' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/projects/setup.php' );
	require_once( get_template_directory() . '/app/frontend/integrations/projects/template.php' );
	require_once( get_template_directory() . '/app/frontend/integrations/projects/functions.php' );
}

/**
 * WooSlider by WooThemes
 * http://www.starjive.com/products/wooslider/
 */
if ( class_exists( 'WooSlider' ) ) {
	if ( version_compare( get_option( 'wooslider-version' ), '2.0.2' ) >= 0 ) {
		require_once( get_template_directory() . '/app/frontend/integrations/wooslider/wooslider.php' );
	}
}

/**
 * WooCommerce by WooThemes
 * @link http://wordpress.org/plugins/woocommerce/
 */
if ( is_woocommerce_activated() ) {
	require_once( get_template_directory() . '/app/frontend/integrations/woocommerce/setup.php' );
	require_once( get_template_directory() . '/app/frontend/integrations/woocommerce/template.php' );
	require_once( get_template_directory() . '/app/frontend/integrations/woocommerce/functions.php' );
}

/**
 * Features by WooThemes
 * @link http://wordpress.org/plugins/features-by-woothemes/
 */
if ( class_exists( 'Woothemes_Features' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/features/features.php' );
}

/**
 * Archives by WooThemes
 * @link http://wordpress.org/plugins/archives-by-woothemes/
 */
if ( class_exists( 'Woothemes_Archives' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/archives/archives.php' );
}

/**
 * Subscribe and Connect by WooThemes
 * @link http://wordpress.org/plugins/subscribe-and-connect/
 */
if ( class_exists( 'Subscribe_And_Connect' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/subscribe-and-connect/subscribe-and-connect.php' );
}

/**
 * Sensei by WooThemes
 * @link http://www.woothemes.com/products/sensei/
 */
if ( class_exists( 'Woothemes_Sensei' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/sensei/setup.php' );
	require_once( get_template_directory() . '/app/frontend/integrations/sensei/template.php' );
	require_once( get_template_directory() . '/app/frontend/integrations/sensei/functions.php' );
}

/**
 * Gravity Forms
 * @link http://www.gravityforms.com
 */
if ( class_exists( 'GFForms' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/gravity-forms/gravity-forms.php' );
}

/**
 * Jetpack
 * @link http://wordpress.org/plugins/jetpack/
 */
if ( class_exists( 'Jetpack' ) ) {
	require_once( get_template_directory() . '/app/frontend/integrations/jetpack/jetpack.php' );
}