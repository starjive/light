<?php

class Widget_Subscribe extends WP_Widget {

	/*----------------------------------------
	  Constructor.
	  ----------------------------------------

	  * The constructor. Sets up the widget.
	----------------------------------------*/

	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_sf_subscribe', 'description' => __( 'Add a subscribe/connect widget.', 'sfwp-locale' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'sf_subscribe' );

		/* Create the widget. */
		parent::__construct( 'sf_subscribe', __( 'Subscribe / Connect', 'sfwp-locale' ), $widget_ops, $control_ops );

	} // End Constructor

	/*----------------------------------------
	  widget()
	  ----------------------------------------

	  * Displays the widget on the frontend.
	----------------------------------------*/

	function widget( $args, $instance ) {

		$html = '';

		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

		$form = ! empty( $instance['form'] ) ? $instance['form'] : '';
		$social = ! empty( $instance['social'] ) ? $instance['social'] : '';
		$single = ! empty( $instance['single'] ) ? $instance['single'] : '';
		$page = ! empty( $instance['page'] ) ? $instance['page'] : '';

		/* Determine whether or not to display the widget. */

		if ( ! is_singular() || ( $single != 'on' && is_single() ) || ( $page != 'on' && is_page() ) ) {

			/* Before widget (defined by themes). */
			echo $before_widget;

			/* Widget content. */

			// Add actions for plugins/themes to hook onto.
			do_action( 'widget_sf_subscribe_top' );

			sf_subscribe_connect( 'true', $title, $form, $social );

			// Add actions for plugins/themes to hook onto.
			do_action( 'widget_sf_subscribe_bottom' );

			/* After widget (defined by themes). */
			echo $after_widget;

		} // End IF Statement

	} // End widget()

	/*----------------------------------------
	  update()
	  ----------------------------------------

	  * Function to update the settings from
	  * the form() function.

	  * Params:
	  * - Array $new_instance
	  * - Array $old_instance
	----------------------------------------*/

	function update ( $new_instance, $old_instance ) {
		$settings = array();

		foreach ( array( 'title', 'form', 'social', 'single', 'page' ) as $setting ) {
			if ( isset( $new_instance[$setting] ) ) {
				$settings[$setting] = sanitize_text_field( $new_instance[$setting] );
			}
		}

		return $settings;
	} // End update()

   /*----------------------------------------
	 form()
	 ----------------------------------------

	 * The form on the widget control in the
	 * widget administration area.

	 * Make use of the get_field_id() and
	 * get_field_name() function when creating
	 * your form elements. This handles the confusing stuff.

	 * Params:
	 * - Array $instance
	----------------------------------------*/

	function form ( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'Subscribe / Connect', 'sfwp-locale' ), 'form' => '', 'social' => '', 'single' => '', 'page' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );
       	?>
		<!-- No options -->
		<p><em><?php printf( __( 'Setup this widget in your <a href="%s">options panel</a> under <strong>Subscribe &amp; Connect</strong>', 'sfwp-locale' ), admin_url( 'admin.php?page=sf' ) ); ?></em>.</p>
        <!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'sfwp-locale' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
        </p>
       	<!-- Widget Subscribe Form: Checkbox Input -->
       	<p>
        	<input id="<?php echo $this->get_field_id( 'form' ); ?>" name="<?php echo $this->get_field_name( 'form' ); ?>" type="checkbox"<?php checked( $instance['form'], 'on' ); ?> />
        	<label for="<?php echo $this->get_field_id( 'form' ); ?>"><?php _e( 'Disable Subscription Form', 'sfwp-locale' ); ?></label>
	   	</p>
	   	<!-- Widget Social Icons: Checkbox Input -->
       	<p>
        	<input id="<?php echo $this->get_field_id( 'social' ); ?>" name="<?php echo $this->get_field_name( 'social' ); ?>" type="checkbox"<?php checked( $instance['social'], 'on' ); ?> />
        	<label for="<?php echo $this->get_field_id( 'social' ); ?>"><?php _e( 'Disable Social Icons', 'sfwp-locale' ); ?></label>
	   	</p>
	   	<!-- Widget Enable In Posts: Checkbox Input -->
       	<p>
        	<input id="<?php echo $this->get_field_id( 'single' ); ?>" name="<?php echo $this->get_field_name( 'single' ); ?>" type="checkbox"<?php checked( $instance['single'], 'on' ); ?> />
        	<label for="<?php echo $this->get_field_id( 'single' ); ?>"><?php _e( 'Disable in Posts', 'sfwp-locale' ); ?></label>
	   	</p>
	   	<!-- Widget Enable In Pages: Checkbox Input -->
       	<p>
        	<input id="<?php echo $this->get_field_id( 'page' ); ?>" name="<?php echo $this->get_field_name( 'page' ); ?>" type="checkbox"<?php checked( $instance['page'], 'on' ); ?> />
        	<label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e( 'Disable in Pages', 'sfwp-locale' ); ?></label>
	   	</p>
<?php
	} // End form()

} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------

  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Widget_Subscribe");' ), 1 );
?>