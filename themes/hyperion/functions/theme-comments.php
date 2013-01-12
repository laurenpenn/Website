<?php // CUSTOM COMMENT TEMPLATE
function hyperion_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
     <div class="comment-container">
     <div class="avatarbg">
      <div class="avatar">
         <?php echo get_avatar($comment,$size='65',$default='<path_to_url>' ); ?>
      </div>
      <span class="reply alignright"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></span>
      </div>
      
      <div class="comment-right">  
        <div class="comment-head">
              <?php printf(__('<span class="name alignleft">%s</span>'), get_comment_author_link()) ?>
              <span class="date alignright"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a></span>         
      <span class="edit alignright"><?php edit_comment_link(__('(Edit)'),'  ','') ?></span>
      </div>       

      <div class="comment-entry">
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
         <?php endif; ?>
	  <?php comment_text() ?>
      </div>
      
      </div>   

      </div>
      </div>
<?php
        }
?>
<?php
function list_pings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
?>
<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php } ?>
