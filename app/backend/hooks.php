<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Hooks
 * All hooks registered by the Framework.
 *
 * @package		WordPress
 * @category	Core
 * @since		1.0
 *
 * TABLE OF CONTENTS
 *
 * - sf_head()
 * - sf_top()
 * - sf_header_before()
 * - sf_header_inside()
 * - sf_header_after()
 * - sf_nav_before()
 * - sf_nav_inside()
 * - sf_nav_after()
 * - sf_content_before()
 * - sf_cotnent_after()
 * - sf_main_before()
 * - sf_main_after()
 * - sf_post_before()
 * - sf_post_after()
 * - sf_post_inside_before()
 * - sf_post_inside_after()
 * - sf_loop_before()
 * - sf_loop_after()
 * - sf_sidebar_before()
 * - sf_sidebar_inside_before()
 * - sf_sidebar_inside_after()
 * - sf_sidebar_after()
 * - sf_footer_top()
 * - sf_footer_before()
 * - sf_footer_inside()
 * - sf_footer_after()
 * - sf_foot()
 *
 * - sf_do_atomic()
 * - sf_apply_atomic()
 * - sf_get_query_context()
 */

// Header
function sf_head() { sf_do_atomic( 'sf_head' ); }
function sf_top() { sf_do_atomic( 'sf_top' ); }
function sf_header_before() { sf_do_atomic( 'sf_header_before' ); }
function sf_header_inside() { sf_do_atomic( 'sf_header_inside' ); }
function sf_header_after() { sf_do_atomic( 'sf_header_after' ); }
function sf_nav_before() { sf_do_atomic( 'sf_nav_before' ); }
function sf_nav_inside() { sf_do_atomic( 'sf_nav_inside' ); }
function sf_nav_after() { sf_do_atomic( 'sf_nav_after' ); }

// Template files: 404, archive, single, page, sidebar, index, search
function sf_content_before() { sf_do_atomic( 'sf_content_before' ); }
function sf_content_after() { sf_do_atomic( 'sf_content_after' ); }
function sf_main_before() { sf_do_atomic( 'sf_main_before' ); }
function sf_main_after() { sf_do_atomic( 'sf_main_after' ); }
function sf_post_before() { sf_do_atomic( 'sf_post_before' ); }
function sf_post_after() { sf_do_atomic( 'sf_post_after' ); }
function sf_post_inside_before() { sf_do_atomic( 'sf_post_inside_before' ); }
function sf_post_inside_after() { sf_do_atomic( 'sf_post_inside_after' ); }
/* change in extensions hook
function sf_page_before() { sf_do_atomic( 'sf_post_before_singular-page' ); }
function sf_page_after() { sf_do_atomic( 'sf_post_after_singular-page' ); }
function sf_page_inside_before() { sf_do_atomic( 'sf_post_inside_before_singular-page' ); }
function sf_page_inside_after() { sf_do_atomic( 'sf_post_inside_after_singular-page' ); }
*/
function sf_page_before() { sf_do_atomic( 'sf_page_before' ); }
function sf_page_after() { sf_do_atomic( 'sf_page_after' ); }
function sf_page_inside_before() { sf_do_atomic( 'sf_page_inside_before' ); }
function sf_page_inside_after() { sf_do_atomic( 'sf_page_inside_after' ); }

function sf_loop_before() { sf_do_atomic( 'sf_loop_before' ); }
function sf_loop_after() { sf_do_atomic( 'sf_loop_after' ); }

// Sidebar
function sf_sidebar_before() { sf_do_atomic( 'sf_sidebar_before' ); }
function sf_sidebar_inside_before() { sf_do_atomic( 'sf_sidebar_inside_before' ); }
function sf_sidebar_inside_after() { sf_do_atomic( 'sf_sidebar_inside_after' ); }
function sf_sidebar_after() { sf_do_atomic( 'sf_sidebar_after' ); }

// Footer
function sf_footer_top() { sf_do_atomic( 'sf_footer_top' ); }
function sf_footer_before() { sf_do_atomic( 'sf_footer_before' ); }
function sf_footer_inside() { sf_do_atomic( 'sf_footer_inside' ); }
function sf_footer_after() { sf_do_atomic( 'sf_footer_after' ); }
function sf_foot() { sf_do_atomic( 'sf_foot' ); }


/**
 * sf_do_atomic()
 *
 * Adds contextual action hooks to the theme.  This allows users to easily add context-based content
 * without having to know how to use WordPress conditional tags. The theme handles the logic.
 *
 * An example of a basic hook would be 'sf_head'. The sf_do_atomic() function extends that to
 * give extra hooks such as 'sf_head_home', 'sf_head_singular', and 'sf_head_singular-page'.
 *
 * Major props to Ptah Dunbar for the do_atomic() function.
 * @link http://ptahdunbar.com/wordpress/smarter-hooks-context-sensitive-hooks
 *
 * @since	1.0
 * @uses sf_get_query_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 */
if ( ! function_exists( 'sf_do_atomic' ) ) {
	function sf_do_atomic( $tag = '', $args = '' ) {
		if ( !$tag ) return false;
		/* Do actions on the basic hook. */
		do_action( $tag, $args );
		/* Loop through context array and fire actions on a contextual scale. */
		foreach ( (array) sf_get_query_context() as $context )
			do_action( "{$tag}_{$context}", $args );
	} // End sf_do_atomic()
}


/**
 * sf_apply_atomic()
 *
 * Adds contextual filter hooks to the theme.  This allows users to easily filter context-based content
 * without having to know how to use WordPress conditional tags. The theme handles the logic.
 *
 * An example of a basic hook would be 'sf_entry_meta'.  The sf_apply_atomic() function extends
 * that to give extra hooks such as 'sf_entry_meta_home', 'sf_entry_meta_singular' and 'sf_entry_meta_singular-page'.
 *
 * @since	1.0
 * @uses	sf_get_query_context() Gets the context of the current page.
 * @param	string $tag Usually the location of the hook but defines what the base hook is.
 * @param	mixed $value The value to be filtered.
 * @return	mixed $value The value after it has been filtered.
 */	
if ( ! function_exists( 'sf_apply_atomic' ) ) {
	function sf_apply_atomic( $tag = '', $value = '' ) {
		if ( ! $tag ) return false;
		/* Get theme prefix. */
		$pre = 'sf';
		/* Apply filters on the basic hook. */
		$value = apply_filters( "{$pre}_{$tag}", $value );
		/* Loop through context array and apply filters on a contextual scale. */
		foreach ( (array)sf_get_query_context() as $context )
			$value = apply_filters( "{$pre}_{$context}_{$tag}", $value );
		/* Return the final value once all filters have been applied. */
		return $value;
	} // End sf_apply_atomic()
}


/**
 * sf_get_query_context()
 *
 * Retrieve the context of the queried template.
 *
 * @since	1.0
 * @return	array $query_context
 */	
if ( ! function_exists( 'sf_get_query_context' ) ) {
	function sf_get_query_context() {
		global $wp_query, $query_context;
	
		/* If $query_context->context has been set, don't run through the conditionals again. Just return the variable. */
		if ( is_object( $query_context ) && isset( $query_context->context ) && is_array( $query_context->context ) ) {
			return $query_context->context;
		}
	
		unset( $query_context );
		$query_context = new stdClass();
		$query_context->context = array();
	
		/* Front page of the site. */
		if ( is_front_page() ) {
			$query_context->context[] = 'home';
		}
	
		/* Blog page. */
		if ( is_home() && ! is_front_page() ) {
			$query_context->context[] = 'blog';
	
		/* Singular views. */
		} elseif ( is_singular() ) {
			$query_context->context[] = 'singular';
			$query_context->context[] = "singular-{$wp_query->post->post_type}";
	
			/* Page Templates. */
			if ( is_page_template() ) {
				$to_skip = array( 'page', 'post' );
	
				$page_template = basename( get_page_template() );
				$page_template = str_replace( '.php', '', $page_template );
				$page_template = str_replace( '.', '-', $page_template );
	
				if ( $page_template && ! in_array( $page_template, $to_skip ) ) {
					$query_context->context[] = $page_template;
				}
			}
	
			$query_context->context[] = "singular-{$wp_query->post->post_type}-{$wp_query->post->ID}";
		}

		/* Archive views. */
		elseif ( is_archive() ) {
			$query_context->context[] = 'archive';
	
			/* Taxonomy archives. */
			if ( is_tax() || is_category() || is_tag() ) {
				$term = $wp_query->get_queried_object();
				$query_context->context[] = 'taxonomy';
				$query_context->context[] = $term->taxonomy;
				$query_context->context[] = "{$term->taxonomy}-" . sanitize_html_class( $term->slug, $term->term_id );
			}
	
			/* User/author archives. */
			elseif ( is_author() ) {
				$query_context->context[] = 'user';
				$query_context->context[] = 'user-' . sanitize_html_class( get_the_author_meta( 'user_nicename', get_query_var( 'author' ) ), $wp_query->get_queried_object_id() );
			}
	
			/* Time/Date archives. */
			else {
				if ( is_date() ) {
					$query_context->context[] = 'date';
					if ( is_year() )
						$query_context->context[] = 'year';
					if ( is_month() )
						$query_context->context[] = 'month';
					if ( get_query_var( 'w' ) )
						$query_context->context[] = 'week';
					if ( is_day() )
						$query_context->context[] = 'day';
				}
				if ( is_time() ) {
					$query_context->context[] = 'time';
					if ( get_query_var( 'hour' ) )
						$query_context->context[] = 'hour';
					if ( get_query_var( 'minute' ) )
						$query_context->context[] = 'minute';
				}
			}
		}

		/* Search results. */
		elseif ( is_search() ) {
			$query_context->context[] = 'search';
		/* Error 404 pages. */
		} elseif ( is_404() ) {
			$query_context->context[] = 'error-404';
		}

	return $query_context->context;
	} // End sf_get_query_context()
}
?>