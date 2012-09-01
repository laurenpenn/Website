<?php /** The template for displaying Comments. */ ?>

<h1>Comment's</h1>
<h2 class="blog-page-space">
Total: <?php comments_number(__('No Comments'), __('1 Comment'), __('% Comments')); ?>
<?php if ( post_password_required() ) : ?>
<?php _e( 'This post is password protected. Enter the password to view any comments.', 'twentyten' ); ?></p>
<?php return; endif;?>
</h2>

<?php if ( have_comments() ) : ?>

<?php wp_list_comments( array( 'callback' => 'twentyten_comment' ) ); ?>



<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
<div class="navigation">
<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'twentyten' ) ); ?></div>
<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
</div> <!-- .navigation -->
<?php endif; // check for comment navigation ?>



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
<p class="nocomments"><?php _e( 'Comments are closed.', 'twentyten' ); ?></p>
<?php endif; // end ! comments_open() ?>

<?php endif; // end have_comments() ?>
<!-- Leave Comment-->
<a href="#write-comment" title="Write Your Comment!" class="comment middle-button" style="margin:28px 0px 0px 4px;"><span class="middle-right"></span>Comment</a>

<div style="display: none;">
  <div id="write-comment" class="popup-comment">
    <?php comment_form(); ?>
  </div>
</div>