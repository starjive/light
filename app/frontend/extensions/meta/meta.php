<?php
	require_once( 'classes/meta.php' );
	$sf_meta_manager = new Meta_Manager( 'sf_meta_', dirname( __FILE__ ), trailingslashit( get_template_directory_uri() . '/app/frontend/extensions/meta/' ), '1.0.0' );
?>