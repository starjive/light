<?php
/**
 * Search Template
 *
 * The search template is used to display search results from the native WordPress search.
 * If no search results are found, the user is assisted in refining their search query in
 * an attempt to produce an appropriate search results set for the user's search query.
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
            	
				<?php get_template_part( 'templates/loops/loop', 'search' ); ?>
                    
            </section><!-- /#main -->
            <?php sf_main_after(); ?>
    
            <?php get_sidebar(); ?>
    
		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>       

    </div><!-- /#content -->
	<?php sf_content_after(); ?>
		
<?php get_footer(); ?>