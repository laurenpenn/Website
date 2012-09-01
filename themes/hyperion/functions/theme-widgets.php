<?php

/*-----------------------------------------------------------------------------------

- Flickr widget
- Twitter widget

-----------------------------------------------------------------------------------*/


/*---------------------------------------------------------------------------------*/
/* Flickr widget */
/*---------------------------------------------------------------------------------*/
if (class_exists('WP_Widget')) {
	class hyperion_flickr extends WP_Widget {
	
		function hyperion_flickr() {
			$widget_ops = array('description' => 'This Flickr widget populates photos from a Flickr ID.' );
			parent::WP_Widget(false, __('Hyperion - Flickr'),$widget_ops);      
		}
	
		function widget($args, $instance) {  
			extract( $args );
			$id = $instance['id'];
			$number = $instance['number'];
			$type = $instance['type'];
			$sorting = $instance['sorting'];
			
			echo $before_widget;
			echo $before_title; ?>
			<?php _e('Photos on <span>flick<span>r</span></span>'); ?>
			<?php echo $after_title; ?>
				
			<div class="wrap">
				<div class="fix"></div>
				<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $number; ?>&amp;display=<?php echo $sorting; ?>&amp;size=s&amp;layout=x&amp;source=<?php echo $type; ?>&amp;<?php echo $type; ?>=<?php echo $id; ?>"></script>        
				<div class="fix"></div>
			</div>
	
		   <?php			
		   echo $after_widget;
	   }
	
	   function update($new_instance, $old_instance) {                
		   return $new_instance;
	   }
	
	   function form($instance) {        
			$id = esc_attr($instance['id']);
			$number = esc_attr($instance['number']);
			$type = esc_attr($instance['type']);
			$sorting = esc_attr($instance['sorting']);
			?>
			<p>
				<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Flickr ID (<a href="http://www.idgettr.com">idGettr</a>):','woothemes'); ?></label>
				<input type="text" name="<?php echo $this->get_field_name('id'); ?>" value="<?php echo $id; ?>" class="widefat" id="<?php echo $this->get_field_id('id'); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number:','woothemes'); ?></label>
				<select name="<?php echo $this->get_field_name('number'); ?>" class="widefat" id="<?php echo $this->get_field_id('number'); ?>">
					<?php for ( $i = 1; $i < 10; $i += 1) { ?>
					<option value="<?php echo $i; ?>" <?php if($number == $i){ echo "selected='selected'";} ?>><?php echo $i; ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type:','woothemes'); ?></label>
				<select name="<?php echo $this->get_field_name('type'); ?>" class="widefat" id="<?php echo $this->get_field_id('type'); ?>">
					<option value="user" <?php if($type == "user"){ echo "selected='selected'";} ?>><?php _e('User', 'woothemes'); ?></option>
					<option value="group" <?php if($type == "group"){ echo "selected='selected'";} ?>><?php _e('Group', 'woothemes'); ?></option>            
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('sorting'); ?>"><?php _e('Sorting:','woothemes'); ?></label>
				<select name="<?php echo $this->get_field_name('sorting'); ?>" class="widefat" id="<?php echo $this->get_field_id('sorting'); ?>">
					<option value="latest" <?php if($sorting == "latest"){ echo "selected='selected'";} ?>><?php _e('Latest', 'woothemes'); ?></option>
					<option value="random" <?php if($sorting == "random"){ echo "selected='selected'";} ?>><?php _e('Random', 'woothemes'); ?></option>            
				</select>
			</p>
			<?php
		}
	} 
	register_widget('hyperion_flickr');
}
	
	
/*---------------------------------------------------------------------------------*/
/* Twitter widget */
/*---------------------------------------------------------------------------------*/
class hyperion_Twitter extends WP_Widget {

   function hyperion_Twitter() {
	   $widget_ops = array('description' => 'Add your Twitter feed to your sidebar with this widget.' );
       parent::WP_Widget(false, __('Hyperion - Twitter Stream'),$widget_ops);      
   }
   
   function widget($args, $instance) {  
    extract( $args );
   	$title = $instance['title'];
    $limit = $instance['limit']; if (!$limit) $limit = 5;
	$username = $instance['username'];
	$unique_id = $args['widget_id'];
	?>
		<?php echo $before_widget; ?>
        <?php if ($title) echo $before_title . $title . $after_title; ?>
        <ul id="twitter_update_list_<?php echo $unique_id; ?>"><li></li></ul>	
        <?php echo hyperion_twitter_script($unique_id,$username,$limit); //Javascript output function ?>	 
        <?php echo $after_widget; ?>
        
   		
	<?php
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        
   
       $title = esc_attr($instance['title']);
       $limit = esc_attr($instance['limit']);
	   $username = esc_attr($instance['username']);
       ?>
       <p>
	   	   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
       </p>
       <p>
	   	   <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username:','woothemes'); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name('username'); ?>"  value="<?php echo $username; ?>" class="widefat" id="<?php echo $this->get_field_id('username'); ?>" />
       </p>
       <p>
	   	   <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:','woothemes'); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name('limit'); ?>"  value="<?php echo $limit; ?>" class="" size="3" id="<?php echo $this->get_field_id('limit'); ?>" />

       </p>
      <?php
   }
   
} 
register_widget('hyperion_Twitter');	

?>