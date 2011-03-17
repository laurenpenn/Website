<?php get_header(); ?>

	<div id="content" class="hfeed content buddypress">

		<?php hybrid_before_buddypress(); // Before BuddyPress hook ?>

<div class="activity no-ajax">
	<?php if ( bp_has_activities( 'display_comments=threaded&include=' . bp_current_action() ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">
		<?php while ( bp_activities() ) : bp_the_activity(); ?>

			<?php locate_template( array( 'activity/entry.php' ), true ) ?>

		<?php endwhile; ?>
		</ul>

	<?php endif; ?>
</div>

		<?php hybrid_after_buddypress(); // After BuddyPress hook ?>

	</div><!-- .content .hfeed .buddypress -->

<?php get_footer(); ?>