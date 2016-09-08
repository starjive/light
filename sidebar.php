<?php
/**
 * Sidebar Template
 *
 * If a `primary` widget area is active and has widgets, display the sidebar.
 *
 * @subpackage Template
 */

global $sf_options;

$layout = sf_get_layout();

if ( 'one-col' != $layout ) {
	if ( sf_active_sidebar( 'primary' ) ) {
?>

	<?php sf_sidebar_before(); ?>
	<aside id="sidebar">
	
		<?php
			sf_sidebar_inside_before();
			sf_sidebar( 'primary' );
			sf_sidebar_inside_after();
		?>
	
	</aside><!-- /#sidebar -->
	<?php sf_sidebar_after(); ?>

<?php
	} // End IF Statement
} // End IF Statement
?>