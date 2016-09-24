<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- sf_image - Get Image from custom field
    - vt_resize - Resize post thumbnail
    - sf_get_youtube_video_image - Get thumbnail from YouTube
- Add default filters to sf_embed()
- sf_get_embed - Get Video
- Show Page Menu
- Get the style path currently selected
- Get page ID
- Tidy up the image source url
- Show image in RSS feed
- Show analytics code footer
- Browser detection body_class() output
- Template Detector
- Framework Updater
	- Framework Update Page
 	- Framework Update Head
 	- Framework Version Getter
- URL shortener
- Text Trimmer
- Google Webfonts array
- Google Fonts Stylesheet Generator
- Enable Home link in WP Menus
- Detects the Charset of String and Converts it to UTF-8
- WP Login logo
- WP Login logo URL
- WP Login logo title
- sf_pagination()
- sf_breadcrumbs()
	- sf_breadcrumbs_get_parents()
	- sf_breadcrumbs_get_term_parents()
- WordPress Admin Bar-related
	- Disable WordPress Admin Bar
	- Enhancements to the WordPress Admin Bar
- sf_prepare_category_ids_from_option()
- Move tracking code from footer to header.
- sf_get_dynamic_values()
- If the user has specified a "posts page", load the "Blog" page template there
- PressTrends API Integration
- sf_ajax_banner_close()
- Sidebars Deprecation Banner
- Static Front Page Detection Banner
- sf_get_theme_version_data()
- sf_display_theme_version_data()
- sf_load_google_fonts()
- sf_process_old_custom_fields()
-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* sf_image - Get Image from custom field  */
/*-----------------------------------------------------------------------------------*/

/*
This function retrieves/resizes the image to be used with the post in this order:

1. Image passed through parameter 'src'
2. WP Post Thumbnail (if option activated)
3. Custom field
4. First attached image in post (if option activated)
5. First inline image in post (if option activated)

Resize options (enabled in options panel):
- vt_resize() is used to natively resize #2 and #4
- Thumb.php is used to resize #1, #3, #4 (only if vt_resize is disabled) and #5

Parameters:
        $key = Custom field key eg. "image"
        $width = Set width manually without using $type
        $height = Set height manually without using $type
        $class = CSS class to use on the img tag eg. "alignleft". Default is "thumbnail"
        $quality = Enter a quality between 80-100. Default is 90
        $id = Assign a custom ID, if alternative is required.
        $link = Echo with anchor ( 'src'), without anchor ( 'img') or original image URL ( 'url').
        $repeat = Auto Img Function. Adjust amount of images to return for the post attachments.
        $offset = Auto Img Function. Offset the $repeat with assigned amount of objects.
        $before = Auto Img Function. Add Syntax before image output.
        $after = Auto Img Function. Add Syntax after image output.
        $single = (true/false) Force thumbnail to link to the post instead of the image.
        $force = Force smaller images to not be effected with image width and height dimensions (proportions fix)
        $return = Return results instead of echoing out.
		$src = A parameter that accepts a img url for resizing. (No anchor)
		$meta = Add a custom meta text to the image and anchor of the image.
		$alignment = Crop alignment for thumb.php (l, r, t, b)
		$size = Custom pre-defined size for WP Thumbnail (string)
		$noheight = Don't output the height on img tag (for responsive designs)
*/

if ( !function_exists('sf_image') ) {
function sf_image($args) {

	/* ------------------------------------------------------------------------- */
	/* SET VARIABLES */
	/* ------------------------------------------------------------------------- */

	global $post;
	global $sf_options;

	//Defaults
	$key = 'image';
	$width = null;
	$height = null;
	$class = '';
	$quality = 90;
	$id = null;
	$link = 'src';
	$repeat = 1;
	$offset = 0;
	$before = '';
	$after = '';
	$single = false;
	$force = false;
	$return = false;
	$is_auto_image = false;
	$src = '';
	$meta = '';
	$alignment = '';
	$size = '';
	$noheight = '';

	$alt = '';
	$img_link = '';

	$attachment_id = array();
	$attachment_src = array();

	if ( ! is_array( $args ) )
		parse_str( $args, $args );

	extract( $args );

    // Set post ID
    if ( empty( $id ) ) {
		$id = $post->ID;
    }

	$thumb_id = esc_html( get_post_meta( $id, '_thumbnail_id', true ) );

	// Set alignment
	if ( $alignment == '' )
		$alignment = esc_html( get_post_meta( $id, '_image_alignment', true ) );

	// Get standard sizes
	if ( ! $width && ! $height ) {
		$width = '100';
		$height = '100';
	}

	// Cast $width and $height to integer
	$width = intval( $width );
	$height = intval( $height );

	/* ------------------------------------------------------------------------- */
	/* FIND IMAGE TO USE */
	/* ------------------------------------------------------------------------- */

	// When a custom image is sent through
	if ( $src != '' ) {
		$custom_field = esc_url( $src );
		$link = 'img';

	// WP 2.9 Post Thumbnail support
	} elseif ( get_option( 'sf_post_image_support' ) == 'true' && ! empty( $thumb_id ) ) {

		if ( get_option( 'sf_pis_resize' ) == 'true' ) {

			if ( 0 == $height ) {
				$img_data = wp_get_attachment_image_src( $thumb_id, array( intval( $width ), 9999 ) );
				$height = $img_data[2];
			}

			// Dynamically resize the post thumbnail
			$vt_crop = get_option( 'sf_pis_hard_crop' );
			if ($vt_crop == 'true' ) $vt_crop = true; else $vt_crop = false;
			$vt_image = vt_resize( $thumb_id, '', $width, $height, $vt_crop );

			// Set fields for output
			$custom_field = esc_url( $vt_image['url'] );
			$width = $vt_image['width'];
			$height = $vt_image['height'];

		} else {
			// Use predefined size string
			if ( $size )
				$thumb_size = $size;
			else
				$thumb_size = array( $width, $height );

			$img_link = get_the_post_thumbnail( $id, $thumb_size, array( 'class' => 'sf-image ' . esc_attr( $class ) ) );
		}

	// Grab the image from custom field
	} else {
    	$custom_field = esc_url( get_post_meta( $id, $key, true ) );
	}

	// Theme Override
	if ( apply_filters( 'sf_image_override', false ) ) {
		// Use predefined size string
		if ( $size )
			$thumb_size = $size;
		else
			$thumb_size = array( $width, $height );

		$img_link = get_the_post_thumbnail( $id, $thumb_size, array( 'class' => 'sf-image ' . esc_attr( $class ) ) );
	}

	// Automatic Image Thumbs - get first image from post attachment
	if ( empty( $custom_field ) && get_option( 'sf_auto_img' ) == 'true' && empty( $img_link ) && ! ( is_singular() && in_the_loop() && $link == 'src' ) ) {

        if( $offset >= 1 )
			$repeat = $repeat + $offset;

        $attachments = get_children( array(	'post_parent' => $id,
											'numberposts' => $repeat,
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'order' => 'DESC',
											'orderby' => 'menu_order date')
											);

		// Search for and get the post attachment
		if ( ! empty( $attachments ) ) {
			$counter = -1;
			foreach ( $attachments as $att_id => $attachment ) {
				$counter++;
				if ( $counter < $offset )
					continue;

				if ( get_option( 'sf_post_image_support' ) == 'true' && get_option( 'sf_pis_resize' ) == 'true' ) {
					// Dynamically resize the post thumbnail
					$vt_crop = get_option( 'sf_pis_hard_crop' );
					if ( $vt_crop == 'true' ) $vt_crop = true; else $vt_crop = false;
					$vt_image = vt_resize( $att_id, '', $width, $height, $vt_crop );

					// Set fields for output
					$custom_field = esc_url( $vt_image['url'] );
					$width = $vt_image['width'];
					$height = $vt_image['height'];
				} else {
					$src = wp_get_attachment_image_src( $att_id, 'large', true );
					$custom_field = esc_url( $src[0] );
					$attachment_id[] = $att_id;
					$src_arr[] = $custom_field;
				}
				$thumb_id = $att_id;
				$is_auto_image = true;
			}

		// Get the first img tag from content
		} else {

			$first_img = '';
			$post = get_post( $id );
			ob_start();
			ob_end_clean();
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
			if ( !empty($matches[1][0]) ) {

				// Save Image URL
				$custom_field = esc_url( $matches[1][0] );

				// Search for ALT tag
				$output = preg_match_all( '/<img.+alt=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
				if ( !empty($matches[1][0]) ) {
					$alt = esc_attr( $matches[1][0] );
				}
			}

		}

	}

	// Check if there is YouTube embed
	if ( empty( $custom_field ) && empty( $img_link ) ) {
		$embed = esc_html( get_post_meta( $id, 'embed', true ) );
		if ( $embed )
	    	$custom_field = esc_url( sf_get_video_image( $embed ) );
	}

	// Return if there is no attachment or custom field set
	if ( empty( $custom_field ) && empty( $img_link ) ) {

		// Check if default placeholder image is uploaded
		$placeholder = get_option( 'sf_default_image' );
		$placeholder = SF()->get_placeholder_image_url();
		if ( $placeholder && !(is_singular() && in_the_loop()) ) {
			$custom_field = esc_url( $placeholder );

			// Resize the placeholder if
			if ( get_option( 'sf_post_image_support' ) == 'true' && get_option( 'sf_pis_resize' ) == 'true' ) {
				// Dynamically resize the post thumbnail
				$vt_crop = get_option( 'sf_pis_hard_crop' );
				if ($vt_crop == 'true' ) $vt_crop = true; else $vt_crop = false;
				$vt_image = vt_resize( '', $placeholder, $width, $height, $vt_crop );

				// Set fields for output
				$custom_field = esc_url( $vt_image['url'] );
				$width = $vt_image['width'];
				$height = $vt_image['height'];
			}
		} else {
	       return;
	    }
	}

	if(empty( $src_arr ) && empty( $img_link ) ) { $src_arr[] = $custom_field; }

	/* ------------------------------------------------------------------------- */
	/* BEGIN OUTPUT */
	/* ------------------------------------------------------------------------- */

    $output = '';

    // Set output height and width
    $set_width = ' width="' . esc_attr( $width ) . '" ';
    $set_height = '';

    if ( ! $noheight && 0 < $height )
    	$set_height = ' height="' . esc_attr( $height ) . '" ';

	// Set standard class
	if ( $class ) $class = 'sf-image ' . esc_attr( $class ); else $class = 'sf-image';

	// Do check to verify if images are smaller then specified.
	if($force == true){ $set_width = ''; $set_height = ''; }

	// WP Post Thumbnail
	if( ! empty( $img_link ) ) {

		if( $link == 'img' ) {  // Output the image without anchors
			$output .= wp_kses_post( $before );
			$output .= $img_link;
			$output .= wp_kses_post( $after );
		} elseif( $link == 'url' ) {  // Output the large image
			$src = wp_get_attachment_image_src( $thumb_id, 'large', true );
			$custom_field = esc_url( $src[0] );
			$output .= $custom_field;
		} else {  // Default - output with link
			if ( ( is_single() || is_page() ) && $single == false ) {
				$rel = 'rel="lightbox"';
				$href = false;
			} else {
				$href = get_permalink( $id );
				$rel = '';
			}

			$title = 'title="' . esc_attr( get_the_title( $id ) ) .'"';

			$output .= wp_kses_post( $before );
			if($href == false){
				$output .= $img_link;
			} else {
				$output .= '<a ' . $title . ' href="' . esc_url( $href ) . '" '. $rel .'>' . $img_link . '</a>';
			}

			$output .= wp_kses_post( $after );
		}
	}
	
	else {
		foreach( $src_arr as $key => $custom_field ) {

			//Set the ID to the Attachment's ID if it is an attachment
			if( $is_auto_image == true && isset( $attachment_id[$key] ) ){
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}

			//Set custom meta
			if ($meta) {
				$alt = esc_attr( $meta );
				$title = 'title="'. esc_attr( $meta ) .'"';
			} else {
				if ( empty( $alt ) ) $alt = esc_attr( get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) );
				$title = 'title="'. esc_attr( get_the_title( $quick_id ) ) .'"';
			}

			if ( empty( $alt ) ) {
			    $alt = esc_attr( get_post( $thumb_id )->post_excerpt ); // If not, Use the Caption
			}

			if ( empty( $alt ) ) {
			    $alt = esc_attr( get_post( $thumb_id )->post_title ); // Finally, use the title
			}

			$img_link =  '<img src="'. esc_url( $custom_field ) . '" alt="' . esc_attr( $alt ) . '" ' . $set_width . $set_height . $title . ' class="' . esc_attr( stripslashes( $class ) ) . '" />';

			if ( $link == 'img' ) {  // Just output the image
				$output .= wp_kses_post( $before );
				$output .= $img_link;
				$output .= wp_kses_post( $after );

			} elseif( $link == 'url' ) {  // Output the URL to original image
				if ( $vt_image['url'] || $is_auto_image ) {
					$src = wp_get_attachment_image_src( $thumb_id, 'full', true );
					$custom_field = esc_url( $src[0] );
				}
				$output .= $custom_field;

			} else {  // Default - output with link

				if ( ( is_single() || is_page() ) && $single == false ) {

					// Link to the large image if single post
					if ( $vt_image['url'] || $is_auto_image ) {
						$src = wp_get_attachment_image_src( $thumb_id, 'full', true );
						$custom_field = esc_url( $src[0] );
					}

					$href = $custom_field;
					$rel = 'rel="lightbox"';
				} else {
					$href = get_permalink( $id );
					$rel = '';
				}

				$output .= wp_kses_post( $before );
				$output .= '<a href="' . esc_url( $href ) . '" ' . $rel . ' ' . $title . '>' . $img_link . '</a>';
				$output .= wp_kses_post( $after );
			}
		}
	}

	// Remove no height attribute - IE fix when no height is set
	$output = str_replace( 'height=""', '', $output );
	$output = str_replace( 'height="0"', '', $output );

	// Return or echo the output
	if ( $return == TRUE )
		return $output;
	else
		echo $output; // Done

}
}

/* Get thumbnail from Video Embed code */
if ( ! function_exists( 'sf_get_video_image' ) ) {
function sf_get_video_image( $embed ) {
	$video_thumb = '';

	// YouTube - get the video code if this is an embed code (old embed)
	preg_match( '/youtube\.com\/v\/([\w\-]+)/', $embed, $match );

	// YouTube - if old embed returned an empty ID, try capuring the ID from the new iframe embed
	if( ! isset( $match[1] ) )
		preg_match( '/youtube\.com\/embed\/([\w\-]+)/', $embed, $match );

	// YouTube - if it is not an embed code, get the video code from the youtube URL
	if( ! isset( $match[1] ) )
		preg_match( '/v\=(.+)&/', $embed, $match );

	// YouTube - get the corresponding thumbnail images
	if( isset( $match[1] ) )
		$video_thumb = "http://img.youtube.com/vi/" . urlencode( $match[1] ) . "/0.jpg";

	if ( is_ssl() ) {
		$video_thumb = str_replace( 'http://', 'https://', $video_thumb );
	}

	// return whichever thumbnail image you would like to retrieve
	return $video_thumb;
} // End sf_get_video_image()
}


/*-----------------------------------------------------------------------------------*/
/* vt_resize - Resize images dynamically using wp built in functions
/*-----------------------------------------------------------------------------------*/
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if ( ! function_exists( 'vt_resize' ) ) {
	function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

		// Cast $width and $height to integer
		$width = intval( $width );
		$height = intval( $height );

		// this is an attachment, so we have the ID
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );
		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {
			$file_path = parse_url( esc_url( $img_url ) );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

			$orig_size = getimagesize( $file_path );

			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}

		$file_info = pathinfo( $file_path );

		// check if file exists
		if ( !isset( $file_info['dirname'] ) && !isset( $file_info['filename'] ) && !isset( $file_info['extension'] )  )
			return;

		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
			return;

		$extension = '.'. $file_info['extension'];

		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width ) {
			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {
				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

				$vt_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);
				return $vt_image;
			}

			// $crop = false or no height set
			if ( $crop == false OR !$height ) {
				// calculate the size proportionaly
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {
					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);
					return $vt_image;
				}
			}

			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0];

			// Check if GD Library installed
			if ( ! function_exists ( 'imagecreatetruecolor' ) ) {
			    echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
			    return;
			}

			// no cache files - let's finally resize it
			if ( function_exists( 'wp_get_image_editor' ) ) {
				$image = wp_get_image_editor( $file_path );
				if ( ! is_wp_error( $image ) ) {
					$image->resize( $width, $height, $crop );
					$save_data = $image->save();
					if ( isset( $save_data['path'] ) ) $new_img_path = $save_data['path'];
				}
			} else {
				$new_img_path = image_resize( $file_path, $width, $height, $crop );
			}

			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

			// resized output
			$vt_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);

			return $vt_image;
		}

		// default output - without resizing
		$vt_image = array (
			'url' => $image_src[0],
			'width' => $width,
			'height' => $height
		);

		return $vt_image;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Show Page Menu */
/*-----------------------------------------------------------------------------------*/
// Show menu in header.php
// Exlude the pages from the slider
function sf_show_pagemenu( $exclude = '' ) {
    // Split the featured pages from the options, and put in an array
    if ( get_option( 'sf_ex_featpages') ) {
        $menupages = get_option( 'sf_featpages' );
        $exclude = $menupages . ',' . $exclude;
    }

    $pages = wp_list_pages( 'sort_column=menu_order&title_li=&echo=0&depth=1&exclude=' . $exclude );
    $pages = preg_replace( '%<a ([^>]+)>%U','<a $1><span>', $pages );
    $pages = str_replace( '</a>','</span></a>', $pages );
    echo $pages;
} // End sf_show_pagemenu()


/*-----------------------------------------------------------------------------------*/
/* Get the style path currently selected */
/*-----------------------------------------------------------------------------------*/
function sf_style_path() {
	$return = '';

	$style = isset( $_REQUEST['style'] ) ? $_REQUEST['style'] : '';

	// Sanitize request input.
	$style = esc_attr( strtolower( trim( strip_tags( $style ) ) ) );

	if ( $style != '' ) {
		$style_path = $style;
	} else {
		$stylesheet = esc_attr( get_option( 'sf_alt_stylesheet' ) );

		// Prevent against an empty return to $stylesheet.
		if ( $stylesheet == '' ) {
			$stylesheet = 'default.css';
		}

		$style_path = str_replace( '.css', '', $stylesheet );
	}

	if ( $style_path == 'default' ) {
		$return = 'images';
	} else {
		$return = 'styles/' . $style_path;
	}

	echo esc_html( $return );
} // End sf_style_path()


/*-----------------------------------------------------------------------------------*/
/* Get page ID */
/*-----------------------------------------------------------------------------------*/
function get_page_id( $page_slug ) {
	$page_id = get_page_by_path( $page_slug );
    if ($page_id) {
        return $page_id->ID;
    } else {
        return null;
    }
} // End get_page_id()

/*-----------------------------------------------------------------------------------*/
/* Tidy up the image source url */
/*-----------------------------------------------------------------------------------*/
function cleanSource( $src ) {
	// remove slash from start of string
	if(strpos($src, "/") == 0) {
		$src = substr($src, -(strlen($src) - 1));
	}

	// Check if same domain so it doesn't strip external sites
	$host = str_replace( 'www.', '', $_SERVER['HTTP_HOST'] );
	if ( ! strpos( $src, $host ) )
		return $src;


	$regex = "/^((ht|f)tp(s|):\/\/)(www\.|)" . $host . "/i";
	$src = preg_replace ( $regex, '', $src );
	$src = htmlentities ( $src );

    // remove slash from start of string
    if ( strpos( $src, '/' ) === 0 ) {
        $src = substr ( $src, -( strlen( $src ) - 1 ) );
    }

	return $src;
} // End cleanSource()

/*-----------------------------------------------------------------------------------*/
/* Show image in RSS feed */
/* Original code by Justin Tadlock */
/*-----------------------------------------------------------------------------------*/
if ( 'true' == get_option( 'sf_rss_thumb', false ) || true == apply_filters( 'sf_add_image_to_rss', false ) ) {
	if ( get_option( 'rss_use_excerpt' ) )
		add_filter( 'the_excerpt_rss', 'add_image_RSS' );
	else
		add_filter( 'the_content_feed', 'add_image_RSS' );
}

/**
 * Maybe add the featured image to the RSS feed.
 * @param   string $content The content of the specified RSS feed item.
 * @since	1.0
 * @return  string
 */
function add_image_RSS ( $content ) {
	if ( ! is_feed() ) return $content;
	global $post, $id;

	// Get the "image" from custom field
	$image = sf_image( 'return=true&link=url' );
	$image_width = intval( apply_filters( 'sf_add_image_to_rss_width', 240 ) );

	// If there's an image, display the image with the content
	if( '' != $image ) {
		$content = '<p style="float: right; margin: 0 0 10px 15px; width:' . esc_attr( intval( $image_width ) ) . 'px; height: auto;">
		<img src="' . esc_url( $image ) . '" width="' . esc_attr( intval( $image_width ) ) . '" style="max-width: 100%; height: auto;" />
		</p>' . $content;
	}
	return $content;
} // End add_image_RSS()



/*-----------------------------------------------------------------------------------*/
/* Show analytics code in footer */
/*-----------------------------------------------------------------------------------*/
function sf_analytics(){
	$output = get_option( 'sf_google_analytics' );
	if ( $output != '' )
		echo stripslashes( $output ) . "\n";
} // End sf_analytics()
add_action( 'wp_footer','sf_analytics' );



/*-----------------------------------------------------------------------------------*/
/* Browser detection body_class() output */
/*-----------------------------------------------------------------------------------*/
add_filter( 'body_class','browser_body_class' );
function browser_body_class( $classes ) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) {
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$browser = substr( "$browser", 25, 8);
		if ($browser == "MSIE 7.0"  ) {
			$classes[] = 'ie7';
			$classes[] = 'ie';
		} elseif ($browser == "MSIE 6.0" ) {
			$classes[] = 'ie6';
			$classes[] = 'ie';
		} elseif ($browser == "MSIE 8.0" ) {
			$classes[] = 'ie8';
			$classes[] = 'ie';
		} elseif ($browser == "MSIE 9.0" ) {
			$classes[] = 'ie9';
			$classes[] = 'ie';
		} else {
			$classes[] = 'ie';
		}
	}
	else $classes[] = 'unknown';

	if( $is_iphone ) $classes[] = 'iphone';

	// Alternative style body class.
	$style = get_option( 'sf_alt_stylesheet', 'default' );
	$style = str_replace( '.css', '', $style );
	if ( '' != $style ) {
		$classes[] = 'alt-style-' . esc_attr( $style );
	}
	return $classes;
} // End browser_body_class()


/*-----------------------------------------------------------------------------------*/
/* Framework Update Page */
/*-----------------------------------------------------------------------------------*/

function sf_update_page() {
	// Clear transients.
	delete_transient( 'sf_critical_update' );
	delete_transient( 'sf_critical_update_data' );
	delete_transient( 'sf_version_data' );

        $method = get_filesystem_method();

        $to = ABSPATH . 'wp-content/themes/' . get_option( 'template' ) . '/app/backend/';
        if(isset($_POST['password'])){

            $cred = $_POST;
            $filesystem = WP_Filesystem($cred);

        }
        elseif(isset($_POST['sf_ftp_cred'])){

             $cred = unserialize(base64_decode($_POST['sf_ftp_cred']));
             $filesystem = WP_Filesystem($cred);

        } else {

           $filesystem = WP_Filesystem();

        };
        $url = admin_url( 'admin.php?page=sf_update' );
        ?>
            <div class="wrap themes-page">
        <?php
            if($filesystem == false){

            request_filesystem_credentials ( $url );

            }  else {

            // Clear the transient to force a fresh update.
            delete_transient( 'sf_version_data' );

            $localversion = esc_html( get_option( 'sf_version' ) );
            $remoteversion = sf_get_fw_version();

            // Test if new version
            $upd = false;
			$loc = explode( '.',$localversion);
			$rem = explode( '.',$remoteversion['version']);

            if( $loc[0] < $rem[0] )
            	$upd = true;
            elseif ( $loc[1] < $rem[1] )
            	$upd = true;
            elseif( $loc[2] < $rem[2] )
            	$upd = true;

            ?>
            <?php screen_icon( 'tools' ); ?>
            <h2><?php _e( 'Framework Updates', 'sfwp-locale' ); ?></h2>
            <span style="display:none"><?php echo $method; ?></span>
            <form method="post"  enctype="multipart/form-data" id="sf-form" action="<?php /* echo $url; */ ?>">

                <?php if( $upd ) { ?>
                <?php wp_nonce_field( 'update-options' ); ?>
                <h3><?php _e( 'A new version of Framework is available.', 'sfwp-locale' ); ?></h3>
                <p><?php _e( 'This updater will download and extract the latest Framework files to your current theme\'s functions folder.', 'sfwp-locale' ); ?></p>
                <p><?php _e( 'We recommend backing up your theme files and updating WordPress to latest version before proceeding.', 'sfwp-locale' ); ?></p>
                <p>&rarr; <strong>Your version:</strong> <?php echo $localversion; ?></p>

                <p>&rarr; <strong>Current Version:</strong> <?php echo $remoteversion['version']; ?></p>
                <input type="submit" class="button" value="Update Framework" />
                <?php } else { ?>
                <h3>You have the latest version of Framework</h3>
                <p>&rarr; <strong>Your version:</strong> <?php echo $localversion; ?></p>
                <?php } ?>
                 <p><a href="<?php echo esc_url( 'http://starjive.com/wordpress/themes/light-framework/release-notes/' ); ?>" title="<?php esc_attr_e( 'Before upgrading, we recommend reading the Framework release notes', 'sfwp-locale' ); ?>"><strong><?php _e( 'Read the Release Notes', 'sfwp-locale' ); ?></strong></a></p>
                <input type="hidden" name="sf_update_save" value="save" />
                <input type="hidden" name="sf_ftp_cred" value="<?php echo esc_attr( base64_encode(serialize($_POST))); ?>" />

            </form>
            <?php } ?>
            </div>
            <?php
};

/*-----------------------------------------------------------------------------------*/
/* Framework Update Head */
/*-----------------------------------------------------------------------------------*/

function sf_update_head() {
  if( isset( $_REQUEST['page'] ) ) {
	// Sanitize page being requested.
	$_page = esc_attr( $_REQUEST['page'] );

	if( $_page == 'sf_update' ) {
		//Setup Filesystem
		$method = get_filesystem_method();

		if( isset( $_POST['sf_ftp_cred'] ) ) {
			$cred = unserialize( base64_decode( $_POST['sf_ftp_cred'] ) );
			$filesystem = WP_Filesystem($cred);
		} else {
		   $filesystem = WP_Filesystem();
		}

		if( $filesystem == false && $_POST['upgrade'] != 'Proceed' ) {

			function sf_update_filesystem_warning() {
					$method = get_filesystem_method();
					echo "<div id='filesystem-warning' class='updated fade'><p>Failed: Filesystem preventing downloads. ( ". $method .")</p></div>";
				}
				add_action( 'admin_notices', 'sf_update_filesystem_warning' );
				return;
		}
		if(isset($_REQUEST['sf_update_save'])){

			// Sanitize action being requested.
			$_action = esc_attr( $_REQUEST['sf_update_save'] );

		if( $_action == 'save' ) {

		$temp_file_addr = download_url( esc_url( 'http://starjive.com/wordpress/themes/light-framework/latest/update.zip' ) );

		if ( is_wp_error($temp_file_addr) ) {

			$error = esc_html( $temp_file_addr->get_error_code() );

			if( $error == 'http_no_url' ) {
			//The source file was not found or is invalid
				function sf_update_missing_source_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Invalid URL Provided</p></div>";
				}
				add_action( 'admin_notices', 'sf_update_missing_source_warning' );
			} else {
				function sf_update_other_upload_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
				}
				add_action( 'admin_notices', 'sf_update_other_upload_warning' );

			}

			return;

		  }
		//Unzip it
		global $wp_filesystem;
		$to = $wp_filesystem->wp_content_dir() . "/themes/" . get_option( 'template' ) . "/app/backend/";

		$dounzip = unzip_file($temp_file_addr, $to);

		unlink($temp_file_addr); // Delete Temp File

		if ( is_wp_error($dounzip) ) {

			//DEBUG
			$error = esc_html( $dounzip->get_error_code() );
			$data = $dounzip->get_error_data($error);
			//echo $error. ' - ';
			//print_r($data);

			if($error == 'incompatible_archive') {
				//The source file was not found or is invalid
				function sf_update_no_archive_warning() {
					echo "<div id='sf-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
				}
				add_action( 'admin_notices', 'sf_update_no_archive_warning' );
			}
			if($error == 'empty_archive') {
				function sf_update_empty_archive_warning() {
					echo "<div id='sf-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
				}
				add_action( 'admin_notices', 'sf_update_empty_archive_warning' );
			}
			if($error == 'mkdir_failed') {
				function sf_update_mkdir_warning() {
					echo "<div id='sf-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure</p></div>";
				}
				add_action( 'admin_notices', 'sf_update_mkdir_warning' );
			}
			if($error == 'copy_failed') {
				function sf_update_copy_fail_warning() {
					echo "<div id='sf-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
				}
				add_action( 'admin_notices', 'sf_update_copy_fail_warning' );
			}

			return;

		}

		function sf_updated_success() {
			echo "<div id='sf-upgraded' class='updated fade'><p>New framework successfully downloaded, extracted and updated.</p></div>";
		}

		add_action( 'admin_notices', 'sf_updated_success' );

		}
	}
	} //End user input save part of the update
 }
}

add_action( 'admin_head', 'sf_update_head' );

/*-----------------------------------------------------------------------------------*/
/* Framework Version Getter */
/*-----------------------------------------------------------------------------------*/

function sf_get_fw_version( $url = '', $check_if_critical = false ) {

	if( ! empty( $url ) ) {
		$fw_url = $url;
	} else {
    	$fw_url = 'http://starjive.com/wordpress/themes/light-framework/latest/changelog.txt';
    }

    $output = array( 'version' => '', 'is_critical' => false );

    $version_data = get_transient( 'sf_version_data' );

	if ( $version_data != '' && $check_if_critical == false ) { return $version_data; }

	$temp_file_addr = download_url( $fw_url );
	if( ! is_wp_error( $temp_file_addr ) && $file_contents = file( $temp_file_addr ) ) {
        foreach ( $file_contents as $line_num => $line ) {
            $current_line =  $line;

            if( $line_num > 1 ) {    // Not the first or second... dodgy :P

                if ( preg_match( '/^[0-9]/', $line ) ) {

						// Do critical update check.
						if ( $check_if_critical && ( strtolower( trim( substr( $line, -10 ) ) ) == 'critical' ) ) {
							$output['is_critical'] = true;
						}

                        $current_line = stristr( $current_line, 'version' );
                        $current_line = preg_replace( '~[^0-9,.]~','',$current_line );
                        $output['version'] = $current_line;
                        break;
                }
            }
        }
        unlink( $temp_file_addr );
    } else {
        $output['version'] = get_option( 'sf_version' );
    }

    // Set the transient containing the latest version number.
	set_transient( 'sf_version_data', $output , 60*60*24 );

	return $output;
} // End sf_get_fw_version()


/*-----------------------------------------------------------------------------------*/
/* Framework Version Checker */
/*-----------------------------------------------------------------------------------*/

function sf_version_checker( $local_version, $check_if_critical = false ) {
	$data = array( 'is_update' => false, 'version' => '1.0.0', 'status' => 'none' );

	if ( ! $local_version ) { return $data; }

	$version_data = sf_get_fw_version( '', $check_if_critical );

	$check = version_compare( $version_data['version'], $local_version ); // Returns 1 if there is an update available.

	if ( $check == 1 ) {
		$data['is_update'] = true;
		$data['version'] = $version_data['version'];
		$data['is_critical'] = $version_data['is_critical'];
	}

	return $data;
} // End sf_version_checker()


/*-----------------------------------------------------------------------------------*/
/* sf_title() */
/*-----------------------------------------------------------------------------------*/

/**
 * Display or return the title for the current screen.
 * @since	1.0
 * @param	boolean $echo Whether or not to echo the title. Default: true.
 * @return	string  The title.
 */
if ( ! function_exists( 'sf_title' ) ) {
function sf_title ( $echo = true ) {
	// If the parameter isn't a boolean, set it to the default value.
	if ( ! is_bool( $echo ) ) {
		$echo = true;
	}
	$sep = '|';
	$raw_title = wp_title( $sep, false, 'right' );

	// Allow child themes/plugins to filter the title value.
	$title = apply_filters( 'sf_title', $raw_title, $sep, $raw_title );
	if ( true == $echo ) echo $title;
	return $title;
} // End sf_title()
}

if ( ! function_exists( 'sf_add_blog_name_to_title' ) ) {
/**
 * Add the site title to the sf_title() text.
 * @since	1.0
 * @param	string $title     Existing title value.
 * @param	string $sep       Separator string.
 * @param	string $raw_title Raw title value.
 * @return	string            Modified title.
 */
function sf_add_blog_name_to_title ( $title, $sep, $raw_title ) {
	$site_title = get_bloginfo( 'name' );
	$title .= apply_filters( 'sf_add_blog_name_to_title', $site_title );
	return $title;
} // End sf_add_blog_name_to_title()
}

if ( ! function_exists( 'sf_maybe_add_page_number_to_title' ) ) {
/**
 * Maybe add the page number, if paginating, to the sf_title() text.
 * @since	1.0
 * @param	string $title     Existing title value.
 * @param	string $sep       Separator string.
 * @param	string $raw_title Raw title value.
 * @return	string            Modified title.
 */
function sf_maybe_add_page_number_to_title ( $title, $sep, $raw_title ) {
	if ( is_paged() ) {
		$page = intval( get_query_var( 'page' ) );
		$paged = intval( get_query_var( 'paged' ) );
		$page_number = $paged;
		if ( 0 < $page ) {
			$page_number = $page;
		}

		$title .= apply_filters( 'sf_maybe_add_page_number_to_title', ' ' . $sep . ' ' . sprintf( __( 'Page %s', 'sfwp-locale' ), intval( $page_number ) ) );
	}
	return $title;
} // End sf_maybe_add_page_number_to_title()
}

if ( ! class_exists( 'WPSEO_Frontend' ) && ! defined( 'WPSEO_VERSION' ) ) {
	add_filter( 'sf_title', 'sf_add_blog_name_to_title', 10, 3 );
	add_filter( 'sf_title', 'sf_maybe_add_page_number_to_title', 10, 3 );
}

/*-----------------------------------------------------------------------------------*/
/* sf_meta() */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_meta' ) ) {
/**
 * Display meta tags.
 * @since	1.0
 * @return	void
 */
function sf_meta () {
	echo '<meta http-equiv="Content-Type" content="'. esc_attr( get_bloginfo( 'html_type' ) ) . '; charset=' . esc_attr( get_bloginfo( 'charset' ) ) . '" />' . "\n";

	do_action( 'sf_meta' );
} // End sf_meta()
}

/*-----------------------------------------------------------------------------------*/
/* Text Trimmer */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_text_trim' ) ) {
	function sf_text_trim( $text, $words = 50 ) {
		$matches = preg_split( "/\s+/", $text, $words + 1);
		$sz = count($matches);
		if ($sz > $words)
		{
			unset($matches[$sz-1]);
			return implode( ' ',$matches)." ...";
		}
		return $text;
	} // End sf_text_trim()
}

/*-----------------------------------------------------------------------------------*/
/* Google Webfonts Array */
/* Documentation:
/*
/* name: The name of the Google Font.
/* variant: The Google Font API variants available for the font.
/*-----------------------------------------------------------------------------------*/

// Available Google webfont names
$GLOBALS['google_fonts'] = sf_get_google_fonts();

/**
 * Return a filtered array of possible system fonts.
 * @since	1.0
 * @return	array Possible system fonts.
 */
function sf_get_system_fonts () {
	return (array)apply_filters( 'sf_get_system_fonts', array(
			'Arial, sans-serif' => __( 'Arial', 'sfwp-locale' ),
			'Verdana, Geneva, sans-serif' => __( 'Verdana', 'sfwp-locale' ),
			'&quot;Trebuchet MS&quot;, Tahoma, sans-serif' => __( 'Trebuchet', 'sfwp-locale' ),
			'Georgia, serif' => __( 'Georgia', 'sfwp-locale' ),
			'&quot;Times New Roman&quot;, serif' => __( 'Times New Roman', 'sfwp-locale' ),
			'Tahoma, Geneva, Verdana, sans-serif' => __( 'Tahoma', 'sfwp-locale' ),
			'Palatino, &quot;Palatino Linotype&quot;, serif' => __( 'Palatino', 'sfwp-locale' ),
			'&quot;Helvetica Neue&quot;, Helvetica, sans-serif' => __( 'Helvetica *', 'sfwp-locale' ),
			'Calibri, Candara, Segoe, Optima, sans-serif' => __( 'Calibri *', 'sfwp-locale' ),
			'&quot;Myriad Pro&quot;, Myriad, sans-serif' => __( 'Myriad Pro *', 'sfwp-locale' ),
			'&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, &quot;Lucida Sans&quot;, sans-serif' => __( 'Lucida', 'sfwp-locale' ),
			'&quot;Arial Black&quot;, sans-serif' => __( 'Arial Black', 'sfwp-locale' ),
			'&quot;Gill Sans&quot;, &quot;Gill Sans MT&quot;, Calibri, sans-serif' => __( 'Gill Sans *', 'sfwp-locale' ),
			'Geneva, Tahoma, Verdana, sans-serif' => __( 'Geneva *', 'sfwp-locale' ),
			'Impact, Charcoal, sans-serif' => __( 'Impact', 'sfwp-locale' ),
			'Courier, &quot;Courier New&quot;, monospace' => __( 'Courier', 'sfwp-locale' ),
			'&quot;Century Gothic&quot;, sans-serif' => __( 'Century Gothic', 'sfwp-locale' )
		)
	);
} // End sf_get_system_fonts()

/**
 * Return a filtered array of possible system fonts test cases.
 * @since	1.0
 * @return	array Possible system fonts test cases.
 */
function sf_get_system_fonts_test_cases () {
	// The test case should always correspond to the text before the first comma in the array key.
	return (array)apply_filters( 'sf_get_system_fonts_test_cases', array(
			'Arial, sans-serif' => 'Arial',
			'Verdana, Geneva, sans-serif' => 'Verdana',
			'&quot;Trebuchet MS&quot;, Tahoma, sans-serif' => 'Trebuchet MS',
			'Georgia, serif' => 'Georgia',
			'&quot;Times New Roman&quot;, serif' => 'Times New Roman',
			'Tahoma, Geneva, Verdana, sans-serif' => 'Tahoma',
			'Palatino, &quot;Palatino Linotype&quot;, serif' => 'Palatino',
			'&quot;Helvetica Neue&quot;, Helvetica, sans-serif' => 'Helvetica Neue',
			'Calibri, Candara, Segoe, Optima, sans-serif' => 'Calibri',
			'&quot;Myriad Pro&quot;, Myriad, sans-serif' => 'Myriad Pro',
			'&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, &quot;Lucida Sans&quot;, sans-serif' => 'Lucida Grande',
			'&quot;Arial Black&quot;, sans-serif' => 'Arial Black',
			'&quot;Gill Sans&quot;, &quot;Gill Sans MT&quot;, Calibri, sans-serif' => 'Gill Sans',
			'Geneva, Tahoma, Verdana, sans-serif' => 'Geneva',
			'Impact, Charcoal, sans-serif' => 'Impact',
			'Courier, &quot;Courier New&quot;, monospace' => 'Courier',
			'&quot;Century Gothic&quot;, sans-serif' => 'Century Gothic'
		)
	);
} // End sf_get_system_fonts_test_cases()

/**
 * Return a filtered array of Google WebFonts.
 * @since	1.0
 * @return	array Google WebFonts.
 */
function sf_get_google_fonts () {
	return (array)apply_filters( 'sf_get_google_fonts', sf_get_google_fonts_store() );
} // End sf_get_google_fonts()

/**
 * Return a raw array of Google WebFonts.
 * @since	1.0
 * @return	array Google WebFonts.
 */
function sf_get_google_fonts_store () {
	$google_fonts = array (
		array( 'name' => 'ABeeZee', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Abel', 'variant' => ':regular' ),
		array( 'name' => 'Abril Fatface', 'variant' => ':regular' ),
		array( 'name' => 'Aclonica', 'variant' => ':regular' ),
		array( 'name' => 'Acme', 'variant' => ':regular' ),
		array( 'name' => 'Actor', 'variant' => ':regular' ),
		array( 'name' => 'Adamina', 'variant' => ':regular' ),
		array( 'name' => 'Advent Pro', 'variant' => ':100,:200,:300,:regular,:500,:600,:700' ),
		array( 'name' => 'Aguafina Script', 'variant' => ':regular' ),
		array( 'name' => 'Akronim', 'variant' => ':regular' ),
		array( 'name' => 'Aladin', 'variant' => ':regular' ),
		array( 'name' => 'Aldrich', 'variant' => ':regular' ),
		array( 'name' => 'Alef', 'variant' => ':regular,:700' ),
		array( 'name' => 'Alegreya', 'variant' => ':regular,:italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Alegreya SC', 'variant' => ':regular,:italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Alegreya Sans', 'variant' => ':100,:100italic,:300,:300italic,:regular,:italic,:500,:500italic,:700,:700italic,:800,:800italic,:900,:900italic' ),
		array( 'name' => 'Alegreya Sans SC', 'variant' => ':100,:100italic,:300,:300italic,:regular,:italic,:500,:500italic,:700,:700italic,:800,:800italic,:900,:900italic' ),
		array( 'name' => 'Alex Brush', 'variant' => ':regular' ),
		array( 'name' => 'Alfa Slab One', 'variant' => ':regular' ),
		array( 'name' => 'Alice', 'variant' => ':regular' ),
		array( 'name' => 'Alike', 'variant' => ':regular' ),
		array( 'name' => 'Alike Angular', 'variant' => ':regular' ),
		array( 'name' => 'Allan', 'variant' => ':regular,:700' ),
		array( 'name' => 'Allerta', 'variant' => ':regular' ),
		array( 'name' => 'Allerta Stencil', 'variant' => ':regular' ),
		array( 'name' => 'Allura', 'variant' => ':regular' ),
		array( 'name' => 'Almendra', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Almendra Display', 'variant' => ':regular' ),
		array( 'name' => 'Almendra SC', 'variant' => ':regular' ),
		array( 'name' => 'Amarante', 'variant' => ':regular' ),
		array( 'name' => 'Amaranth', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Amatic SC', 'variant' => ':regular,:700' ),
		array( 'name' => 'Amethysta', 'variant' => ':regular' ),
		array( 'name' => 'Anaheim', 'variant' => ':regular' ),
		array( 'name' => 'Andada', 'variant' => ':regular' ),
		array( 'name' => 'Andika', 'variant' => ':regular' ),
		array( 'name' => 'Angkor', 'variant' => ':regular' ),
		array( 'name' => 'Annie Use Your Telescope', 'variant' => ':regular' ),
		array( 'name' => 'Anonymous Pro', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Antic', 'variant' => ':regular' ),
		array( 'name' => 'Antic Didone', 'variant' => ':regular' ),
		array( 'name' => 'Antic Slab', 'variant' => ':regular' ),
		array( 'name' => 'Anton', 'variant' => ':regular' ),
		array( 'name' => 'Arapey', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Arbutus', 'variant' => ':regular' ),
		array( 'name' => 'Arbutus Slab', 'variant' => ':regular' ),
		array( 'name' => 'Architects Daughter', 'variant' => ':regular' ),
		array( 'name' => 'Archivo Black', 'variant' => ':regular' ),
		array( 'name' => 'Archivo Narrow', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Arimo', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Arizonia', 'variant' => ':regular' ),
		array( 'name' => 'Armata', 'variant' => ':regular' ),
		array( 'name' => 'Artifika', 'variant' => ':regular' ),
		array( 'name' => 'Arvo', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Asap', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Asset', 'variant' => ':regular' ),
		array( 'name' => 'Astloch', 'variant' => ':regular,:700' ),
		array( 'name' => 'Asul', 'variant' => ':regular,:700' ),
		array( 'name' => 'Atomic Age', 'variant' => ':regular' ),
		array( 'name' => 'Aubrey', 'variant' => ':regular' ),
		array( 'name' => 'Audiowide', 'variant' => ':regular' ),
		array( 'name' => 'Autour One', 'variant' => ':regular' ),
		array( 'name' => 'Average', 'variant' => ':regular' ),
		array( 'name' => 'Average Sans', 'variant' => ':regular' ),
		array( 'name' => 'Averia Gruesa Libre', 'variant' => ':regular' ),
		array( 'name' => 'Averia Libre', 'variant' => ':300,:300italic,:regular,:italic,:700,:700italic' ),
		array( 'name' => 'Averia Sans Libre', 'variant' => ':300,:300italic,:regular,:italic,:700,:700italic' ),
		array( 'name' => 'Averia Serif Libre', 'variant' => ':300,:300italic,:regular,:italic,:700,:700italic' ),
		array( 'name' => 'Bad Script', 'variant' => ':regular' ),
		array( 'name' => 'Balthazar', 'variant' => ':regular' ),
		array( 'name' => 'Bangers', 'variant' => ':regular' ),
		array( 'name' => 'Basic', 'variant' => ':regular' ),
		array( 'name' => 'Battambang', 'variant' => ':regular,:700' ),
		array( 'name' => 'Baumans', 'variant' => ':regular' ),
		array( 'name' => 'Bayon', 'variant' => ':regular' ),
		array( 'name' => 'Belgrano', 'variant' => ':regular' ),
		array( 'name' => 'Belleza', 'variant' => ':regular' ),
		array( 'name' => 'BenchNine', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Bentham', 'variant' => ':regular' ),
		array( 'name' => 'Berkshire Swash', 'variant' => ':regular' ),
		array( 'name' => 'Bevan', 'variant' => ':regular' ),
		array( 'name' => 'Bigelow Rules', 'variant' => ':regular' ),
		array( 'name' => 'Bigshot One', 'variant' => ':regular' ),
		array( 'name' => 'Bilbo', 'variant' => ':regular' ),
		array( 'name' => 'Bilbo Swash Caps', 'variant' => ':regular' ),
		array( 'name' => 'Bitter', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Black Ops One', 'variant' => ':regular' ),
		array( 'name' => 'Bokor', 'variant' => ':regular' ),
		array( 'name' => 'Bonbon', 'variant' => ':regular' ),
		array( 'name' => 'Boogaloo', 'variant' => ':regular' ),
		array( 'name' => 'Bowlby One', 'variant' => ':regular' ),
		array( 'name' => 'Bowlby One SC', 'variant' => ':regular' ),
		array( 'name' => 'Brawler', 'variant' => ':regular' ),
		array( 'name' => 'Bree Serif', 'variant' => ':regular' ),
		array( 'name' => 'Bubblegum Sans', 'variant' => ':regular' ),
		array( 'name' => 'Bubbler One', 'variant' => ':regular' ),
		array( 'name' => 'Buda', 'variant' => ':300' ),
		array( 'name' => 'Buenard', 'variant' => ':regular,:700' ),
		array( 'name' => 'Butcherman', 'variant' => ':regular' ),
		array( 'name' => 'Butterfly Kids', 'variant' => ':regular' ),
		array( 'name' => 'Cabin', 'variant' => ':regular,:italic,:500,:500italic,:600,:600italic,:700,:700italic' ),
		array( 'name' => 'Cabin Condensed', 'variant' => ':regular,:500,:600,:700' ),
		array( 'name' => 'Cabin Sketch', 'variant' => ':regular,:700' ),
		array( 'name' => 'Caesar Dressing', 'variant' => ':regular' ),
		array( 'name' => 'Cagliostro', 'variant' => ':regular' ),
		array( 'name' => 'Calligraffitti', 'variant' => ':regular' ),
		array( 'name' => 'Cambo', 'variant' => ':regular' ),
		array( 'name' => 'Candal', 'variant' => ':regular' ),
		array( 'name' => 'Cantarell', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Cantata One', 'variant' => ':regular' ),
		array( 'name' => 'Cantora One', 'variant' => ':regular' ),
		array( 'name' => 'Capriola', 'variant' => ':regular' ),
		array( 'name' => 'Cardo', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Carme', 'variant' => ':regular' ),
		array( 'name' => 'Carrois Gothic', 'variant' => ':regular' ),
		array( 'name' => 'Carrois Gothic SC', 'variant' => ':regular' ),
		array( 'name' => 'Carter One', 'variant' => ':regular' ),
		array( 'name' => 'Caudex', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Cedarville Cursive', 'variant' => ':regular' ),
		array( 'name' => 'Ceviche One', 'variant' => ':regular' ),
		array( 'name' => 'Changa One', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Chango', 'variant' => ':regular' ),
		array( 'name' => 'Chau Philomene One', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Chela One', 'variant' => ':regular' ),
		array( 'name' => 'Chelsea Market', 'variant' => ':regular' ),
		array( 'name' => 'Chenla', 'variant' => ':regular' ),
		array( 'name' => 'Cherry Cream Soda', 'variant' => ':regular' ),
		array( 'name' => 'Cherry Swash', 'variant' => ':regular,:700' ),
		array( 'name' => 'Chewy', 'variant' => ':regular' ),
		array( 'name' => 'Chicle', 'variant' => ':regular' ),
		array( 'name' => 'Chivo', 'variant' => ':regular,:italic,:900,:900italic' ),
		array( 'name' => 'Cinzel', 'variant' => ':regular,:700,:900' ),
		array( 'name' => 'Cinzel Decorative', 'variant' => ':regular,:700,:900' ),
		array( 'name' => 'Clicker Script', 'variant' => ':regular' ),
		array( 'name' => 'Coda', 'variant' => ':regular,:800' ),
		array( 'name' => 'Coda Caption', 'variant' => ':800' ),
		array( 'name' => 'Codystar', 'variant' => ':300,:regular' ),
		array( 'name' => 'Combo', 'variant' => ':regular' ),
		array( 'name' => 'Comfortaa', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Coming Soon', 'variant' => ':regular' ),
		array( 'name' => 'Concert One', 'variant' => ':regular' ),
		array( 'name' => 'Condiment', 'variant' => ':regular' ),
		array( 'name' => 'Content', 'variant' => ':regular,:700' ),
		array( 'name' => 'Contrail One', 'variant' => ':regular' ),
		array( 'name' => 'Convergence', 'variant' => ':regular' ),
		array( 'name' => 'Cookie', 'variant' => ':regular' ),
		array( 'name' => 'Copse', 'variant' => ':regular' ),
		array( 'name' => 'Corben', 'variant' => ':regular,:700' ),
		array( 'name' => 'Courgette', 'variant' => ':regular' ),
		array( 'name' => 'Cousine', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Coustard', 'variant' => ':regular,:900' ),
		array( 'name' => 'Covered By Your Grace', 'variant' => ':regular' ),
		array( 'name' => 'Crafty Girls', 'variant' => ':regular' ),
		array( 'name' => 'Creepster', 'variant' => ':regular' ),
		array( 'name' => 'Crete Round', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Crimson Text', 'variant' => ':regular,:italic,:600,:600italic,:700,:700italic' ),
		array( 'name' => 'Croissant One', 'variant' => ':regular' ),
		array( 'name' => 'Crushed', 'variant' => ':regular' ),
		array( 'name' => 'Cuprum', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Cutive', 'variant' => ':regular' ),
		array( 'name' => 'Cutive Mono', 'variant' => ':regular' ),
		array( 'name' => 'Damion', 'variant' => ':regular' ),
		array( 'name' => 'Dancing Script', 'variant' => ':regular,:700' ),
		array( 'name' => 'Dangrek', 'variant' => ':regular' ),
		array( 'name' => 'Dawning of a New Day', 'variant' => ':regular' ),
		array( 'name' => 'Days One', 'variant' => ':regular' ),
		array( 'name' => 'Delius', 'variant' => ':regular' ),
		array( 'name' => 'Delius Swash Caps', 'variant' => ':regular' ),
		array( 'name' => 'Delius Unicase', 'variant' => ':regular,:700' ),
		array( 'name' => 'Della Respira', 'variant' => ':regular' ),
		array( 'name' => 'Denk One', 'variant' => ':regular' ),
		array( 'name' => 'Devonshire', 'variant' => ':regular' ),
		array( 'name' => 'Didact Gothic', 'variant' => ':regular' ),
		array( 'name' => 'Diplomata', 'variant' => ':regular' ),
		array( 'name' => 'Diplomata SC', 'variant' => ':regular' ),
		array( 'name' => 'Domine', 'variant' => ':regular,:700' ),
		array( 'name' => 'Donegal One', 'variant' => ':regular' ),
		array( 'name' => 'Doppio One', 'variant' => ':regular' ),
		array( 'name' => 'Dorsa', 'variant' => ':regular' ),
		array( 'name' => 'Dosis', 'variant' => ':200,:300,:regular,:500,:600,:700,:800' ),
		array( 'name' => 'Dr Sugiyama', 'variant' => ':regular' ),
		array( 'name' => 'Droid Sans', 'variant' => ':regular,:700' ),
		array( 'name' => 'Droid Sans Mono', 'variant' => ':regular' ),
		array( 'name' => 'Droid Serif', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Duru Sans', 'variant' => ':regular' ),
		array( 'name' => 'Dynalight', 'variant' => ':regular' ),
		array( 'name' => 'EB Garamond', 'variant' => ':regular' ),
		array( 'name' => 'Eagle Lake', 'variant' => ':regular' ),
		array( 'name' => 'Eater', 'variant' => ':regular' ),
		array( 'name' => 'Economica', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Electrolize', 'variant' => ':regular' ),
		array( 'name' => 'Elsie', 'variant' => ':regular,:900' ),
		array( 'name' => 'Elsie Swash Caps', 'variant' => ':regular,:900' ),
		array( 'name' => 'Emblema One', 'variant' => ':regular' ),
		array( 'name' => 'Emilys Candy', 'variant' => ':regular' ),
		array( 'name' => 'Engagement', 'variant' => ':regular' ),
		array( 'name' => 'Englebert', 'variant' => ':regular' ),
		array( 'name' => 'Enriqueta', 'variant' => ':regular,:700' ),
		array( 'name' => 'Erica One', 'variant' => ':regular' ),
		array( 'name' => 'Esteban', 'variant' => ':regular' ),
		array( 'name' => 'Euphoria Script', 'variant' => ':regular' ),
		array( 'name' => 'Ewert', 'variant' => ':regular' ),
		array( 'name' => 'Exo', 'variant' => ':100,:100italic,:200,:200italic,:300,:300italic,:regular,:italic,:500,:500italic,:600,:600italic,:700,:700italic,:800,:800italic,:900,:900italic' ),
		array( 'name' => 'Exo 2', 'variant' => ':100,:100italic,:200,:200italic,:300,:300italic,:regular,:italic,:500,:500italic,:600,:600italic,:700,:700italic,:800,:800italic,:900,:900italic' ),
		array( 'name' => 'Expletus Sans', 'variant' => ':regular,:italic,:500,:500italic,:600,:600italic,:700,:700italic' ),
		array( 'name' => 'Fanwood Text', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Fascinate', 'variant' => ':regular' ),
		array( 'name' => 'Fascinate Inline', 'variant' => ':regular' ),
		array( 'name' => 'Faster One', 'variant' => ':regular' ),
		array( 'name' => 'Fasthand', 'variant' => ':regular' ),
		array( 'name' => 'Fauna One', 'variant' => ':regular' ),
		array( 'name' => 'Federant', 'variant' => ':regular' ),
		array( 'name' => 'Federo', 'variant' => ':regular' ),
		array( 'name' => 'Felipa', 'variant' => ':regular' ),
		array( 'name' => 'Fenix', 'variant' => ':regular' ),
		array( 'name' => 'Finger Paint', 'variant' => ':regular' ),
		array( 'name' => 'Fjalla One', 'variant' => ':regular' ),
		array( 'name' => 'Fjord One', 'variant' => ':regular' ),
		array( 'name' => 'Flamenco', 'variant' => ':300,:regular' ),
		array( 'name' => 'Flavors', 'variant' => ':regular' ),
		array( 'name' => 'Fondamento', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Fontdiner Swanky', 'variant' => ':regular' ),
		array( 'name' => 'Forum', 'variant' => ':regular' ),
		array( 'name' => 'Francois One', 'variant' => ':regular' ),
		array( 'name' => 'Freckle Face', 'variant' => ':regular' ),
		array( 'name' => 'Fredericka the Great', 'variant' => ':regular' ),
		array( 'name' => 'Fredoka One', 'variant' => ':regular' ),
		array( 'name' => 'Freehand', 'variant' => ':regular' ),
		array( 'name' => 'Fresca', 'variant' => ':regular' ),
		array( 'name' => 'Frijole', 'variant' => ':regular' ),
		array( 'name' => 'Fruktur', 'variant' => ':regular' ),
		array( 'name' => 'Fugaz One', 'variant' => ':regular' ),
		array( 'name' => 'GFS Didot', 'variant' => ':regular' ),
		array( 'name' => 'GFS Neohellenic', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Gabriela', 'variant' => ':regular' ),
		array( 'name' => 'Gafata', 'variant' => ':regular' ),
		array( 'name' => 'Galdeano', 'variant' => ':regular' ),
		array( 'name' => 'Galindo', 'variant' => ':regular' ),
		array( 'name' => 'Gentium Basic', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Gentium Book Basic', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Geo', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Geostar', 'variant' => ':regular' ),
		array( 'name' => 'Geostar Fill', 'variant' => ':regular' ),
		array( 'name' => 'Germania One', 'variant' => ':regular' ),
		array( 'name' => 'Gilda Display', 'variant' => ':regular' ),
		array( 'name' => 'Give You Glory', 'variant' => ':regular' ),
		array( 'name' => 'Glass Antiqua', 'variant' => ':regular' ),
		array( 'name' => 'Glegoo', 'variant' => ':regular' ),
		array( 'name' => 'Gloria Hallelujah', 'variant' => ':regular' ),
		array( 'name' => 'Goblin One', 'variant' => ':regular' ),
		array( 'name' => 'Gochi Hand', 'variant' => ':regular' ),
		array( 'name' => 'Gorditas', 'variant' => ':regular,:700' ),
		array( 'name' => 'Goudy Bookletter 1911', 'variant' => ':regular' ),
		array( 'name' => 'Graduate', 'variant' => ':regular' ),
		array( 'name' => 'Grand Hotel', 'variant' => ':regular' ),
		array( 'name' => 'Gravitas One', 'variant' => ':regular' ),
		array( 'name' => 'Great Vibes', 'variant' => ':regular' ),
		array( 'name' => 'Griffy', 'variant' => ':regular' ),
		array( 'name' => 'Gruppo', 'variant' => ':regular' ),
		array( 'name' => 'Gudea', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Habibi', 'variant' => ':regular' ),
		array( 'name' => 'Hammersmith One', 'variant' => ':regular' ),
		array( 'name' => 'Hanalei', 'variant' => ':regular' ),
		array( 'name' => 'Hanalei Fill', 'variant' => ':regular' ),
		array( 'name' => 'Handlee', 'variant' => ':regular' ),
		array( 'name' => 'Hanuman', 'variant' => ':regular,:700' ),
		array( 'name' => 'Happy Monkey', 'variant' => ':regular' ),
		array( 'name' => 'Headland One', 'variant' => ':regular' ),
		array( 'name' => 'Henny Penny', 'variant' => ':regular' ),
		array( 'name' => 'Herr Von Muellerhoff', 'variant' => ':regular' ),
		array( 'name' => 'Holtwood One SC', 'variant' => ':regular' ),
		array( 'name' => 'Homemade Apple', 'variant' => ':regular' ),
		array( 'name' => 'Homenaje', 'variant' => ':regular' ),
		array( 'name' => 'IM Fell DW Pica', 'variant' => ':regular,:italic' ),
		array( 'name' => 'IM Fell DW Pica SC', 'variant' => ':regular' ),
		array( 'name' => 'IM Fell Double Pica', 'variant' => ':regular,:italic' ),
		array( 'name' => 'IM Fell Double Pica SC', 'variant' => ':regular' ),
		array( 'name' => 'IM Fell English', 'variant' => ':regular,:italic' ),
		array( 'name' => 'IM Fell English SC', 'variant' => ':regular' ),
		array( 'name' => 'IM Fell French Canon', 'variant' => ':regular,:italic' ),
		array( 'name' => 'IM Fell French Canon SC', 'variant' => ':regular' ),
		array( 'name' => 'IM Fell Great Primer', 'variant' => ':regular,:italic' ),
		array( 'name' => 'IM Fell Great Primer SC', 'variant' => ':regular' ),
		array( 'name' => 'Iceberg', 'variant' => ':regular' ),
		array( 'name' => 'Iceland', 'variant' => ':regular' ),
		array( 'name' => 'Imprima', 'variant' => ':regular' ),
		array( 'name' => 'Inconsolata', 'variant' => ':regular,:700' ),
		array( 'name' => 'Inder', 'variant' => ':regular' ),
		array( 'name' => 'Indie Flower', 'variant' => ':regular' ),
		array( 'name' => 'Inika', 'variant' => ':regular,:700' ),
		array( 'name' => 'Irish Grover', 'variant' => ':regular' ),
		array( 'name' => 'Istok Web', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Italiana', 'variant' => ':regular' ),
		array( 'name' => 'Italianno', 'variant' => ':regular' ),
		array( 'name' => 'Jacques Francois', 'variant' => ':regular' ),
		array( 'name' => 'Jacques Francois Shadow', 'variant' => ':regular' ),
		array( 'name' => 'Jim Nightshade', 'variant' => ':regular' ),
		array( 'name' => 'Jockey One', 'variant' => ':regular' ),
		array( 'name' => 'Jolly Lodger', 'variant' => ':regular' ),
		array( 'name' => 'Josefin Sans', 'variant' => ':100,:100italic,:300,:300italic,:regular,:italic,:600,:600italic,:700,:700italic' ),
		array( 'name' => 'Josefin Slab', 'variant' => ':100,:100italic,:300,:300italic,:regular,:italic,:600,:600italic,:700,:700italic' ),
		array( 'name' => 'Joti One', 'variant' => ':regular' ),
		array( 'name' => 'Judson', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Julee', 'variant' => ':regular' ),
		array( 'name' => 'Julius Sans One', 'variant' => ':regular' ),
		array( 'name' => 'Junge', 'variant' => ':regular' ),
		array( 'name' => 'Jura', 'variant' => ':300,:regular,:500,:600' ),
		array( 'name' => 'Just Another Hand', 'variant' => ':regular' ),
		array( 'name' => 'Just Me Again Down Here', 'variant' => ':regular' ),
		array( 'name' => 'Kameron', 'variant' => ':regular,:700' ),
		array( 'name' => 'Kantumruy', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Karla', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Kaushan Script', 'variant' => ':regular' ),
		array( 'name' => 'Kavoon', 'variant' => ':regular' ),
		array( 'name' => 'Kdam Thmor', 'variant' => ':regular' ),
		array( 'name' => 'Keania One', 'variant' => ':regular' ),
		array( 'name' => 'Kelly Slab', 'variant' => ':regular' ),
		array( 'name' => 'Kenia', 'variant' => ':regular' ),
		array( 'name' => 'Khmer', 'variant' => ':regular' ),
		array( 'name' => 'Kite One', 'variant' => ':regular' ),
		array( 'name' => 'Knewave', 'variant' => ':regular' ),
		array( 'name' => 'Kotta One', 'variant' => ':regular' ),
		array( 'name' => 'Koulen', 'variant' => ':regular' ),
		array( 'name' => 'Kranky', 'variant' => ':regular' ),
		array( 'name' => 'Kreon', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Kristi', 'variant' => ':regular' ),
		array( 'name' => 'Krona One', 'variant' => ':regular' ),
		array( 'name' => 'La Belle Aurore', 'variant' => ':regular' ),
		array( 'name' => 'Lancelot', 'variant' => ':regular' ),
		array( 'name' => 'Lato', 'variant' => ':100,:100italic,:300,:300italic,:regular,:italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'League Script', 'variant' => ':regular' ),
		array( 'name' => 'Leckerli One', 'variant' => ':regular' ),
		array( 'name' => 'Ledger', 'variant' => ':regular' ),
		array( 'name' => 'Lekton', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Lemon', 'variant' => ':regular' ),
		array( 'name' => 'Libre Baskerville', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Life Savers', 'variant' => ':regular,:700' ),
		array( 'name' => 'Lilita One', 'variant' => ':regular' ),
		array( 'name' => 'Lily Script One', 'variant' => ':regular' ),
		array( 'name' => 'Limelight', 'variant' => ':regular' ),
		array( 'name' => 'Linden Hill', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Lobster', 'variant' => ':regular' ),
		array( 'name' => 'Lobster Two', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Londrina Outline', 'variant' => ':regular' ),
		array( 'name' => 'Londrina Shadow', 'variant' => ':regular' ),
		array( 'name' => 'Londrina Sketch', 'variant' => ':regular' ),
		array( 'name' => 'Londrina Solid', 'variant' => ':regular' ),
		array( 'name' => 'Lora', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Love Ya Like A Sister', 'variant' => ':regular' ),
		array( 'name' => 'Loved by the King', 'variant' => ':regular' ),
		array( 'name' => 'Lovers Quarrel', 'variant' => ':regular' ),
		array( 'name' => 'Luckiest Guy', 'variant' => ':regular' ),
		array( 'name' => 'Lusitana', 'variant' => ':regular,:700' ),
		array( 'name' => 'Lustria', 'variant' => ':regular' ),
		array( 'name' => 'Macondo', 'variant' => ':regular' ),
		array( 'name' => 'Macondo Swash Caps', 'variant' => ':regular' ),
		array( 'name' => 'Magra', 'variant' => ':regular,:700' ),
		array( 'name' => 'Maiden Orange', 'variant' => ':regular' ),
		array( 'name' => 'Mako', 'variant' => ':regular' ),
		array( 'name' => 'Marcellus', 'variant' => ':regular' ),
		array( 'name' => 'Marcellus SC', 'variant' => ':regular' ),
		array( 'name' => 'Marck Script', 'variant' => ':regular' ),
		array( 'name' => 'Margarine', 'variant' => ':regular' ),
		array( 'name' => 'Marko One', 'variant' => ':regular' ),
		array( 'name' => 'Marmelad', 'variant' => ':regular' ),
		array( 'name' => 'Marvel', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Mate', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Mate SC', 'variant' => ':regular' ),
		array( 'name' => 'Maven Pro', 'variant' => ':regular,:500,:700,:900' ),
		array( 'name' => 'McLaren', 'variant' => ':regular' ),
		array( 'name' => 'Meddon', 'variant' => ':regular' ),
		array( 'name' => 'MedievalSharp', 'variant' => ':regular' ),
		array( 'name' => 'Medula One', 'variant' => ':regular' ),
		array( 'name' => 'Megrim', 'variant' => ':regular' ),
		array( 'name' => 'Meie Script', 'variant' => ':regular' ),
		array( 'name' => 'Merienda', 'variant' => ':regular,:700' ),
		array( 'name' => 'Merienda One', 'variant' => ':regular' ),
		array( 'name' => 'Merriweather', 'variant' => ':300,:300italic,:regular,:italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Merriweather Sans', 'variant' => ':300,:300italic,:regular,:italic,:700,:700italic,:800,:800italic' ),
		array( 'name' => 'Metal', 'variant' => ':regular' ),
		array( 'name' => 'Metal Mania', 'variant' => ':regular' ),
		array( 'name' => 'Metamorphous', 'variant' => ':regular' ),
		array( 'name' => 'Metrophobic', 'variant' => ':regular' ),
		array( 'name' => 'Michroma', 'variant' => ':regular' ),
		array( 'name' => 'Milonga', 'variant' => ':regular' ),
		array( 'name' => 'Miltonian', 'variant' => ':regular' ),
		array( 'name' => 'Miltonian Tattoo', 'variant' => ':regular' ),
		array( 'name' => 'Miniver', 'variant' => ':regular' ),
		array( 'name' => 'Miss Fajardose', 'variant' => ':regular' ),
		array( 'name' => 'Modern Antiqua', 'variant' => ':regular' ),
		array( 'name' => 'Molengo', 'variant' => ':regular' ),
		array( 'name' => 'Molle', 'variant' => ':italic' ),
		array( 'name' => 'Monda', 'variant' => ':regular,:700' ),
		array( 'name' => 'Monofett', 'variant' => ':regular' ),
		array( 'name' => 'Monoton', 'variant' => ':regular' ),
		array( 'name' => 'Monsieur La Doulaise', 'variant' => ':regular' ),
		array( 'name' => 'Montaga', 'variant' => ':regular' ),
		array( 'name' => 'Montez', 'variant' => ':regular' ),
		array( 'name' => 'Montserrat', 'variant' => ':regular,:700' ),
		array( 'name' => 'Montserrat Alternates', 'variant' => ':regular,:700' ),
		array( 'name' => 'Montserrat Subrayada', 'variant' => ':regular,:700' ),
		array( 'name' => 'Moul', 'variant' => ':regular' ),
		array( 'name' => 'Moulpali', 'variant' => ':regular' ),
		array( 'name' => 'Mountains of Christmas', 'variant' => ':regular,:700' ),
		array( 'name' => 'Mouse Memoirs', 'variant' => ':regular' ),
		array( 'name' => 'Mr Bedfort', 'variant' => ':regular' ),
		array( 'name' => 'Mr Dafoe', 'variant' => ':regular' ),
		array( 'name' => 'Mr De Haviland', 'variant' => ':regular' ),
		array( 'name' => 'Mrs Saint Delafield', 'variant' => ':regular' ),
		array( 'name' => 'Mrs Sheppards', 'variant' => ':regular' ),
		array( 'name' => 'Muli', 'variant' => ':300,:300italic,:regular,:italic' ),
		array( 'name' => 'Mystery Quest', 'variant' => ':regular' ),
		array( 'name' => 'Neucha', 'variant' => ':regular' ),
		array( 'name' => 'Neuton', 'variant' => ':200,:300,:regular,:italic,:700,:800' ),
		array( 'name' => 'New Rocker', 'variant' => ':regular' ),
		array( 'name' => 'News Cycle', 'variant' => ':regular,:700' ),
		array( 'name' => 'Niconne', 'variant' => ':regular' ),
		array( 'name' => 'Nixie One', 'variant' => ':regular' ),
		array( 'name' => 'Nobile', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Nokora', 'variant' => ':regular,:700' ),
		array( 'name' => 'Norican', 'variant' => ':regular' ),
		array( 'name' => 'Nosifer', 'variant' => ':regular' ),
		array( 'name' => 'Nothing You Could Do', 'variant' => ':regular' ),
		array( 'name' => 'Noticia Text', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Noto Sans', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Noto Serif', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Nova Cut', 'variant' => ':regular' ),
		array( 'name' => 'Nova Flat', 'variant' => ':regular' ),
		array( 'name' => 'Nova Mono', 'variant' => ':regular' ),
		array( 'name' => 'Nova Oval', 'variant' => ':regular' ),
		array( 'name' => 'Nova Round', 'variant' => ':regular' ),
		array( 'name' => 'Nova Script', 'variant' => ':regular' ),
		array( 'name' => 'Nova Slim', 'variant' => ':regular' ),
		array( 'name' => 'Nova Square', 'variant' => ':regular' ),
		array( 'name' => 'Numans', 'variant' => ':regular' ),
		array( 'name' => 'Nunito', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Odor Mean Chey', 'variant' => ':regular' ),
		array( 'name' => 'Offside', 'variant' => ':regular' ),
		array( 'name' => 'Old Standard TT', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Oldenburg', 'variant' => ':regular' ),
		array( 'name' => 'Oleo Script', 'variant' => ':regular,:700' ),
		array( 'name' => 'Oleo Script Swash Caps', 'variant' => ':regular,:700' ),
		array( 'name' => 'Open Sans', 'variant' => ':300,:300italic,:regular,:italic,:600,:600italic,:700,:700italic,:800,:800italic' ),
		array( 'name' => 'Open Sans Condensed', 'variant' => ':300,:300italic,:700' ),
		array( 'name' => 'Oranienbaum', 'variant' => ':regular' ),
		array( 'name' => 'Orbitron', 'variant' => ':regular,:500,:700,:900' ),
		array( 'name' => 'Oregano', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Orienta', 'variant' => ':regular' ),
		array( 'name' => 'Original Surfer', 'variant' => ':regular' ),
		array( 'name' => 'Oswald', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Over the Rainbow', 'variant' => ':regular' ),
		array( 'name' => 'Overlock', 'variant' => ':regular,:italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Overlock SC', 'variant' => ':regular' ),
		array( 'name' => 'Ovo', 'variant' => ':regular' ),
		array( 'name' => 'Oxygen', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Oxygen Mono', 'variant' => ':regular' ),
		array( 'name' => 'PT Mono', 'variant' => ':regular' ),
		array( 'name' => 'PT Sans', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'PT Sans Caption', 'variant' => ':regular,:700' ),
		array( 'name' => 'PT Sans Narrow', 'variant' => ':regular,:700' ),
		array( 'name' => 'PT Serif', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'PT Serif Caption', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Pacifico', 'variant' => ':regular' ),
		array( 'name' => 'Paprika', 'variant' => ':regular' ),
		array( 'name' => 'Parisienne', 'variant' => ':regular' ),
		array( 'name' => 'Passero One', 'variant' => ':regular' ),
		array( 'name' => 'Passion One', 'variant' => ':regular,:700,:900' ),
		array( 'name' => 'Pathway Gothic One', 'variant' => ':regular' ),
		array( 'name' => 'Patrick Hand', 'variant' => ':regular' ),
		array( 'name' => 'Patrick Hand SC', 'variant' => ':regular' ),
		array( 'name' => 'Patua One', 'variant' => ':regular' ),
		array( 'name' => 'Paytone One', 'variant' => ':regular' ),
		array( 'name' => 'Peralta', 'variant' => ':regular' ),
		array( 'name' => 'Permanent Marker', 'variant' => ':regular' ),
		array( 'name' => 'Petit Formal Script', 'variant' => ':regular' ),
		array( 'name' => 'Petrona', 'variant' => ':regular' ),
		array( 'name' => 'Philosopher', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Piedra', 'variant' => ':regular' ),
		array( 'name' => 'Pinyon Script', 'variant' => ':regular' ),
		array( 'name' => 'Pirata One', 'variant' => ':regular' ),
		array( 'name' => 'Plaster', 'variant' => ':regular' ),
		array( 'name' => 'Play', 'variant' => ':regular,:700' ),
		array( 'name' => 'Playball', 'variant' => ':regular' ),
		array( 'name' => 'Playfair Display', 'variant' => ':regular,:italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Playfair Display SC', 'variant' => ':regular,:italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Podkova', 'variant' => ':regular,:700' ),
		array( 'name' => 'Poiret One', 'variant' => ':regular' ),
		array( 'name' => 'Poller One', 'variant' => ':regular' ),
		array( 'name' => 'Poly', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Pompiere', 'variant' => ':regular' ),
		array( 'name' => 'Pontano Sans', 'variant' => ':regular' ),
		array( 'name' => 'Port Lligat Sans', 'variant' => ':regular' ),
		array( 'name' => 'Port Lligat Slab', 'variant' => ':regular' ),
		array( 'name' => 'Prata', 'variant' => ':regular' ),
		array( 'name' => 'Preahvihear', 'variant' => ':regular' ),
		array( 'name' => 'Press Start 2P', 'variant' => ':regular' ),
		array( 'name' => 'Princess Sofia', 'variant' => ':regular' ),
		array( 'name' => 'Prociono', 'variant' => ':regular' ),
		array( 'name' => 'Prosto One', 'variant' => ':regular' ),
		array( 'name' => 'Puritan', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Purple Purse', 'variant' => ':regular' ),
		array( 'name' => 'Quando', 'variant' => ':regular' ),
		array( 'name' => 'Quantico', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Quattrocento', 'variant' => ':regular,:700' ),
		array( 'name' => 'Quattrocento Sans', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Questrial', 'variant' => ':regular' ),
		array( 'name' => 'Quicksand', 'variant' => ':300,:regular,:700' ),
		array( 'name' => 'Quintessential', 'variant' => ':regular' ),
		array( 'name' => 'Qwigley', 'variant' => ':regular' ),
		array( 'name' => 'Racing Sans One', 'variant' => ':regular' ),
		array( 'name' => 'Radley', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Raleway', 'variant' => ':100,:200,:300,:regular,:500,:600,:700,:800,:900' ),
		array( 'name' => 'Raleway Dots', 'variant' => ':regular' ),
		array( 'name' => 'Rambla', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Rammetto One', 'variant' => ':regular' ),
		array( 'name' => 'Ranchers', 'variant' => ':regular' ),
		array( 'name' => 'Rancho', 'variant' => ':regular' ),
		array( 'name' => 'Rationale', 'variant' => ':regular' ),
		array( 'name' => 'Redressed', 'variant' => ':regular' ),
		array( 'name' => 'Reenie Beanie', 'variant' => ':regular' ),
		array( 'name' => 'Revalia', 'variant' => ':regular' ),
		array( 'name' => 'Ribeye', 'variant' => ':regular' ),
		array( 'name' => 'Ribeye Marrow', 'variant' => ':regular' ),
		array( 'name' => 'Righteous', 'variant' => ':regular' ),
		array( 'name' => 'Risque', 'variant' => ':regular' ),
		array( 'name' => 'Roboto', 'variant' => ':100,:100italic,:300,:300italic,:regular,:italic,:500,:500italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Roboto Condensed', 'variant' => ':300,:300italic,:regular,:italic,:700,:700italic' ),
		array( 'name' => 'Roboto Slab', 'variant' => ':100,:300,:regular,:700' ),
		array( 'name' => 'Rochester', 'variant' => ':regular' ),
		array( 'name' => 'Rock Salt', 'variant' => ':regular' ),
		array( 'name' => 'Rokkitt', 'variant' => ':regular,:700' ),
		array( 'name' => 'Romanesco', 'variant' => ':regular' ),
		array( 'name' => 'Ropa Sans', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Rosario', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Rosarivo', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Rouge Script', 'variant' => ':regular' ),
		array( 'name' => 'Ruda', 'variant' => ':regular,:700,:900' ),
		array( 'name' => 'Rufina', 'variant' => ':regular,:700' ),
		array( 'name' => 'Ruge Boogie', 'variant' => ':regular' ),
		array( 'name' => 'Ruluko', 'variant' => ':regular' ),
		array( 'name' => 'Rum Raisin', 'variant' => ':regular' ),
		array( 'name' => 'Ruslan Display', 'variant' => ':regular' ),
		array( 'name' => 'Russo One', 'variant' => ':regular' ),
		array( 'name' => 'Ruthie', 'variant' => ':regular' ),
		array( 'name' => 'Rye', 'variant' => ':regular' ),
		array( 'name' => 'Sacramento', 'variant' => ':regular' ),
		array( 'name' => 'Sail', 'variant' => ':regular' ),
		array( 'name' => 'Salsa', 'variant' => ':regular' ),
		array( 'name' => 'Sanchez', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Sancreek', 'variant' => ':regular' ),
		array( 'name' => 'Sansita One', 'variant' => ':regular' ),
		array( 'name' => 'Sarina', 'variant' => ':regular' ),
		array( 'name' => 'Satisfy', 'variant' => ':regular' ),
		array( 'name' => 'Scada', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Schoolbell', 'variant' => ':regular' ),
		array( 'name' => 'Seaweed Script', 'variant' => ':regular' ),
		array( 'name' => 'Sevillana', 'variant' => ':regular' ),
		array( 'name' => 'Seymour One', 'variant' => ':regular' ),
		array( 'name' => 'Shadows Into Light', 'variant' => ':regular' ),
		array( 'name' => 'Shadows Into Light Two', 'variant' => ':regular' ),
		array( 'name' => 'Shanti', 'variant' => ':regular' ),
		array( 'name' => 'Share', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Share Tech', 'variant' => ':regular' ),
		array( 'name' => 'Share Tech Mono', 'variant' => ':regular' ),
		array( 'name' => 'Shojumaru', 'variant' => ':regular' ),
		array( 'name' => 'Short Stack', 'variant' => ':regular' ),
		array( 'name' => 'Siemreap', 'variant' => ':regular' ),
		array( 'name' => 'Sigmar One', 'variant' => ':regular' ),
		array( 'name' => 'Signika', 'variant' => ':300,:regular,:600,:700' ),
		array( 'name' => 'Signika Negative', 'variant' => ':300,:regular,:600,:700' ),
		array( 'name' => 'Simonetta', 'variant' => ':regular,:italic,:900,:900italic' ),
		array( 'name' => 'Sintony', 'variant' => ':regular,:700' ),
		array( 'name' => 'Sirin Stencil', 'variant' => ':regular' ),
		array( 'name' => 'Six Caps', 'variant' => ':regular' ),
		array( 'name' => 'Skranji', 'variant' => ':regular,:700' ),
		array( 'name' => 'Slackey', 'variant' => ':regular' ),
		array( 'name' => 'Smokum', 'variant' => ':regular' ),
		array( 'name' => 'Smythe', 'variant' => ':regular' ),
		array( 'name' => 'Sniglet', 'variant' => ':regular,:800' ),
		array( 'name' => 'Snippet', 'variant' => ':regular' ),
		array( 'name' => 'Snowburst One', 'variant' => ':regular' ),
		array( 'name' => 'Sofadi One', 'variant' => ':regular' ),
		array( 'name' => 'Sofia', 'variant' => ':regular' ),
		array( 'name' => 'Sonsie One', 'variant' => ':regular' ),
		array( 'name' => 'Sorts Mill Goudy', 'variant' => ':regular,:italic' ),
		array( 'name' => 'Source Code Pro', 'variant' => ':200,:300,:regular,:500,:600,:700,:900' ),
		array( 'name' => 'Source Sans Pro', 'variant' => ':200,:200italic,:300,:300italic,:regular,:italic,:600,:600italic,:700,:700italic,:900,:900italic' ),
		array( 'name' => 'Special Elite', 'variant' => ':regular' ),
		array( 'name' => 'Spicy Rice', 'variant' => ':regular' ),
		array( 'name' => 'Spinnaker', 'variant' => ':regular' ),
		array( 'name' => 'Spirax', 'variant' => ':regular' ),
		array( 'name' => 'Squada One', 'variant' => ':regular' ),
		array( 'name' => 'Stalemate', 'variant' => ':regular' ),
		array( 'name' => 'Stalinist One', 'variant' => ':regular' ),
		array( 'name' => 'Stardos Stencil', 'variant' => ':regular,:700' ),
		array( 'name' => 'Stint Ultra Condensed', 'variant' => ':regular' ),
		array( 'name' => 'Stint Ultra Expanded', 'variant' => ':regular' ),
		array( 'name' => 'Stoke', 'variant' => ':300,:regular' ),
		array( 'name' => 'Strait', 'variant' => ':regular' ),
		array( 'name' => 'Sue Ellen Francisco', 'variant' => ':regular' ),
		array( 'name' => 'Sunshiney', 'variant' => ':regular' ),
		array( 'name' => 'Supermercado One', 'variant' => ':regular' ),
		array( 'name' => 'Suwannaphum', 'variant' => ':regular' ),
		array( 'name' => 'Swanky and Moo Moo', 'variant' => ':regular' ),
		array( 'name' => 'Syncopate', 'variant' => ':regular,:700' ),
		array( 'name' => 'Tangerine', 'variant' => ':regular,:700' ),
		array( 'name' => 'Taprom', 'variant' => ':regular' ),
		array( 'name' => 'Tauri', 'variant' => ':regular' ),
		array( 'name' => 'Telex', 'variant' => ':regular' ),
		array( 'name' => 'Tenor Sans', 'variant' => ':regular' ),
		array( 'name' => 'Text Me One', 'variant' => ':regular' ),
		array( 'name' => 'The Girl Next Door', 'variant' => ':regular' ),
		array( 'name' => 'Tienne', 'variant' => ':regular,:700,:900' ),
		array( 'name' => 'Tinos', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Titan One', 'variant' => ':regular' ),
		array( 'name' => 'Titillium Web', 'variant' => ':200,:200italic,:300,:300italic,:regular,:italic,:600,:600italic,:700,:700italic,:900' ),
		array( 'name' => 'Trade Winds', 'variant' => ':regular' ),
		array( 'name' => 'Trocchi', 'variant' => ':regular' ),
		array( 'name' => 'Trochut', 'variant' => ':regular,:italic,:700' ),
		array( 'name' => 'Trykker', 'variant' => ':regular' ),
		array( 'name' => 'Tulpen One', 'variant' => ':regular' ),
		array( 'name' => 'Ubuntu', 'variant' => ':300,:300italic,:regular,:italic,:500,:500italic,:700,:700italic' ),
		array( 'name' => 'Ubuntu Condensed', 'variant' => ':regular' ),
		array( 'name' => 'Ubuntu Mono', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Ultra', 'variant' => ':regular' ),
		array( 'name' => 'Uncial Antiqua', 'variant' => ':regular' ),
		array( 'name' => 'Underdog', 'variant' => ':regular' ),
		array( 'name' => 'Unica One', 'variant' => ':regular' ),
		array( 'name' => 'UnifrakturCook', 'variant' => ':700' ),
		array( 'name' => 'UnifrakturMaguntia', 'variant' => ':regular' ),
		array( 'name' => 'Unkempt', 'variant' => ':regular,:700' ),
		array( 'name' => 'Unlock', 'variant' => ':regular' ),
		array( 'name' => 'Unna', 'variant' => ':regular' ),
		array( 'name' => 'VT323', 'variant' => ':regular' ),
		array( 'name' => 'Vampiro One', 'variant' => ':regular' ),
		array( 'name' => 'Varela', 'variant' => ':regular' ),
		array( 'name' => 'Varela Round', 'variant' => ':regular' ),
		array( 'name' => 'Vast Shadow', 'variant' => ':regular' ),
		array( 'name' => 'Vibur', 'variant' => ':regular' ),
		array( 'name' => 'Vidaloka', 'variant' => ':regular' ),
		array( 'name' => 'Viga', 'variant' => ':regular' ),
		array( 'name' => 'Voces', 'variant' => ':regular' ),
		array( 'name' => 'Volkhov', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Vollkorn', 'variant' => ':regular,:italic,:700,:700italic' ),
		array( 'name' => 'Voltaire', 'variant' => ':regular' ),
		array( 'name' => 'Waiting for the Sunrise', 'variant' => ':regular' ),
		array( 'name' => 'Wallpoet', 'variant' => ':regular' ),
		array( 'name' => 'Walter Turncoat', 'variant' => ':regular' ),
		array( 'name' => 'Warnes', 'variant' => ':regular' ),
		array( 'name' => 'Wellfleet', 'variant' => ':regular' ),
		array( 'name' => 'Wendy One', 'variant' => ':regular' ),
		array( 'name' => 'Wire One', 'variant' => ':regular' ),
		array( 'name' => 'Yanone Kaffeesatz', 'variant' => ':200,:300,:regular,:700' ),
		array( 'name' => 'Yellowtail', 'variant' => ':regular' ),
		array( 'name' => 'Yeseva One', 'variant' => ':regular' ),
		array( 'name' => 'Yesteryear', 'variant' => ':regular' ),
		array( 'name' => 'Zeyada', 'variant' => ':regular' )
	);

	return $google_fonts;
} // End sf_get_google_fonts_store()

/*-----------------------------------------------------------------------------------*/
/* Google Webfonts Stylesheet Generator */
/*-----------------------------------------------------------------------------------*/
/*
INSTRUCTIONS: Needs to be loaded for the Google Fonts options to work for font options. Add this to
the specific themes /app/frontend/actions.php or functions.php: add_action( 'wp_head', 'sf_google_webfonts' );
*/

if ( ! function_exists( 'sf_google_webfonts' ) ) {
	function sf_google_webfonts() {
		global $google_fonts;
		$fonts_to_load = array();
		$output = '';

		// Setup Options array
		global $sf_options;

		// Go through the options
		if ( ! empty( $sf_options ) && ! empty( $google_fonts ) ) {
			foreach ( $sf_options as $option ) {
				// Check if option has "face" in array
				if ( is_array( $option ) && isset( $option['face'] ) ) {
					// Go through the google font array
					foreach ( $google_fonts as $font ) {
						// Check if the google font name exists in the current "face" option
						if ( $option['face'] == $font['name'] && ! in_array( $font['name'], array_keys( $fonts_to_load ) ) ) {
							// Add google font to output
							$variant = '';
							if ( isset( $font['variant'] ) ) $variant = $font['variant'];
							$fonts_to_load[$font['name']] = $variant;
						}
					}
				}
			}

			// Output google font css in header
			if ( 0 < count( $fonts_to_load ) ) {
				$fonts_and_variants = array();
				foreach ( $fonts_to_load as $k => $v ) {
					$fonts_and_variants[] = $k . $v;
				}
				$fonts_and_variants = array_map( 'urlencode', $fonts_and_variants );
				$fonts = join( '%7C', $fonts_and_variants );

				$output .= "\n<!-- Google Webfonts -->\n";
				$output .= '<link href="http'. ( is_ssl() ? 's' : '' ) .'://fonts.googleapis.com/css?family=' . $fonts .'" rel="stylesheet" type="text/css" />'."\n";

				echo $output;
			}
		}
	} // End sf_google_webfonts()
}


/*-----------------------------------------------------------------------------------*/
/* Enable Home link in WP Menus
/*-----------------------------------------------------------------------------------*/
if ( !function_exists( 'sf_home_page_menu_args' ) ) {
	function sf_home_page_menu_args( $args ) {
		$args['show_home'] = true;
		return $args;
	} // End sf_home_page_menu_args()
	add_filter( 'wp_page_menu_args', 'sf_home_page_menu_args' );
}

/*---------------------------------------------------------------------------------*/
/* Detects the Charset of String and Converts it to UTF-8 */
/*---------------------------------------------------------------------------------*/
if ( !function_exists( 'sf_encoding_convert') ) {
	function sf_encoding_convert($str_to_convert) {
		if ( function_exists( 'mb_detect_encoding') ) {
			$str_lang_encoding = mb_detect_encoding($str_to_convert);
			//if no encoding detected, assume UTF-8
			if (!$str_lang_encoding) {
				//UTF-8 assumed
				$str_lang_converted_utf = $str_to_convert;
			} else {
				//Convert to UTF-8
				$str_lang_converted_utf = mb_convert_encoding($str_to_convert, 'UTF-8', $str_lang_encoding);
			}
		} else {
			$str_lang_converted_utf = $str_to_convert;
		}

		return $str_lang_converted_utf;
	}
}

/*---------------------------------------------------------------------------------*/
/* WP Login logo */
/*---------------------------------------------------------------------------------*/
if ( !function_exists( 'sf_custom_login_logo' ) ) {
	function sf_custom_login_logo() {
		$logo = get_option( 'sf_custom_login_logo' );
	    $dimensions = @getimagesize( $logo );
	    $background_size = 'background-size: auto;';
	    if ( 0 >= $dimensions[1] ) {
	    	$dimensions[1] = '67';
	    	$background_size = '';
	    }

		echo '<style type="text/css">body #login h1 a { background-image:url( ' . esc_url( $logo ) . ' ); height: ' . intval( $dimensions[1] ) . 'px; width: auto; ' . $background_size . ' }</style>';
	} // End sf_custom_login_logo()
	if ( '' != get_option( 'sf_custom_login_logo') ) {
		add_action( 'login_head', 'sf_custom_login_logo' );
	}
}

/*---------------------------------------------------------------------------------*/
/* WP Login logo URL */
/*---------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_custom_login_logo_url' ) ) {
	function sf_custom_login_logo_url( $text ) {
		return get_option( 'sf_custom_login_logo_url' ); // Escaping via esc_url() is done in wp-login.php.
	} // End sf_custom_login_logo_url()

	if ( '' != get_option( 'sf_custom_login_logo_url' ) ) {
		add_filter( 'login_headerurl', 'sf_custom_login_logo_url', 10 );
	}
}

/*---------------------------------------------------------------------------------*/
/* WP Login logo title */
/*---------------------------------------------------------------------------------*/
if ( ! function_exists( 'sf_custom_login_logo_title' ) ) {
	function sf_custom_login_logo_title( $text ) {
		return get_option( 'sf_custom_login_logo_title' ); // Escaping via esc_attr() is done in wp-login.php.
	} // End sf_custom_login_logo_title()

	if ( '' != get_option( 'sf_custom_login_logo_title' ) ) {
		add_filter( 'login_headertitle', 'sf_custom_login_logo_title', 10 );
	}
}

/*-----------------------------------------------------------------------------------*/
/* sf_pagination() - Custom loop pagination function  */
/*-----------------------------------------------------------------------------------*/
/*
/* Additional documentation: http://codex.wordpress.org/Function_Reference/paginate_links
/*
/* Params:
/*
/* Arguments Array:
/*
/* 'base' (optional) 				- The query argument on which to determine the pagination (for advanced users)
/* 'format' (optional) 				- The format in which the query argument is formatted in it's raw format (for advanced users)
/* 'total' (optional) 				- The total amount of pages
/* 'current' (optional) 			- The current page number
/* 'prev_next' (optional) 			- Whether to include the previous and next links in the list or not.
/* 'prev_text' (optional) 			- The previous page text. Works only if 'prev_next' argument is set to true.
/* 'next_text' (optional) 			- The next page text. Works only if 'prev_next' argument is set to true.
/* 'show_all' (optional) 			- If set to True, then it will show all of the pages instead of a short list of the pages near the current page. By default, the 'show_all' is set to false and controlled by the 'end_size' and 'mid_size' arguments.
/* 'end_size' (optional) 			- How many numbers on either the start and the end list edges.
/* 'mid_size' (optional) 			- How many numbers to either side of current page, but not including current page.
/* 'add_fragment' (optional) 		- An array of query args to add using add_query_arg().
/* 'type' (optional) 				- Controls format of the returned value. Possible values are:
									  'plain' - A string with the links separated by a newline character.
									  'array' - An array of the paginated link list to offer full control of display.
									  'list' - Unordered HTML list.
/* 'before' (optional) 				- The HTML to display before the paginated links.
/* 'after' (optional) 				- The HTML to display after the paginated links.
/* 'echo' (optional) 				- Whether or not to display the paginated links (alternative is to "return").
/* 'use_search_permastruct' (optiona;) - Whether or not to use the "pretty" URL permastruct for search URLs.
/*
/* Query Parameter (optional) 		- Specify a custom query which you'd like to paginate.
/*
/*-----------------------------------------------------------------------------------*/
/**
 * sf_pagination() is used for paginating the various archive pages created by WordPress. This is not
 * to be used on single.php or other single view pages.
 *
 * @since	1.0
 * @uses	paginate_links() Creates a string of paginated links based on the arguments given.
 * @param	array $args Arguments to customize how the page links are output.
 * @param	object $query An optional custom query to paginate.
 */

if ( ! function_exists( 'sf_pagination' ) ) {
	function sf_pagination( $args = array(), $query = '' ) {
		global $wp_rewrite, $wp_query;

		do_action( 'sf_pagination_start' );

		if ( $query ) {

			$wp_query = $query;

		} // End IF Statement

		/* If there's not more than one page, return nothing. */
		if ( 1 >= $wp_query->max_num_pages ) {
			return;
		}

		/* Get the current page. */
		$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );

		/* Get the max number of pages. */
		$max_num_pages = intval( $wp_query->max_num_pages );

		/* Set up some default arguments for the paginate_links() function. */
		$defaults = array(
			'base' => esc_url_raw( add_query_arg( 'paged', '%#%' ) ),
			'format' => '',
			'total' => $max_num_pages,
			'current' => $current,
			'prev_next' => true,
			'prev_text' => __( '&larr; Previous', 'sfwp-locale' ), // Translate in WordPress. This is the default.
			'next_text' => __( 'Next &rarr;', 'sfwp-locale' ), // Translate in WordPress. This is the default.
			'show_all' => false,
			'end_size' => 1,
			'mid_size' => 1,
			'add_fragment' => '',
			'type' => 'plain',
			'before' => '<div class="pagination sf-pagination">', // Begin sf_pagination() arguments.
			'after' => '</div>',
			'echo' => true,
			'use_search_permastruct' => true
		);

		/* Allow themes/plugins to filter the default arguments. */
		$defaults = apply_filters( 'sf_pagination_args_defaults', $defaults );

		/* Add the $base argument to the array if the user is using permalinks. */
		if( $wp_rewrite->using_permalinks() && ! is_search() ) {
			$defaults['base'] = user_trailingslashit( trailingslashit( get_pagenum_link() ) . 'page/%#%' );
		}

		/* Force search links to use raw permastruct for more accurate multi-word searching. */
		if ( is_search() ) {
			$defaults['use_search_permastruct'] = false;
		}

		/* If we're on a search results page, we need to change this up a bit. */
		if ( is_search() ) {
		/* If we're in BuddyPress, or the user has selected to do so, use the default "unpretty" URL structure. */
			if ( class_exists( 'BP_Core_User' ) || $defaults['use_search_permastruct'] == false ) {
				$search_query = get_query_var( 's' );
				$paged = get_query_var( 'paged' );
				$base = add_query_arg( 's', urlencode( $search_query ) );
				$base = add_query_arg( 'paged', '%#%' );
				$defaults['base'] = esc_url_raw( $base );
			} else {
				$search_permastruct = $wp_rewrite->get_search_permastruct();
				if ( ! empty( $search_permastruct ) ) {
					$base = get_search_link();
					$base = add_query_arg( 'paged', '%#%', $base );
					$defaults['base'] = esc_url_raw( $base );
				}
			}
		}

		/* Merge the arguments input with the defaults. */
		$args = wp_parse_args( $args, $defaults );

		/* Allow developers to overwrite the arguments with a filter. */
		$args = apply_filters( 'sf_pagination_args', $args );

		/* Don't allow the user to set this to an array. */
		if ( 'array' == $args['type'] )
			$args['type'] = 'plain';

		/* Make sure raw querystrings are displayed at the end of the URL, if using pretty permalinks. */
		$pattern = '/\?(.*?)\//i';

		preg_match( $pattern, $args['base'], $raw_querystring );

		if( $wp_rewrite->using_permalinks() && $raw_querystring ) {
			$raw_querystring[0] = str_replace( '', '', $raw_querystring[0] );
			@$args['base'] = str_replace( $raw_querystring[0], '', $args['base'] );
			@$args['base'] .= substr( $raw_querystring[0], 0, -1 );
		}

		/* Get the paginated links. */
		$page_links = paginate_links( $args );

		/* Remove 'page/1' from the entire output since it's not needed. */
		$page_links = str_replace( array( '&#038;paged=1\'', '/page/1\'' ), '\'', $page_links );

		/* Wrap the paginated links with the $before and $after elements. */
		$page_links = $args['before'] . $page_links . $args['after'];

		/* Allow devs to completely overwrite the output. */
		$page_links = apply_filters( 'sf_pagination', $page_links );

		do_action( 'sf_pagination_end' );

		/* Return the paginated links for use in themes. */
		if ( $args['echo'] ) {
			echo $page_links;
		} else {
			return $page_links;
		}
	} // End sf_pagination()
} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* sf_breadcrumbs() - Custom breadcrumb generator function  */
/*
/* Params:
/*
/* Arguments Array:
/*
/* 'separator' 			- The character to display between the breadcrumbs.
/* 'before' 			- HTML to display before the breadcrumbs.
/* 'after' 				- HTML to display after the breadcrumbs.
/* 'front_page' 		- Include the front page at the beginning of the breadcrumbs.
/* 'show_home' 			- If $show_home is set and we're not on the front page of the site, link to the home page.
/* 'echo' 				- Specify whether or not to echo the breadcrumbs. Alternative is "return".
/* 'show_posts_page'	- If a static front page is set and there is a posts page, toggle whether or not to display that page's tree.
/*
/*-----------------------------------------------------------------------------------*/
/**
 * The code below is inspired by Justin Tadlock's Hybrid Core.
 *
 * sf_breadcrumbs() shows a breadcrumb for all types of pages.  Themes and plugins can filter $args or input directly.
 * Allow filtering of only the $args using get_the_breadcrumb_args.
 *
 * @since	1.0
 * @param	array $args Mixed arguments for the menu.
 * @return	string Output of the breadcrumb menu.
 */
function sf_breadcrumbs( $args = array() ) {
	global $wp_query, $wp_rewrite;

	/* Create an empty variable for the breadcrumb. */
	$breadcrumb = '';

	/* Create an empty array for the trail. */
	$trail = array();
	$path = '';

	/* Set up the default arguments for the breadcrumb. */
	$defaults = array(
		'separator' => '›',
		'before' => '<span class="breadcrumb-title">' . __( 'You are here:', 'sfwp-locale' ) . '</span>',
		'after' => false,
		'front_page' => true,
		'show_home' => __( 'Home', 'sfwp-locale' ),
		'echo' => true,
		'show_posts_page' => true,
		'show_only_first_taxonomy_tree' => false
	);

	/* Allow singular post views to have a taxonomy's terms prefixing the trail. */
	if ( is_singular() ) {
		$defaults["singular_{$wp_query->post->post_type}_taxonomy"] = false;
	}

	/* Apply filters to the arguments. */
	$args = apply_filters( 'sf_breadcrumbs_args', $args );

	/* Parse the arguments and extract them for easy variable naming. */
	extract( wp_parse_args( $args, $defaults ) );

	/* If $show_home is set and we're not on the front page of the site, link to the home page. */
	if ( !is_front_page() && $show_home )
		$trail[] = '<a href="' . esc_url( home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . esc_html( $show_home ) . '</a>';

	/* If viewing the front page of the site. */
	if ( is_front_page() ) {
		if ( !$front_page )
			$trail = false;
		elseif ( $show_home )
			$trail['trail_end'] = "{$show_home}";
	}

	/* If viewing the "home"/posts page. */
	elseif ( is_home() ) {
		$home_page = get_page( $wp_query->get_queried_object_id() );
		$trail = array_merge( $trail, sf_breadcrumbs_get_parents( $home_page->post_parent, '' ) );
		$trail['trail_end'] = get_the_title( $home_page->ID );
	}

	/* If viewing a singular post (page, attachment, etc.). */
	elseif ( is_singular() ) {

		/* Get singular post variables needed. */
		$post = $wp_query->get_queried_object();
		$post_id = absint( $wp_query->get_queried_object_id() );
		$post_type = $post->post_type;
		$parent = $post->post_parent;
		$post_type_object = get_post_type_object( $post_type );

		/* If an attachment, check if there are any pages in its hierarchy based on the slug. */
		if ( 'attachment' == $post_type ) {
			/* If $front has been set, add it to the $path. */
			if ( ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['slug'] ) )
				$path .= $post_type_object->rewrite['slug'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) && '/' != $path )
				$trail = array_merge( $trail, sf_breadcrumbs_get_parents( '', $path ) );
		}

		/* If there's an archive page, add it to the trail. */
		if ( ! empty( $post_type_object->has_archive ) )
			$trail['post_type_archive_link'] = '<a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . esc_html( $post_type_object->labels->name ) . '</a>';

		/* If the post type path returns nothing and there is a parent, get its parents. */
		if ( empty( $path ) && 0 !== $parent || 'attachment' == $post_type )
			$trail = array_merge( $trail, sf_breadcrumbs_get_parents( $parent, '' ) );

		/* Toggle the display of the posts page on single blog posts. */
		if ( 'post' == $post_type && $show_posts_page == true && 'page' == get_option( 'show_on_front' ) ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page != '' && is_numeric( $posts_page ) ) {
				$trail = array_merge( $trail, sf_breadcrumbs_get_parents( $posts_page, '' ) );
			}
		}

		/* Display terms for specific post type taxonomy if requested. */
		if ( isset( $args["singular_{$post_type}_taxonomy"] ) && $post_type != 'page' ) {
			$raw_terms = get_the_terms( $post_id, $args["singular_{$post_type}_taxonomy"] );

			if ( is_array( $raw_terms ) && 0 < count( $raw_terms ) && ! is_wp_error( $raw_terms ) ) {
				$links = array();
				$count = 0;

				$sorted = $raw_terms;

				$terms_by_ancestor = array();
				foreach ( $raw_terms as $k => $v ) {
					$ancestors = array_reverse( get_ancestors( $v->term_id, $args["singular_{$post_type}_taxonomy"] ) );
					if ( isset( $ancestors[0] ) ) {
						$key = $ancestors[0];
					} else {
						$key = $v->term_id;
					}
					$terms_by_ancestor[$key][$v->term_id] = get_term_by( 'term_id', $v->term_id, $args["singular_{$post_type}_taxonomy"] );
				}

				if ( 0 < count( $terms_by_ancestor ) ) {
					$sorted = array();
					foreach ( $terms_by_ancestor as $k => $v ) {
						if ( 0 < count( $v ) ) {
							foreach ( $v as $i => $j ) {
								$sorted[$i] = $j;
							}
						}
					}
					foreach ( $sorted as $k => $v ) {
						if ( isset( $sorted[$v->parent] ) ) {
							unset( $sorted[$v->parent] );
						}
					}
				}

				foreach ( $sorted as $k => $v ) {
					$count++;
					if ( isset( $args['show_only_first_taxonomy_tree'] ) && true == (bool)$args['show_only_first_taxonomy_tree'] && 1 < $count ) continue; // Display only the first match.
					$parents = sf_get_term_parents( $v->term_id, $args["singular_{$post_type}_taxonomy"], true, '|-|', $v->name, array() );
					if ( $parents != '' && ! is_wp_error( $parents ) ) {
						$parents_arr = explode( '|-|', $parents );
						foreach ( $parents_arr as $p ) {
							if ( $p != '' && ! in_array( $p, $links ) ) { $links[] = $p; }
						}
					}
				}

				if ( 0 < count( $links ) ) {
					foreach ( $links as $k => $v ) {
						$trail[] = $v;
					}
				}
			}
		}

		/* End with the post title. */
		$post_title = get_the_title( $post_id ); // Force the post_id to make sure we get the correct page title.
		if ( !empty( $post_title ) )
			$trail['trail_end'] = $post_title;
	}

	/* If we're viewing any type of archive. */
	elseif ( is_archive() ) {

		/* If viewing a taxonomy term archive. */
		if ( is_tax() || is_category() || is_tag() ) {

			/* Get some taxonomy and term variables. */
			$term = $wp_query->get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );

			/* Get the path to the term archive. Use this to determine if a page is present with it. */
			if ( is_category() )
				$path = get_option( 'category_base' );
			elseif ( is_tag() )
				$path = get_option( 'tag_base' );
			else {
				if ( $taxonomy->rewrite['with_front'] && $wp_rewrite->front )
					$path = trailingslashit( $wp_rewrite->front );
				$path .= $taxonomy->rewrite['slug'];
			}

			/* Get parent pages by path if they exist. */
			if ( $path )
				$trail = array_merge( $trail, sf_breadcrumbs_get_parents( '', $path ) );

			/* If the taxonomy is hierarchical, list its parent terms. */
			if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent )
				$trail = array_merge( $trail, sf_breadcrumbs_get_term_parents( $term->parent, $term->taxonomy ) );

			/* Add the term name to the trail end. */
			$trail['trail_end'] = $term->name;
		}

		/* If viewing a post type archive. */
		elseif ( is_post_type_archive() ) {

			/* Get the post type object. */
			$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

			/* If $front has been set, add it to the $path. */
			if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['archive'] ) )
				$path .= $post_type_object->rewrite['archive'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) && '/' != $path )
				$trail = array_merge( $trail, sf_breadcrumbs_get_parents( '', $path ) );

			/* Add the post type [plural] name to the trail end. */
			$trail['trail_end'] = $post_type_object->labels->name;
		}

		/* If viewing an author archive. */
		elseif ( is_author() ) {
			/* If $front has been set, add it to $path. */
			if ( !empty( $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If an $author_base exists, add it to $path. */
			if ( !empty( $wp_rewrite->author_base ) )
				$path .= $wp_rewrite->author_base;

			/* If $path exists, check for parent pages. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, sf_breadcrumbs_get_parents( '', $path ) );

			/* Add the author's display name to the trail end. */
			$trail['trail_end'] = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
		}

		/* If viewing a time-based archive. */
		elseif ( is_time() ) {
			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g:i a', 'sfwp-locale' ) );

			elseif ( get_query_var( 'minute' ) )
				$trail['trail_end'] = sprintf( __( 'Minute %1$s', 'sfwp-locale' ), get_the_time( __( 'i', 'sfwp-locale' ) ) );

			elseif ( get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g a', 'sfwp-locale' ) );
		}

		/* If viewing a date-based archive. */
		elseif ( is_date() ) {
			/* If $front has been set, check for parent pages. */
			if ( $wp_rewrite->front )
				$trail = array_merge( $trail, sf_breadcrumbs_get_parents( '', $wp_rewrite->front ) );

			if ( is_day() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'sfwp-locale' ) ) . '">' . get_the_time( __( 'Y', 'sfwp-locale' ) ) . '</a>';
				$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F', 'sfwp-locale' ) ) . '">' . get_the_time( __( 'F', 'sfwp-locale' ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'j', 'sfwp-locale' ) );
			}

			elseif ( get_query_var( 'w' ) ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'sfwp-locale' ) ) . '">' . get_the_time( __( 'Y', 'sfwp-locale' ) ) . '</a>';
				$trail['trail_end'] = sprintf( __( 'Week %1$s', 'sfwp-locale' ), get_the_time( esc_attr__( 'W', 'sfwp-locale' ) ) );
			}

			elseif ( is_month() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'sfwp-locale' ) ) . '">' . get_the_time( __( 'Y', 'sfwp-locale' ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'F', 'sfwp-locale' ) );
			}

			elseif ( is_year() ) {
				$trail['trail_end'] = get_the_time( __( 'Y', 'sfwp-locale' ) );
			}
		}
	}

	/* If viewing search results. */
	elseif ( is_search() )
		$trail['trail_end'] = sprintf( __( 'Search results for &quot;%1$s&quot;', 'sfwp-locale' ), esc_attr( get_search_query() ) );

	/* If viewing a 404 error page. */
	elseif ( is_404() )
		$trail['trail_end'] = __( '404 Not Found', 'sfwp-locale' );

	/* Allow child themes/plugins to filter the trail array. */
	$trail = apply_filters( 'sf_breadcrumbs_trail', $trail, $args );

	/* Connect the breadcrumb trail if there are items in the trail. */
	if ( is_array( $trail ) ) {

		/* Open the breadcrumb trail containers. */
		$breadcrumb = '<div class="breadcrumb breadcrumbs sf-breadcrumbs"><div class="breadcrumb-trail">';

		/* If $before was set, wrap it in a container. */
		if ( !empty( $before ) )
			$breadcrumb .= '<span class="trail-before">' . wp_kses_post( $before ) . '</span> ';

		/* Wrap the $trail['trail_end'] value in a container. */
		if ( !empty( $trail['trail_end'] ) )
			$trail['trail_end'] = '<span class="trail-end">' . wp_kses_post( $trail['trail_end'] ) . '</span>';

		/* Format the separator. */
		if ( !empty( $separator ) )
			$separator = '<span class="sep">' . wp_kses_post( $separator ) . '</span>';

		/* Join the individual trail items into a single string. */
		$breadcrumb .= join( " {$separator} ", $trail );

		/* If $after was set, wrap it in a container. */
		if ( !empty( $after ) )
			$breadcrumb .= ' <span class="trail-after">' . wp_kses_post( $after ) . '</span>';

		/* Close the breadcrumb trail containers. */
		$breadcrumb .= '</div></div>';
	}

	/* Allow developers to filter the breadcrumb trail HTML. */
	$breadcrumb = apply_filters( 'sf_breadcrumbs', $breadcrumb );

	/* Output the breadcrumb. */
	if ( $echo )
		echo $breadcrumb;
	else
		return $breadcrumb;
} // End sf_breadcrumbs()

if ( ! function_exists( 'sf_set_default_breadcrumb_taxonomies' ) ) {
/**
 * Cater for post types where we know the taxonomy. These should be done in each plugin, in future.
 * @since	1.0
 * @param	array $args Arguments.
 * @return	array       Arguments.
 */
function sf_set_default_breadcrumb_taxonomies ( $args ) {
	$post_types = get_post_types( array( 'public' => true ) );
	if ( 0 < count( $post_types ) ) {
		foreach ( $post_types as $k => $v ) {
			$all_taxonomies = get_object_taxonomies( $k, 'objects' );
			$taxonomies     = array();

			// Get public taxonomies
			foreach ( $all_taxonomies as $taxonomy ) {
				if ( $taxonomy->public ) {
					$taxonomies[] = $taxonomy->name;
				}
			}

			// Choose the first taxonomy, if one is present.
			if ( $taxonomies ) {
				$post_types[$k] = current( $taxonomies );
			}

			if ( '' != $post_types[$k] && ! isset( $args['singular_' . $k . '_taxonomy'] ) && is_singular() && ( $k == get_post_type() ) ) {
				$args['singular_' . $k . '_taxonomy'] = $post_types[$k];
			}
		}
	}

	return $args;
} // End sf_set_default_breadcrumb_taxonomies()
}
add_filter( 'sf_breadcrumbs_args', 'sf_set_default_breadcrumb_taxonomies' );

if ( ! function_exists( 'sf_maybe_add_shop_page_link' ) ) {
/**
 * If WooCommerce is present, and we've got a post_type_archive_link, replace it with the shop page.
 * @since	1.0
 * @param	array $trail The breadcrumb trail array.
 * @return	array        The modified breadcrumb trail array.
 */
function sf_maybe_add_shop_page_link ( $trail ) {
	if ( is_singular() && 'product' == get_post_type() && function_exists( 'wc_get_page_id' ) ) {
		$permalinks   = get_option( 'woocommerce_permalinks' );
		$shop_page_id = wc_get_page_id( 'shop' );
		$shop_page    = get_post( $shop_page_id );

		// If permalinks contain the shop page in the URI prepend the breadcrumb with shop
		if ( isset( $trail['post_type_archive_link'] ) ) {
			if ( $shop_page_id && $shop_page && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && get_option( 'page_on_front' ) !== $shop_page_id ) {
				$trail['post_type_archive_link'] = '<a href="' . esc_url( get_permalink( $shop_page_id ) ) . '" title="' . esc_attr( $shop_page->post_title ) . '">' . esc_html( $shop_page->post_title ) . '</a>';
			} else {
				if ( true == (bool)apply_filters( 'sf_hide_product_post_type_archive_link', false ) ) {
					unset( $trail['post_type_archive_link'] );
				}
			}
		}
	}
	return $trail;
} // End sf_set_default_breadcrumb_taxonomies()
}
add_filter( 'sf_breadcrumbs_trail', 'sf_maybe_add_shop_page_link' );

/*-----------------------------------------------------------------------------------*/
/* sf_breadcrumbs_get_parents() - Retrieve the parents of the current page/post */
/*-----------------------------------------------------------------------------------*/
/**
 * Gets parent pages of any post type or taxonomy by the ID or Path.  The goal of this function is to create
 * a clear path back to home given what would normally be a "ghost" directory.  If any page matches the given
 * path, it'll be added.  But, it's also just a way to check for a hierarchy with hierarchical post types.
 *
 * @since	1.0
 * @param	int $post_id ID of the post whose parents we want.
 * @param	string $path Path of a potential parent page.
 * @return	array $trail Array of parent page links.
 */
function sf_breadcrumbs_get_parents( $post_id = '', $path = '' ) {
	/* Set up an empty trail array. */
	$trail = array();

	/* If neither a post ID nor path set, return an empty array. */
	if ( empty( $post_id ) && empty( $path ) )
		return $trail;

	/* If the post ID is empty, use the path to get the ID. */
	if ( empty( $post_id ) ) {

		/* Get parent post by the path. */
		$parent_page = get_page_by_path( $path );

		/* ********************************************************************
		Modification: The above line won't get the parent page if
		the post type slug or parent page path is not the full path as required
		by get_page_by_path. By using get_page_with_title, the full parent
		trail can be obtained. This may still be buggy for page names that use
		characters or long concatenated names.
		Author: Byron Rode
		Date: 06 June 2011
		******************************************************************* */

		if( empty( $parent_page ) )
		        // search on page name (single word)
			$parent_page = get_page_by_title ( $path );

		if( empty( $parent_page ) )
			// search on page title (multiple words)
			$parent_page = get_page_by_title ( str_replace( array('-', '_'), ' ', $path ) );

		/* End Modification */

		/* If a parent post is found, set the $post_id variable to it. */
		if ( !empty( $parent_page ) )
			$post_id = $parent_page->ID;
	}

	/* If a post ID and path is set, search for a post by the given path. */
	if ( $post_id == 0 && !empty( $path ) ) {

		/* Separate post names into separate paths by '/'. */
		$path = trim( $path, '/' );
		preg_match_all( "/\/.*?\z/", $path, $matches );

		/* If matches are found for the path. */
		if ( isset( $matches ) ) {

			/* Reverse the array of matches to search for posts in the proper order. */
			$matches = array_reverse( $matches );

			/* Loop through each of the path matches. */
			foreach ( $matches as $match ) {

				/* If a match is found. */
				if ( isset( $match[0] ) ) {

					/* Get the parent post by the given path. */
					$path = str_replace( $match[0], '', $path );
					$parent_page = get_page_by_path( trim( $path, '/' ) );

					/* If a parent post is found, set the $post_id and break out of the loop. */
					if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
						$post_id = $parent_page->ID;
						break;
					}
				}
			}
		}
	}

	/* While there's a post ID, add the post link to the $parents array. */
	while ( $post_id ) {
		/* Get the post by ID. */
		$page = get_page( $post_id );

		/* Add the formatted post link to the array of parents. */
		$parents[]  = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a>';

		/* Set the parent post's parent to the post ID. */
		$post_id = $page->post_parent;
	}

	/* If we have parent posts, reverse the array to put them in the proper order for the trail. */
	if ( isset( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent posts. */
	return $trail;
} // End sf_breadcrumbs_get_parents()

/*-----------------------------------------------------------------------------------*/
/* sf_breadcrumbs_get_term_parents() - Retrieve the parents of the current term */
/*-----------------------------------------------------------------------------------*/
/**
 * Searches for term parents of hierarchical taxonomies.  This function is similar to the WordPress
 * function get_category_parents() but handles any type of taxonomy.
 *
 * @since	1.0
 * @param	int $parent_id The ID of the first parent.
 * @param	object|string $taxonomy The taxonomy of the term whose parents we want.
 * @return	array $trail Array of links to parent terms.
 */
function sf_breadcrumbs_get_term_parents( $parent_id = '', $taxonomy = '' ) {
	/* Set up some default arrays. */
	$trail = array();
	$parents = array();

	/* If no term parent ID or taxonomy is given, return an empty array. */
	if ( empty( $parent_id ) || empty( $taxonomy ) )
		return $trail;

	/* While there is a parent ID, add the parent term link to the $parents array. */
	while ( $parent_id ) {

		/* Get the parent term. */
		$parent = get_term( $parent_id, $taxonomy );

		/* Add the formatted term link to the array of parent terms. */
		$parents[] = '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( $parent->name ) . '">' . $parent->name . '</a>';

		/* Set the parent term's parent as the parent ID. */
		$parent_id = $parent->parent;
	}

	/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
	if ( !empty( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent terms. */
	return $trail;
} // End sf_breadcrumbs_get_term_parents()

/**
 * Retrieve term parents with separator.
 *
 * @param int $id Term ID.
 * @param string $taxonomy.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate terms.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to terms to prevent duplicates.
 * @return string
 */

if ( ! function_exists( 'sf_get_term_parents' ) ) {
function sf_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
	$chain = '';
	$parent = get_term( $id, $taxonomy );
	if ( is_wp_error( $parent ) )
		return $parent;

	if ( $nicename ) {
		$name = $parent->slug;
	} else {
		$name = $parent->name;
	}

	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= sf_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
	}

	if ( $link ) {
		$chain .= '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( sprintf( __( 'View %s', 'sfwp-locale' ), $parent->name ) ) . '">' . esc_html( $parent->name ) . '</a>' . $separator;
	} else {
		$chain .= $name.$separator;
	}
	return $chain;
} // End sf_get_term_parents()
}

/*-----------------------------------------------------------------------------------*/
/* sf_prepare_category_ids_from_option()
 *
 * Setup an array of category IDs, from a given theme option.
 * Attempt to transform category slugs into ID values as well.
 *
 * Params: String $option
 * Return: Array $cats
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_prepare_category_ids_from_option' ) ) {
	function sf_prepare_category_ids_from_option ( $option ) {
		$cats = array();

		$stored_cats = get_option( $option );

		$cats_raw = explode( ',', $stored_cats );

		if ( is_array( $cats_raw ) && ( count( $cats_raw ) > 0 ) ) {
			foreach ( $cats_raw as $k => $v ) {
				$value = trim( $v );

				if ( is_numeric( $value ) ) {
					$cats_raw[$k] = $value;
				} else {
					$cat_obj = get_category_by_slug( $value );
					if ( isset( $cat_obj->term_id ) ) {
						$cats_raw[$k] = $cat_obj->term_id;
					}
				}

				$cats = $cats_raw;
			}
		}

		return $cats;
	} // End sf_prepare_category_ids_from_option()
}

/*-----------------------------------------------------------------------------------*/
/* Move tracking code from footer to header */
/*-----------------------------------------------------------------------------------*/

add_action( 'init', 'sf_move_tracking_code', 20 );

function sf_move_tracking_code () {
	$move_code = get_option( 'sf_move_tracking_code' );

	if ( ! is_admin() && isset( $move_code ) && ( $move_code == 'true' ) ) {
		remove_action( 'wp_footer', 'sf_analytics' );
		add_action( 'wp_head', 'sf_analytics', 10 );
	}
} // End sf_move_tracking_code()


/*-----------------------------------------------------------------------------------*/
/* sf_get_dynamic_value() */
/* Replace values in a provided array with theme options, if available. */
/*
/* $settings array should resemble: $settings = array( 'theme_option_without_sf_' => 'default_value' );
/*
/* @since	1.0 */
/*-----------------------------------------------------------------------------------*/

function sf_get_dynamic_values ( $settings ) {
	$all = SF()->settings->get_all();
	if ( is_array( $all ) &&  0 < count( $all ) ) {
		foreach ( $settings as $k => $v ) {
			$k = str_replace( 'sf_', '', $k ); // Make sure we remove the prefix.
			if ( isset( $all['sf_' . $k] ) ) { $settings[$k] = $all['sf_' . $k]; }
		}
	}

	return (array)apply_filters( 'sf_get_dynamic_values', $settings );
} // End sf_get_dynamic_values()

/*-----------------------------------------------------------------------------------*/
/* If the user has specified a "posts page", load the "Blog" page template there */
/*-----------------------------------------------------------------------------------*/

add_filter( 'template_include', 'sf_load_posts_page_blog_template', 10 );

if ( ! function_exists( 'sf_load_posts_page_blog_template' ) ) {
	function sf_load_posts_page_blog_template ( $template ) {
		if ( 'page' == get_option( 'show_on_front' ) && ( '' != get_option( 'page_for_posts' ) ) && is_home() ) {
			$tpl = locate_template( array( 'template-blog.php' ) );
			if ( $tpl != '' ) { $template = $tpl; }
		}
		return $template;
	} // End sf_load_posts_page_blog_template()
}

/*-----------------------------------------------------------------------------------*/
/* THE END */
/*-----------------------------------------------------------------------------------*/

/**
 * sf_ajax_banner_close function.
 *
 * @access	public
 * @since	1.0
 */
function sf_ajax_banner_close () {
	if( ! current_user_can( 'install_plugins' ) ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'sfwp-locale' ) );

	if( ! check_admin_referer( 'sf_banner_close' ) ) wp_die( __( 'You have taken too long. Please go back and retry.', 'sfwp-locale' ) );

	$banner = ( isset( $_GET['banner'] ) ) ? $_GET['banner'] : '';

	if( ! $banner ) die;

	// Run the update.
	$response = set_user_setting( 'hidebanner' . $banner, '1' );

	$sendback = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
	wp_safe_redirect( esc_url_raw( $sendback ) );
	exit;
} // End toggle_notifications_status()

/*-----------------------------------------------------------------------------------*/
/* Static Front Page Detection Banner */
/*-----------------------------------------------------------------------------------*/

if ( is_admin() && current_user_can( 'manage_options' ) && ( 0 < intval( get_option( 'page_on_front' ) ) ) ) {
	add_action( 'sf_container_inside', 'sf_add_static_front_page_banner' );
}

/**
 * Add a Static Front Page Detection banner on all Options screens.
 * @since	1.0
 * @return	void
 */
function sf_add_static_front_page_banner () {
	if ( get_user_setting( 'hidebannerstaticfrontpage', '0' ) == '1' ) { return; }
	$theme_data = sf_get_theme_version_data();
	$close_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=sf_banner_close&banner=staticfrontpage' ), 'sf_banner_close' );
	$html = '';
	$html .= '<div id="staticfrontpage-banner" class="sf-banner">' . "\n";
	$html .= '<span class="main">' . sprintf( __( 'You have setup a static front page in %1$sSettings > Reading%2$s.  Please set it to show "Your latest posts" if you want to display the default homepage in %3$s.', 'sfwp-locale' ), '<strong><a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">', '</a></strong>', $theme_data['theme_name'], '<strong>', '</strong>' ) . '</span>' . "\n";
	$html .= '<span class="close-banner"><a href="' . $close_url . '">' . __( 'Close', 'sfwp-locale' ) . '</a></span>' . "\n";
	$html .= '</div>' . "\n";

	echo $html;
} // End sf_add_static_front_page_banner()

/**
 * Get the version data for the currently active theme.
 * @since	1.0
 * @return	array [theme_version, theme_name, sf_version, is_child, child_theme_version, child_theme_name]
 */
if ( ! function_exists( 'sf_get_theme_version_data' ) ) {
function sf_get_theme_version_data () {
	$response = array(
					'theme_version' => '',
					'theme_name' => '',
					'sf_version' => get_option( 'sf_version' ),
					'is_child' => is_child_theme(),
					'child_theme_version' => '',
					'child_theme_name' => ''
					);

	if ( function_exists( 'wp_get_theme' ) ) {
		$theme_data = wp_get_theme();
		if ( true == $response['is_child'] ) {
			$response['theme_version'] = $theme_data->parent()->Version;
			$response['theme_name'] = $theme_data->parent()->Name;

			$response['child_theme_version'] = $theme_data->Version;
			$response['child_theme_name'] = $theme_data->Name;
		} else {
			$response['theme_version'] = $theme_data->Version;
			$response['theme_name'] = $theme_data->Name;
		}
	} else {
		$theme_data = wp_get_theme( get_template_directory() . '/style.css' );
		$response['theme_version'] = $theme_data['Version'];
		$response['theme_name'] = $theme_data['Name'];

		if ( true == $response['is_child'] ) {
			$theme_data = wp_get_theme( get_stylesheet_directory() . '/style.css' );
			$response['child_theme_version'] = $theme_data['Version'];
			$response['child_theme_name'] = $theme_data['Name'];
		}
	}

	return $response;
} // End sf_get_theme_version_data()
}

if ( ! function_exists( 'sf_display_theme_version_data' ) ) {
/**
 * Display the version data for the currently active theme.
 * @since	1.0
 * @return	void
 */
function sf_display_theme_version_data ( $echo = true ) {
	$data = sf_get_theme_version_data();
	$html = '';

	// Theme Version
	if ( true == $data['is_child'] ) {
		$html .= '<span class="theme">' . esc_html( $data['child_theme_name'] . ' ' . $data['child_theme_version'] ) . '</span>' . "\n";
		$html .= '<span class="parent-theme">' . esc_html( $data['theme_name'] . ' ' . $data['theme_version'] ) . '</span>' . "\n";
	} else {
		$html .= '<span class="theme">' . esc_html( $data['theme_name'] . ' ' . $data['theme_version'] ) . '</span>' . "\n";
	}

	// Framework Version
	$html .= '<span class="framework">' . esc_html( sprintf( __( 'Framework %s', 'sfwp-locale' ), $data['sf_version'] ) ) . '</span>' . "\n";

	if ( true == $echo ) { echo $html; } else { return $html; }
} // End sf_display_theme_version_data()
}

if ( ! function_exists( 'sf_load_google_fonts' ) ) {
/**
 * Load relevant Google Fonts for use in the "Custom Typography" shortcode.
 * @since	1.0
 * @return	void
 */
function sf_load_google_fonts() {
	global $sf_used_google_fonts;

	if( $sf_used_google_fonts && is_array( $sf_used_google_fonts ) ) {
		$fonts = '';
		$c = 0;
		foreach( $sf_used_google_fonts as $font ) {
			if( $c > 0 ) {
				$fonts .= '|';
			} else {
				++$c;
			}
			$fonts .= $font;
		}

		if( '' != $fonts ) {
			sf_shortcode_typography_loadgooglefonts( $fonts , 'sf-used-google-fonts' );
		}
	}
} // End sf_load_google_fonts()
}
add_action( 'wp_footer', 'sf_load_google_fonts' );

if ( ! function_exists( 'sf_trim_excerpt' ) ) {
/**
 * A spin off of wp_trim_excerpt(), primarily used for additional control when removing the dropcap shortcode from excerpts.
 * @since	1.0
 * @return	void
 */
function sf_trim_excerpt ( $text ) {
	$text = strip_shortcodes( $text );

	/** This filter is documented in wp-includes/post-template.php */
	$text = apply_filters( 'the_content', $text );
	$text = str_replace(']]>', ']]&gt;', $text);

	/**
	 * Filter the number of words in an excerpt.
	 *
	 * @since	1.0
	 * @param	int $number The number of words. Default 55.
	 */
	$excerpt_length = apply_filters( 'excerpt_length', 55 );
	/**
	 * Filter the string in the "more" link displayed after a trimmed excerpt.
	 *
	 * @since	1.0
	 * @param	string $more_string The string shown within the more link.
	 */
	$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
	$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

	return $text;
} // End sf_trim_excerpt()
}

if ( ! function_exists( 'sf_process_old_custom_fields' ) ) {
	/**
	 * sf_process_old_custom_fields converts old custom fields into featured images
	 * @since	1.0
	 * @param	integer $n offset incrementer
	 * @return	integer post id or negative value for fail
	 */
	function sf_process_old_custom_fields( $n = 0 ) {
		$processed_posts = 0;
		// The Query
		$query_args['posts_per_page'] = 5;
		$query_args['offset'] = $n * 5;
		$query_args['post_type'] = array( 'post' );
		$query_args['post_status'] = 'any';
		$the_query = new WP_Query($query_args);
		// Total count of query
		$found_posts = intval( $the_query->found_posts );

		if ($the_query->have_posts()) {
			$count = 0;
			$processed_posts++;
			// Loop
			while ($the_query->have_posts()) {
				$the_query->the_post();

				$post_id = get_the_id();
				$url = get_post_meta( $post_id, 'image', true );
				$desc = "";
				if ( '' != $url ) {
					// Download the custom field
					$image = media_sideload_image($url, $post_id, $desc);
					if ( is_wp_error( $image ) ) {
						// Failure
						$processed_posts = 0;
					} else {
						// Get existing attachments
						$attachments = get_posts( array(
							'post_type' 	=> 'attachment',
							'number_posts' 	=> 1,
							'post_status' 	=> null,
							'post_parent' 	=> $post_id,
							'orderby' 		=> 'post_date',
							'order' 		=> 'DESC',
						) );
						if ( isset( $attachments[0] ) ) {
							$thumbnail_id = intval( $attachments[0]->ID );
							if ( 0 < $thumbnail_id ) {
								// Set Featured Image attachment ID
								update_post_meta( $post_id, '_thumbnail_id', $thumbnail_id );
								// Delete existing older post meta
								delete_post_meta( $post_id, 'image' );
								// Process Completed Successfully
								$processed_posts = $post_id;
							} // End If Statement
						} else {
							// Fail
							$processed_posts = 0;
						} // End If Statement
					} // End If Statement
				} // End If Statement

				// Output processed post title
				echo get_the_id() . ' - "<em>' .  get_the_title( $post_id ) . '</em>" .... processed successfully</br>';

			} // End While Loop
		} // End If Statement
		wp_reset_postdata();

		// Check if end of query has been reached
		if ( $found_posts >= ( ( $n * 5 ) + 5 ) ) {
			return -1;
		} else {
			return $processed_posts;
		} // End If Statement

	} // End sf_process_old_custom_fields()
} // End If Statement


/**
 * Widget Area functions.
 * @since	1.0
 * @return	
 */
if ( ! function_exists( 'sf_sidebar' ) ) {
	function sf_sidebar( $id = 1 ) {
		return dynamic_sidebar( $id );
	} // End sf_sidebar()
}

if ( ! function_exists( 'sf_active_sidebar' ) ) {
	function sf_active_sidebar( $id ) {
		if( is_active_sidebar( $id ) )
			return true;

		return false;
	} // End sf_active_sidebar()
}

?>
