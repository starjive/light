<?php
/**
 * Header Template
 *
 * Here we setup all logic and HTML that is required for the header section of all screens.
 *
 * @subpackage Template
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
<title><?php sf_title(); ?></title>
<?php sf_meta(); ?>
<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php endif; ?>
<?php wp_head(); ?>
<?php sf_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php sf_top(); ?>
<div id="wrapper">

	<div id="inner-wrapper">
	
	<?php sf_header_before(); ?>
	<header id="header" class="col-full">
	
		<?php sf_header_inside(); ?>
		
	</header>
	<?php sf_header_after(); ?>