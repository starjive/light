<?php
/**
 * Framework Screen
 *
 * Shows a tools screen.
 *
 * @category	Admin
 * @version		1.0
 * @since		1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SF_Screen_Framework extends SF_Screen_Admin_Base {
	/**
	 * Class constructor.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function __construct () {
		parent::__construct();

		add_action( 'sf_admin_menu_after', array( $this, 'admin_menus' ) );
	} // End __construct()

	/**
	 * Add admin menus/screens.
	 * @access public
	 * @since	1.0
	 * @return	void
	 */
	public function admin_menus () {
		global $current_user;
		$current_user_id = $current_user->user_login;
		$super_user = apply_filters( 'sf_super_user', '' );

		if( $super_user !== $current_user_id && ! empty( $super_user ) ) return;

		// Framework
		$framework = add_submenu_page( 'sf', __( 'Framework', 'sfwp-locale' ), __( 'Framework', 'sfwp-locale' ), 'manage_options', 'sf-framework', array( $this, 'about_screen' ) );

		add_action( 'admin_print_styles-'. $framework, array( $this, 'admin_css' ) );

		add_action( 'load-' . $framework, array( $this, 'maybe_remove_existing_plugin_install_overrides' ) );
	} // End admin_menus()

	/**
	 * The "About" screen.
	 * @access  public
	 * @since	1.0
	 * @return  void
	 */
	public function about_screen () {
		do_action( 'sf_screen_get_header', 'sf-framework', 'tools' );
		$this->_intro();
		$this->_get_started();
		if ( is_multisite() ) {
			$this->_has_supported_plugins_multisite();
		} else {
			$this->_has_supported_plugins();
		}
		do_action( 'sf_screen_get_footer', 'sf-framework', 'tools' );
	} // End about_screen()

	/**
	 * Generic intro header for each of the screens.
	 * @access  private
	 * @since	1.0
	 * @return  void
	 */
	private function _intro () {
		$ct = $this->_theme_obj;
		?>
		<div id="current-theme">
		<h1><?php echo sprintf( __( '%s', 'sfwp-locale' ), '<span class="theme-name">' . $ct->display( 'Name' ) . '</span>' ); ?></h1>
		</div><!--/#current-theme-->
		<?php
	} // End _intro()

	/**
	 * Theme options for the "Getting Started" screen.
	 * @access  private
	 * @since	1.0
	 * @return  void
	 */
	private function _get_started () {
		$this->_theme_meta();
		?>
		<p class="getting-started-buttons">
			<?php if ( current_user_can( 'install_themes' ) ) { ?>
			<a href="<?php echo esc_url( add_query_arg( 'page', 'sf_update', admin_url( 'admin.php' ) ) ); ?>" class="button button-primary"><?php printf( __( 'Update Framework', 'sfwp-locale' ) ); ?> <span class="dashicons dashicons-update"></span></a>
			<?php } ?>
			<a href="<?php echo esc_url( add_query_arg( 'page', 'sf-backup', admin_url( 'admin.php' ) ) ); ?>" class="button button-secondary"><?php printf( __( 'Backup/Import Your Settings', 'sfwp-locale' ) ); ?></a>
			<a href="<?php echo esc_url( get_option( 'sf_manual', 'http://starjive.com/wordpress/themes/light-framework/documentation/' ) ); ?>" class="button button-secondary"><?php printf( __( 'Read the Documentation', 'sfwp-locale' ) ); ?></a>
		</p>
		<?php

	} // End _get_started()

	/**
	 * Display meta data about the theme.
	 * @access  private
	 * @since	1.0
	 * @return  void
	 */
	private function _theme_meta () {
	?>
	<table class="theme-info sf-theme-info wp-list-table widefat">
		<thead>
			<th><?php _e( 'Theme', 'sfwp-locale' ); ?></th>
			<th class="version"><?php _e( 'Version', 'sfwp-locale' ); ?></th>
		</thead>
		<tbody>
			<?php if ( is_child_theme() ) { ?>
			<tr class="odd">
				<td>
					<strong><?php echo $this->_theme_data['child_theme_name']; ?></strong>
				</td>
				<td class="version">
					<?php echo $this->_theme_data['child_theme_version']; ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td>
					<strong><?php echo $this->_theme_data['theme_name']; ?></strong>
				</td>

				<td class="version">
					<?php echo $this->_theme_data['theme_version']; ?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr class="odd">
				<td>
					<strong><?php _e( 'Framework', 'sfwp-locale' ); ?></strong>
				</td>
				<td class="version">
					<?php echo $this->_theme_data['sf_version']; ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php
	} // End _theme_meta()
} // End Class

SF()->screens['framework'] = new SF_Screen_Framework();
?>