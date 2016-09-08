<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Component Widget
 *
 * A standardized component widget.
 *
 * @package WordPress
 * @category Widgets
 * @since	1.0
 *
 * TABLE OF CONTENTS
 *
 * protected $sf_widget_cssclass
 * protected $sf_widget_description
 * protected $sf_widget_idbase
 * protected $sf_widget_title
 *
 * - __construct()
 * - widget()
 * - update()
 * - form()
 * - load_component()
 */
 class Widget_Component extends WP_Widget {
	protected $sf_widget_cssclass;
	protected $sf_widget_description;
	protected $sf_widget_idbase;
	protected $sf_widget_title;

	/**
	 * Constructor function.
	 * @since	1.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->sf_widget_cssclass = 'widget_sf_component';
		$this->sf_widget_description = __( 'This is a standardized component loading widget. Intended primarily for use in the "Widgets" Page Template widget region.', 'sfwp-locale' );
		$this->sf_widget_idbase = 'sf_component';
		$this->sf_widget_title = __( 'Component', 'sfwp-locale' );

		$this->sf_widget_componentslist = array(
			'blog' => __( 'Blog', 'sfwp-locale' ),
			'business-slider' => __( '"Business" Slider', 'sfwp-locale' ),
			'magazine-slider' => __( '"Magazine" Slider', 'sfwp-locale' ),
			'magazine-grid' => __( '"Magazine" Grid', 'sfwp-locale' ),
			'current-page-content' => __( 'Current Page Content', 'sfwp-locale' )
		);

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->sf_widget_cssclass, 'description' => $this->sf_widget_description );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => $this->sf_widget_idbase );

		/* Create the widget. */
		parent::__construct( $this->sf_widget_idbase, $this->sf_widget_title, $widget_ops, $control_ops );
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since	1.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return	void
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { echo $before_title . $title . $after_title; }

		$component = ! empty( $instance['component'] ) ? $instance['component'] : '';
		$slide_group = ! empty( $instance['slide_group'] ) ? $instance['slide_group'] : '';

		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->sf_widget_cssclass . '_top' );

		if ( in_array( $component, array_keys( $this->sf_widget_componentslist ) ) ) {
			$this->load_component( esc_attr( $component ), esc_attr( $slide_group ) );
		}

		// Add actions for plugins/themes to hook onto.
		do_action( $this->sf_widget_cssclass . '_bottom' );

		/* After widget (defined by themes). */
		echo $after_widget;
	} // End widget()

	/**
	 * Method to update the settings from the form() method.
	 * @since	1.0
	 * @param  array $new_instance New settings.
	 * @param  array $old_instance Previous settings.
	 * @return array               Updated settings.
	 */
	public function update ( $new_instance, $old_instance ) {
		$settings = array();

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$settings['title'] = strip_tags( $new_instance['title'] );

		foreach ( array( 'component', 'slide_group' ) as $setting ) {
			if ( isset( $new_instance[$setting] ) ) {
				$settings[$setting] = sanitize_text_field( $new_instance[$setting] );
			}
		}

		return $settings;
	} // End update()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since	1.0
	 * @param  array $instance The settings for this instance.
	 * @return	void
	 */
    public function form( $instance ) {

		/* Set up some default widget settings. */
		/* Make sure all keys are added here, even with empty string values. */
		$defaults = array(
						'title'			=> '',
						'component' 	=> '',
						'slide_group' 	=> ''
					);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'sfwp-locale' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>
		<!-- Widget Component: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'component' ); ?>"><?php _e( 'Component:', 'sfwp-locale' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'component' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'component' ); ?>">
			<?php foreach ( $this->sf_widget_componentslist as $k => $v ) { ?>
				<option value="<?php echo $k; ?>"<?php selected( $instance['component'], $k ); ?>><?php echo $v; ?></option>
			<?php } ?>
			</select>
		</p>
		<?php if ( 'business-slider' == $instance[ 'component' ] ) { ?>
		<!-- Widget Select Group: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'slide_group' ); ?>"><?php _e( 'Slide Group:', 'sfwp-locale' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'slide_group' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'slide_group' ); ?>">
			<?php foreach ( $this->get_slide_groups() as $k => $v ) { ?>
				<option value="<?php echo $k; ?>"<?php selected( $instance['slide_group'], $k ); ?>><?php echo $v; ?></option>
			<?php } ?>
			</select>
		</p>
		<?php } ?>
<?php
	} // End form()

	/**
	 * Load the desired component, if a method is available for it.
	 * @param	string $component The component to potentially be loaded.
	 * @since	1.0
	 * @return	void
	 */
	protected function load_component ( $component, $slide_group = 0 ) {
		switch ( $component ) {
			case 'blog':
				get_template_part( 'templates/loops/loop', 'blog' );
			break;

			case 'business-slider':
			echo '<div class="business">' . "\n";
				$args = array();
				if ( '' != $slide_group && 0 != $slide_group ) {
					$args[ 'use_slide_page' ] = true;
					$args[ 'slide_page' ] = $slide_group;
				}
				sf_slider_biz( $args );
			echo '</div>' . "\n";
			break;

			case 'magazine-slider':
			echo '<div class="magazine">' . "\n";
				sf_slider_magazine();
			echo '</div>' . "\n";
			break;

			case 'magazine-grid':
			echo '<div class="magazine">' . "\n";
				get_template_part( 'templates/loops/loop', 'magazine' );
			echo '</div>' . "\n";
			break;

			case 'current-page-content':
			if ( have_posts() ) { $count = 0;
				while ( have_posts() ) { the_post(); $count++;
					sf_get_template_part( 'templates/contents/content', 'page' ); // Get the page content template file, contextually.
				}
			}
			break;

			default:
			break;
		}
	} // End load_component()

	/**
	 * Load Slide Groups
	 * @since	1.0
	 * @return	array $groups Slide Groups
	 */
	protected function get_slide_groups() {
		// Setup an array of slide-page terms for a dropdown.
		$groups = array();

		$slide_groups = get_terms( 'slide-page' );
		foreach ( $slide_groups as $slide_group ) {
			$groups[ $slide_group->term_id ] = $slide_group->name;
		}

		return $groups;
	} // End get_slide_groups()

} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget("Widget_Component");' ), 1 );

?>