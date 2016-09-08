<?php
/**
 * Template Name: Widgets
 *
 * This template displays content from the "Widgets Page Template" registered sidebar.
 * If no widgets are present in this registered sidebar, the default page content is displayed instead.
 *
 * It is possible to override this registered sidebar for multiple pages using plugins.
 *
 * @subpackage Template
 */

get_header();
?>
       
    <!-- #content Starts -->
	<?php sf_content_before(); ?>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    

            <!-- #main Starts -->
            <?php sf_main_before(); ?>
            <section id="main">                     
<?php
	sf_loop_before();
	
	if ( is_active_sidebar( 'widgets-page-template' ) ) {
?>
<div id="widgets-container">
<?php dynamic_sidebar( 'widgets-page-template' ); ?>
</div><!--/#widgets-container-->
<?php
	} else {
		if ( have_posts() ) { $count = 0;
			while ( have_posts() ) { the_post(); $count++;
				sf_get_template_part( 'templates/contents/content', 'page' ); // Get the page content template file, contextually.
			}
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