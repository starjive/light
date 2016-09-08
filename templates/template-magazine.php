<?php
/**
 * Template Name: Magazine
 *
 * The magazine page template displays your posts with a "magazine"-style
 * content slider at the top and a grid of posts below it.
 *
 * @subpackage Template
 */

 global $sf_options, $post;
 get_header();

 if ( is_paged() ) $is_paged = true; else $is_paged = false;

 $page_template = sf_get_page_template();
?>

    <!-- #content Starts -->
	<?php sf_content_before(); ?>
    <div id="content" class="col-full magazine">

    	<div id="main-sidebar-container">

            <!-- #main Starts -->
            <?php sf_main_before(); ?>
            <section id="main">

			<?php
				sf_loop_before();

				// Show page content first
				if (have_posts()) { $count = 0;
					while (have_posts()) { the_post(); $count++;

						// Remove post more from page content
						remove_action( 'sf_post_inside_after', 'sf_post_more' );

						// Use business content so we don't output a page title
						sf_get_template_part( 'templates/contents/content', 'page-template-business' );

						// Add post more again
						add_action( 'sf_post_inside_after', 'sf_post_more' );
					}
				}

				// Load the Magazine Slider
			    if ( $sf_options['sf_slider_magazine'] == 'true' && ! $is_paged ) {
			    	if ( get_option( 'sf_exclude' ) )
			    		update_option( 'sf_exclude', '' );
			    	sf_slider_magazine();
			    }

				// Load the magazine grid
				get_template_part( 'templates/loops/loop', 'magazine' );

				sf_loop_after();
			?>

            </section><!-- /#main -->
            <?php sf_main_after(); ?>

            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php sf_content_after(); ?>


<?php get_footer(); ?>

