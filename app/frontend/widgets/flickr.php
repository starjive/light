<?php

class Widget_Flickr extends WP_Widget {

	/*----------------------------------------
	  Constructor.
	  ----------------------------------------

	  * The constructor. Sets up the widget.
	----------------------------------------*/

	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_sf_flickr', 'description' => __( 'This Flickr widget populates photos from a Flickr ID.', 'sfwp-locale' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'sf_flickr' );

		/* Create the widget. */
		parent::__construct( 'sf_flickr', __('Flickr', 'sfwp-locale' ), $widget_ops, $control_ops );

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

		$number = ! empty( $instance['number'] ) ? $instance['number'] : 5;
		$id = ! empty( $instance['id'] ) ? $instance['id'] : '';
		$sorting = ! empty( $instance['sorting'] ) ? $instance['sorting'] : '';
		$type = ! empty( $instance['type'] ) ? $instance['type'] : '';
		$size = ! empty( $instance['size'] ) ? $instance['size'] : '';

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Use the default title if no title is set. */
		if ( ! $title ) { $title = __( 'Photos on', 'sfwp-locale' ) . ' <span>flick<span>r</span></span>'; }

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {

			echo $before_title . $title . $after_title;

		} // End IF Statement

		/* Widget content. */

		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_sf_flickr_top' );

		$html = '';

		/* Construct the remainder of the query string, using only the non-empty fields. */
		$fields = array(
						'count'		=> $number,
						'display'	=> $sorting,
						'source'	=> $type,
						$type		=> $id,
						'size'		=> $size
					);

		$query_string = '';

		foreach ( $fields as $k => $v ) {
			if ( $v == '' ) {} else {
				$query_string .= '&amp;' . $k . '=' . $v;
			}
		}

		$html .= '<div class="wrap">' . "\n";
			$html .= '<div class="fix"></div><!--/.fix-->' . "\n";
				$html .= '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?layout=x' . $query_string . '"></script>' . "\n";
			$html .= '<div class="fix"></div><!--/.fix-->' . "\n";
		$html .= '</div><!--/.wrap-->' . "\n";

		echo $html;

		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_sf_flickr_bottom' );

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

	function update ( $new_instance, $old_instance ) {
		$settings = array();

		foreach ( array( 'title', 'id', 'type', 'sorting', 'size' ) as $setting ) {
			if ( isset( $new_instance[$setting] ) ) {
				$settings[$setting] = sanitize_text_field( $new_instance[$setting] );
			}
		}

		foreach ( array( 'number' ) as $setting ) {
			if ( isset( $new_instance[$setting] ) ) {
				$settings[$setting] = absint( $new_instance[$setting] );
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

   function form( $instance ) {

       /* Set up some default widget settings. */
		$defaults = array(
						'title' => '',
						'id' => '',
						'number' => '',
						'type' => 'user',
						'sorting' => 'latest',
						'size' => 's'
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'sfwp-locale' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>
		<!-- Widget Flickr ID: Text Input -->
		<p>
		    <label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Flickr ID (<a href="http://www.idgettr.com">idGettr</a>):', 'sfwp-locale' ); ?></label>
		    <input type="text" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo $instance['id']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'id' ); ?>" />
		</p>
		<!-- Widget Number: Select Input -->
		<p>
		    <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number:', 'sfwp-locale' ); ?></label>
		    <select name="<?php echo $this->get_field_name( 'number' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>">
		        <?php for ( $i = 1; $i <= 10; $i += 1) { ?>
		        <option value="<?php echo $i; ?>"<?php selected( $instance['number'], $i ); ?>><?php echo $i; ?></option>
		        <?php } ?>
		    </select>
		</p>
		<!-- Widget Type: Select Input -->
		<p>
		    <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type:', 'sfwp-locale' ); ?></label>
		    <select name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>">
		        <option value="user"<?php selected( $instance['type'], 'user' ); ?>><?php _e( 'User', 'sfwp-locale' ); ?></option>
		        <option value="group"<?php selected( $instance['type'], 'group' ); ?>><?php _e( 'Group', 'sfwp-locale' ); ?></option>
		    </select>
		</p>
		<!-- Widget Sorting: Select Input -->
		<p>
		    <label for="<?php echo $this->get_field_id( 'sorting' ); ?>"><?php _e( 'Sorting:', 'sfwp-locale' ); ?></label>
		    <select name="<?php echo $this->get_field_name( 'sorting' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'sorting' ); ?>">
		        <option value="latest"<?php selected( $instance['sorting'], 'latest' ); ?>><?php _e( 'Latest', 'sfwp-locale' ); ?></option>
		        <option value="random"<?php selected( $instance['sorting'], 'random' ); ?>><?php _e( 'Random', 'sfwp-locale' ); ?></option>
		    </select>
		</p>
		<!-- Widget Size: Select Input -->
		<p>
		    <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size:', 'sfwp-locale' ); ?></label>
		    <select name="<?php echo $this->get_field_name( 'size' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>">
		        <option value="s"<?php selected( $instance['size'], 's' ); ?>><?php _e( 'Square', 'sfwp-locale' ); ?></option>
		        <option value="m"<?php selected( $instance['size'], 'm' ); ?>><?php _e( 'Medium', 'sfwp-locale' ); ?></option>
		        <option value="t"<?php selected( $instance['size'], 't' ); ?>><?php _e( 'Thumbnail', 'sfwp-locale' ); ?></option>
		    </select>
		</p>
<?php
	} // End form()

} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------

  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Widget_Flickr");' ), 1 );
?>