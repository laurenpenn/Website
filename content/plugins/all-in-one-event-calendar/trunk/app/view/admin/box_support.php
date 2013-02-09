<div class="timely">
<h2 class="timely-logo"><a href="http://time.ly/" title="<?php esc_attr_e( 'Timely', AI1EC_PLUGIN_NAME ); ?>" target="_blank"></a></h2>

<div class="timely-intro">
	<h2>
		<?php _e( 'Timely’s All-in-One Event Calendar is a<br />revolutionary new way to find and share events.', AI1EC_PLUGIN_NAME ); ?>
	</h2>
</div>

<div class="ai1ec-download row-fluid">
	<div class="span12">
		<a class="btn btn-large disabled ai1ec-download-btn">
			<?php printf(
				__( 'You are currently running the %s version %s', AI1EC_PLUGIN_NAME ),
				'<div><i class="timely-icon-checkmark"></i> ' .
					__( '<strong>Lite</strong> Calendar', AI1EC_PLUGIN_NAME ) .
					'</div>',
				AI1EC_VERSION );
			?>
		</a>
	</div>
</div>
<div class="ai1ec-download row-fluid">
	<div class="span6">
		<div>
			<a href="<?php echo admin_url( 'edit.php?post_type=' . AI1EC_POST_TYPE . '&amp;page=' . AI1EC_PLUGIN_NAME . '-upgrade' ) ?>" class="btn btn-large ai1ec-download-btn">
				<?php printf(
					__( 'Upgrade to the %s for free', AI1EC_PLUGIN_NAME ),
					'<div><i class="timely-icon-gift"></i> ' .
						__( '<strong>Standard</strong> Calendar', AI1EC_PLUGIN_NAME ) .
						'</div>',
					AI1EC_VERSION );
				?>
			</a>
			<ul class="icons">
				<li><a title="<?php esc_attr_e( 'Extended Views', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Display events in your choice of layout, including improved Month, Day, Week and Agenda views, as well as the attractive Posterboard view. Lock to specific views or allow viewers to choose. Easily customize the colours and fonts, or write your own custom theme.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Extended Views', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Import/Export Events', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Import and sync event feeds from other All-in-One Calendars, or from any other .ics calendar system, including Google Calendar, iCal, Outlook and more.<br /><br />Every event or category feed can be exported to other All-in-One Calendars or to other .ics calendar systems, offering multiple event notification options—even on mobile devices.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Import/Export Events', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Facebook Integration', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Connect your Facebook account to import your Facebook events, your friends’ events, and the events of your Pages and Groups, including their associated event photos. Optionally, export individual All-in-One events back to Facebook.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Facebook Integration', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Filter by Category and Tag', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Filter your calendar by any combination of category or tag. Assign colors and avatars to your event categories. Subscribe to filtered event feeds or display them on any page in your site; have each filtered view appear alongside any content or advertising you’d like.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Filter by Category and Tag', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Recurring Events', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Create recurring events more easily, even in complex patterns. Allow events to repeat on any day of the week, month, or year. Schedule complex recurrence patterns such as “every first and third Monday of the month.”', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Recurring Events', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Upcoming Events Widgets', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Includes an Upcoming Events widget that can be displayed in your sidebar or footer. Widgets can be filtered to show only events in a certain category, tag or a combination of both.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Upcoming Events Widgets', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Locations and Maps', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'The Standard Calendar can provide detailed location information for your event and includes the option to embed a Google Map directly into your event details. Additionally, you can specify exact longitude and latitude co-ordinates.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Locations and Maps', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Basic Support', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Browse our Developer Guide, find the answers to many previously asked questions on our Support Forums, or review dozens of articles written specifically about common questions.<br /><br />Have a problem that you can’t solve? Our Support Staff can take a quick look at your installation and point you in the right direction. If we discover a bug, we’ll do our best to fix it.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Basic Support', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
			</ul>
		</div>
	</div>
	<div class="span6">
		<div>
			<a href="http://time.ly/timely-all-in-one-calendar-pro"
				class="btn btn-large btn-primary ai1ec-download-btn">
				<?php printf(
					__( 'Upgrade to the %s for only $75', AI1EC_PLUGIN_NAME ),
					'<div><i class="timely-icon-shopping-cart"></i> ' .
						__( '<strong>Pro</strong> Calendar', AI1EC_PLUGIN_NAME ) .
						'</div>' );
				?>
			</a>
			<ul class="icons">
				<li><a title="<?php esc_attr_e( 'All Standard Features', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'You get every feature that comes with the Standard Calendar.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'All Standard Features', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Front-end Event Creation Form', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Allow your users, even anonymous users if you wish, to contribute their own events to your calendar. The form offers security and editorial control of submitted events.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Front-end Event Creation Form', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'User Feed Submission', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Allow users to submit their own calendar’s iCalendar (.ics) feed through the front-end of your site. You receive a notification of the contribution for review and can easily add it to your calendar’s list of subscribed feeds.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'User Feed Submission', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Stream View', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'A streamlined and mobile-friendly view of your calendar’s upcoming events that includes thumbnails, category avatars and more.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Stream View', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Bulk CSV Upload', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Upload a feed of events as a file in either comma-separated values (CSV) or iCalendar (.ics) format.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Bulk CSV Upload', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Super Widgets', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Embed your calendar outside of WordPress and distribute it to other websites – even sites hosted on other servers.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Super Widgets', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'Premium Support', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Get priority support by e-mail and view your active support requests right in WordPress. Examples include:<ul class="icons"><li class="timely-icon-checkmark"> Setting up the calendar</li><li class="timely-icon-checkmark"> Minor customizations</li><li class="timely-icon-checkmark"> Changing the size/positioning of the calendar</li><li class="timely-icon-checkmark"> Resolving plugin or theme conflicts (if possible)</li><li class="timely-icon-checkmark"> Assistance resolving issues covered by documentation</li></ul>', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'Premium Support', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
				<li><a title="<?php esc_attr_e( 'One Year of Free Updates', AI1EC_PLUGIN_NAME ); ?>"
					data-content="<?php esc_attr_e( 'Your Pro Calendar license gives you free updates to every new release of the Pro Calendar for one year after your date of purchase.', AI1EC_PLUGIN_NAME ); ?>">
					<i class="timely-icon-checkmark"></i> <?php _e( 'One Year of Free Updates', AI1EC_PLUGIN_NAME ); ?>
				</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="ai1ec-news">
	<h2>
		<?php _e( 'Timely News', AI1EC_PLUGIN_NAME ); ?>
		<small>
			<a href="http://help.time.ly/" target="_blank"
				class="btn btn-primary pull-right ai1ec-get-support">
				<i class="timely-icon-dedicated-support"></i> <?php _e( 'Get Support', AI1EC_PLUGIN_NAME ); ?>
				<i class="timely-icon-chevron-right"></i>
			</a>

			<a href="http://time.ly/blog" target="_blank"><?php _e( 'view all news', AI1EC_PLUGIN_NAME ); ?> <i class="icon-arrow-right"></i></a>
		</small>
	</h2>
	<div>
	<?php if( count( $news ) > 0 ) : ?>
		<?php foreach( $news as $n ) : ?>
			<article>
				<header>
					<strong><a href="<?php echo $n->get_permalink() ?>" target="_blank"><?php echo $n->get_title() ?></a></strong>
				</header>
				<div>
					<?php echo preg_replace( '/\s+?(\S+)?$/', '', $n->get_description() ); ?>
				</div>
			</article>
		<?php endforeach ?>
	<?php else : ?>
		<p><em>No news available.</em></p>
	<?php endif ?>
	</div>
</div>

<div class="ai1ec-follow-fan">
	<div class="ai1ec-facebook-like-top">
		<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
		<fb:like href="http://www.facebook.com/timelycal" layout="button_count" show_faces="true" width="110" font="lucida grande"></fb:like>
	</div>
	<a href="http://twitter.com/_Timely" class="twitter-follow-button"><?php _e( 'Follow @_Timely', AI1EC_PLUGIN_NAME ) ?></a>
	<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
</div>

<br class="clear" />
</div>
