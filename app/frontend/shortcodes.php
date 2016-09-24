<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

	/**
	 * View Full Article
	 *
	 * This function produces a link to view the full article.
	 *
	 * @example <code>[view_full_article]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_view_full_article' ) ) {
		function sf_shortcode_view_full_article ( $atts ) {
			$defaults = array(
				'label' => __( 'Continue Reading', 'sfwp-locale' ),
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$output = sprintf( '<span class="read-more">%1$s<a href="%3$s" title="%4$s">%4$s</a>%2$s</span> ', $atts['before'], $atts['after'], get_permalink( get_the_ID() ), $atts['label'] );
			return apply_filters( 'sf_shortcode_view_full_article', $output, $atts );
		} // End sf_shortcode_view_full_article()
	}
	add_shortcode( 'view_full_article', 'sf_shortcode_view_full_article' );


	/**
	 * Custom Field
	 *
	 * This function produces the value of a specified custom field.
	 *
	 * @example <code>[sf_custom_field name="test"]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_custom_field' ) ) {
		function sf_shortcode_custom_field ( $atts ) {
			$defaults = array(
				'name' => '',
				'before' => '',
				'after' => '',
				'id' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			foreach ( array( 'before', 'after' ) as $k => $v  ) {
				if ( ! empty( $atts[$v] ) ) {
					$atts[$v] = wp_kses_post( $atts[$v] );
				}
			}

			$post_id = get_the_ID();
			if ( is_numeric( $id ) ) { $post_id = $atts['id']; }

			$custom_field = get_post_meta( $post_id, esc_attr( $atts['name'] ), true );

			$output = '';

			if ( $custom_field ) {
				$output = esc_attr( $custom_field );
			}
			return apply_filters('sf_shortcode_custom_field', $output, $atts);
		} // End sf_shortcode_custom_field()
	}
	add_shortcode( 'custom_field', 'sf_shortcode_custom_field' );


	/**
	 * Post Date
	 *
	 * This function produces the date the post in question was published.
	 *
	 * @example <code>[post_date]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_post_date' ) ) {
		function sf_shortcode_post_date ( $atts ) {
			$defaults = array(
				'format' => get_option( 'date_format' ),
				'before' => '',
				'after' => '',
				'label' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$output = sprintf( '<abbr class="date time published updated" title="%5$s">%1$s%3$s%4$s%2$s</abbr> ', $atts['before'], $atts['after'], $atts['label'], get_the_time($atts['format']), get_the_time('Y-m-d\TH:i:sO') );
			return apply_filters( 'sf_shortcode_post_date', $output, $atts );
		} // End sf_shortcode_post_date()
	}
	add_shortcode( 'post_date', 'sf_shortcode_post_date' );


	/**
	 * Post Time
	 *
	 * This function produces the time the post in question was published.
	 *
	 * @example <code>[post_time]</code> is the default usage
	 * @example <code>[post_time format="g:i a" before="<b>" after="</b>"]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_time' ) ) {
		function sf_shortcode_post_time ( $atts ) {
			$defaults = array(
				'format' => get_option( 'time_format' ),
				'before' => '',
				'after' => '',
				'label' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$output = sprintf( '<abbr class="time published" title="%5$s">%1$s%3$s%4$s%2$s</abbr> ', $atts['before'], $atts['after'], $atts['label'], get_the_time($atts['format']), get_the_time('Y-m-d\TH:i:sO') );
			return apply_filters( 'sf_shortcode_post_time', $output, $atts );
		} // End sf_shortcode_post_time()
	}
	add_shortcode( 'post_time', 'sf_shortcode_post_time' );


	/**
	 * Post Author
	 *
	 * This function produces the author of the post (display name)
	 *
	 * @example <code>[post_author]</code> is the default usage
	 * @example <code>[post_author before="<b>" after="</b>"]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_author' ) ) {
		function sf_shortcode_post_author ( $atts ) {
			$defaults = array(
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$output = sprintf('<span class="author vcard">%2$s<span class="fn">%1$s</span>%3$s</span>', esc_html( get_the_author() ), $atts['before'], $atts['after']);
			return apply_filters( 'sf_shortcode_post_author', $output, $atts );
		} // End sf_shortcode_post_author()
	}
	add_shortcode( 'post_author', 'sf_shortcode_post_author' );


	/**
	 * Post Author Link
	 *
	 * This function produces the author of the post (link to author URL)
	 *
	 * @example <code>[post_author_link]</code> is the default usage
	 * @example <code>[post_author_link before="<b>" after="</b>"]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_author_link' ) ) {
		function sf_shortcode_post_author_link ( $atts ) {
			$defaults = array(
				'nofollow' => FALSE,
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$author = get_the_author();

			//	Link?
			if ( '' != get_the_author_meta( 'url' ) ) {
				//	Build the link
				$author = '<a href="' . esc_url( get_the_author_meta( 'url' ) ) . '" title="' . esc_attr( sprintf( __( 'Visit %s&#8217;s website', 'sfwp-locale' ), $author ) ) . '" rel="external">' . esc_html( $author ) . '</a>';
			}

			$output = sprintf('<span class="author vcard">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after']);
			return apply_filters( 'sf_shortcode_post_author_link', $output, $atts );
		} // End sf_shortcode_post_author_link()
	}
	add_shortcode( 'post_author_link', 'sf_shortcode_post_author_link' );


	/**
	 * Post Author Posts Link
	 *
	 * This function produces the display name of the post's author, with a link to their author archive screen.
	 *
	 * @example <code>[post_author_posts_link]</code> is the default usage
	 * @example <code>[post_author_posts_link before="<b>" after="</b>"]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_author_posts_link' ) ) {
		function sf_shortcode_post_author_posts_link ( $atts ) {
			$defaults = array(
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			// Darn you, WordPress!
			ob_start();
			the_author_posts_link();
			$author = ob_get_clean();

			$output = sprintf('<span class="author vcard">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after']);
			return apply_filters( 'sf_shortcode_post_author_posts_link', $output, $atts );
		} // End sf_shortcode_post_author_posts_link()
	}
	add_shortcode( 'post_author_posts_link', 'sf_shortcode_post_author_posts_link' );


	/**
	 * Post Comments
	 *
	 * This function produces the comment link, or a message if comments are closed.
	 *
	 * @example <code>[post_comments]</code> is the default usage
	 * @example <code>[post_comments zero="No Comments" one="1 Comment" more="% Comments"]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_comments' ) ) {
		function sf_shortcode_post_comments ( $atts ) {
			global $post;

			$defaults = array(
				'zero' => '<i class="fa fa-comment"></i> 0',
				'one' => '<i class="fa fa-comment"></i> 1',
				'more' => '<i class="fa fa-comment"></i> %',
				'hide_if_off' => 'enabled',
				'closed_text' => apply_filters( 'sf_post_more_comment_closed_text', __( 'Comments are closed', 'sfwp-locale' ) ),
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			if ( ( get_option( 'sf_comments' ) === 'none' || ! comments_open() ) && $atts['hide_if_off'] === 'enabled' )
				return;

			if ( $post->comment_status == 'open' ) {
				// Darn you, WordPress!
				ob_start();
				comments_number( $atts['zero'], $atts['one'], $atts['more'] );
				$comments = ob_get_clean();
				$comments = sprintf( '<a href="%s">%s</a>', get_comments_link(), $comments );
			} else {
				$comments = $atts['closed_text'];
			}

			$output = sprintf('<span class="post-comments comments">%2$s%1$s%3$s</span>', $comments, $atts['before'], $atts['after']);
			return apply_filters( 'sf_shortcode_post_comments', $output, $atts );
		} // End sf_shortcode_post_comments()
	}
	add_shortcode( 'post_comments', 'sf_shortcode_post_comments' );


	/**
	 * Post Tags
	 *
	 * This function produces a collection of tags for this post, linked to their appropriate archive screens.
	 *
	 * @example <code>[post_tags]</code> is the default usage
	 * @example <code>[post_tags sep=", " before="Tags: " after="bar"]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_tags' ) ) {
		function sf_shortcode_post_tags ( $atts ) {
			$defaults = array(
				'sep' => ', ',
				'before' => __( 'Tags: ', 'sfwp-locale' ),
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$tags = get_the_tag_list( $atts['before'], trim($atts['sep']) . ' ', $atts['after'] );

			if ( !$tags ) return;

			$output = sprintf('<p class="tags"><i class="fa fa-tag"></i> %s</p> ', $tags);
			return apply_filters( 'sf_shortcode_post_tags', $output, $atts );
		} // End sf_shortcode_post_tags()
	}
	add_shortcode( 'post_tags', 'sf_shortcode_post_tags' );


	/**
	 * Post Categories
	 *
	 * This function produces the category link list
	 *
	 * @example <code>[post_categories]</code> is the default usage
	 * @example <code>[post_categories sep=", "]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_categories' ) ) {
		function sf_shortcode_post_categories ( $atts ) {
			$defaults = array(
				'sep' => ', ',
				'before' => '',
				'after' => '',
				'taxonomy' => 'category'
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$terms = get_the_terms( get_the_ID(), esc_html( $atts['taxonomy'] ) );
			$cats = '';

			if ( is_array( $terms ) && 0 < count( $terms ) ) {
				$links_array = array();
				foreach ( $terms as $k => $v ) {
					$term_name = get_term_field( 'name', $v->term_id, $atts['taxonomy'] );
					$links_array[] = '<a href="' . esc_url( get_term_link( $v, $atts['taxonomy'] ) ) . '" title="' . esc_attr( sprintf( __( 'View all items in %s', 'sfwp-locale' ), $term_name ) ) . '">' . esc_html( $term_name ) . '</a>';
				}

				$cats = join( $atts['sep'], $links_array );
			}

			$output = sprintf('<span class="categories">%2$s%1$s%3$s</span> ', $cats, $atts['before'], $atts['after']);
			return apply_filters( 'sf_shortcode_post_categories', $output, $atts );
		} // End sf_shortcode_post_categories()
	}
	add_shortcode( 'post_categories', 'sf_shortcode_post_categories' );


	/**
	 * Post Edit
	 *
	 * This function produces the "edit post" link for logged in users.
	 *
	 * @example <code>[post_edit]</code> is the default usage
	 * @example <code>[post_edit link="Edit", before="<b>" after="</b>"]</code>
	 */
	if ( ! function_exists( 'sf_shortcode_post_edit' ) ) {
		function sf_shortcode_post_edit ( $atts ) {
			$defaults = array(
				'link' => '<i class="fa fa-edit"></i>',
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			// Darn you, WordPress!
			ob_start();
			edit_post_link( $atts['link'], $atts['before'], $atts['after'] ); // if logged in
			$edit = ob_get_clean();

			$output = $edit;
			return apply_filters( 'sf_shortcode_post_edit', $output, $atts );
		} // End sf_shortcode_post_edit()
	}
	add_shortcode( 'post_edit', 'sf_shortcode_post_edit' );


	/**
	 * "Back to Top" Link
	 *
	 * This function produces a "back to top" link, which links to a specified ID on the current page.
	 *
	 * @example <code>[footer_backtotop]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_footer_backtotop' ) ) {
		function sf_shortcode_footer_backtotop ( $atts ) {
			$defaults = array(
				'text' => __( 'Back to top', 'sfwp-locale' ),
				'href' => '#wrapper',
				'nofollow' => true,
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$nofollow = $atts['nofollow'] ? 'rel="nofollow"' : '';

			$output = sprintf( '%s<a href="%s" %s class="backtotop">%s</a>%s', $atts['before'], esc_url( $atts['href'] ), $nofollow, $atts['text'], $atts['after'] );
			return apply_filters( 'sf_shortcode_footer_backtotop', $output, $atts );
		} // End sf_shortcode_footer_backtotop()
	}
	add_shortcode( 'footer_backtotop', 'sf_shortcode_footer_backtotop' );


	/**
	 * Child Theme Link
	 *
	 * This function produces a link to the child theme's URL, if one is specified.
	 *
	 * @example <code>[footer_childtheme_link]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_footer_childtheme_link' ) ) {
		function sf_shortcode_footer_childtheme_link ( $atts ) {
			$defaults = array(
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			if ( is_child_theme() ) {
				$theme_data = wp_get_theme( get_stylesheet_directory() . '/style.css' );
				define( 'CHILD_THEME_URL', $theme_data['URI'] );
				define( 'CHILD_THEME_NAME', $theme_data['Name'] );
			}
			if ( ! isset( $theme_data['URI'] ) ) { return; }

			$output = sprintf( '%s<a href="%s" title="%s">%s</a>%s', $atts['before'], esc_url( $theme_data['URI'] ), esc_attr( $theme_data['Name'] ), esc_html( $theme_data['Name'] ), $atts['after'] );
			return apply_filters( 'sf_shortcode_footer_childtheme_link', $output, $atts );
		} // End sf_shortcode_footer_childtheme_link()
	}
	add_shortcode( 'footer_childtheme_link', 'sf_shortcode_footer_childtheme_link' );


	/**
	 * WordPress Link
	 *
	 * This function produces a link back to WordPress.org.
	 *
	 * @example <code>[footer_wordpress_link]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_footer_wordpress_link' ) ) {
		function sf_shortcode_footer_wordpress_link ( $atts ) {
			$defaults = array(
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$output = sprintf( '%s<a href="%s" target="_blank" title="%s">%s</a>%s', $atts['before'], 'http://wordpress.org/', 'WordPress', 'WordPress', $atts['after'] );
			return apply_filters( 'sf_shortcode_footer_wordpress_link', $output, $atts );
		} // End sf_shortcode_footer_wordpress_link()
	}
	add_shortcode( 'footer_wordpress_link', 'sf_shortcode_footer_wordpress_link' );


	/**
	 * Link (with optional affiliate link)
	 *
	 * This function produces link back to us, with an affiliate link if you've specified one in the Framework.
	 *
	 * @example <code>[footer_sf_link]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_footer_sf_link' ) ) {
		function sf_shortcode_footer_sf_link ( $atts ) {
			global $sf_options;

			$defaults = array(
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$sf_link = 'http://www.starjive.com/';
			if ( isset( $sf_options['sf_footer_aff_link'] ) && '' != $sf_options['sf_footer_aff_link'] ) {
				$sf_link = $sf_options['sf_footer_aff_link'];
			}

			$output = sprintf( '%s<a href="%s" target="_blank" alt="%s" title="%s">%s</a>%s', $atts['before'], esc_url( $sf_link ), 'WordPress Themes & Plugins' , 'WordPress Themes & Plugins', 'STARJIVE STUDIOS', $atts['after'] );
			return apply_filters( 'sf_shortcode_footer_sf_link', $output, $atts );
		} // End sf_shortcode_footer_sf_link()
	}
	add_shortcode( 'footer_sf_link', 'sf_shortcode_footer_sf_link' );


	/**
	 * Login/Logout Link
	 *
	 * This function produces a login or logout link, depending on the user's login status.
	 *
	 * @example <code>[footer_loginout]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_footer_loginout' ) ) {
		function sf_shortcode_footer_loginout ( $atts ) {
			$defaults = array(
				'redirect' => '',
				'before' => '',
				'after' => ''
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			if ( ! is_user_logged_in() )
				$link = '<a href="' . esc_url( wp_login_url($atts['redirect']) ) . '">' . __( 'Log in', 'sfwp-locale' ) . '</a>';
			else
				$link = '<a href="' . esc_url( wp_logout_url($atts['redirect']) ) . '">' . __( 'Log out', 'sfwp-locale' ) . '</a>';


			$output = $atts['before'] . apply_filters('loginout', $link) . $atts['after'];
			return apply_filters( 'sf_shortcode_footer_loginout', $output, $atts );
		} // End sf_shortcode_footer_loginout()
	}
	add_shortcode( 'footer_loginout', 'sf_shortcode_footer_loginout' );


	/**
	 * Copyright Text
	 *
	 * This function produces the default footer copyright text.
	 *
	 * @example <code>[site_copyright]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_site_copyright' ) ) {
		function sf_shortcode_site_copyright ( $atts ) {
			$defaults = array(
				'before' => '<p>',
				'after' => '</p>'
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$output = sprintf( '%1$s%3$s %4$s %5$s %2$s', $atts['before'], $atts['after'], "&copy; " . date( 'Y' ), get_bloginfo( 'name' ) . '.', __( 'All Rights Reserved.', 'sfwp-locale' ) );
			return apply_filters( 'sf_shortcode_site_copyright', $output, $atts );
		} // End sf_shortcode_site_copyright()
	}
	add_shortcode( 'site_copyright', 'sf_shortcode_site_copyright' );


	/**
	 * Credit Text
	 *
	 * This function produces the default footer credit text.
	 *
	 * @example <code>[site_credit]</code> is the default usage
	 */
	if ( ! function_exists( 'sf_shortcode_site_credit' ) ) {
		function sf_shortcode_site_credit ( $atts ) {
			$defaults = array(
				'before' => '<p>',
				'after' => '</p>'
			);
			$atts = shortcode_atts( $defaults, $atts );

			$atts = array_map( 'wp_kses_post', $atts );

			$output = sprintf( '%1$s%3$s %4$s %5$s %6$s%2$s', $atts['before'], $atts['after'], __( 'Powered by', 'sfwp-locale' ), '[footer_wordpress_link]' . '.', __( 'Designed by', 'sfwp-locale' ), '[footer_sf_link]' );
			return do_shortcode( apply_filters( 'sf_shortcode_site_credit', $output, $atts ) );
		} // End sf_shortcode_site_credit()
	}
	add_shortcode( 'site_credit', 'sf_shortcode_site_credit' );

?>