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

$title_before = '<h1 class="title entry-title">';
$title_after = '</h1>';

if ( ! is_single() ) {
	$title_before = $title_before . '<a href="' . get_permalink( get_the_ID() ) . '" rel="bookmark" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">';
	$title_after = '</a>' . $title_after;
}

$page_link_args = apply_filters( 'sf_pagelinks_args', array( 'before' => '<div class="page-link">' . __( 'Pages:', 'sfwp-locale' ), 'after' => '</div>' ) );

sf_post_before();
?>
<article <?php post_class(); ?>>
<?php
	sf_post_inside_before();
?>
	<header>
		<?php the_title( $title_before, $title_after ); ?>
	</header>
<?php
	sf_post_meta();
?>
	<section class="entry">
	    <?php
	    	the_content();
	    	wp_link_pages( $page_link_args );
	    ?>
	</section><!-- /.entry -->
<?php
	sf_post_inside_after();
?>
</article><!-- /.post -->
<?php
	sf_post_after();
	comments_template();
?>