<?php
/*
-----------------------------------------------------------------------------------

 	Plugin Name: builder Recent Posts Widget
 	Plugin URI: http://www.orange-idea.com
 	Description: A widget that show recent posts ( Specified by cat-id )
 	Version: 1.0
 	Author: OrangeIdea
 	Author URI:  http://www.orange-idea.com
 
-----------------------------------------------------------------------------------
*/


/**
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'buildertheme_posts_load_widgets' );

function buildertheme_posts_load_widgets()
{
	register_widget('buildertheme_Recent_Posts_Widget');
}


/**
 * Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update. 
 *
 */
class buildertheme_Recent_Posts_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function buildertheme_Recent_Posts_Widget()
	{
		/* Widget settings. */
		$widget_ops = array('classname' => 'buildertheme_posts_widget', 'description' => __( 'Display Recent Posts by Categories' , 'builder' ) );
		
		/* Widget control settings. */
		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'buildertheme_posts_widget' );
		
		/* Create the widget. */
		$this->WP_Widget( 'buildertheme_posts_widget', __( 'buildertheme : Recent Posts' , 'builder' ), $widget_ops, $control_ops);
	}
	
	function widget($args, $instance)
	{
		extract($args);
		
		$title = $instance['title'];
		$categories = $instance['categories'];
		$num_posts = $instance['num_posts'];
		
		echo $before_widget;
		?>
		<!-- BEGIN WIDGET -->
		<?php
		if($title) {
			echo $before_title.$title.$after_title;
		}
		?>
		
		<?php $recent_posts = new WP_Query(array('showposts' => $num_posts, 'post_type' => 'post', 'cat' => $categories)); ?>
	
		<ul class="recent-post-widget unstyled">
			<?php while($recent_posts->have_posts()): $recent_posts->the_post(); ?>
			<li>
                <a href='<?php the_permalink(); ?>' title='<?php the_title(); ?>' class="bg-link"><?php the_title(); ?></a>
                <div class="small-meta">
                    <?php the_time('M j, Y'); ?> / <?php comments_popup_link(); ?>
                </div> <!-- /small-meta -->
                <div class="clear"></div>	
            </li>	
			<?php endwhile; ?>
		</ul>					
		
		<!-- END WIDGET -->
		<?php
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['categories'] = $new_instance['categories'];
		$instance['num_posts'] = $new_instance['num_posts'];
		
		return $instance;
	}


	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form($instance)
	{
		/* Set up some default widget settings. */
		$defaults = array('title' => __( 'Recent Posts' , 'builder' ) , 'categories' => 'all', 'num_posts' => 4);
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' , 'builder' ) ?></label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e( 'Filter by Category:' , 'builder' ); ?></label> 
			<select id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" class="widefat categories" style="width:100%;">
				<option value='all' <?php if ( 'all' == $instance['categories'] ) echo 'selected="selected"'; ?>>all categories</option>
				<?php $categories = get_categories( 'hide_empty=0&depth=1&type=post' ); ?>
				<?php foreach( $categories as $category ) { ?>
				<option value='<?php echo $category->term_id; ?>' <?php if ($category->term_id == $instance['categories']) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
				<?php } ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('num_posts'); ?>"><?php _e( 'Number of posts:' , 'builder' ); ?></label>
			<input class="widefat" style="width: 30px;" id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" value="<?php echo $instance['num_posts']; ?>" />
		</p>
		
	<?php 
	}
}
?>