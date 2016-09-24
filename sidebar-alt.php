<?php
/**
 * Secondary Sidebar Template
 *
 * If a `secondary` widget area is active and has widgets,
 * and the selected layout has a third column, display the sidebar.
 *
 * @subpackage Template
 */

global $post, $wp_query, $sf_options;

$selected_layout = 'one-col';
$layouts = array( 'three-col-left', 'three-col-middle', 'three-col-right' );
$selected_layout = sf_get_layout();

if ( in_array( $selected_layout, $layouts ) ) {

	if ( sf_active_sidebar( 'secondary' ) ) {
?>

	<?php sf_sidebar_before(); ?>
	<aside id="sidebar-alt">
	
		<?php
			sf_sidebar_inside_before();
			sf_sidebar( 'secondary' );
			sf_sidebar_inside_after();
		?>
		
	</aside><!-- /#sidebar-alt -->
	<?php sf_sidebar_after(); ?>

<?php
	} // End IF Statement
} // End IF Statement
?>