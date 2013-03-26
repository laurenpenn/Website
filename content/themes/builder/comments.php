<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments" style="margin-bottom:0px;"><?php _e("This post is password protected. Enter the password to view comments.","builder"); ?></p>
	<?php
		return;
	}
?>
<?php if ( have_comments() ) : ?>
        <ul class="unstyled commentsul" style="margin-top:-35px;">
        	<?php wp_list_comments('max_depth=3&callback=mytheme_comment'); ?>     
       </ul>
	
 <?php else : // this is displayed if there are no comments so far ?>
	<?php if ( comments_open() ) : ?>
		<!-- If comments are open, but there are no comments. -->
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e("Comments are closed.","builder"); ?></p>
	<?php endif; ?>
<?php endif; ?>
<?php if ( comments_open() ) : ?>


<div id="respond" style="padding-top:40px;">
<h4 style="font-weight:600 !important; margin-bottom:12px;"><?php comment_form_title( 'Leave a Reply', 'Leave a Reply to %s' ); ?></h4>

<!--- replace comment_form();  -->
<?php paginate_comments_links('prev_text=back&next_text=forward'); ?>
<div class="cancel-comment-reply">
	<small><?php cancel_comment_reply_link(); ?></small>
</div>

<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<p><?php _e("You must be","builder"); ?> <a href="<?php echo wp_login_url( get_permalink() ); ?>"><?php _e("logged in","builder"); ?></a> <?php _e("to post a comment.","builder"); ?></p>
<?php else : ?>
<form class="form" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="contact-form">


<?php if ( is_user_logged_in() ) : ?>
	<h6 style="margin-bottom:18px;"><?php _e("Logged in as","builder"); ?> <i class="icon-user"></i> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a> / <i class="icon-remove-sign"></i> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account"><?php _e("Log out","builder"); ?></a></h6>
<?php else : ?>

    <input type="text" class="span4" style="margin-right:25px;" placeholder="Name" name="author" value="<?php echo esc_attr($comment_author); ?>" />
    <br>
    <input  class="span4" type="text" placeholder="E-mail" name="email" value="<?php echo esc_attr($comment_author_email); ?>" />
<?php endif; ?>
    <textarea type="text" placeholder="Message" id="comment" name="comment" rows="5" style="width:98%"></textarea><br>
	<button name="submit" id="submit_form" type="submit"  class="btn btn-small"><?php _e("Post comment","builder"); ?></button>
		
	
<p><?php comment_id_fields(); ?></p>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>