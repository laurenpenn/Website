<?php get_header(); ?>

	<div id="content" class="hfeed content buddypress">

		<?php hybrid_before_buddypress(); // Before BuddyPress hook ?>

		<?php do_action( 'bp_before_directory_blogs_content' ) ?>

		<?php do_action( 'template_notices' ) ?>

		<h3><?php _e( 'Create a Blog', 'buddypress' ) ?> &nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . BP_BLOGS_SLUG . '/' ?>"><?php _e( 'Blogs Directory', 'buddypress' ) ?></a></h3>

		<?php do_action( 'bp_before_create_blog_content' ) ?>

		<?php if ( bp_blog_signup_enabled() ) : ?>

			<?php bp_show_blog_signup_form() ?>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'Blog registration is currently disabled', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>

		<?php do_action( 'bp_after_create_blog_content' ) ?>

		<?php do_action( 'bp_after_directory_blogs_content' ) ?>

		<?php hybrid_after_buddypress(); // After BuddyPress hook ?>

	</div><!-- .content .hfeed .buddypress -->

<?php get_footer(); ?>