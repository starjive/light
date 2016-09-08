<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sensei Logic
 */

if ( ! function_exists( 'sf_sensei_support' ) ) {
	function sf_sensei_support() {
		/**
		* Compatibility
		* Declare Sensei support
		*/
		add_theme_support( 'sensei' );
	}
}

if ( ! function_exists( 'sf_sensei_css' ) ) {
	/**
	 * Sensei css
	 * Enqueues Sensei CSS
	 */
	function sf_sensei_css() {
		wp_register_style( 'sf-sensei-css', get_template_directory_uri() . '/app/frontend/integrations/sensei/css/sensei.css' );
		wp_enqueue_style( 'sf-sensei-css' );
	}
}

if ( ! function_exists( 'sf_sensei_remove_pagination' ) ) {
	/**
	 * Sensei pagination
	 * Removes Sensei pagination
	 */
	function sf_sensei_remove_pagination() {
		global $sf_sensei;
		remove_action( 'sensei_pagination', array( $sf_sensei->frontend, 'sensei_output_content_pagination' ), 10 );
	}
}

if ( ! function_exists( 'sf_sensei_remove_wrappers' ) ) {
	/**
	 * Sensei wrappers
	 * Removes Sensei wrappers
	 */
	function sf_sensei_remove_wrappers() {
		global $sf_sensei;
		remove_action( 'sensei_before_main_content', array( $sf_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
		remove_action( 'sensei_after_main_content', array( $sf_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );
	}
}

if (!function_exists('sf_custom_breadcrumbs_args')) {
	/**
	 * Custom Breadcrumb for Sensei pages
	 * @param  array $args
	 * @return array @args modified
	 */
	function sf_custom_breadcrumbs_args ( $args ) {
		if ( !is_sensei() ) {
			return $args;
		}
		$textdomain = 'sfwp-locale';
		$args = apply_filters( 'sf_sensei_breadcrumb_args', array( 'show_home' => __( 'Home', $textdomain ) ) );
		return $args;
	} // End sf_custom_breadcrumbs_args()
}

