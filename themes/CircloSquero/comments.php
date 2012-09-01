<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to twentyten_comment which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>

			<div id="comments">
<?php if ( post_password_required() ) : ?>
				<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'twentyten' ); ?></p>
			</div><!-- #comments -->
<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<?php if ( have_comments() ) : ?>
			<h3 class="comments-title">Comments:</h3>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'twentyten' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
			</div> <!-- .navigation -->
<?php endif; // check for comment navigation ?>

			<ol class="commentlist">
				<?php
					/* Loop through and list the comments. Tell wp_list_comments()
					 * to use twentyten_comment() to format the comments.
					 * If you want to overload this in a child theme then you can
					 * define twentyten_comment() and that will be used instead.
					 * See twentyten_comment() in twentyten/functions.php for more.
					 */
					wp_list_comments( array( 'callback' => 'cs_comments' ) );
				?>
			</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'twentyten' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
			</div><!-- .navigation -->
<?php endif; // check for comment navigation ?>

<?php else : // or, if we don't have comments:

	/* If there are no comments and comments are closed,
	 * let's leave a little note, shall we?
	 */
	if ( ! comments_open() ) :
?>
	<p class="nocomments"><?php if (is_single()) {echo 'Comments are closed.';}; ?></p>
<?php endif; // end ! comments_open() ?>

<?php endif; // end have_comments()  ?>



            <?php $defaults = array( 'fields' => apply_filters( 'comment_form_default_fields', array(
    'author' => '<p class="comment-form-author formfloatl formm">' .
                '<label for="author">' . get_option_tree( 'comment_name' ) . '</label> <br/> 
                <input id="author" name="author" type="text" value="' .
                esc_attr( $commenter['comment_author'] ) . '" size="30" tabindex="1"' . $aria_req . ' />' .
                '</p><!-- #form-section-author .form-section -->',
    'email'  => '<p class="comment-form-email formfloatl formm">' .
                '<label for="email">' .  get_option_tree( 'comment_email' ) . '</label><br/> 
                <input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" tabindex="2"' . $aria_req . ' />' .
                '</p><!-- #form-section-email .form-section -->',
    'url'    => '<p class="comment-form-url formfloatr formm">' .
                '<label for="url">' . get_option_tree( 'comment_website' ) . '</label><br/>' .
                '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" tabindex="3" />' .
                '</p><div class="clear"></div><!-- #form-section-url .form-section -->' ) ),
    'comment_field' => '<p class="comment-form-comment">' .
                '<label for="comment">' . '' . '</label><br/>' .
                '<textarea id="comment" name="comment" cols="45" rows="8" tabindex="4" aria-required="true"></textarea>' .
                '</p><!-- #form-section-comment .form-section -->',
    'must_log_in' => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
        'comment_notes_before' => ' ',
    'comment_notes_after' => '',
    'id_form' => 'commentform',
    'id_submit' => 'submitC',
    'title_reply' => '<h3 class="comments-title">'.get_option_tree( 'comment_leavereply' ).'</h3>',
    'title_reply_to' => get_option_tree( 'comment_leavereplyto' ),
    'cancel_reply_link' => get_option_tree( 'comment_cancelreply' ),
    'label_submit' => get_option_tree( 'comment_post' )
); ?>


<div id="posalji_komm"><?php comment_form($defaults); ?></div>

</div><!-- #comments -->
