<?php
/**
 * Single Post Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a post ('post' post_type).
 * @link http://codex.wordpress.org/Post_Types#Post
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
					
					if (have_posts()) { $count = 0;
						while (have_posts()) { the_post(); $count++;
							
							sf_get_template_part( 'templates/contents/content', get_post_type() ); // Get the post content template file, contextually.
						}
					}
					sf_loop_after();
				?>
				
            </section><!-- /#main -->
            <?php sf_main_after(); ?>
    
            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar('alt'); ?>

    </div><!-- /#content -->
	<?php sf_content_after(); ?>

<?php get_footer(); ?>