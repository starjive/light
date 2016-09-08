<?php

class Widget_Announce extends WP_Widget {

	/*----------------------------------------
	  Constructor.
	  ----------------------------------------

	  * The constructor. Sets up the widget.
	----------------------------------------*/

	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'announce-widget', 'description' => __( 'Use this widget to add any type of Ad as a widget.', 'sfwp-locale' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'announce-widget' );

		/* Create the widget. */
		parent::__construct( 'announce-widget', __( 'Announce/Ad', 'sfwp-locale' ), $widget_ops, $control_ops );

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
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$adcode = ! empty( $instance['adcode'] ) ? $instance['adcode'] : '';
		$image = ! empty( $instance['image'] ) ? $instance['image'] : '';
		$href = ! empty( $instance['href'] ) ? $instance['href'] : '';
		$alt = ! empty( $instance['alt'] ) ? $instance['alt'] : '';

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {

			echo $before_title . $title . $after_title;

		} // End IF Statement

		/* Widget content. */

		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_sf_adspace_top' );

		$html = '';

		if( $adcode != '' ) {
			$html .= $adcode;
		} else {
			if ( $href != '' ) {
				$html .= '<a href="' . $href . '">';

				// If we have an image, display that. Otherwise, use the alt text as a text link.
				if ( $image != '' ) {
					$html .= '<img src="' . $image . '" alt="' . $alt . '" />';
				} else {
					if ( $alt != '' ) {
						$html .= $alt;
					}
				}

				$html .= '</a>';
			}
		}

		echo $html;

		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_sf_adspace_bottom' );

		/* After widget (defined by themes). */
		echo $after_widget;

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

	function update( $new_instance, $old_instance ) {
		$settings = array();
		foreach ( array( 'title', 'alt', 'image', 'href' ) as $setting ) {
			if ( isset( $new_instance[$setting] ) ) {
				$settings[$setting] = strip_tags( $new_instance[$setting] );
			}
		}
		// Users without unfiltered_html cannot update this arbitrary HTML field
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$settings['adcode'] = $old_instance['adcode'];
		} else {
			$settings['adcode'] = $new_instance['adcode'];
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
		$defaults = array( 'title' => __( 'Announce', 'sfwp-locale' ), 'adcode' => '', 'image' => '', 'href' => '', 'alt' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );

		/* Make the ad code read-only if the user can't work with unfiltered HTML. */
		$read_only = '';
		if ( ! current_user_can( 'unfiltered_html' ) ) { $read_only = ' readonly="readonly"'; }
?>
		<!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'sfwp-locale' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
        </p>
        <!-- Widget Ad Code: Textarea -->
		<p>
            <label for="<?php echo $this->get_field_id( 'adcode' ); ?>"><?php _e( 'Code:', 'sfwp-locale' ); ?></label>
            <textarea name="<?php echo $this->get_field_name( 'adcode' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'adcode' ); ?>"<?php echo $read_only; ?>><?php echo $instance['adcode']; ?></textarea>
        </p>
        <p><strong><?php _e( 'or', 'sfwp-locale' ); ?></strong></p>
        <!-- Widget Image: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e( 'Image URL:', 'sfwp-locale' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo $instance['image']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'image' ); ?>" />
        </p>
        <!-- Widget Href: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'href' ); ?>"><?php _e( 'Link URL:', 'sfwp-locale' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'href' ); ?>" value="<?php echo $instance['href']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'href' ); ?>" />
        </p>
        <!-- Widget Alt Text: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'alt' ); ?>"><?php _e( 'Alt text:', 'sfwp-locale' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'alt' ); ?>" value="<?php echo $instance['alt']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'alt' ); ?>" />
        </p>
<?php
	} // End form()

} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------

  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Widget_Announce");' ), 1 );
?>