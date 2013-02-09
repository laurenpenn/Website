<?php
// This is a podPress replacement file.

if (empty($wp)) {
	require_once('wp-config.php');
	wp('feed=atom');
}
$blog_charset = get_bloginfo('charset');
header('Content-type: application/atom+xml; charset=' . $blog_charset, true);
$more = 1;

echo '<?xml version="1.0" encoding="' . $blog_charset . '"?' . '>'; ?>
<feed
	xmlns="http://www.w3.org/2005/Atom"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:thr="http://purl.org/syndication/thread/1.0"
	xml:lang="<?php echo get_option('rss_language'); ?>"
	<?php do_action('atom_ns'); ?>
  >
	<id><?php bloginfo_rss('atom_url') ?></id>
	<title type="text"><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<link rel="self" type="application/atom+xml" href="<?php bloginfo_rss('atom_url') ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo_rss('rss2_url') ?>" />
	<link rel="alternate" type="text/html" hreflang="<?php echo get_option('rss_language'); ?>" href="<?php bloginfo_rss('home') ?>" />
	<subtitle type="text"><?php bloginfo_rss("description") ?></subtitle>
	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT')); ?></updated>

	<generator uri="http://wordpress.org/" version="<?php bloginfo_rss('version'); ?>">WordPress</generator>
	<author>
		<name><?php bloginfo_rss('name'); ?></name>
		<uri><?php bloginfo_rss('siteurl'); ?></uri>
	</author>
	<?php ######### The rights tag and other license information for this feed will be inserted via 'atom_head' action. ######### ?>
	<?php do_action('atom_head'); ?>
	<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<entry>
		<id><?php the_guid(); ?></id>
		<title type="<?php html_type_rss(); ?>"><![CDATA[<?php the_title_rss() ?>]]></title>
		<link rel="alternate" type="text/html" hreflang="<?php echo get_option('rss_language'); ?>" href="<?php permalink_single_rss() ?>" />
		<link rel="related" type="application/rss+xml" href="<?php echo comments_rss(); ?>" />
		<author>
			<name><?php the_author() ?></name>
			<uri><?php $x = get_the_author_url(); if($x != 'http://' && $x != '') { echo $x; } else { echo bloginfo_rss('siteurl'); } ?></uri>
		</author>
		<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', $post->post_modified_gmt, false); ?></updated>
		<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
		<?php the_category_rss('atom') ?>
		<summary type="<?php html_type_rss(); ?>">
			<![CDATA[<?php the_excerpt_rss(); ?><br /><br />(<a href="<?php comments_link(); ?>"><?php echo __('Comments');?></a>)]]>
		</summary>
	<?php if ( !get_settings('rss_use_excerpt') ) : ?>
		<content type="<?php html_type_rss(); ?>" xml:base="<?php the_permalink_rss() ?>">
			<![CDATA[<?php the_content('', 0, '') ?><br /><br />(<a href="<?php comments_link(); ?>"><?php echo __('Comments');?></a>)]]>
		</content>
	<?php endif; ?>
	<?php ######### The rights tag and other license information for podcast episode will be inserted via 'atom_head' action. ######### ?>
	<?php do_action('atom_entry'); ?>
	</entry>
	<?php $items_count++; if (($items_count == get_option('posts_per_rss')) && empty($m)) { break; } } } ?>
</feed>