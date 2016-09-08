<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

	/**
	 * Return the Framework version number.
	 *
	 * @since	1.0
	 * @return	string
	 */
	function sf_get_version () {
		return '6.2.5';
	}

	function sf_version_init () {
		$sf_version = sf_get_version();
		if ( get_option( 'sf_version' ) != $sf_version ) {
			update_option( 'sf_version', $sf_version );
		}
	} // End sf_version_init()
	add_action( 'init', 'sf_version_init', 10 );


	/**
	 * Return the Theme version number.
	 *
	 * @since	1.0
	 * @return	string
	 */
	function sf_version () {
		$data = sf_get_theme_version_data();
		echo "\n<!-- Theme Version -->\n";
		if ( isset( $data['is_child'] ) && true == $data['is_child'] ) echo '<meta name="generator" content="'. esc_attr( $data['child_theme_name'] . ' ' . $data['child_theme_version'] ) . '" />' ."\n";
		echo '<meta name="generator" content="'. esc_attr( $data['theme_name'] . ' ' . $data['theme_version'] ) . '" />' ."\n";
		echo '<meta name="generator" content="Framework '. esc_attr( $data['sf_version'] ) .'" />' ."\n";
	}


	/**
	 * Load the required Framework Files.
	 *
	 * @since	1.0
	 * @return	void
	 */
	$functions_path = get_template_directory() . '/app/backend/';
	$classes_path = $functions_path . 'classes/';


	/**
	 * Load core classes for the Framework.
	 *
	 * @since	1.0
	 * @return	void
	 */
	require_once( $classes_path . 'core.php' );                        // Framework core class.
	require_once( $classes_path . 'fields.php' );                      // Form fields generator class.
	require_once( $classes_path . 'fields-settings.php' );             // Theme settings class. Extends SF_Fields.
	require_once( $classes_path . 'fields-meta.php' );                 // Post meta fields class. Extends SF_Fields.
	require_once( $classes_path . 'settings.php' );                    // A class to handle all basic settings interactions.
	require_once( $classes_path . 'meta.php' );                        // Meta box generator class.


	/**
	 * Returns the main instance of SF to prevent the need to use globals.
	 *
	 * @since	1.0
	 * @return	object SF
	 */
	function SF() {
		return SF::instance();
	} // End SF()
	// Run the SF() function to generate the initial instance.
	SF();


	/**
	 * Load the other Framework files.
	 *
	 * @since	1.0
	 * @return	void
	 */
	require_once( $functions_path . 'functions.php' );					// Functions used in the Framework and in the theme files.
	require_once( $functions_path . 'setup.php' );						// Setup the Framework.
	require_once( $functions_path . 'interface.php' );					// Administration interfaces.
	require_once( $functions_path . 'hooks.php' );						// Contextual hooks.


	/**
	 * Load certain files only in the WordPress admin.
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( is_admin() ) {
		require_once( $classes_path . 'screen-admin-base.php' );		// Base class for common functionality used on more technical admin screens.
		require_once( $classes_path . 'screen.php' );					// Admin screen class.
		require_once( $classes_path . 'screen-welcome.php' );			// Welcome screen class.
		require_once( $classes_path . 'screen-framework.php' );			// Framework screen class.
		require_once( $classes_path . 'backup.php' );					// SF_Backup Class.
	}


	/**
	 * Add or remove Generator meta tags.
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( get_option( 'sf_disable_generator') == "true" ) {
		remove_action( 'wp_head',  'wp_generator' );
	} else {
		add_action( 'wp_head', 'sf_version', 10 );
	}

?>
