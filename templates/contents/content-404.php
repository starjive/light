<?php
/**
 * Error Content Template
 *
 * This template is the content template for error screens. It is used to display a message
 * to the viewer when no appropriate page can be found by WordPress.
 *
 * @subpackage Template
 */

/**
 * Settings for this template file.
 *
 * This is where the specify the HTML tags for the title.
 * These options can be filtered via a child theme.
 *
 * @link http://codex.wordpress.org/Plugin_API#Filters
 */

 global $sf_options;

 $title_before = '<h1 class="title entry-title">';
 $title_after = '</h1>';

 $page_link_args = apply_filters( 'sf_pagelinks_args', array( 'before' => '<div class="page-link">' . __( 'Pages:', 'sfwp-locale' ), 'after' => '</div>' ) );

 sf_post_before();
?>
<article <?php post_class(); ?>>
<?php sf_post_inside_before();	?>

	<header>
		<?php echo $title_before . apply_filters( 'sf_404_title', __( 'Error 404 - Page not found!', 'sfwp-locale' ) ) . $title_after; ?>
	</header>

	<section class="entry">
	    <?php
	    	echo apply_filters( 'sf_404_content', __( 'The page you are trying to reach does not exist, or has been moved. Please use the menus or the search box to find what you are looking for.', 'sfwp-locale' ) );
	    	if (  ( isset( $sf_options['sf_post_content'] ) && $sf_options['sf_post_content'] == 'content' ) || is_singular() ) wp_link_pages( $page_link_args );
	    ?>
	</section><!-- /.entry -->
<?php
	sf_post_inside_after();
?>
</article><!-- /.post -->
<?php
	sf_post_after();
?>