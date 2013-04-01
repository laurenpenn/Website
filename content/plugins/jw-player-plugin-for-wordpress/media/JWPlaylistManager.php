<?php

$p_items = array();
$playlists = jwplayer_get_playlists();
$form_action_url = admin_url("upload.php?page=jwplayer-playlists");

$new_playlist_id = -1;
if (isset($_POST[LONGTAIL_KEY . "playlist_create"]) || isset($_POST["save"])) {
  $post_title = $_POST[LONGTAIL_KEY . "playlist_name"];
  $new_playlist = array();
  $new_playlist["post_title"] = $post_title;
  $new_playlist["post_type"] = "jw_playlist";
  $new_playlist["post_status"] = null;
  $new_playlist["post_parent"] = null;
  if (isset($_POST["save"])) {
    $new_playlist_id = isset($_POST[LONGTAIL_KEY . "playlist_select"]) ? $_POST[LONGTAIL_KEY . "playlist_select"] : $playlists[0]->ID;
  } else {
    $new_playlist_id = wp_insert_post($new_playlist);
    $playlists = jwplayer_get_playlists();
  }
  $current_playlist = $new_playlist_id;
} else if (isset($_POST["delete"])) {
  wp_delete_post($_POST[LONGTAIL_KEY . "playlist_select"]);
  $playlists = jwplayer_get_playlists();
  $current_playlist = $playlists[0]->ID;
}

if (!isset($current_playlist)) {
  if (isset($_POST[LONGTAIL_KEY . "playlist_select"])) {
    $current_playlist = $_POST[LONGTAIL_KEY . "playlist_select"];
  } else if (isset($_GET["playlist"])) {
    $current_playlist = $_GET["playlist"];
  } else if (!empty($playlists)) {
    $current_playlist = $playlists[0]->ID;
  } else {
    $current_playlist = -1;
  }
}

if (isset($_GET["p_items"])) {
  $p_items = json_decode(str_replace("\\", "", $_GET["p_items"]));
} else if (isset($_POST["playlist_items"]) && $_POST["old_playlist"] == $current_playlist) {
  $p_items = json_decode(str_replace("\\", "", $_POST["playlist_items"]));
} else {
  $p_items = explode(",", get_post_meta($current_playlist, LONGTAIL_KEY . "playlist_items", true));
}

update_post_meta($new_playlist_id, LONGTAIL_KEY . "playlist_items", implode(",", $p_items));

$file_order = "asc";
$file_class = "sortable asc";
$author_order = "asc";
$author_class = "sortable asc";
$date_order = "asc";
$date_class = "sortable asc";
$order_by = "date";
$order = "desc";
if (isset($_GET["orderby"]) && isset($_GET["order"])) {
  $order_by = $_GET["orderby"];
  $order = $_GET["order"];
  if ($order_by == "title") {
    $file_order = $order == "desc" ? "asc" : "desc";
    $file_class = "sorted $order";
  } else if ($order_by == "post_author") {
    $author_order = $order == "desc" ? "asc" : "desc";
    $author_class = "sorted $order";
  } else if ($order_by == "date") {
    $date_order = $order == "desc" ? "asc" : "desc";
    $date_class = "sorted $order";
  }
} else {
  $date_order = "desc";
  $date_class = "sortable desc";
}

$playlist_items = get_jw_playlist_items($p_items);
$paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
$search = isset($_POST["s"]) ? $_POST["s"] : "";
$media_items = get_jw_media_items($paged, $order_by, $order, $search, $p_items);
if ($paged > 1 && !$media_items->have_posts()) {
  $paged = 1;
  $media_items = get_jw_media_items($paged, $order_by, $order, $search, $p_items);
}
$total = ceil($media_items->found_posts / 10);

$page_links = paginate_links( array(
  'base' => add_query_arg( 'paged', '%#%' ),
  'format' => '',
  'prev_text' => __('&laquo;', 'jw-player-plugin-for-wordpress'),
  'next_text' => __('&raquo;', 'jw-player-plugin-for-wordpress'),
  'total' => $total,
  'current' => $paged,
  'add_args' => array('playlist' => $current_playlist, 'orderby' => $order_by, 'order' => $order)
));

function get_jw_media_items($page, $column = "date", $sort = "DESC", $search="", $playlist_items = array()) {
  $args = array(
    'post_parent' => null,
    'posts_per_page' => 10,
    'paged' => $page,
    'post_status' => 'inherit',
    'post_type' => 'attachment',
    'orderby' => $column,
    'order' => $sort,
    'post__not_in' => $playlist_items,
    's' => $search
  );
  $query = new WP_Query($args);
  return $query;
}

function get_jw_playlist_items($playlist_item_ids = array()) {
  $args = array(
    'post_parent' => null,
    'posts_per_page'=>-1,
    'post_status' => 'inherit',
    'post_type' => 'attachment',
    'post__in' => $playlist_item_ids
  );
  $items = new WP_Query($args);
  $ordered_items = array();
  foreach ($playlist_item_ids as $playlist_item_id) {
    while ($items->have_posts()) {
      $item = $items->next_post();
      if ($item->ID == $playlist_item_id) {
        $ordered_items[$playlist_item_id] = $item;
      }
    }
  }
  return $ordered_items;
}

function jwplayer_get_playlists() {
  $playlist = array(
    "post_type" => "jw_playlist",
    "post_status" => null,
    "post_parent" => null,
    "nopaging" => true,
  );
  return query_posts($playlist);
}

?>

<div class="wrap">
  <h2><?php _e("JW Player Plugin Playlist Manager", 'jw-player-plugin-for-wordpress'); ?></h2>

  <script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery("#playlist_the-list, #the-list").sortable({
        connectWith: "#playlist_the-list, #the-list",
        revert: true,
        items: "tr:not(#no-posts)",
        stop: function(e, ui) {
          var posts = jQuery("#playlist_the-list tr:not(#no-posts)");
          if (posts.length > 0) {
            jQuery("#no-posts").hide();
          } else {
            jQuery("#no-posts").show();
          }
          var media = jQuery("#the-list tr:not(#no-media)");
          if (media.length > 0) {
            jQuery("#no-media").hide();
          } else {
            jQuery("#no-media").show();
          }
          updatePlaylist();
        }
      });
    });

    function updatePlaylist() {
      var desc = false;
      var item_list = document.getElementById("playlist_items");
      var p_items = new Array();
      var old_p_items =  eval('(' + item_list.value + ')');
      if (old_p_items[0] == "") {old_p_items = new Array();}
      var all = jQuery('#playlist_the-list').sortable('toArray'), len = all.length;
      jQuery.each(all, function(i, id) {
        var order = desc ? (len - i) : (1 + i);
        jQuery('#' + id + ' .menu_order input').val(order);
        p_items.push(id.replace("post-", ""));
      });
      update_page_numbers(p_items, old_p_items);
      document.getElementById("playlist_items").value = dump(p_items);
    }

    function update_page_numbers(p_items, old_p_items) {
      var pages = jQuery(".page-numbers");
      var j = 0;
      for (j = 0; j < pages.length; j++) {
        var page = pages[j];
        if (page.href) {
          page.href = page.href.replace(encodeURI("&p_items=" + dump(old_p_items)), "");
          page.href = page.href + encodeURI("&p_items=" + dump(p_items));
        }
      }
      var sort_links = jQuery(".sort-links");
      var k = 0;
      for (k = 0; k < sort_links.length; k++) {
        var sort_link = sort_links[k];
        if (sort_link.href) {
          sort_link.href = sort_link.href.replace(encodeURI("&p_items=" + dump(old_p_items)), "");
          sort_link.href = sort_link.href + encodeURI("&p_items=" + dump(p_items));
        }
      }
    }

    function dump (object, depth) {
      if (object == null) {
        return 'null';
      } else if (typeof(object) != 'object') {
        if (typeof(object) == 'string'){
          return"\""+object+"\"";
        }
        return object;
      }
      var type = typeOf(object);
      (depth == undefined) ? depth = 1 : depth++;
      var result = (type == "array") ? "[" : "{";
      var loopRan = false;
      if (type == "array") {
        for (var i = 0; i < object.length; i++) {
          loopRan = true;
          result += dump(object[i], depth)+", ";
        }
      } else {
        for (var j in object) {
          loopRan = true;
          if (type == "object") { result += "\""+j+"\": "};
          result += dump(object[j], depth)+", ";
        }
      }
      if (loopRan) {
        result = result.substring(0, result.length-1-depth);
      }
      result  += (type == "array") ? "]" : "}";
      return result;
    }

    function typeOf(value) {
      var s = typeof value;
      if (s === 'object') {
        if (value) {
          if (value instanceof Array) {
            s = 'array';
          }
        } else {
          s = 'null';
        }
      }
      return s;
    }

    function createPlaylistHandler() {
      var playlistName = document.forms[0]["<?php echo LONGTAIL_KEY . "playlist_name"; ?>"];
      if (playlistName.value == "") {
        alert("<?php _e("Your playlist must have a valid name.", 'jw-player-plugin-for-wordpress'); ?>");
        return false;
      }
      return true;
    }

    function deletePlaylistHandler() {
      return confirm("<?php _e("Are you sure wish to delete the Playlist?", 'jw-player-plugin-for-wordpress'); ?>");
    }

  </script>

  <form action="<?php echo $form_action_url; ?>" method="post">
    <div>
      <div style="width: 1000px;">
        <p class="ml-submit">
          <label for="<?php echo LONGTAIL_KEY . "playlist_name"; ?>"><?php _e("New Playlist:", 'jw-player-plugin-for-wordpress'); ?></label>
          <input type="text" value="" id="<?php echo LONGTAIL_KEY . "playlist_name"; ?>" name="<?php echo LONGTAIL_KEY . "playlist_name"; ?>" />
          <input type="submit" class="button savebutton" style="" name="<?php echo LONGTAIL_KEY . "playlist_create"; ?>" id="<?php echo LONGTAIL_KEY . "playlist_create"; ?>" value="<?php esc_attr_e("Create Playlist", 'jw-player-plugin-for-wordpress'); ?>" onclick="return createPlaylistHandler()" />
        </p>
        <div class="ml-submit" style="padding: 0 0; float: left;">
          <div class="alignleft actions">
            <div class="hide-if-no-js">
              <label for="<?php echo LONGTAIL_KEY . "playlist_select"; ?>"><?php _e("Playlist:", 'jw-player-plugin-for-wordpress'); ?></label>
              <select onchange="this.form.submit()" id="<?php echo LONGTAIL_KEY . "playlist_select"; ?>" name="<?php echo LONGTAIL_KEY . "playlist_select"; ?>">
                <?php foreach ($playlists as $playlist_list) { ?>
                <option value="<?php echo $playlist_list->ID; ?>" <?php selected($playlist_list->ID, $current_playlist); ?>>
                  <?php echo $playlist_list->post_title; ?>
                </option>
                <?php } ?>
              </select>
              <input type="submit" class="button savebutton" name="save" id="save-all" value="<?php esc_attr_e('Save', 'jw-player-plugin-for-wordpress'); ?>" />
              <input type="submit" class="button savebutton" name="delete" id="delete-all" value="<?php esc_attr_e('Delete', 'jw-player-plugin-for-wordpress'); ?>" onclick="return deletePlaylistHandler()" />
              <input type="hidden" id="playlist_items" name="playlist_items" value='<?php echo json_encode($p_items); ?>' />
              <input type="hidden" id="old_playlist" name="old_playlist" value="<?php echo $current_playlist; ?>" />
              <span style="margin-left: 230px;"><?php _e("Media List", 'jw-player-plugin-for-wordpress'); ?></span>
            </div>
          </div>
        </div>
        <div style="float: right;">
          <label class="screen-reader-text" for="media-search-input"><?php _e("Search Media:", 'jw-player-plugin-for-wordpress'); ?></label>
          <input type="text" id="media-search-input" name="s" value="">
          <input type="submit" name="" id="search-submit" class="button" value="<?php _e("Search Media", 'jw-player-plugin-for-wordpress'); ?>">
        </div>
        <div style="clear: both;"></div>
      </div>
      <div style="width: 1000px; padding-top: 10px;">
        <div style="width: 475px; float: left;">
          <table class="wp-list-table widefat fixed media" cellspacing="0">
            <thead>
              <tr>
                <th scope="col" id="playlist_icon" class="manage-column column-icon" style=""></th>
                <th scope="col" id="playlist_title" class="manage-column column-title" style=""><span><?php _e("File", 'jw-player-plugin-for-wordpress'); ?></span></th>
                <th scope="col" id="playlist_author" class="manage-column column-author sortable desc" style="width: 20%; padding: 7px 7px 8px;"><span><?php _e("Author", 'jw-player-plugin-for-wordpress'); ?></span></th>
                <th scope="col" id="playlist_date" class="manage-column column-date sortable asc" style="width: 20%; padding: 7px 7px 8px;"><span><?php _e("Date", 'jw-player-plugin-for-wordpress'); ?></span></th>
              </tr>
            </thead>

            <tbody id="playlist_the-list">
              <?php foreach ($playlist_items as $key => $playlist_item) { ?>
                <tr id="post-<?php echo $playlist_item->ID; ?>" class="alternate author-self status-inherit playlist-item" valign="top" style="width: 475px;">
                  <td class="column-icon media-icon">
                    <?php $mime_type = substr($playlist_item->post_mime_type, 0, 5); ?>
                    <?php $image = $mime_type == "image" ? $playlist_item->guid : "http://localhost/wordpress/wp-includes/images/crystal/video.png"; ?>
                    <?php $width = $mime_type == "image" ? "32" : "24"; ?>
                    <img width="<?php echo $width; ?>" height="32" src="<?php echo $image; ?>"
                         class="attachment-80x60" alt="<?php echo $playlist_item->post_title; ?>" title="<?php echo $playlist_item->post_title; ?>">

                  </td>
                  <td class="title column-title" style="color: #21759B;"><strong>
                    <?php $title = $playlist_item->post_title ? $playlist_item->post_title : $playlist_item->guid ?>
                    <?php echo $title; ?>
                  </strong>
                  </td>
                  <td class="author column-author"><?php echo get_post_meta($playlist_item->ID, LONGTAIL_KEY . "creator", true); ?></td>
                  <td class="date column-date"><?php echo mysql2date(__('Y/m/d', 'jw-player-plugin-for-wordpress'), $playlist_item->post_date); ?></td>
                </tr>
              <?php } ?>
              <?php $style = empty($playlist_item) ? "" : "style='display: none;'"; ?>
              <tr id="no-posts" class="alternate author-self status-inherit" <?php echo $style; ?>>
                <td colspan="4" style="text-align: center; height: 50px;"><?php _e("Drag items from the Media List to start building your playlist.", 'jw-player-plugin-for-wordpress'); ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="padding-left: 50px; width: 475px; float: left;">
          <table class="wp-list-table widefat fixed media" cellspacing="0">
            <thead>
              <tr>
                <th scope="col" id="icon" class="manage-column column-icon" style=""></th>
                <th scope="col" id="title" class="manage-column column-title <?php echo $file_class; ?>" style="">
                  <a class="sort-links" href="http://localhost/wordpress/wp-admin/upload.php?page=jwplayer-playlists&orderby=title&amp;order=<?php echo $file_order; ?>">
                    <span>File</span>
                    <span class="sorting-indicator"></span>
                  </a>
                </th>
                <th scope="col" id="author" class="manage-column column-author <?php echo $author_class; ?>" style="width: 20%;">
                  <a class="sort-links" href="http://localhost/wordpress/wp-admin/upload.php?page=jwplayer-playlists&orderby=post_author&amp;order=<?php echo $author_order; ?>">
                    <span>Author</span>
                    <span class="sorting-indicator"></span>
                  </a>
                </th>
                <th scope="col" id="date" class="manage-column column-date <?php echo $date_class; ?>" style="width: 20%;">
                  <a class="sort-links" href="http://localhost/wordpress/wp-admin/upload.php?page=jwplayer-playlists&orderby=date&amp;order=<?php echo $date_order; ?>">
                    <span>Date</span>
                    <span class="sorting-indicator"></span>
                  </a>
                </th>
              </tr>
            </thead>

            <tbody id="the-list">
              <?php while ($media_items->have_posts()) { ?>
              <?php $media_item = $media_items->next_post(); ?>
              <tr id="post-<?php echo $media_item->ID; ?>" class="alternate author-self status-inherit" valign="top" style="width: 475px;">
                <td class="column-icon media-icon">
                  <?php $mime_type = substr($media_item->post_mime_type, 0, 5); ?>
                  <?php $image = $mime_type == "image" ? $media_item->guid : "http://localhost/wordpress/wp-includes/images/crystal/video.png"; ?>
                  <?php $width = $mime_type == "image" ? "32" : "24"; ?>
                  <img width="<?php echo $width; ?>" height="32" src="<?php echo $image; ?>"
                       class="attachment-80x60" alt="<?php echo $media_item->post_title; ?>" title="<?php echo $media_item->post_title; ?>">

                </td>
                <td class="title column-title" style="color: #21759B;"><strong>
                  <?php $title = $media_item->post_title ? $media_item->post_title : $media_item->guid ?>
                  <?php echo $title; ?>
                </strong>
                </td>
                <td class="author column-author"><?php echo get_post_meta($media_item->ID, LONGTAIL_KEY . "creator", true); ?></td>
                <td class="date column-date"><?php echo mysql2date(__('Y/m/d', 'jw-player-plugin-for-wordpress'), $media_item->post_date); ?></td>
              </tr>
              <?php } ?>
              <?php $style = $media_items->found_posts > 0 ? "style='display: none;'" : ""; ?>
              <tr id="no-media" class="alternate author-self status-inherit" <?php echo $style; ?>>
                <td colspan="4" style="text-align: center; height: 50px;">No Results</td>
              </tr>
            </tbody>
          </table>
          <?php if ($page_links) { ?>
              <div class="tablenav">
                <div class='tablenav-pages'>
                  <span style="font-size: 13px;"><?php _e("Available Media:", 'jw-player-plugin-for-wordpress'); ?></span>
                  <?php echo $page_links; ?>
                </div>
              </div>
          <?php }?>
        </div>
        <div style="clear: both;"></div>
      </div>
    </div>
  </form>
</div>