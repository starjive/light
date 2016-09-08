<div class="wrap" id="sf-filters">
<h2><?php _e( 'Filters', 'sfwp-locale' ); ?></h2>
<?php if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' ) { echo '<div class="updated fade"><p><strong>' . __( 'Filters Settings Updated.', 'sfwp-locale' ) . '</strong></p></div>'; } ?>
<?php echo $this->_generate_sections_menu(); ?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=sf-meta-manager&updated=true' ) ); ?>">
<?php
wp_nonce_field( 'meta-options-update' );
$this->_generate_sections_html();
submit_button();
?>
</form>
</div><!--.wrap #sf-filters-->