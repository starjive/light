<?php
/**
 * Light functions.
 *
 * @package light
 */

	/**
	 * Set path to Framework and theme specific functions.
	 *
	 * @since	1.0
	 * @return	void
	 */
	$functions_path = get_template_directory() . '/app/backend/';
	$includes_path = get_template_directory() . '/app/frontend/';

	/**
	 * Framework init.
	 *
	 * @since	1.0
	 * @return	void
	 */
	require_once ( $functions_path . 'init.php' );	// Framework Init

	/**
	 * Load the theme-specific files, with support for overriding via a child theme.
	 *
	 * @since	1.0
	 * @return	void
	 */
	$includes = array(
		'app/frontend/options.php', 									// Options settings & custom settings
		'app/frontend/functions.php', 									// Theme functions
		'app/frontend/actions.php', 									// Theme actions & user defined hooks
		'app/frontend/comments.php', 									// Custom comments/pingback loop
		'app/frontend/enqueue.php', 									// Load JavaScript & Styles
		'app/frontend/integrations.php',								// Plugin integrations
		'app/frontend/widgets.php',										// Widgets
		'app/frontend/shortcodes.php',	 								// Theme shortcodes
		'app/frontend/extensions/meta/meta.php',						// Extensions Meta Manager
		'app/frontend/extensions/hooks/hooks.php',						// Extensions Hook Manager
	);

	/**
	 * Allow child themes/plugins to add widgets to be loaded.
	 *
	 * @since	1.0
	 * @return	void
	 */
	$includes = apply_filters( 'sf_includes', $includes );
	foreach ( $includes as $i ) {
		locate_template( $i, true );
	}

?>