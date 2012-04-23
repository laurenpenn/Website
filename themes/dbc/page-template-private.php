<?php
/**
 * Template Name: Private
 *
 * For pages that require a user to login to view. This template includes a login and registration form.
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content" role="main">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<div class="hfeed">

			<?php if ( is_user_logged_in() ) { ?>
				
				<?php if ( have_posts() ) : ?>
	
					<?php while ( have_posts() ) : the_post(); ?>
	
						<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>
	
						<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
	
							<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>
	
							<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
	
							<div class="entry-content">
								<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'dbc' ) ); ?>
								<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'dbc' ), 'after' => '</p>' ) ); ?>
							</div><!-- .entry-content -->
	
							<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>
	
							<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>
	
						</div><!-- .hentry -->
	
						<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>
	
						<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>
	
						<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>
	
					<?php endwhile; ?>
	
				<?php endif; ?>
				
			<?php } else { //user is not logged in ?>
				
				<h1>Private Page</h1>
				
				<p>The page you are trying to view requires that you log in.</p>
				
				<div class="col-1">
					
					<h2>Login</h2>
								
					<form method="post" action="<?php echo site_url() ?>/wp-login.php" id="loginform" name="loginform">
			
						<p class="login-username">
							<label for="user_login">Username</label>
							<input type="text" tabindex="1" size="20" value="" class="input" id="user_login" name="log">
						</p>
						<p class="login-password">
							<label for="user_pass">Password</label>
							<input type="password" tabindex="1" size="20" value="" class="input" id="user_pass" name="pwd">
						</p>
						
						<p class="login-remember"><label><input type="checkbox" tabindex="90" value="forever" id="rememberme" name="rememberme"> Remember Me</label></p>
						<p class="login-submit">
							<input type="submit" tabindex="100" value="Log In" class="button-primary" id="wp-submit" name="wp-submit">
							<input type="hidden" value="<?php the_permalink(); ?>" name="redirect_to">
						</p>
						
						<p><a href="http://admin.dentonbible.org/wp-login.php?action=lostpassword">Lost your password?</a></p>
						
					</form>
								
				</div>
				
				<div class="col-2">
					
					<h2>Register</h2>
				
					<?php switch_to_blog(1); ?>
					<?php gravity_form( 9, false, true, false, null, false); ?>
					<?php restore_current_blog(); ?>
				
				</div>
								
			<?php } //end logged in check ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>