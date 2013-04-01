<?php

if( !function_exists('add_action') )
	die("access denied.");
	
function powerpress_admin_customfeeds_columns($data=array())
{
	$data['name'] = __('Category Name', 'powerpress');
	$data['feed-slug'] = __('Slug', 'powerpress');
	$data['url'] = __('Feed URL', 'powerpress');
	return $data;
}

add_filter('manage_powerpressadmin_categoryfeeds_columns', 'powerpress_admin_customfeeds_columns');

function powerpress_admin_categoryfeeds()
{
	$General = powerpress_get_settings('powerpress_general');

?>
<h2><?php echo __('Category Podcasting', 'powerpress'); ?></h2>
<p>
	<?php echo __('Category Podcasting adds custom podcast settings to specific blog category feeds, allowing you to organize episodes by topic.', 'powerpress'); ?>
</p>
<p>
	<?php echo sprintf( __('If you are looking to organize episodes by file or format, please use %s.', 'powerpress'),
		'<a href="'. admin_url('admin.php?page=powerpress/powerpressadmin_customfeeds.php') .'" title="'. __('Custom Podcast Channels') .'">'. __('Custom Podcast Channels') .'</a>'); ?>
</p>'<style type="text/css">
.column-url {
	width: 40%;
}
.column-name {
	width: 30%;
}
.column-feed-slug {
	width: 15%;
}
.column-episode-count {
	width: 15%;
}
.category-list {
	width: 100%;
}
</style>
<div id="col-container">

<div id="col-right">
<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php 
	if( function_exists('print_column_headers') )
	{
		print_column_headers('powerpressadmin_categoryfeeds');
	}
	else
	{
	?>
	<th scope="col" id="name" class="manage-column column-name"><?php echo __('Category Name', 'powerpress'); ?></th>
	<th scope="col" id="feed-slug" class="manage-column column-feed-slug"><?php echo __('Slug', 'powerpress'); ?></th>
	<th scope="col" id="url" class="manage-column column-url"><?php echo __('Feed URL', 'powerpress'); ?></th>
	<?php
	}
?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php
		print_column_headers('powerpressadmin_categoryfeeds', false);
?>
	</tr>
	</tfoot>
	<tbody>
<?php
	
	
	$Feeds = array();
	if( isset($General['custom_cat_feeds']) )
		$Feeds = $General['custom_cat_feeds'];
		
	$count = 0;
	while( list($null, $cat_ID) = each($Feeds) )
	{
		$category = get_category_to_edit($cat_ID);
		
		
		$columns = powerpress_admin_customfeeds_columns();
		$hidden = array();

		if( $count % 2 == 0 )
			echo '<tr valign="middle" class="alternate">';
		else
			echo '<tr valign="middle">';
			
		$edit_link = admin_url('admin.php?page=powerpress/powerpressadmin_categoryfeeds.php&amp;action=powerpress-editcategoryfeed&amp;cat=') . $cat_ID;
		
		$feed_title = $category->name;
		$url = get_category_feed_link($cat_ID);
		$short_url = str_replace('http://', '', $url);
		$short_url = str_replace('www.', '', $short_url);
		if (strlen($short_url) > 35)
			$short_url = substr($short_url, 0, 32).'...';

		foreach($columns as $column_name=>$column_display_name) {
			$class = "class=\"column-$column_name\"";
			
			
			
			//$short_url = '';
			
			switch($column_name) {
				case 'feed-slug': {
					
					echo "<td $class>{$category->slug}";
					echo "</td>";
					
				}; break;
				case 'name': {

					echo '<td '.$class.'><strong><a class="row-title" href="'.$edit_link.'" title="' . esc_attr(sprintf(__('Edit "%s"', 'powerpress'), $feed_title)) . '">'.$feed_title.'</a></strong><br />';
					$actions = array();
					$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit', 'powerpress') . '</a>';
					$actions['remove'] = "<a class='submitdelete' href='". admin_url() . wp_nonce_url("admin.php?page=powerpress/powerpressadmin_categoryfeeds.php&amp;action=powerpress-delete-category-feed&amp;cat=$cat_ID", 'powerpress-delete-category-feed-' . $cat_ID) . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to remove podcast settings for category feed '%s'\n  'Cancel' to stop, 'OK' to delete.", 'powerpress'), $feed_title )) . "') ) { return true;}return false;\">" . __('Remove', 'powerpress') . "</a>";
					$action_count = count($actions);
					$i = 0;
					echo '<div class="row-actions">';
					foreach ( $actions as $action => $linkaction ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo '<span class="'.$action.'">'.$linkaction.$sep .'</span>';
					}
					echo '</div>';
					echo '</td>';
					
				};	break;
					
				case 'url': {
				
					echo "<td $class><a href='$url' title='". esc_attr(sprintf(__('Visit %s', 'powerpress'), $feed_title))."' target=\"_blank\">$short_url</a>";
						echo '<div class="row-actions">';
							echo '<span class="'.$action .'"><a href="http://www.feedvalidator.org/check.cgi?url='. urlencode( str_replace('&amp;', '&', $url) ) .'" target="_blank">' . __('Validate Feed', 'powerpress') . '</a></span>';
						echo '</div>';
					echo "</td>";
					
				};	break;
					
				case 'episode-count': {
				
					echo "<td $class>$episode_total";
					echo "</td>";
					
				}; break;
				default: {
				
				};	break;
			}
		}
		echo "\n    </tr>\n";
		$count++;
	}
?>
	</tbody>
</table>
</div> <!-- col-right -->

<div id="col-left">
<div class="col-wrap">
<div class="form-wrap">
<h3><?php echo __('Add Podcast Settings to existing Category Feed', 'powerpress'); ?></h3>
<input type="hidden" name="action" value="powerpress-addcategoryfeed" />
<?php
	//wp_original_referer_field(true, 'previous'); 
	wp_nonce_field('powerpress-add-category-feed');
?>

<div class="form-field form-required">
	<label for="feed_name"><?php echo __('Category', 'powerpress') ?></label>
<?php
	wp_dropdown_categories(  array('class'=>'category-list', 'show_option_none'=>__('Select Category', 'powerpress'), 'orderby'=>'name', 'hide_empty'=>0, 'hierarchical'=>1, 'name'=>'cat', 'id'=>'cat_id' ) );
?>
	
    
</div>

<p class="submit"><input type="submit" class="button" name="submit" value="<?php echo __('Add Podcast Settings to Category Feed', 'powerpress'); ?>" /></p>

</div>
</div>

</div> <!-- col-left -->

</div> <!-- col-container -->

<h3><?php echo __('Example Usage', 'powerpress'); ?></h3>
<p>
	<?php echo __('Example 1: You have a podcast that covers two topics that sometimes share same posts and sometimes do not. Use your main podcast feed as a combined feed of both topics 	and use category feeds to distribute topic specific episodes.', 'powerpress'); ?>
</p>
<p>
	<?php echo __('Example 2: You want to use categories to keep episodes separate from each other. Each category can be used to distribute separate podcasts with the main podcast feed combining all categories to provide a network feed.', 'powerpress'); ?>
</p>

<?php
	}
?>