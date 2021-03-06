<?php
/**
 * Post Content Template
 *
 * This template is the default page content template. It is used to display the content of the
 * `single.php` template file, contextually, as well as in archive lists or search results.
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

$settings = array(
				'thumb_w' => 100,
				'thumb_h' => 100,
				'thumb_align' => 'alignleft',
				'post_content' => 'excerpt',
				'comments' => 'both'
				);

$settings = sf_get_dynamic_values( $settings );

$title_before = '<h1 class="title entry-title">';
$title_after = '</h1>';

if ( ! is_single() ) {
	$title_before = '<h2 class="title entry-title">';
	$title_after = '</h2>';
$title_before = $title_before . '<a href="' . esc_url( get_permalink( get_the_ID() ) ) . '" rel="bookmark" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">';
$title_after = '</a>' . $title_after;
}

$page_link_args = apply_filters( 'sf_pagelinks_args', array( 'before' => '<div class="page-link">' . __( 'Pages:', 'sfwp-locale' ), 'after' => '</div>' ) );

sf_post_before();
?>
<article <?php post_class(); ?>>
<?php
	sf_post_inside_before();
	if ( 'content' != $settings['post_content'] && ! is_singular() )
		sf_image( 'width=' . esc_attr( $settings['thumb_w'] ) . '&height=' . esc_attr( $settings['thumb_h'] ) . '&class=thumbnail ' . esc_attr( $settings['thumb_align'] ) );
?>
	<header>
		<?php the_title( $title_before, $title_after ); ?>
	</header>
<?php
	sf_post_meta();
?>
	<section class="entry">
	    <?php
	    	 the_excerpt();
	    ?>
	</section><!-- /.entry -->
	<div class="fix"></div>
<?php
	sf_post_inside_after();
?>
</article><!-- /.post -->
<?php
sf_post_after();
$comm = $settings['comments'];
if ( ( 'post' == $comm || 'both' == $comm ) && is_single() ) { comments_template(); }
?>