<?php
/**
 * Page Content Template
 *
 * This template is the default page content template. It is used to display the content of the
 * `page.php` template file, contextually, as well as in archive lists or search results.
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

$heading_tag = 'h1';
if ( is_front_page() ) { $heading_tag = 'h2'; }
$title_before = '<' . $heading_tag . ' class="title entry-title">';
$title_after = '</' . $heading_tag . '>';

$page_link_args = apply_filters( 'sf_pagelinks_args', array( 'before' => '<div class="page-link">' . __( 'Pages:', 'sfwp-locale' ), 'after' => '</div>' ) );

sf_page_before();
?>
<article <?php post_class(); ?>>
<?php
	sf_page_inside_before();
?>
	<header>
		<?php the_title( $title_before, $title_after ); ?>
	</header>

	<section class="entry">
	    <?php
	    	if ( ! is_singular() ) {
	    		the_excerpt();
	    	} else {
	    		the_content( __( 'Continue Reading &rarr;', 'sfwp-locale' ) );
	    	}
	    	wp_link_pages( $page_link_args );
	    ?>
	</section><!-- /.entry -->
	<div class="fix"></div>
<?php
	sf_page_inside_after();
?>
</article><!-- /.post -->
<?php
	sf_page_after();
	$comm = get_option( 'sf_comments' );
	if ( ( $comm == 'page' || $comm == 'both' ) && is_page() ) { comments_template(); }
?>