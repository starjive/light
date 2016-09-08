<?php
	require_once( 'classes/hooks.php' );
	$sf_meta_manager = new Hook_Manager( 'sf_hooks_', dirname( __FILE__ ), trailingslashit( get_template_directory_uri() . '/app/frontend/extensions/hooks/' ), '1.0.0' );
?>