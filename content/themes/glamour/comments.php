<?php
	$req = get_option('require_name_email');
	if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die ( 'Please do not load this page directly. Thanks!' );
	if ( ! empty($post->post_password) ) :
	if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password ) :
?>
<div class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'your-theme') ?></div>
<?php return; endif; endif; ?>

<?php if ( have_comments() ) : ?>
<?php
	$ping_count = $comment_count = 0;
	foreach ( $comments as $comment )
		get_comment_type() == "comment" ? ++$comment_count : ++$ping_count;
?>

<?php if ( ! empty($comments_by_type['comment']) ) : ?>
	<div id="comments-list" class="comments">
		<h3 style="padding-bottom:20px; padding-top:20px;">
			<?php if($comment_count > 1){

				echo $comment_count.' '.get_option('cf_one_comments', 'Comments');
			}else{
				echo get_option('cf_one_comment', 'One Comment');
			}
			
			?>
			<a class="comment-link" href="#respond" title="<?php echo get_option('leave_comment', 'Leave a Reply'); ?>">(<?php echo get_option('leave_comment', 'Leave a Reply'); ?>)</a>
		</h3>
		
		<ol>
			<?php wp_list_comments('type=comment&callback=custom_comments'); ?>
		</ol>
		
		<?php $total_pages = get_comment_pages_count(); if ( $total_pages > 1 ) : ?>                                    
		<div id="comments-nav-below" class="comments-navigation">
			<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
		</div>
		<?php endif; ?>                                
		
	</div>
	<br/>
	<div class="page_line"></div>
	<?php endif; ?>


	<?php endif ?>

	<?php if ( 'open' == $post->comment_status ) : ?>
	<div id="respond"></div>
	<br />
	<div class="contact_title">
		<h3><?php echo get_option('leave_comment', 'Leave a Reply'); ?></h3>
	</div>
	

	<div class="contact_form" style="padding-top:0px">
		<form id="commentform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">

		<?php if ( $user_ID ) : ?>

		<div style="padding-left:30px;">
		<?php 
			printf(__('Logged in as <a href="%1$s" title="Logged in as %2$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'your-theme'),
			get_option('siteurl') . '/wp-admin/profile.php',
			wp_specialchars($user_identity, true),
			wp_logout_url(get_permalink()) );
		?>
		</div>

		<?php else : ?>
			<div class="contact_form_item">
				<div class="contact_form_title"><?php echo get_option('cf_name', 'Name'); ?></div>
				<div class="contact_form_input_bg">
					<div class="contact_form_bg"><input id="author" name="author" type="text" value="<?php echo $comment_author ?>"/></div>
				</div>
				<div class="contact_form_required"><?php echo get_option('cf_required', '(* required)'); ?></div>
			</div>
			
			<div class="contact_form_item">
				<div class="contact_form_title"><?php echo get_option('cf_email', 'Email Address'); ?></div>
				<div class="contact_form_input_bg">
					<div class="contact_form_bg"><input id="email" name="email" type="text" value="<?php echo $comment_author_email ?>" /></div>
				</div>
				<div class="contact_form_required"><?php echo get_option('cf_will_not', '( * required - will not be published)'); ?></div>
			</div>
			
			<div class="contact_form_item">
				<div class="contact_form_title"><?php echo get_option('cf_web', 'Web Site'); ?></div>
				<div class="contact_form_input_bg">
					<div class="contact_form_bg"><input id="url" name="url" type="text" value="<?php echo $comment_author_url ?>"/></div>
				</div>
			</div>
		
		<?php endif ?>

		<div class="contact_form_item" style="height:130px;">
			<div class="contact_form_title"><?php echo get_option('cf_comment', 'Comment'); ?></div>
			<div class="contact_form_textarea_input_bg">
				<div class="contact_form_message_bg"><textarea id="comment" name="comment" rows="0"  cols="0"></textarea></div>
			</div>
			<div class="contact_form_required"><?php echo get_option('cf_required', '(* required)'); ?></div>
		</div>
		
		<div style="margin-bottom:30px;">
			<?php do_action('comment_form', $post->ID); ?>
			<input type="submit" name="Submit" value="<?php echo get_option("post_comment", "Post Comment"); ?>" style="width:86px; height:26px; margin:0; cursor:pointer; padding:0; left:0; color:#555; background:url(<?php bloginfo('template_url'); ?>/images/submit_bg.png);	margin-top:10px;	color:#000;" />
			<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
		</div>
		<?php comment_id_fields(); ?>  
		</form>
		
	</div>
<?php endif ?>
