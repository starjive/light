<?php

class Widget_Tabs extends WP_Widget {

	/*----------------------------------------
	  Constructor.
	  ----------------------------------------

	  * The constructor. Sets up the widget.
	----------------------------------------*/

	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_sf_tabs', 'description' => __( 'This widget is the Tabs that classically goes into the sidebar. It contains the Popular posts, Latest Posts, Recent comments and a Tag cloud.', 'sfwp-locale' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'sf_tabs' );

		/* Create the widget. */
		parent::__construct( 'sf_tabs', __( 'Tabs', 'sfwp-locale' ), $widget_ops, $control_ops );

	} // End Constructor


   function widget($args, $instance) {
       extract( $args );

       $number = $instance['number']; if ($number == '') $number = 5;
       $thumb_size = $instance['thumb_size']; if ($thumb_size == '' AND $thumb_size != 0) $thumb_size = 45;
	   $order = $instance['order']; if ($order == '') $order = "pop";
	   $days = $instance['days']; if ($days == '') $days = '';
	   $pop = ''; if ( array_key_exists( 'pop', $instance ) ) $pop = $instance['pop'];
	   $latest = ''; if ( array_key_exists( 'latest', $instance ) ) $latest = $instance['latest'];
	   $comments = ''; if ( array_key_exists( 'comments', $instance ) ) $comments = $instance['comments'];
	   $tags = ''; if ( array_key_exists( 'tags', $instance ) ) $tags = $instance['tags'];
       ?>

		<?php echo $before_widget; ?>
 		<div id="tabs">

            <ul class="Tabs">
                <?php if ( $order == "latest" && !$latest == "on") { ?><li class="latest"><a href="#tab-latest"><?php _e( 'Latest', 'sfwp-locale' ); ?></a></li>
                <?php } elseif ( $order == "comments" && !$comments == "on") { ?><li class="comments"><a href="#tab-comm"><?php _e( 'Comments', 'sfwp-locale' ); ?></a></li>
                <?php } elseif ( $order == "tags" && !$tags == "on") { ?><li class="tags"><a href="#tab-tags"><?php _e( 'Tags', 'sfwp-locale' ); ?></a></li>
                <?php } ?>
                <?php if (!$pop == "on") { ?><li class="popular"><a href="#tab-pop"><?php _e( 'Popular', 'sfwp-locale' ); ?></a></li><?php } ?>
                <?php if ($order <> "latest" && !$latest == "on") { ?><li class="latest"><a href="#tab-latest"><?php _e( 'Latest', 'sfwp-locale' ); ?></a></li><?php } ?>
                <?php if ($order <> "comments" && !$comments == "on") { ?><li class="comments"><a href="#tab-comm"><?php _e( 'Comments', 'sfwp-locale' ); ?></a></li><?php } ?>
                <?php if ($order <> "tags" && !$tags == "on") { ?><li class="tags"><a href="#tab-tags"><?php _e( 'Tags', 'sfwp-locale' ); ?></a></li><?php } ?>
            </ul>

            <div class="clear"></div>

            <div class="boxes box inside">

	            <?php if ( $order == "latest" && !$latest == "on") { ?>
                <ul id="tab-latest" class="list">
                    <?php if ( function_exists( 'sf_widget_tabs_latest') ) sf_widget_tabs_latest($number, $thumb_size); ?>
                </ul>
	            <?php } elseif ( $order == "comments" && !$comments == "on") { ?>
				<ul id="tab-comm" class="list">
                    <?php if ( function_exists( 'sf_widget_tabs_comments') ) sf_widget_tabs_comments($number, $thumb_size); ?>
                </ul>
	            <?php } elseif ( $order == "tags" && !$tags == "on") { ?>
                <div id="tab-tags" class="list">
                    <?php wp_tag_cloud( 'smallest=12&largest=20' ); ?>
                </div>
                <?php } ?>

                <?php if (!$pop == "on") { ?>
                <ul id="tab-pop" class="list">
                    <?php if ( function_exists( 'sf_widget_tabs_popular') ) sf_widget_tabs_popular($number, $thumb_size, $days); ?>
                </ul>
                <?php } ?>
                <?php if ($order <> "latest" && !$latest == "on") { ?>
                <ul id="tab-latest" class="list">
                    <?php if ( function_exists( 'sf_widget_tabs_latest') ) sf_widget_tabs_latest($number, $thumb_size); ?>
                </ul>
                <?php } ?>
                <?php if ($order <> "comments" && !$comments == "on") { ?>
				<ul id="tab-comm" class="list">
                    <?php if ( function_exists( 'sf_widget_tabs_comments') ) sf_widget_tabs_comments($number, $thumb_size); ?>
                </ul>
                <?php } ?>
                <?php if ($order <> "tags" && !$tags == "on") { ?>
                <div id="tab-tags" class="list">
                    <?php wp_tag_cloud( 'smallest=12&largest=20' ); ?>
                </div>
                <?php } ?>

            </div><!-- /.boxes -->

        </div><!-- /Tabs -->

        <?php echo $after_widget; ?>
         <?php
   }

   /*----------------------------------------
	  update()
	  ----------------------------------------

	* Function to update the settings from
	* the form() function.

	* Params:
	* - Array $new_instance
	* - Array $old_instance
	----------------------------------------*/

	function update ( $new_instance, $old_instance ) {
		$settings = array();

		foreach ( array( 'number', 'thumb_size', 'days' ) as $setting ) {
			if ( isset( $new_instance[$setting] ) ) {
				$settings[$setting] = absint( $new_instance[$setting] );
			}
		}

		foreach ( array( 'order', 'pop', 'latest', 'comments', 'tags' ) as $setting ) {
			if ( isset( $new_instance[$setting] ) ) {
				$settings[$setting] = sanitize_text_field( $new_instance[$setting] );
			}
		}

		return $settings;
	} // End update()

   /*----------------------------------------
	 form()
	 ----------------------------------------

	  * The form on the widget control in the
	  * widget administration area.

	  * Make use of the get_field_id() and
	  * get_field_name() function when creating
	  * your form elements. This handles the confusing stuff.

	  * Params:
	  * - Array $instance
	----------------------------------------*/

   function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
						'number' => 5,
						'thumb_size' => 45,
						'order' => 'pop',
						'days' => '',
						'pop' => '',
						'latest' => '',
						'comments' => '',
						'tags' => ''
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
       <p>
	       <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts:', 'sfwp-locale' ); ?>
	       <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $instance['number']; ?>" />
	       </label>
       </p>
       <p>
	       <label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbnail Size (0=disable):', 'sfwp-locale' ); ?>
	       <input class="widefat" id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" type="text" value="<?php echo $instance['thumb_size']; ?>" />
	       </label>
       </p>
       <p>
	       <label for="<?php echo $this->get_field_id( 'days' ); ?>"><?php _e( 'Popular limit (days):', 'sfwp-locale' ); ?>
	       <input class="widefat" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="text" value="<?php echo $instance['days']; ?>" />
	       </label>
       </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'First Visible Tab:', 'sfwp-locale' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>">
                <option value="pop" <?php selected( $instance['order'], 'pop' ); ?>><?php _e( 'Popular', 'sfwp-locale' ); ?></option>
                <option value="latest" <?php selected( $instance['order'], 'latest' ); ?>><?php _e( 'Latest', 'sfwp-locale' ); ?></option>
                <option value="comments" <?php selected( $instance['order'], 'comments' ); ?>><?php _e( 'Comments', 'sfwp-locale' ); ?></option>
                <option value="tags" <?php selected( $instance['order'], 'tags' ); ?>><?php _e( 'Tags', 'sfwp-locale' ); ?></option>
            </select>
        </p>
       <p><strong><?php _e( 'Hide Tabs:', 'sfwp-locale' ); ?></strong></p>
       <p>
        <input id="<?php echo $this->get_field_id( 'pop' ); ?>" name="<?php echo $this->get_field_name( 'pop' ); ?>" type="checkbox" <?php checked( $instance['pop'], 'on' ); ?>><?php _e( 'Popular', 'sfwp-locale' ); ?></input>
	   </p>
	   <p>
	       <input id="<?php echo $this->get_field_id( 'latest' ); ?>" name="<?php echo $this->get_field_name( 'latest' ); ?>" type="checkbox" <?php checked( $instance['latest'], 'on' ); ?>><?php _e( 'Latest', 'sfwp-locale' ); ?></input>
	   </p>
	   <p>
	       <input id="<?php echo $this->get_field_id( 'comments' ); ?>" name="<?php echo $this->get_field_name( 'comments' ); ?>" type="checkbox" <?php checked( $instance['comments'], 'on' ); ?>><?php _e( 'Comments', 'sfwp-locale' ); ?></input>
	   </p>
	   <p>
	       <input id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="checkbox" <?php checked( $instance['tags'], 'on' ); ?>><?php _e( 'Tags', 'sfwp-locale' ); ?></input>
       </p>
<?php
	} // End form()

} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------

  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Widget_Tabs");' ), 1 );

/*-----------------------------------------------------------------------------------*/
/* Tabs - Javascript */
/*-----------------------------------------------------------------------------------*/
// Add Javascript
if(is_active_widget( null,null,'sf_tabs' ) == true) {
	add_action( 'wp_footer','sf_widget_tabs_js' );
}

function sf_widget_tabs_js(){
?>
<!-- Tabs Widget -->
<script type="text/javascript">
jQuery(document).ready(function(){
	// UL = .Tabs
	// Tab contents = .inside

	var tag_cloud_class = '#tagcloud';

	//Fix for tag clouds - unexpected height before .hide()
	var tag_cloud_height = jQuery( '#tagcloud').height();

	jQuery( '.inside ul li:last-child').css( 'border-bottom','0px' ); // remove last border-bottom from list in tab content
	jQuery( '.Tabs').each(function(){
		jQuery(this).children( 'li').children( 'a:first').addClass( 'selected' ); // Add .selected class to first tab on load
	});
	jQuery( '.inside > *').hide();
	jQuery( '.inside > *:first-child').show();

	jQuery( '.Tabs li a').click(function(evt){ // Init Click funtion on Tabs

		var clicked_tab_ref = jQuery(this).attr( 'href' ); // Strore Href value

		jQuery(this).parent().parent().children( 'li').children( 'a').removeClass( 'selected' ); //Remove selected from all tabs
		jQuery(this).addClass( 'selected' );
		jQuery(this).parent().parent().parent().children( '.inside').children( '*').hide();

		jQuery( '.inside ' + clicked_tab_ref).fadeIn(500);

		 evt.preventDefault();

	})
})
</script>
<?php
}

/*-----------------------------------------------------------------------------------*/
/* Tabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_widget_tabs_popular' ) ) {
	function sf_widget_tabs_popular( $posts = 5, $size = 45, $days = null ) {
		global $post;

		if ( $days ) {
			global $popular_days;
			$popular_days = $days;

			// Register the filtering function
			add_filter( 'posts_where', 'sf_filter_where' );
		}

		$popular = get_posts( array( 'suppress_filters' => false, 'ignore_sticky_posts' => 1, 'orderby' => 'comment_count', 'numberposts' => $posts ) );
		foreach($popular as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ( $size != 0 ) { echo sf_widget_tabs_image_output( $post, 'thumbnail', array( $size, $size ) ); } ?>
		<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach;
		if ( $days ) {
			// Register the filtering function
			remove_filter( 'posts_where', 'sf_filter_where' );
		}
	}
}

/**
 * sf_widget_tabs_image_output outputs image using native WP functions
 * @param  object 	$post  			WP post object
 * @param  string 	$class 			<img> markup class attribute
 * @param  mixed 	$size  			name of image or size in array(width,height)
 * @return string 	$image_markup 	html markup of image
 */
function sf_widget_tabs_image_output( $post, $class = 'thumbnail', $size ) {
	$image_markup = '';
	$default_attr = array(
		'class' => $class,
		'title' => trim( strip_tags( $post->post_title ) )
	);
	if ( has_post_thumbnail( $post->ID ) ) {
        $image_markup = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( $post->post_title ) . '">';
        $image_markup .= get_the_post_thumbnail( $post->ID, $size, $default_attr );
        $image_markup .= '</a>';
    }
    return $image_markup;
} // End sf_widget_tabs_image_output()

//Create a new filtering function that will add our where clause to the query
function sf_filter_where( $where = '' ) {
  global $popular_days;
  //posts in the last X days
  $where .= " AND post_date > '" . date('Y-m-d', strtotime('-'.$popular_days.' days')) . "'";
  return $where;
}

/*-----------------------------------------------------------------------------------*/
/* Tabs - Latest Posts */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_widget_tabs_latest' ) ) {
	function sf_widget_tabs_latest( $posts = 5, $size = 45 ) {
		global $post;
		$latest = get_posts( array( 'suppress_filters' => false, 'ignore_sticky_posts' => 1, 'orderby' => 'post_date', 'order' => 'desc', 'numberposts' => $posts ) );
		foreach($latest as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ( $size != 0 ) { echo sf_widget_tabs_image_output( $post, 'thumbnail', array( $size, $size ) ); } ?>
		<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach;
	}
}



/*-----------------------------------------------------------------------------------*/
/* Tabs - Latest Comments */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_widget_tabs_comments' ) ) {
	function sf_widget_tabs_comments( $posts = 5, $size = 35 ) {
		global $wpdb;

		$comments = get_comments( array( 'number' => $posts, 'status' => 'approve', 'post_status' => 'publish' ) );
		if ( $comments ) {
			foreach ( (array) $comments as $comment) {
			$post = get_post( $comment->comment_post_ID );
			?>
				<li class="recentcomments">
					<?php if ( $size > 0 ) echo get_avatar( $comment, $size ); ?>
					<a href="<?php echo get_comment_link( $comment->comment_ID ); ?>" title="<?php echo wp_filter_nohtml_kses( $comment->comment_author ); ?> <?php echo esc_attr_x( 'on', 'comment topic', 'sfwp-locale' ); ?> <?php echo esc_attr( $post->post_title ); ?>"><?php echo wp_filter_nohtml_kses($comment->comment_author); ?>: <?php echo stripslashes( substr( wp_filter_nohtml_kses( $comment->comment_content ), 0, 50 ) ); ?>...</a>
					<div class="fix"></div>
				</li>
			<?php
			}
 		}
	}
}

?>