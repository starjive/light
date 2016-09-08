<?php
/**
 * Integrates this theme with the Projects by plugin
 * http://wordpress.org/plugins/projects-by-woothemes/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

global $sf_options;

/**
 * Support
 */
add_action( 'after_setup_theme', 'sf_projects_support' );

/**
 * Styles
 * Disable stock Projects css and enqueue our own.
 */
add_filter( 'projects_enqueue_styles', '__return_false' );
add_action( 'wp_enqueue_scripts', 'sf_projects_scripts', 10 );

/**
 * Layout
 * Replace Projects wrappers with our own and filter the body class
 */
remove_action( 'projects_before_main_content', 'projects_output_content_wrapper', 10 );
remove_action( 'projects_after_main_content', 'projects_output_content_wrapper_end', 10 );
add_action( 'projects_before_main_content', 'sf_projects_before_content', 10 );
add_action( 'projects_after_main_content', 'sf_projects_after_content', 20 );

/**
 * Loop
 */
add_filter( 'projects_loop_columns', 'sf_custom_projects_loop_columns' );

/**
 * Single Project Layout
 * Make it look a bit more simple
 */
add_action( 'projects_single_project_summary', 'sf_projects_before_summary', 5 );
add_action( 'projects_single_project_summary', 'sf_projects_after_summary', 30 );
remove_action( 'projects_before_single_project_summary', 'projects_template_single_title', 10 );
add_action( 'projects_single_project_summary', 'sf_projects_template_single_title', 5 );
add_action( 'projects_before_single_project_summary', 'sf_projects_before_media', 5 );
add_action( 'projects_before_single_project_summary', 'sf_projects_after_media', 45 );
remove_action( 'projects_before_single_project_summary', 'projects_template_single_short_description', 20 );
add_action( 'projects_single_project_summary', 'projects_template_single_short_description', 5 );
remove_action( 'projects_after_single_project', 'projects_output_testimonial', 1 );
add_action( 'projects_single_project_summary', 'projects_output_testimonial', 50 );

/**
 * Pagination
 * Replace the WooCommerce pagination function with sf_pagination.
 */
remove_action( 'projects_after_loop', 'projects_pagination', 10 );
add_action( 'projects_after_loop', 'sf_projects_pagination', 10 );

remove_action( 'projects_after_single_project', 'projects_single_pagination', 5 );
add_action( 'projects_after_single_project', 'sf_postnav', 5 );

/**
 * Breadcrumbs
 */
add_action( 'projects_before_main_content', 'sf_projects_breadcrumbs', 15 );

/**
 * Old Layout
 */
add_filter( 'body_class', 'sf_simplify_layout_body_class' );
add_action( 'projects_before_loop', 'sf_projects_category_nav', 20 );
add_action( 'init', 'sf_projects_maybe_remove_description' );