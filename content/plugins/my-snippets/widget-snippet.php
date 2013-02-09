<?php
/**
 * My Snippets Widget Class
 *
 * The My Snippets Widget displays custom post metadata that is input on singular posts (pages,
 * other post types, etc.). The widget only displays if metadata is available.  Otherwise, it displays
 * nothing.  The Default Title field only displays in the instance that no My Snippets Title is entered.
 *
 * @package MySnippets
 */

class My_Snippets_Widget_Snippet extends WP_Widget {

	function My_Snippets_Widget_Snippet() {
		$widget_ops = array( 'classname' => 'snippet', 'description' => __('Displays custom post snippets on a post-by-post basis.', 'my-snippets') );
		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'my-snippet-widget' );
		$this->WP_Widget( 'my-snippet-widget', __('Snippet', 'my-snippets'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		global $wp_query;

		extract( $args );

		if ( !is_singular() )
			return false;

		$id = $wp_query->get_queried_object_id();

		$my_snippet = get_post_meta( $id, 'Snippet', true );

		if ( empty( $my_snippet ) )
			return false;

		$my_snippet_title = get_post_meta( $id, 'Snippet Title', true );

		if ( $my_snippet_title )
			$title = apply_filters( 'widget_title', $my_snippet_title );
		else
			$title = apply_filters('widget_title', $instance['title'] );

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

			echo '<div class="snippet-content">';
			echo do_shortcode( $my_snippet );
			echo '</div>';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$defaults = array( 'title' => __('Snippet', 'my-snippets') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Default Title:', 'my-snippets'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
	<?php
	}
}

?>