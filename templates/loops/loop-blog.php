<?php
/**
 * Loop - Blog
 *
 * This is the loop file used on the "Blog" page template.
 *
 * @subpackage Template
 */

global $more, $wp_query, $paged; $more = 0;

sf_loop_before();

// Fix for the WordPress 3.0 "paged" bug.
$paged = 1;
if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
if ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
$paged = intval( $paged );

$query_args = array(
					'post_type' => 'post',
					'paged' => $paged
				);

$query_args = apply_filters( 'sf_blog_template_query_args', $query_args );

$new_query = new WP_Query( $query_args );

$original_query = $wp_query;
$wp_query = $new_query;

if ( have_posts() ) { $count = 0;
?>
<div class="fix"></div>
<?php
	while ( have_posts() ) { the_post(); $count++;
		sf_get_template_part( 'templates/contents/content', get_post_type() );
	}
} else {
	get_template_part( 'templates/contents/content', 'noposts' );
}

$wp_query = $original_query;

sf_loop_after();

sf_pagenav( $new_query );
unset( $new_query );
?>
