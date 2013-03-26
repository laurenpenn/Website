<?php
/*
-----------------------------------------------------------------------------------

 	Plugin Name: Twitter Widget
 	Plugin URI: http://www.orange-idea.com
 	Description: A widget that displays messages from twitter.com
 	Version: 1.0
 	Author: OrangeIdea
 	Author URI:  http://www.orange-idea.com
 
-----------------------------------------------------------------------------------
*/


// Add function to widgets_init that'll load our widget
add_action( 'widgets_init', 'buildertheme_twitter_load_widgets' );

// Register widget
function buildertheme_twitter_load_widgets() {
	register_widget( 'buildertheme_Twitter_Widget' );
}

// Widget class
class buildertheme_Twitter_Widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
function buildertheme_Twitter_Widget() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'builder_twitter_widget' , 'description' => __( 'Twitter Widget' , 'builder' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'builder_twitter_widget' );
		
		/* Create the widget. */
		$this->WP_Widget('builder_twitter_widget', __( 'builder : Twitter Widget' , 'builder' ) , $widget_ops, $control_ops );
	
}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
function widget( $args, $instance ) {
	extract( $args );

	// Our variables from the widget settings
	$title = apply_filters('widget_title', $instance['title'] );
	$user_name = $instance['user_name'];
	$count_message = $instance['count_message'];	

	// Before widget (defined by theme functions file)
	echo $before_widget;

	// Display the widget title if one was input
	if ( $title )
		echo $before_title . $title . $after_title;

	// Display video widget
	?>


<script type="text/javascript">
			(function($) {
				JQTWEET = {
					
					// Set twitter username, number of tweets & id/class to append tweets
					user: '<?php echo $instance['user_name']; ?>',
					numTweets: <?php echo $instance['count_message']; ?>,
					appendTo: '#jstwitter',
				
					// core function of jqtweet
					loadTweets: function() {
						$.ajax({
							url: 'http://api.twitter.com/1/statuses/user_timeline.json/',
							type: 'GET',
							dataType: 'jsonp',
							data: {
								screen_name: JQTWEET.user,
								include_rts: true,
								count: JQTWEET.numTweets,
								include_entities: true
							},
							success: function(data, textStatus, xhr) {
				
							 var html = '<div class="tweet" style="float:left !important">TWEET_TEXT<div class="time">AGO</div>';
				
								 // append tweets into page
								 for (var i = 0; i < data.length; i++) {
									$(JQTWEET.appendTo).append(
										html.replace('TWEET_TEXT', JQTWEET.ify.clean(data[i].text))
											.replace(/USER/g, data[i].user.screen_name)
											.replace('AGO', JQTWEET.timeAgo(data[i].created_at))
											.replace(/ID/g, data[i].id_str)
									);
				
								 }					
							}	
				
						});
						
					}, 
					
						
					/**
					  * relative time calculator FROM TWITTER
					  * @param {string} twitter date string returned from Twitter API
					  * @return {string} relative time like "2 minutes ago"
					  */
					timeAgo: function(dateString) {
						var rightNow = new Date();
						var then = new Date(dateString);

						
						if ($.browser.msie) {
							// IE can't parse these crazy Ruby dates
							then = Date.parse(dateString.replace(/( \+)/, ' UTC$1'));
						}
				
						var diff = rightNow - then;
				
						var second = 1000,
						minute = second * 60,
						hour = minute * 60,
						day = hour * 24,
						week = day * 7;
				
						if (isNaN(diff) || diff < 0) {
							return ""; // return blank string if unknown
						}
				
						if (diff < second * 2) {
							// within 2 seconds
							return "right now";
						}
				
						if (diff < minute) {
							return Math.floor(diff / second) + " seconds ago";
						}
				
						if (diff < minute * 2) {
							return "about 1 minute ago";
						}
				
						if (diff < hour) {
							return Math.floor(diff / minute) + " minutes ago";
						}
				
						if (diff < hour * 2) {
							return "about 1 hour ago";
						}
				
						if (diff < day) {
							return  Math.floor(diff / hour) + " hours ago";
						}
				
						if (diff > day && diff < day * 2) {
							return "yesterday";
						}
				
						if (diff < day * 365) {
							return Math.floor(diff / day) + " days ago";
						}
				
						else {
							return "over a year ago";
						}
					}, // timeAgo()
					
					
					/**
					  * The Twitalinkahashifyer!
					  * http://www.dustindiaz.com/basement/ify.html
					  * Eg:
					  * ify.clean('your tweet text');
					  */
					ify:  {
					  link: function(tweet) {
						return tweet.replace(/\b(((https*\:\/\/)|www\.)[^\"\']+?)(([!?,.\)]+)?(\s|$))/g, function(link, m1, m2, m3, m4) {
						  var http = m2.match(/w/) ? 'http://' : '';
						  return '<a class="twtr-hyperlink" target="_blank" href="' + http + m1 + '">' + ((m1.length > 25) ? m1.substr(0, 24) + '...' : m1) + '</a>' + m4;
						});
					  },
				
					  at: function(tweet) {
						return tweet.replace(/\B[@＠]([a-zA-Z0-9_]{1,20})/g, function(m, username) {
						  return '<a target="_blank" class="twtr-atreply" href="http://twitter.com/intent/user?screen_name=' + username + '">@' + username + '</a>';
						});
					  },
				
					  list: function(tweet) {
						return tweet.replace(/\B[@＠]([a-zA-Z0-9_]{1,20}\/\w+)/g, function(m, userlist) {
						  return '<a target="_blank" class="twtr-atreply" href="http://twitter.com/' + userlist + '">@' + userlist + '</a>';
						});
					  },
				
					  hash: function(tweet) {
						return tweet.replace(/(^|\s+)#(\w+)/gi, function(m, before, hash) {
						  return before + '<a target="_blank" class="twtr-hashtag" href="http://twitter.com/search?q=%23' + hash + '">#' + hash + '</a>';
						});
					  },
				
					  clean: function(tweet) {
						return this.hash(this.at(this.list(this.link(tweet))));
					  }
					} // ify
				
					
				};
				})(jQuery);
		</script>
			<div id="jstwitter" class="clearfix"></div>
	
	<?php

	// After widget (defined by theme functions file)
	echo $after_widget;
	
}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
function update( $new_instance, $old_instance ) {
	$instance = $old_instance;

	// Strip tags to remove HTML (important for text inputs)
	$instance['title'] = strip_tags( $new_instance['title'] );
	
	// Stripslashes for html inputs
	$instance['user_name'] = stripslashes( $new_instance['user_name']);
	$instance['count_message'] = stripslashes( $new_instance['count_message']);	

	// No need to strip tags

	return $instance;
}


/*-----------------------------------------------------------------------------------*/
/*	Widget Settings (Displays the widget settings controls on the widget panel)
/*-----------------------------------------------------------------------------------*/
	 
function form( $instance ) {

	// Set up some default widget settings
	$defaults = array( 'title' => __( 'Twetter Feed' , 'builder' ), 'user_name' => 'Orange_Idea_RU', 'count_message' => '2', );
	
	$instance = wp_parse_args( (array) $instance, $defaults ); ?>

	<!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'builder' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
	</p>

	
	<!-- User Name For Twitter Service Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'user_name' ); ?>"><?php _e( 'User Name:' , 'builder'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'user_name' ); ?>" name="<?php echo $this->get_field_name( 'user_name' ); ?>" value="<?php echo stripslashes(htmlspecialchars(( $instance['user_name'] ), ENT_QUOTES)); ?>" />
	</p>

	<!-- Count Messages: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'count_message' ); ?>"><?php _e( 'The Number of Displayed Messages:' , 'builder' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'count_message' ); ?>" name="<?php echo $this->get_field_name( 'count_message' ); ?>" value="<?php echo stripslashes(htmlspecialchars(( $instance['count_message'] ), ENT_QUOTES)); ?>" />
	</p>
		
	<?php
	}
}
?>