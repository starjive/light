<?php
/**
 * Index Template
 *
 * Here we setup all logic and XHTML that is required for the index template, used as both the homepage
 * and as a fallback template, if a more appropriate template file doesn't exist for a specific context.
 *
 * @subpackage Template
 */

get_header();
global $sf_options;
?>      
    
    <!-- #content Starts -->
	<?php sf_content_before(); ?>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    
		
            <!-- #main Starts -->
            <?php sf_main_before(); ?>
            <section id="main" class="col-left">
            	
				<?php get_template_part( 'templates/loops/loop', 'index' ); ?>
                    
            </section><!-- /#main -->
            <?php sf_main_after(); ?>
    
            <?php get_sidebar(); ?>
    
		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>       

    </div><!-- /#content -->
	<?php sf_content_after(); ?>
		
<?php get_footer(); ?>