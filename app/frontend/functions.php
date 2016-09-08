<?php

	/**
	 * Page / Post navigation
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_pagenav' ) ) {
		function sf_pagenav( $custom_query = '' ) {
	
			global $sf_options, $wp_query, $paged, $page;
	
			if ( is_object( $custom_query ) && is_a( $custom_query, 'WP_Query' ) ) {
				$original_query = $wp_query;
				$wp_query = $custom_query;
			}
	
			// Set query max pages
			$max_pages = 1;
			if ( isset( $wp_query->max_num_pages ) ) $max_pages = $wp_query->max_num_pages;
	
	
			// Change the icon classes if is_rtl().
			$left_class = 'left';
			$right_class = 'right';
			if ( is_rtl() ) {
				$left_class = 'right';
				$right_class = 'left';
			}
	
			// If the user has set the option to use simple paging links, display those. By default, display the pagination.
			if ( isset( $sf_options['sf_pagination_type'] ) && 'simple' == $sf_options['sf_pagination_type'] ) {
				if ( get_next_posts_link( '', $max_pages ) || get_previous_posts_link() ) {
			?>
				<div class="nav-entries">
					<?php next_posts_link( '<span class="nav-prev fl"><i class="fa fa-angle-' . esc_attr( $left_class ) . '"></i> '. __( 'Older posts', 'sfwp-locale' ) . '</span>', $max_pages ); ?>
					<?php previous_posts_link( '<span class="nav-next fr">'. __( 'Newer posts', 'sfwp-locale' ) . ' <i class="fa fa-angle-' . esc_attr( $right_class ) . '"></i></span>' ); ?>
					<div class="fix"></div>
				</div>
			<?php
				} // End IF Statement
				if ( is_object( $custom_query ) && is_a( $custom_query, 'WP_Query' ) ) {
					$wp_query = $original_query;
				}
			} else {
				sf_pagination( array(), $custom_query );
			} // End IF Statement
	
		} // End sf_pagenav()
	} // End IF Statement
	
	if (!function_exists('sf_postnav')) {
		function sf_postnav() {
			if ( is_single() ) {
	
				// Change the icon classes if is_rtl().
				$left_class = 'left';
				$right_class = 'right';
				if ( is_rtl() ) {
					$left_class = 'right';
					$right_class = 'left';
				}
			?>
				<div class="post-entries">
					<div class="nav-prev fl"><?php previous_post_link( '%link', '<i class="fa fa-angle-' . esc_attr( $left_class ) . '"></i> %title' ) ?></div>
					<div class="nav-next fr"><?php next_post_link( '%link', '%title <i class="fa fa-angle-' . esc_attr( $right_class ) . '"></i>' ) ?></div>
					<div class="fix"></div>
				</div>
	
			<?php
			}
		}
	}


	/**
	 * Custom Post Type - Slides (Business Slider).
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'sf_add_slides' ) ) {
		function sf_add_slides() {
			global $sf_options, $wp_version;
	
			if ( isset( $sf_options['sf_biz_slides_disable'] ) && ( $sf_options['sf_biz_slides_disable'] == 'true' ) ) { return; }
	
			$icon = get_template_directory_uri() .'/app/frontend/assets/images/slides.png';
			if ( '3.9' <= $wp_version ) {
				$icon = 'dashicons-images-alt2';
			}
	
			// "Slides" Custom Post Type
			$labels = array(
				'name' => _x( 'Slides', 'post type general name', 'sfwp-locale' ),
				'singular_name' => _x( 'Slide', 'post type singular name', 'sfwp-locale' ),
				'add_new' => _x( 'Add New', 'slide', 'sfwp-locale' ),
				'add_new_item' => __( 'Add New Slide', 'sfwp-locale' ),
				'edit_item' => __( 'Edit Slide', 'sfwp-locale' ),
				'new_item' => __( 'New Slide', 'sfwp-locale' ),
				'view_item' => __( 'View Slide', 'sfwp-locale' ),
				'search_items' => __( 'Search Slides', 'sfwp-locale' ),
				'not_found' =>  __( 'No slides found', 'sfwp-locale' ),
				'not_found_in_trash' => __( 'No slides found in Trash', 'sfwp-locale' ),
				'parent_item_colon' => ''
			);
	
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_icon' => $icon,
				'menu_position' => null,
				'taxonomies' => array( 'slide-page' ),
				'supports' => array( 'title','editor','thumbnail','excerpt' )
			);
	
			register_post_type( 'slide', $args );
	
			// "Slide Pages" Custom Taxonomy
			$labels = array(
				'name' => _x( 'Slide Groups', 'taxonomy general name', 'sfwp-locale' ),
				'singular_name' => _x( 'Slide Group', 'taxonomy singular name', 'sfwp-locale' ),
				'search_items' =>  __( 'Search Slide Groups', 'sfwp-locale' ),
				'all_items' => __( 'All Slide Groups', 'sfwp-locale' ),
				'parent_item' => __( 'Parent Slide Group', 'sfwp-locale' ),
				'parent_item_colon' => __( 'Parent Slide Group:', 'sfwp-locale' ),
				'edit_item' => __( 'Edit Slide Group', 'sfwp-locale' ),
				'update_item' => __( 'Update Slide Group', 'sfwp-locale' ),
				'add_new_item' => __( 'Add New Slide Group', 'sfwp-locale' ),
				'new_item_name' => __( 'New Slide Group Name', 'sfwp-locale' ),
				'menu_name' => __( 'Slide Groups', 'sfwp-locale' )
			);
	
			$args = array(
				'hierarchical' => true,
				'labels' => $labels,
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'slide-page' )
			);
	
			register_taxonomy( 'slide-page', array( 'slide' ), $args );
		}
	
		add_action( 'init', 'sf_add_slides' );
	}


	/**
	 * Subscribe / Connect
	 *
	 * @since	1.0
	 * @return	void
	 */
	if (!function_exists('sf_subscribe_connect')) {
		function sf_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '') {
	
			//Setup default variables, overriding them if the "Theme Options" have been saved.
			$settings = array(
							'connect' => 'false',
							'connect_title' => __('Subscribe', 'sfwp-locale' ),
							'connect_related' => 'true',
							'connect_content' => __( 'Subscribe to our e-mail newsletter to receive updates.', 'sfwp-locale' ),
							'connect_newsletter_id' => '',
							'connect_mailchimp_list_url' => '',
							'feed_url' => '',
							'connect_rss' => '',
							'connect_twitter' => '',
							'connect_facebook' => '',
							'connect_youtube' => '',
							'connect_flickr' => '',
							'connect_linkedin' => '',
							'connect_delicious' => '',
							'connect_rss' => '',
							'connect_googleplus' => '',
							'connect_dribbble' => '',
							'connect_instagram' => '',
							'connect_vimeo' => '',
							'connect_pinterest' => ''
							);
			$settings = sf_get_dynamic_values( $settings );
	
			// Get language for form
			$locale = get_locale();
			if ( '' == $locale )
				$locale = 'en_US';
	
			// Setup title
			if ( $widget != 'true' )
				$title = $settings[ 'connect_title' ];
	
			// Setup related post (not in widget)
			$related_posts = '';
			if ( $settings[ 'connect_related' ] == "true" AND $widget != "true" )
				$related_posts = do_shortcode( '[related_posts limit="5"]' );
	
	?>
		<?php if ( $settings[ 'connect' ] == "true" OR $widget == 'true' ) : ?>
		<aside id="connect">
			<h3><?php if ( $title ) echo stripslashes( $title ); else _e( 'Subscribe', 'sfwp-locale' ); ?></h3>
	
			<div <?php if ( $related_posts != '' ) echo 'class="col-left"'; ?>>
				<p><?php if ( $settings['connect_content'] != '') echo stripslashes( $settings['connect_content'] ); else _e( 'Subscribe to our e-mail newsletter to receive updates.', 'sfwp-locale' ); ?></p>
	
				<?php if ( $settings['connect_newsletter_id'] != "" AND $form != 'on' ) : ?>
				<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="https://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('https://feedburner.google.com/fb/a/mailverify?uri=<?php echo $settings['connect_newsletter_id']; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
					<input class="email" type="text" name="email" value="<?php _e( 'E-mail', 'sfwp-locale' ); ?>" onfocus="if (this.value == '<?php _e( 'E-mail', 'sfwp-locale' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'sfwp-locale' ); ?>';}" />
					<input type="hidden" value="<?php echo $settings['connect_newsletter_id']; ?>" name="uri"/>
					<input type="hidden" value="<?php echo esc_attr( get_bloginfo('name') ); ?>" name="title"/>
					<input type="hidden" name="loc" value="<?php echo $locale; ?>"/>
					<input class="submit button" type="submit" name="submit" value="<?php _e( 'Submit', 'sfwp-locale' ); ?>" />
				</form>
				<?php endif; ?>
	
				<?php if ( $settings['connect_mailchimp_list_url'] != "" AND $form != 'on' AND $settings['connect_newsletter_id'] == "" ) : ?>
				<!-- Begin MailChimp Signup Form -->
				<div id="mc_embed_signup">
					<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="<?php echo $settings['connect_mailchimp_list_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $settings['connect_mailchimp_list_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
						<input type="text" name="EMAIL" class="required email" value="<?php _e('E-mail', 'sfwp-locale' ); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e( 'E-mail', 'sfwp-locale' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'sfwp-locale' ); ?>';}">
						<input type="submit" value="<?php _e( 'Submit', 'sfwp-locale' ); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button">
					</form>
				</div>
				<!--End mc_embed_signup-->
				<?php endif; ?>
	
				<?php if ( $social != 'on' ) : ?>
				<div class="social<?php if ( $related_posts == '' AND $settings['connect_newsletter_id' ] != "" ) echo ' fr'; ?>">
					<?php if ( $settings['connect_rss' ] == "true" ) { ?>
					<a href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"></a>
	
					<?php } if ( $settings['connect_twitter' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="Twitter"></a>
	
					<?php } if ( $settings['connect_facebook' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="Facebook"></a>
	
					<?php } if ( $settings['connect_youtube' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="YouTube"></a>
	
					<?php } if ( $settings['connect_flickr' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="Flickr"></a>
	
					<?php } if ( $settings['connect_linkedin' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="LinkedIn"></a>
	
					<?php } if ( $settings['connect_delicious' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="Delicious"></a>
	
					<?php } if ( $settings['connect_googleplus' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="Google+"></a>
	
					<?php } if ( $settings['connect_dribbble' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_dribbble'] ); ?>" class="dribbble" title="Dribbble"></a>
	
					<?php } if ( $settings['connect_instagram' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_instagram'] ); ?>" class="instagram" title="Instagram"></a>
	
					<?php } if ( $settings['connect_vimeo' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_vimeo'] ); ?>" class="vimeo" title="Vimeo"></a>
	
					<?php } if ( $settings['connect_pinterest' ] != "" ) { ?>
					<a target="_blank" href="<?php echo esc_url( $settings['connect_pinterest'] ); ?>" class="pinterest" title="Pinterest"></a>
	
					<?php } ?>
				</div>
				<?php endif; ?>
	
			</div><!-- col-left -->
	
			<?php if ( $settings['connect_related'] == "true" AND $related_posts != '' ) : ?>
			<div class="related-posts col-right">
				<h4><?php _e( 'Related Posts:', 'sfwp-locale' ); ?></h4>
				<?php echo $related_posts; ?>
			</div><!-- col-right -->
			<?php wp_reset_query(); endif; ?>
	
			<div class="fix"></div>
		</aside>
		<?php endif; ?>
	<?php
		}
	}


	/**
	 * Archive Title
	 *
	 * The main page title, used on the various post archive templates.
	 *
	 * @since	1.0
	 * @param	string $before Optional. Content to prepend to the title.
	 * @param	string $after Optional. Content to append to the title.
	 * @param	bool $echo Optional, default to true.Whether to display or return.
	 * @return	null|string Null on no title. String if $echo parameter is false.
	 */
	 if ( ! function_exists( 'sf_archive_title' ) ) {
	
		function sf_archive_title ( $before = '', $after = '', $echo = true ) {
	
			global $wp_query;
	
			if ( is_category() || is_tag() || is_tax() ) {
	
				$taxonomy_obj = $wp_query->get_queried_object();
				$term_id = $taxonomy_obj->term_id;
				$taxonomy_short_name = $taxonomy_obj->taxonomy;
	
				$taxonomy_raw_obj = get_taxonomy( $taxonomy_short_name );
	
			} // End IF Statement
	
			$title = '';
			$delimiter = ' | ';
			$date_format = get_option( 'date_format' );
	
			// Category Archive
			if ( is_category() ) {
	
				$title = '<span class="fl cat">' . __( 'Archive', 'sfwp-locale' ) . $delimiter . single_cat_title( '', false ) . '</span> <span class="fr catrss">';
				$cat_obj = $wp_query->get_queried_object();
				$cat_id = $cat_obj->cat_ID;
				$title .= '<a href="' . get_term_feed_link( $term_id, $taxonomy_short_name, '' ) . '" class="fa fa-rss fa-large" ></a></span>';
	
				$has_title = true;
			}
	
			// Day Archive
			if ( is_day() ) {
	
				$title = __( 'Archive', 'sfwp-locale' ) . $delimiter . get_the_time( $date_format );
			}
	
			// Month Archive
			if ( is_month() ) {
	
				$date_format = apply_filters( 'sf_archive_title_date_format', 'F, Y' );
				$title = __( 'Archive', 'sfwp-locale' ) . $delimiter . get_the_time( $date_format );
			}
	
			// Year Archive
			if ( is_year() ) {
	
				$date_format = apply_filters( 'sf_archive_title_date_format', 'Y' );
				$title = __( 'Archive', 'sfwp-locale' ) . $delimiter . get_the_time( $date_format );
			}
	
			// Author Archive
			if ( is_author() ) {
	
				$title = __( 'Author Archive', 'sfwp-locale' ) . $delimiter . get_the_author_meta( 'display_name', get_query_var( 'author' ) );
			}
	
			// Tag Archive
			if ( is_tag() ) {
	
				$title = __( 'Tag Archives', 'sfwp-locale' ) . $delimiter . single_tag_title( '', false );
			}
	
			// Post Type Archive
			if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {
	
				/* Get the post type object. */
				$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );
	
				$title = $post_type_object->labels->name . ' ' . __( 'Archive', 'sfwp-locale' );
			}
	
			// Post Format Archive
			if ( get_query_var( 'taxonomy' ) == 'post_format' ) {
	
				$post_format = str_replace( 'post-format-', '', get_query_var( 'post_format' ) );
	
				$title = get_post_format_string( $post_format ) . ' ' . __( ' Archives', 'sfwp-locale' );
			}
	
			// General Taxonomy Archive
			if ( is_tax() ) {
	
				$title = sprintf( __( '%1$s Archives: %2$s', 'sfwp-locale' ), $taxonomy_raw_obj->labels->name, $taxonomy_obj->name );
	
			}
	
			if ( strlen($title) == 0 )
			return;
	
			$title = $before . $title . $after;
	
			// Allow for external filters to manipulate the title value.
			$title = apply_filters( 'sf_archive_title', $title, $before, $after );
	
			if ( $echo )
				echo $title;
			else
				return $title;
	
		} // End sf_archive_title()
	
	 } // End IF Statement


	/**
	 * Get Post image attachments
	 *
	 * This function will get all the attached post images that have been uploaded via the
	 * WP post image upload and return them in an array.
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_get_post_images($offset = 1) {
		// Arguments
		$repeat = 100; 				// Number of maximum attachments to get
		$photo_size = 'large';		// The WP "size" to use for the large image
	
		global $post;
	
		$output = array();
	
		$id = get_the_id();
		$attachments = get_children( array(
		'post_parent' => $id,
		'numberposts' => $repeat,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order' => 'ASC',
		'orderby' => 'menu_order date' )
		);
		if ( !empty($attachments) ) :
			$output = array();
			$count = 0;
			foreach ( $attachments as $att_id => $attachment ) {
				$count++;
				if ($count <= $offset) continue;
				$url = wp_get_attachment_image_src($att_id, $photo_size, true);
					$output[] = array( 'url' => $url[0], 'caption' => $attachment->post_excerpt, 'id' => $att_id, 'alt' => get_post_meta( $att_id, '_wp_attachment_image_alt', true ) );
			}
		endif;
		return $output;
	} // End sf_get_post_images()


	/**
	 * Add custom CSS class to the <body> tag if the lightbox option is enabled.
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_add_lightbox_body_class ( $classes ) {
		global $sf_options;
	
		if ( isset( $sf_options['sf_enable_lightbox'] ) && $sf_options['sf_enable_lightbox'] == 'true' ) {
			$classes[] = 'has-lightbox';
		}
	
		return $classes;
	} // End sf_add_lightbox_body_class()
	add_filter( 'body_class', 'sf_add_lightbox_body_class', 10 );


	/**
	 * Load PrettyPhoto JavaScript and CSS if the lightbox option is enabled.
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_load_prettyphoto () {
		global $sf_options;
	
		if ( ! isset( $sf_options['sf_enable_lightbox'] ) || $sf_options['sf_enable_lightbox'] == 'false' ) { return; }
	
		$filter = current_filter();
	
		switch ( $filter ) {
			case 'sf_add_javascript':
				wp_enqueue_script( 'prettyPhoto' );
			break;
	
			case 'sf_add_css':
				wp_enqueue_style( 'prettyPhoto' );
			break;
		}
	} // End sf_load_prettyphoto()
	add_action( 'sf_add_javascript', 'sf_load_prettyphoto', 10 );
	add_action( 'sf_add_css', 'sf_load_prettyphoto', 10 );


	/**
	 * Google Maps
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_maps_contact_output($args){
	
		$key = get_option('sf_maps_apikey');
	
		// No More API Key needed
	
		if ( !is_array($args) )
			parse_str( $args, $args );
	
		extract($args);
		$mode = '';
		$streetview = 'off';
		$map_height = get_option('sf_maps_single_height');
		$featured_w = get_option('sf_home_featured_w');
		$featured_h = get_option('sf_home_featured_h');
		$zoom = get_option('sf_maps_default_mapzoom');
		$type = get_option('sf_maps_default_maptype');
		$marker_title = get_option('sf_contact_title');
		if ( $zoom == '' ) { $zoom = 6; }
		$lang = get_option('sf_maps_directions_locale');
		$locale = '';
		if(!empty($lang)){
			$locale = ',locale :"'.$lang.'"';
		}
		$extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';
	
		if(empty($map_height)) { $map_height = 250;}
	
		if(is_home() && !empty($featured_h) && !empty($featured_w)){
		?>
		<div id="single_map_canvas" style="width:<?php echo intval( $featured_w ); ?>px; height: <?php echo intval( $featured_h ); ?>px"></div>
		<?php } else { ?>
		<div id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div>
		<?php } ?>
		<?php $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min'; ?>
		<script src="<?php echo esc_attr( esc_url( get_template_directory_uri() . '/app/frontend/assets/js/markers' . $suffix . '.js' ) ); ?>" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				function initialize() {
	
	
				<?php if($streetview == 'on'){ ?>
	
	
				<?php } else { ?>
	
					<?php switch ($type) {
							case 'G_NORMAL_MAP':
								$type = 'ROADMAP';
								break;
							case 'G_SATELLITE_MAP':
								$type = 'SATELLITE';
								break;
							case 'G_HYBRID_MAP':
								$type = 'HYBRID';
								break;
							case 'G_PHYSICAL_MAP':
								$type = 'TERRAIN';
								break;
							default:
								$type = 'ROADMAP';
								break;
					} ?>
	
					var myLatlng = new google.maps.LatLng(<?php echo $geocoords; ?>);
					var myOptions = {
					  zoom: <?php echo $zoom; ?>,
					  center: myLatlng,
					<?php if(get_option('sf_maps_scroll') == 'true'){ ?>
					  scrollwheel: false,
					  <?php } ?>
					  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
					};
					var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);
	
					<?php if($mode == 'directions'){ ?>
					directionsPanel = document.getElementById("featured-route");
					directions = new GDirections(map, directionsPanel);
					directions.load("from: <?php echo esc_js( $from ); ?> to: <?php echo esc_js( $to ); ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
					<?php
					} else { ?>
	
						<?php
							$map_vars = array(
								'the_title' => $marker_title,
								'callout' => get_option('sf_maps_callout_text'),
								'the_link' => get_permalink( get_the_id() ),
								'root' => esc_url( get_template_directory_uri() )
							);
						?>
						var mapData = <?php echo json_encode( $map_vars ); ?>;
	
						var root = mapData.root;
						var callout = mapData.callout;
						var the_link = mapData.the_link;
						var the_title = mapData.the_title;
	
						var point = new google.maps.LatLng(<?php echo $geocoords; ?>);
	
					<?php
					if(is_page()){
						$custom = get_option('sf_cat_custom_marker_pages');
						if(!empty($custom)){
							$color = $custom;
						}
						else {
							$color = get_option('sf_cat_colors_pages');
							if (empty($color)) {
								$color = 'red';
							}
						}
					?>
						var color = '<?php echo $color; ?>';
						createMarker(map,point,root,the_link,the_title,color,callout);
					<?php } else { ?>
						var color = '<?php echo get_option('sf_cat_colors_pages'); ?>';
						createMarker(map,point,root,the_link,the_title,color,callout);
					<?php
					}
						if(isset($_POST['sf_maps_directions_search'])){ ?>
	
						directionsPanel = document.getElementById("featured-route");
						directions = new GDirections(map, directionsPanel);
						directions.load("from: <?php echo htmlspecialchars($_POST['sf_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
	
	
	
						directionsDisplay = new google.maps.DirectionsRenderer();
						directionsDisplay.setMap(map);
						directionsDisplay.setPanel(document.getElementById("featured-route"));
	
						<?php if($walking == 'on'){ ?>
						var travelmodesetting = google.maps.DirectionsTravelMode.WALKING;
						<?php } else { ?>
						var travelmodesetting = google.maps.DirectionsTravelMode.DRIVING;
						<?php } ?>
						var start = '<?php echo htmlspecialchars($_POST['sf_maps_directions_search']); ?>';
						var end = '<?php echo $address; ?>';
						var request = {
							origin:start,
							destination:end,
							travelMode: travelmodesetting
						};
						directionsService.route(request, function(response, status) {
							if (status == google.maps.DirectionsStatus.OK) {
								directionsDisplay.setDirections(response);
							}
						});
	
						<?php } ?>
					<?php } ?>
				<?php } ?>
	
	
				  }
				  function handleNoFlash(errorCode) {
					  if (errorCode == FLASH_UNAVAILABLE) {
						alert("Error: Flash doesn't appear to be supported by your browser");
						return;
					  }
					 }
	
	
	
			initialize();
	
			});
		jQuery(window).load(function(){
	
			var newHeight = jQuery('#featured-content').height();
			newHeight = newHeight - 5;
			if(newHeight > 300){
				jQuery('#single_map_canvas').height(newHeight);
			}
	
		});
	
		</script>

	<?php
	}



	/**
	 * Add custom CSS class to the <body> tag if the boxed layout option is enabled
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_add_boxedlayout_body_class ( $classes ) {
		global $sf_options;
	
		if ( isset( $sf_options['sf_style_disable'] ) && $sf_options['sf_style_disable'] != 'true' && isset( $sf_options['sf_layout_boxed'] ) && $sf_options['sf_layout_boxed'] == 'true' ) {
			$classes[] = 'boxed-layout';
		}
	
		return $classes;
	} // End sf_add_boxedlayout_body_class()
	add_filter( 'body_class', 'sf_add_boxedlayout_body_class', 10 );


	/**
	 * Is IE
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'is_ie' ) ) {
		function is_ie ( $version = '6.0' ) {
			$supported_versions = array( '6.0', '7.0', '8.0', '9.0' );
			$agent = substr( $_SERVER['HTTP_USER_AGENT'], 25, 4 );
			$current_version = substr( $_SERVER['HTTP_USER_AGENT'], 30, 3 );
			$response = false;
			if ( in_array( $version, $supported_versions ) && 'MSIE' == $agent && ( $version == $current_version ) ) {
				$response = true;
			}
	
			return $response;
		} // End is_ie()
	}


	/**
	 * Check if WooCommerce is activated
	 *
	 * @since	1.0
	 * @return	void
	 */
	if ( ! function_exists( 'is_woocommerce_activated' ) ) {
		function is_woocommerce_activated() {
			if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
		}
	}


	/**
	 * sf_archive_description()
	 *
	 * Display a description, if available, for the archive being viewed (category, tag, other taxonomy).
	 *
	 * @since	1.0
	 * @uses	do_atomic(), get_queried_object(), term_description()
	 * @echo	string
	 * @filter	sf_archive_description
	 */
	if ( ! function_exists( 'sf_archive_description' ) ) {
		function sf_archive_description ( $echo = true ) {
			do_action( 'sf_archive_description' );
	
			// Archive Description, if one is available.
			$term_obj = get_queried_object();
	
			$description = '';
	
			if ( isset( $term_obj->term_id ) && isset( $term_obj->taxonomy ) ) {
				$description = term_description( $term_obj->term_id, $term_obj->taxonomy );
			}
	
			if ( isset( $description ) && '' != $description ) {
				// Allow child themes/plugins to filter here ( 1: text in DIV and paragraph, 2: term object )
				$description = apply_filters( 'sf_archive_description', '<div class="archive-description">' . $description . '</div><!--/.archive-description-->', $term_obj );
			}
	
			if ( $echo != true ) { return $description; }
	
			echo $description;
		} // End sf_archive_description()
	}


	/**
	 * Add body class if Fixed Mobile width enabled
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_add_fixed_mobile_class ( $classes ) {
		global $sf_options;
	
		if ( isset( $sf_options['sf_remove_responsive'] ) && $sf_options['sf_remove_responsive'] == 'true' ) {
			$classes[] = 'fixed-mobile';
		}
	
		return $classes;
	} // End sf_add_fixed_mobile_class()
	add_filter( 'body_class', 'sf_add_fixed_mobile_class', 10 );


	/**
	 * Get a menu name
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_get_menu_name( $location ){
		if( ! has_nav_menu( $location ) ) return false;
		$menus = get_nav_menu_locations();
		$menu_title = wp_get_nav_menu_object( $menus[$location] ) -> name;
		return $menu_title;
	}




	/**
	 * Construct an array of arguments for use in the query for the "Magazine" grid.
	 *
	 * @since	1.0
	 * @return	array Constructed array of arguments.
	 */
	if ( ! function_exists( 'sf_get_magazine_query_args' ) ) {
		function sf_get_magazine_query_args () {
			global $sf_options;
			// Exclude stored duplicates
			$exclude = '';
			$cats = array();
			$cats_exclude = array();
		
			// Exclude slider posts
			if ( $sf_options['sf_slider_magazine_exclude'] == 'true' ) {
				$exclude = get_option( 'sf_exclude' );
			}
		
			// Fix for the WordPress 3.0 "paged" bug.
			$paged = 1;
			if ( get_query_var( 'paged' ) && ( get_query_var( 'paged' ) != '' ) ) { $paged = get_query_var( 'paged' ); }
			if ( get_query_var( 'page' ) && ( get_query_var( 'page' ) != '' ) ) { $paged = get_query_var( 'page' ); }
		
			$args = array(
							'post_type' => 'post'
						);
		
			if ( $paged > 1 ) {
				$args['paged'] = $paged;
			}
		
			if ( $exclude != '' ) {
				$args['post__not_in'] = $exclude;
			}
		
			return $args;
		} // End sf_get_magazine_query_args()
	}


	/**
	 * This function produces the "edit post" link for logged in users.
	 *
	 * @since	1.0
	 * @return	void
	 */
	function sf_custom_post_edit() {
		echo do_shortcode( '[post_edit]' );
		return;
	} // End sf_custom_post_edit()
	add_action( 'sf_post_after', 'sf_custom_post_edit', 90 );
	add_action( 'sf_page_after', 'sf_custom_post_edit', 100 );
