<?php
/**
 * Latest message
 *
 * Pulls in the latest product from dbcmedia.org
 *
 * @package Hybrid
 * @subpackage Template
 */
?>
<div id="latest-message">

	<div class="latest-message-title">This Week's Message</div>

	<?php if(function_exists('fetch_feed')) {
	
		include_once(ABSPATH . WPINC . '/feed.php');               // include the required file
		$feed = fetch_feed('http://dbcmedia.org/sermons/feed/'); // specify the source feed
	
		$limit = $feed->get_item_quantity(1); // specify number of items
		$items = $feed->get_items(0, $limit); // create an array of items
	
	}
	if ($limit == 0) echo '<div>The feed is either empty or unavailable.</div>';
	else foreach ($items as $item) : ?>
			<div class="latest-message-left">
				<div class="latest-message-message-title"><a href="<?php echo $item->get_permalink(); ?>" rel="external"><?php echo $item->get_title(); ?></a></div>
				<div class="latest-message-message-date"><?php echo $item->get_date('F j'); ?></div>
			</div>
			
			<div class="latest-message-right">
				<div class="latest-message-podcast"><a href="http://itunes.apple.com/podcast/denton-bible-church-sunday/id335635432" rel="external">DBC Podcast</a></div>					
				<div class="latest-message-listen"><a href="<?php echo $item->get_permalink(); ?>" rel="external">Listen</a></div>
			</div>
	<?php endforeach; ?>
	
</div>