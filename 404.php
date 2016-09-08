<?php
/**
 * 404 Template
 *
 * This template is displayed when the page being requested by the viewer cannot be found
 * or doesn't exist. From here, we'll try to assist the user and keep them browsing the website.
 * @link http://codex.wordpress.org/Pages
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
            <section id="main" class="col-left">
			
				<?php
					sf_loop_before();
					sf_get_template_part( 'templates/contents/content', '404' ); // Get the 404 content template file, contextually.
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