<?php
/**
 * Custom search widget for CircloSquero Theme
 */
class CircloSquero_Search extends WP_Widget {
    /** constructor */
    function CircloSquero_Search() {
        parent::WP_Widget(false, $name = 'CircloSquero Search');	
    }

    /** @see WP_Widget::widget */
            function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$CS_default_search = apply_filters( 'widget_cs_search', $instance['search'], $instance );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
            ?>
                <form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" >
                <div><input type="text" value="<?php echo get_option_tree( 't_search' );?>" name="s" id="s" class="CS_searchform" onfocus="if (this.value == '<?php echo get_option_tree( 't_search' );?>') {this.value = '';}";" /><input type="submit" id="searchsubmit" value="" class="CS_searchform_button" />
                </div>
                </form><div class="clear"></div>
            <?php
		echo $after_widget;
	}

    /** @see WP_Widget::update */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'search' => 'Search...'  ) );
		$title = $instance['title'];
                $CS_default_search = $instance['search'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
                <p><label for="<?php echo $this->get_field_id('search'); ?>"><?php _e('Default search text:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>" type="text" value="<?php echo esc_attr($CS_default_search); ?>" /></label></p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'search' => 'Search...'));
		$instance['title'] = strip_tags($new_instance['title']);
                $instance['search'] = strip_tags($new_instance['search']);
		return $instance;
	}

} 

add_action('widgets_init', create_function('', 'return register_widget("CircloSquero_Search");')); // register the custom search
?>
