<?php
/**
 * Loop - Search
 *
 * This is the loop logic used on the search results screen.
 *
 * @subpackage Template
 */

 global $more; $more = 0;
 
sf_loop_before();
if (have_posts()) { $count = 0;

	$title_before = '<h1 class="archive_header">';
	$title_after = '</h1>';
	
	echo $title_before . sprintf( __( 'Search results for &quot;%s&quot;', 'sfwp-locale' ), get_search_query() ) . $title_after;
?>

<div class="fix"></div>

<?php
	while (have_posts()) { the_post(); $count++;

		sf_get_template_part( 'templates/contents/content', 'search' );

	} // End WHILE Loop
} else {
	get_template_part( 'templates/contents/content', 'noposts' );
} // End IF Statement

sf_loop_after();

sf_pagenav();
?>