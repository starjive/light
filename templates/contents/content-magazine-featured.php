<?php
/**
 * Magazine Featured Content Template
 *
 * This template is used for the posts in the featured area on the
 * "Magazine" page template.
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

 global $sf_options, $more;
 $more = 0;

 $title_before = '<h2 class="title entry-title"><a href="' . get_permalink( get_the_ID() ) . '" rel="bookmark" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">';
 $title_after = '</a></h2>';

 $page_link_args = apply_filters( 'sf_pagelinks_args', array( 'before' => '<div class="page-link">' . __( 'Pages:', 'sfwp-locale' ), 'after' => '</div>' ) );

 sf_post_before();
?>
<article <?php post_class(); ?>>
<?php
	sf_post_inside_before();

	if ( ( ( isset($sf_options['sf_magazine_f_w']) ) && ( ( $sf_options['sf_magazine_f_w'] <= 0 ) || ( $sf_options['sf_magazine_f_w'] == '')  ) ) || ( !isset($sf_options['sf_magazine_f_w']) ) ) {	$sf_options['sf_magazine_f_w'] = '100'; }
	if ( ( isset($sf_options['sf_magazine_f_h']) ) && ( $sf_options['sf_magazine_f_h'] <= 0 )  ) { $sf_options['sf_magazine_f_h'] = '100'; }

	if ( isset( $sf_options['sf_magazine_featured_post_content'] ) && $sf_options['sf_magazine_featured_post_content'] != 'content' ) {
	?>
		<a href="<?php echo get_permalink(); ?>"><?php sf_image( 'link=img&width='.$sf_options['sf_magazine_f_w'].'&height='.$sf_options['sf_magazine_f_h'].'&class=thumbnail '.$sf_options['sf_magazine_f_align'] ); ?></a>
	<?php
	}
?>
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
	    	if ( isset( $sf_options['sf_magazine_featured_post_content'] ) && ( $sf_options['sf_magazine_featured_post_content'] == 'content' ) ) {
	    		the_content( __( 'Continue Reading &rarr;', 'sfwp-locale' ) );
	    	} else {
	    		the_excerpt();
	    	}
	    ?>
	</section><!-- /.entry -->
	<div class="fix"></div>
<?php
	sf_post_inside_after();
?>
</article><!-- /.post -->
<?php
	sf_post_after();
?>