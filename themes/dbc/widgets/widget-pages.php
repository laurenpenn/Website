<?php
/**
 * DBC Pages Widget
 *
 * Displays child pages of the current page
 *
 * @package Hybrid
 * @subpackage Widgets
 */

/**
 * Output of the Pages widget.
 * Arguments are parameters of the wp_list_pages() function.
 * @link http://codex.wordpress.org/Template_Tags/wp_list_pages
 *
 * @since 0.6
 */
class DBC_Widget_Pages extends WP_Widget {

	var $prefix;
	var $textdomain;

	function DBC_Widget_Pages() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = 'dbc';

		$widget_ops = array( 'classname' => 'dbc-pages', 'description' => __( 'Displays pages.', $this->textdomain) );
		$control_ops = array( 'width' => 800, 'height' => 350, 'id_base' => "{$this->prefix}-dbc-pages" );
		$this->WP_Widget( "{$this->prefix}-dbc-pages", __( 'Pages', $this->textdomain), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		global $post;
		extract( $args );

		$args = array();
		
		/* Open the output of the widget. */
		echo $before_widget;

		/* If there is a title given, add it along with the $before_title and $after_title variables. */
		if ( $instance['title'] )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Output the page list. */

		if(!$post->post_parent){
			// will display the subpages of this top level page
			$children = wp_list_pages( array( 'title_li' => '', 'child_of' => $post->ID, 'echo' => 0 ) );
		} elseif($post->ancestors){
			// diplays only the subpages of parent level
			$ancestors = end($post->ancestors);
			$children = wp_list_pages( array( 'title_li' => '', 'child_of' => $ancestors, 'echo' => 0 ) );
		} else {
			// diplays all pages
			$children = wp_list_pages( array( 'title_li' => '', 'echo' => 0 ) );
		}
		
		if ($children) {
			echo '<ul id="sidebar-menu" class="quickTree menu">';
			echo $children;
			echo '</ul>';
		}

		/* Close the output of the widget. */
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$defaults = array(
			'title' => __( 'Pages', $this->textdomain)
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<div class="hybrid-widget-controls columns-3">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->textdomain ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>