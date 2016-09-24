<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;


function sf_setup_screen_header_footer () {
	/**
	 * Setup the default Framework admin screen header.
	 * @since	1.0
	 */
	add_action( 'sf_screen_get_header', array( 'SF_Screen', 'get_header' ), 10, 2 );

	/**
	 * Setup the default Framework admin screen footer.
	 * @since	1.0
	 */
	add_action( 'sf_screen_get_footer', array( 'SF_Screen', 'get_footer' ), 10, 2 );
} // End sf_setup_screen_header_footer()

add_action( 'admin_init', 'sf_setup_screen_header_footer' );

/**
 * Set the default placeholder image URL to the default image provided within the Framework.
 * @since	1.0
 * @param  string $url The current empty placeholder image URL.
 * @return string      The default placeholder image URL.
 */
function sf_set_default_placeholder_image_url ( $url ) {
	if ( '' == $url ) {
		return SF()->get_assets_url() . 'images/placeholder.png';
	} else {
		return $url;
	}
} // End sf_set_default_placeholder_image_url()

/**
 * Set the default placeholder image path to the default image provided within the Framework.
 * @since	1.0
 * @param  string $path The current empty placeholder image path.
 * @return string       The default placeholder image path.
 */
function sf_set_default_placeholder_image_path ( $path ) {
	if ( '' == $path ) {
		return SF()->get_assets_path() . 'images/placeholder.png';
	} else {
		return $path;
	}
} // End sf_set_default_placeholder_image_path()

if ( true == (bool)apply_filters( 'sf_use_default_placeholder_image', false ) ) {
	add_filter( 'sf_placeholder_image_url', 'sf_set_default_placeholder_image_url' );
	add_filter( 'sf_placeholder_image_path', 'sf_set_default_placeholder_image_path' );
}

/**
 * Enqueue menu.css.
 * Used to control the display of Framework menu items across the dashboard
 * @since	1.0
 * @return	void
 */
function sf_menu_styles() {
	$token = 'sf';
	$sf_version = sf_get_version();

	wp_register_style( $token . '-menu', esc_url( SF()->get_assets_url() . 'css/menu.css' ), array(), $sf_version );
	wp_enqueue_style( $token . '-menu' );
}

add_action( 'admin_enqueue_scripts', 'sf_menu_styles' );

if ( ! function_exists( 'sf_update_options_filter' ) ) {
	function sf_update_options_filter( $new_value, $old_value ) {
		if ( !current_user_can( 'unfiltered_html' ) ) {
			// Options that get KSES'd
			foreach( sf_ksesed_option_keys() as $option ) {
				$new_value[$option] = wp_kses_post( $new_value[$option] );
			}
			// Options that cannot be set without unfiltered HTML
			foreach( sf_disabled_if_not_unfiltered_html_option_keys() as $option ) {
				$new_value[$option] = $old_value[$option];
			}
		}
		return $new_value;
	}
}

if ( ! function_exists( 'sf_prevent_option_update' ) ) {
	function sf_prevent_option_update( $new_value, $old_value ) {
		return $old_value;
	}
}

/**
 * This is the list of options that are run through KSES on save for users without
 * the unfiltered_html capability
 */
if ( ! function_exists( 'sf_ksesed_option_keys' ) ) {
	function sf_ksesed_option_keys() {
		return array();
	}
}

/**
 * This is the list of standalone options that are run through KSES on save for users without
 * the unfiltered_html capability
 */
if ( ! function_exists( 'sf_ksesed_standalone_options' ) ) {
	function sf_ksesed_standalone_options() {
		return array( 'sf_footer_left_text', 'sf_footer_right_text', 'sf_connect_content' );
	}
}

/**
 * This is the list of options that users without the unfiltered_html capability
 * are not able to update
 */
if ( ! function_exists( 'sf_disabled_if_not_unfiltered_html_option_keys' ) ) {
	function sf_disabled_if_not_unfiltered_html_option_keys() {
		return array( 'sf_google_analytics', 'sf_custom_css' );
	}
}

add_filter( 'pre_update_option_sf_options', 'sf_update_options_filter', 10, 2 );
foreach( sf_ksesed_standalone_options() as $o ) {
	add_filter( 'pre_update_option_' . $o, 'wp_kses_post' );
}
unset( $o );

if ( ! function_exists( 'sf_admin_menu_after' ) ) {
/**
 * Load Framework menu items that should always appear last.
 * @since	1.0
 * @return	void
 */
function sf_admin_menu_after () {
	global $current_user;
	$current_user_id = $current_user->user_login;
	$super_user = apply_filters( 'sf_super_user', '' );

	do_action( 'sf_admin_menu_after_before_defaults' );

	// Update Framework Menu Item
	if( ( $super_user == $current_user_id || empty( $super_user ) ) && current_user_can( 'install_themes' ) ) {
		$sf_update_page = add_submenu_page( 'sf', 'Framework Update', 'Update Framework', 'manage_options', 'sf_update', 'sf_update_page' );
	}

	do_action( 'sf_admin_menu_after' );
} // End sf_admin_menu_after()
}

add_action( 'admin_menu', 'sf_admin_menu_after', 50 );

// If this is the Listings theme, add the Content Builder admin menu item.
if ( function_exists( 'sf_content_builder_menu' ) ) {
	add_action( 'sf_admin_menu_after_before_defaults', 'sf_content_builder_menu' );
}

/**
 * Unset the interal Framework admin menu items, and preserve the screens themselves (linked to elsewhere).
 * @since	1.0
 * @return	void
 */
function sf_unset_internal_sf_menu_items () {
	remove_submenu_page( 'sf', 'sf-backup' );
	remove_submenu_page( 'sf', 'sf_update' );
} // End sf_unset_internal_sf_menu_items()

add_action( 'admin_head', 'sf_unset_internal_sf_menu_items' );

/**
 * Load admin CSS on specific screens.
 * @since	1.0
 * @return	void
 */
function sf_load_admin_css () {
	$load_on = (array)apply_filters( 'sf_load_admin_css', array( 'sf', 'sf-framework', 'sf-backup' ) );
	wp_register_style( 'sf-admin', esc_url( SF()->get_assets_url() . 'css/admin.css' ), array(), '1.0.0', 'all' );

	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $load_on ) )
		wp_enqueue_style( 'sf-admin' );
} // End sf_load_admin_css()

add_action( 'admin_enqueue_scripts', 'sf_load_admin_css' );

/**
 * Make sure to flush the rewrite rules when saving on the settings screen.
 * @since	1.0
 */
add_action( 'sf_settings_save_before', 'sf_flush_rewriterules' );

global $pagenow;
if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'sf' ) {
	if ( get_option( 'sf_version_checker' ) == 'true' ) { add_action( 'admin_notices', 'sf_update_notice', 10 ); }

	add_action( 'admin_notices', 'sf_critical_update_notice', 8 ); // Periodically check for critical Framework updates.
}

/**
 * sf_update_notice function.
 *
 * @description Notify users of framework updates, if necessary.
 * @since	1.0
 * @access	public
 * @return	void
 */
if ( ! function_exists( 'sf_update_notice' ) ) {
	function sf_update_notice () {
		$local_version = get_option( 'sf_version' );
		if ( $local_version == '' ) { return; }
		$update_data = sf_version_checker( $local_version );

		$html = '';

		if ( is_array( $update_data ) && $update_data['is_update'] == true ) {
			$html = '<div id="sf_update" class="updated fade"><p>' . sprintf( __( 'Framework update is available (v%s). %sDownload new version%s (%sSee Changelog%s)', 'sfwp-locale' ), $update_data['version'], '<a href="' . admin_url( 'admin.php?page=sf_update' ) . '">', '</a>', '<a href="http://starjive.com/wordpress/themes/light-framework/updates/functions-changelog.txt" target="_blank" title="Changelog">', '</a>' ) . '</p></div>';
		}

		if ( $html != '' ) { echo $html; }
	} // End sf_update_notice()
}

/**
 * sf_critical_update_notice function.
 *
 * @description Notify users of critical framework updates, if necessary.
 * @since	1.0
 * @access	public
 * @return	void
 */
if ( ! function_exists( 'sf_critical_update_notice' ) ) {
	function sf_critical_update_notice () {
		// Determine if the check has happened.
		$critical_update = get_transient( 'sf_critical_update' );
		$critical_update_data = get_transient( 'sf_critical_update_data' );

		if ( ! $critical_update || ! is_array( $critical_update_data ) ) {

			$local_version = get_option( 'sf_version' );
			if ( $local_version == '' ) { return; }

			$update_data = sf_version_checker( $local_version, true );

			// Set this to "has been checked" for 2 weeks.
			set_transient( 'sf_critical_update', true, 60*60*336 );

			// Cache the data as well.
			set_transient( 'sf_critical_update_data', $update_data, 60*60*336 );
		} else {
			$update_data = $critical_update_data;
		}

		$html = '';

		// Generate output based on returned/stored data.
		if ( is_array( $update_data ) && $update_data['is_update'] == true && $update_data['is_critical'] == true ) {

			// Remove the generic update notice.
			remove_action( 'admin_notices', 'sf_update_notice', 10 );

			$html = '<div id="sf_important_update" class="error fade"><p>' . sprintf( __( 'An important Framework update is available (v%s). %sDownload new version%s (%sSee Changelog%s)', 'sfwp-locale' ), $update_data['version'], '<a href="' . admin_url( 'admin.php?page=sf_update' ) . '">', '</a>', '<a href="http://starjive.com/wordpress/themes/light-framework/updates/functions-changelog.txt" target="_blank" title="Changelog">', '</a>' ) . '</p></div>';
		}

		if ( $html != '' ) { echo $html; }
	} // End sf_critical_update_notice()
}

?>