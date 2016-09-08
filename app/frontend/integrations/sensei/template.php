<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sensei template functions
 */

if ( ! function_exists( 'sf_sensei_layout_wrap' ) ) {
	/**
	 * Open Sensei layout wrap
	 * Contains the entire sensei page
	 */
	function sf_sensei_layout_wrap() {
		echo '<div id="content" class="col-full">';
		echo '<div id="main-sidebar-container">';
	}
}

if ( ! function_exists( 'sf_sensei_layout_wrap_end' ) ) {
	/**
	 * Close Sensei layout wrap
	 * Contains the entire sensei page
	 */
	function sf_sensei_layout_wrap_end() {
		echo '</div>';
		get_sidebar( 'alt' );
		echo '</div>';
	}
}

if ( ! function_exists( 'sf_sensei_content_wrap' ) ) {
	/**
	 * Open Sensei content wrap
	 * Contains the sensei content and appends sidebar
	 */
	function sf_sensei_content_wrap() {
		echo '<section id="main">';
	}
}

if ( ! function_exists( 'sf_sensei_content_wrap_end' ) ) {
	/**
	 * Close Sensei content wrap
	 * Contains the sensei content and appends sidebar
	 */
	function sf_sensei_content_wrap_end() {
		echo '</section>';
		get_sidebar();
	}
}

if ( ! function_exists( 'sensei_breadcrumbs' ) ) {
	function sensei_breadcrumbs() {
		global  $sf_options;
		if ( isset( $sf_options['sf_breadcrumbs_show'] ) && 'true' == $sf_options['sf_breadcrumbs_show'] ) {
			sf_breadcrumbs();
		}
	}
}

if ( ! function_exists( 'sf_sensei_pagination' ) ) {
	/**
	 * Woo Sensei Pagination
	 * Replaces the standard Sensei archive pagination with sf_pagination();
	 */
	function sf_sensei_pagination() {
		global $wp_query, $sf_sensei;

		$paged 			= $wp_query->get( 'paged' );
		$course_page_id = intval( $sf_sensei->settings->settings[ 'course_page' ] );

		if ( ( is_post_type_archive( 'course' ) || ( is_page( $course_page_id ) ) ) && ( isset( $paged ) && 0 == $paged ) ) {
			// Silence
		} elseif( is_singular( 'course' ) ) {
			$sf_sensei->frontend->sensei_get_template( 'wrappers/pagination-posts.php' );
		} elseif( is_singular( 'lesson' ) ) {
			$sf_sensei->frontend->sensei_get_template( 'wrappers/pagination-lesson.php' );
		} elseif( is_singular( 'quiz' ) ) {
			$sf_sensei->frontend->sensei_get_template( 'wrappers/pagination-quiz.php' );
		} else {
			sf_pagination();
		}
	}
}