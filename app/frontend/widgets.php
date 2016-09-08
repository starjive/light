<?php
if ( ! defined( 'ABSPATH' ) ) exit;

	/**
	 * Load the widgets, with support for overriding the widget via a child theme.
	 *
	 * @since	1.0
	 * @return	void
	 */
	$widgets = array(
		'app/frontend/widgets/announce.php',
		'app/frontend/widgets/blogauthor.php',
		'app/frontend/widgets/embed.php',
		'app/frontend/widgets/flickr.php',
		'app/frontend/widgets/subscribe.php',
		'app/frontend/widgets/tabs.php',
		'app/frontend/widgets/component.php'
	);

	/**
	 * Allow child themes/plugins to add widgets to be loaded.
	 *
	 * @since	1.0
	 * @return	void
	 */
	$widgets = apply_filters( 'sf_widgets', $widgets );
	
	foreach ( $widgets as $w ) {
		locate_template( $w, true );
	}
