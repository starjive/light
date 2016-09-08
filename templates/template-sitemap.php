<?php
/**
 * Template Name: Sitemap
 *
 * The sitemap page template displays a user-friendly overview
 * of the content of your website.
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
            <section id="main">

				<?php sf_loop_before(); ?>
                <!-- Post Starts -->
                <?php sf_post_before(); ?>
                <article class="post">

                    <?php sf_post_inside_before(); ?>

                    <h1 class="title"><?php the_title(); ?></h1>

                    <section class="entry">

                        <h3><?php _e( 'Pages', 'sfwp-locale' ); ?></h3>
                        <ul>
                            <?php wp_list_pages( 'depth=0&sort_column=menu_order&title_li=' ); ?>
                        </ul>

                        <h3><?php _e( 'Categories', 'sfwp-locale' ); ?></h3>
                        <ul>
                            <?php wp_list_categories( 'title_li=&hierarchical=0&show_count=1' ); ?>
                        </ul>

                        <h3><?php _e( 'Posts per category', 'sfwp-locale' ); ?></h3>
                        <?php
                            $cats = get_categories();
                            foreach ( $cats as $cat ) {
                                $loop = new WP_Query( array( 'cat' => intval( $cat->cat_ID ) ) );
                        ?>
                            <h4><?php echo esc_html( $cat->cat_name ); ?></h4>
                            <ul>
                                <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                <li style="font-weight:normal !important;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> - <?php _e( 'Comments', 'sfwp-locale' ); ?> (<?php echo $post->comment_count; ?>)</li>
                                <?php endwhile;  ?>
                            </ul>

                        <?php } wp_reset_postdata(); ?>

                    	<?php if ( is_woocommerce_activated() ): ?>

		        	    <h3><?php _e( 'Product Categories', 'sfwp-locale' ); ?></h3>
		        	    <ul>
		        	    	<?php wp_list_categories( 'taxonomy=product_cat&pad_counts=1&title_li=' ); ?>
					    </ul>

		    		    <h3><?php _e( 'Products', 'sfwp-locale' ); ?></h3>
		    		    <ul>
			    		    <?php
			    		    	$args = array( 	'post_type' => 'product', 
			    		    					'posts_per_page' => -1, 
			    		    					'meta_query' => array ( array( 	'key' => '_visibility',
			    		    													'value' => array('catalog', 'visible'),
																				'compare' => 'IN'
																)) 
										);
								$loop = new WP_Query( $args );
								while ( $loop->have_posts() ) : $loop->the_post();
							?>
							<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
						</ul>

	        	    	<?php endif; ?>

                    </section><!-- /.entry -->

                    <?php sf_post_inside_after(); ?>

                </article><!-- /.post -->
                <?php sf_post_after(); ?>
                <div class="fix"></div>

            </section><!-- /#main -->
            <?php sf_main_after(); ?>

            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php sf_content_after(); ?>

<?php get_footer(); ?>