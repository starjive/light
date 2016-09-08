<?php
/**
 * Archive Template
 *
 * The archive template is a placeholder for archives that don't have a template file. 
 * Ideally, all archives would be handled by a more appropriate template according to the
 * current page context (for example, `tag.php` for a `post_tag` archive).
 *
 * @subpackage Template
 */

global $sf_options;
get_header();
?>      
    <!-- #content Starts -->
	<?php sf_content_before(); ?>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    
		
            <!-- #main Starts -->
            <?php sf_main_before(); ?>
            <section id="main" class="col-left">
            	
				<?php get_template_part( 'templates/loops/loop', 'archive' ); ?>
                    
            </section><!-- /#main -->
            <?php sf_main_after(); ?>
    
            <?php get_sidebar(); ?>
    
		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>       

    </div><!-- /#content -->
	<?php sf_content_after(); ?>
		
<?php get_footer(); ?>