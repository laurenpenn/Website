<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

?>

<div class="wrap">
  <h2><?php echo "JW Player Licensing"; ?></h2>
  <form name="<?php echo LONGTAIL_KEY . "form"; ?>" method="post" action="admin.php?page=jwplayer-update">
    <div>
      <p><span><?php _e("By default, this plugin uses the latest non-commercial version of the JW Player 5.  We are working hard to upgrade our plugin to support both non-commercial and commercial versions of JW Player 6.", 'jw-player-plugin-for-wordpress'); ?></span></p>
      <p><span><?php _e("The WordPress Plugin currently supports JW Player 5 installations only.  If you have previously purchased a JW Player 5 license for the commercial player, you can upload it here:", 'jw-player-plugin-for-wordpress'); ?></span></p>
      <input type="submit" class="button-secondary action" name="Update" value="<?php _e("Upload a Commercial V5 Player Here", 'jw-player-plugin-for-wordpress'); ?>" />
    </div>
  </form>
</div>
