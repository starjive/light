<?php
/**
 * Default Content Template
 *
 * This template is the default content template. It is used to display the content of a
 * template file, when no more specific content-*.php file is available.
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
<?php
	sf_post_inside_before();
?>
	<section class="entry">
	    <?php
	    	the_content();
	    	if ( isset( $sf_options['sf_post_content'] ) && 'content' == $sf_options['sf_post_content'] || is_singular() ) wp_link_pages( $page_link_args );
	    ?>
	</section><!-- /.entry -->
	<div class="fix"></div>
<?php
	sf_post_inside_after();
?>
</article><!-- /.post -->
<?php
	sf_post_after();
/*
	$comm = $sf_options[ 'sf_comments' ];
	if ( ( $comm == 'page' || $comm == 'both' ) && is_page() ) { comments_template(); }
*/
?>