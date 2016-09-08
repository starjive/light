<?php
/**
 * Template Name: Business
 *
 * The business page template displays your posts with a "business"-style
 * content slider at the top.
 *
 * @subpackage Template
 */

global $sf_options, $wp_query;
get_header();

$page_template = sf_get_page_template();
?>
    <!-- #content Starts -->
	<?php sf_content_before(); ?>
	<?php if ( ( isset( $sf_options['sf_slider_biz'] ) && 'true' == $sf_options['sf_slider_biz'] ) && ( isset( $sf_options['sf_slider_biz_full'] ) && 'true' == $sf_options['sf_slider_biz_full'] ) ) { $saved = $wp_query; sf_slider_biz(); $wp_query = $saved; } ?>
    <div id="content" class="col-full business">

    	<div id="main-sidebar-container">

            <!-- #main Starts -->
            <?php sf_main_before(); ?>

	<?php if ( ( isset( $sf_options['sf_slider_biz'] ) && 'true' == $sf_options['sf_slider_biz'] ) && ( isset( $sf_options['sf_slider_biz_full'] ) && 'false' == $sf_options['sf_slider_biz_full'] ) ) { $saved = $wp_query; sf_slider_biz(); $wp_query = $saved; } ?>

            <section id="main">
<?php
	sf_loop_before();

	if ( have_posts() ) { $count = 0;
		while ( have_posts() ) { the_post(); $count++;
			sf_get_template_part( 'templates/contents/content', 'page-template-business' ); // Get the page content template file, contextually.
		}
	}

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