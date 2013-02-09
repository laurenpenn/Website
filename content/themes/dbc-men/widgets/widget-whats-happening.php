<?php
/**
 * What's Happening Widget Class
 *
 * The What's Happening widget displays an image that can link elsewhere.
 *
 * @since 0.1
 *
 * @package Hybrid
 * @subpackage Classes
 */

class DBC_Widget_Whats_Happening extends WP_Widget {

	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6
	 */
	function DBC_Widget_Whats_Happening() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = 'dbc';

		$widget_ops = array( 'classname' => 'whats-happening', 'description' => __( 'Displays an image that can link elsewhere.', $this->textdomain ) );
		$control_ops = array( 'width' => 525, 'height' => 350, 'id_base' => "{$this->prefix}-whats-happening" );
		$this->WP_Widget( "{$this->prefix}-whats-happening", __( 'What\'s Happening', $this->textdomain ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$args = array();

		$args['type'] = $instance['type']; 
		$args['format'] = $instance['format'];
		$args['before'] = $instance['before'];
		$args['after'] = $instance['after'];
		$args['show_post_count'] = isset( $instance['show_post_count'] ) ? $instance['show_post_count'] : false;
		$args['limit'] = !empty( $instance['limit'] ) ? intval( $instance['limit'] ) : '';
		$args['echo'] = false;

		echo $before_widget;

		if ( $instance['title'] )
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;

		echo 'test';

		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = $new_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['before'] = strip_tags( $new_instance['before'] );
		$instance['after'] = strip_tags( $new_instance['after'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['show_post_count'] = ( isset( $new_instance['show_post_count'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6
	 */
	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Archives', $this->textdomain ),
			'limit' => '',
			'type' => 'monthly',
			'format' => 'html',
			'before' => '',
			'after' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$type = array( 'alpha' => __( 'Alphabetical', $this->textdomain ), 'daily' => __( 'Daily', $this->textdomain ), 'monthly' => __( 'Monthly', $this->textdomain ),'postbypost' => __( 'Post By Post', $this->textdomain ), 'weekly' => __( 'Weekly', $this->textdomain ), 'yearly' => __( 'Yearly', $this->textdomain ) );
		$format = array( 'custom' => __( 'Custom', $this->textdomain ), 'html' => __( 'HTML', $this->textdomain ), 'option' => __( 'Option', $this->textdomain ) );

		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><code>limit</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" />
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>
