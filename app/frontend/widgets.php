<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

	/**
	 * Widgetized areas
	 *
	 * @since		1.0
	 */
	if ( ! function_exists( 'the_widgets_init' ) ) {
		function the_widgets_init() {
			if ( ! function_exists( 'register_sidebars' ) )
				return;

			// Sidebars Primary
			register_sidebar( array(
				'name' => __( 'Primary', 'sfwp-locale' ),
				'id' => 'primary',
				'description' => __( 'The default primary sidebar for your website, used in two or three-column layouts.', 'sfwp-locale' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>'
			) );
			// Sidebars Secondary
			register_sidebar( array(
				'name' => __( 'Secondary', 'sfwp-locale' ),
				'id' => 'secondary',
				'description' => __( 'A secondary sidebar for your website, used in three-column layouts.', 'sfwp-locale' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>'
			) );
			// Footer
			$total = get_option( 'sf_footer_sidebars', 4 );
			if ( ! $total ) $total = 4;
			for ( $i = 1; $i <= intval( $total ); $i++ ) {
				register_sidebar( array(
					'name' => sprintf( __( 'Footer %d', 'sfwp-locale' ), $i ),
					'id' => sprintf( 'footer-%d', $i ),
					'description' => sprintf( __( 'Widgetized Footer Region %d.', 'sfwp-locale' ), $i ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<h3>',
					'after_title' => '</h3>'
				) );
			}
			// Page Template
			register_sidebar( array(
				'name' => __( '"Widgets" Page Template', 'sfwp-locale' ),
				'id' => 'widgets-page-template',
				'description' => __( 'The widgetized area used on the "Widgets" page template (displays only if widgets are added here). Defaults to page content if no widgets are added.', 'sfwp-locale' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>'
			) );
			// Header
			register_sidebar( array(
				'name' => __( 'Header', 'sfwp-locale' ),
				'id' => 'header', 'description' => __( 'Optional widgetized area in your right header', 'sfwp-locale' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h3>',
				'after_title' => '</h3>'
			) );
		} // End the_widgets_init()
	}
	add_action( 'init', 'the_widgets_init' );


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

?>