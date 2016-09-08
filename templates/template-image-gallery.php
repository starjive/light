<?php
/**
 * Template Name: Image Gallery
 *
 * The image gallery page template displays a styled
 * image grid of a maximum of 60 posts with images attached.
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

				<?php sf_loop_before(); ?>

                <!-- Post Starts -->
                <?php sf_post_before(); ?>
                <article class="post">

                    <?php sf_post_inside_before(); ?>

                    <h1 class="title"><?php the_title(); ?></h1>

                    <section class="entry">
                    <?php $loop = new WP_Query( array( 'posts_per_page' => 60 ) ); ?>
                    <?php if ( $loop->have_posts() ) { while ( $loop->have_posts() ) { $loop->the_post(); ?>
                        <?php $loop->is_home = false; ?>
                        <?php sf_image( 'width=100&height=100&class=thumbnail alignleft&single=true' ); ?>
                    <?php } } wp_reset_postdata(); ?>
                    	<div class="fix"></div>
                    </section>

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