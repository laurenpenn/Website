<?php
/**
 * RSS2 Podcast Feed Template for displaying RSS2 Podcast Posts feed.
 *
 * @package WordPress
 */
 
 $FeaturedPodcastID = 0;
 $iTunesFeatured = get_option('powerpress_itunes_featured');
 $feed_slug = get_query_var('feed');
 if( !empty($iTunesFeatured[ $feed_slug ]) )
 {
		$FeaturedPodcastID = $iTunesFeatured[ $feed_slug ];
		$GLOBALS['powerpress_feed']['itunes_feature'] = true; // So any custom order value is not used when looping through the feeds.
 }
 $iTunesOrderNumber = 2; // One reserved for featured episode
 
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
<?php
		
		$ItemCount = 0;
	?>
<?php while( have_posts()) : the_post(); ?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php
		if( empty($GLOBALS['powerpress_feed']['feed_maximizer_on']) ) // If feed maximizer off
		{
		?>
		<comments><?php comments_link_feed(); ?></comments>
		<dc:creator><?php the_author() ?></dc:creator>
<?php the_category_rss('rss2') ?>
<?php if (get_option('rss_use_excerpt')) : ?>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php else : ?>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php if ( strlen( $post->post_content ) > 0 ) : ?>
		<content:encoded><![CDATA[<?php the_content_feed('rss2') ?>]]></content:encoded>
<?php else : ?>
		<content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
<?php endif; ?>
<?php endif; ?>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link(null, 'rss2') ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
		<?php
		}
		else // If feed maximizer on
		{ // itunes does not like CDATA, so we're changing it to the other method...
		?>
		<description><?php echo esc_html( get_the_excerpt() ); ?></description>
		<?php
		}
		?>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'); ?>
	<?php
	if( !empty($iTunesFeatured[ $feed_slug ]) )
	{
		echo "\t<itunes:order>";
		if( $FeaturedPodcastID == get_the_ID() )
		{
			echo 1;
			$FeaturedPodcastID = 0;
		}
		else
		{
			echo $iTunesOrderNumber;
			$iTunesOrderNumber++;
		}
		echo "</itunes:order>\n";
	}
	
	// Decide based on count if we want to flip on the feed maximizer...
	$ItemCount++;
	
	if( empty($GLOBALS['powerpress_feed']['feed_maximizer_on']) && $ItemCount >= 10 && !empty($GLOBALS['powerpress_feed']['maximize_feed']) )
	{
		$GLOBALS['powerpress_feed']['feed_maximizer_on'] = true; // All future items will be minimized in order to maximize episode count
	}
	
	?>
	</item>
<?php endwhile; ?>
<?php 
	
	if( !empty($FeaturedPodcastID) )
	{
		query_posts( array('p'=>$FeaturedPodcastID) );
		if( have_posts())
		{
			the_post(); 
	// Featured podcast epiosde, give it the highest itunes:order value...
?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
		<description><?php echo esc_html( get_the_excerpt() ); ?></description>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'); ?>
	<?php
	echo "\t<itunes:order>";
	echo 1;
	echo "</itunes:order>\n";
	?>
	</item>
<?php 
		}
		wp_reset_query();
	}
?>
</channel>
</rss>
