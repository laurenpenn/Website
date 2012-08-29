<?php
// This is a podPress replacement file.

if (empty($wp)) {
	require_once('wp-config.php');
	wp('feed=atom');
}

header('Content-type: text/xml; charset=' . get_settings('blog_charset'), true);
//header('Content-type: application/atom+xml; charset=' . get_settings('blog_charset'), true);
$more = 1;
?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<feed
  xmlns="http://www.w3.org/2005/Atom"
  xmlns:creativeCommons="http://backend.userland.com/creativeCommonsRssModule"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xml:lang="<?php echo get_option('rss_language'); ?>"
  <?php do_action('atom_ns'); ?>
  >
	<id><?php bloginfo_rss('atom_url') ?></id>
	<title><?php bloginfo_rss('name') ?></title>
	<link rel="self" type="application/atom+xml" href="<?php bloginfo_rss('atom_url') ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo_rss('rss2_url') ?>" />
	<link rel="alternate" type="text/html" hreflang="<?php echo get_option('rss_language'); ?>" href="<?php bloginfo_rss('home') ?>" />
	<subtitle type="xhtml">	
		<div xmlns="http://www.w3.org/1999/xhtml">
			<strong><?php bloginfo_rss('description') ?></strong><br /><br />
			Insert more <strong>info</strong> about your blog here.
		</div>
	</subtitle>
	
	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('gmt'), false); ?></updated>
	<rights>Copyright <?php echo mysql2date('Y', get_lastpostdate('gmt'), false); ?> <?php bloginfo_rss('name') ?></rights>
	<creativeCommons:license>http://creativecommons.org/licenses/by-nc-sa/2.5/</creativeCommons:license>
	<generator uri="http://wordpress.org/" version="<?php bloginfo_rss('version'); ?>">WordPress</generator>

	<author>
		<name><?php bloginfo_rss('name') ?></name>
		<uri><?php bloginfo_rss('siteurl'); ?></uri>
	</author>
	<?php do_action('atom_head'); ?>
	<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<entry>
		<id><?php the_guid(); ?></id>
		<title type="html"><?php the_title_rss() ?></title>
		<link rel="alternate" type="text/html" hreflang="<?php echo get_option('rss_language'); ?>" href="<?php permalink_single_rss() ?>" />
		<link rel="related" type="application/rss+xml" href="<?php echo comments_rss(); ?>" />
		<author>
			<name><?php the_author() ?></name>
			<uri><?php $x = get_the_author_url(); if($x != 'http://' && $x != '') { echo $x; } else { echo bloginfo_rss('siteurl'); } ?></uri>
		</author>
		<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', $post->post_modified_gmt, false); ?></updated>
		<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
		<?php the_category_rss('rdf') ?>
		<rights>Copyright <?php echo get_post_time('Y', true); ?> <?php the_author() ?></rights>
		<creativeCommons:license>http://creativecommons.org/licenses/by-nc-sa/2.5/</creativeCommons:license>
		<summary type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<?php the_excerpt_rss(); ?>
				<br /><br />
				(<a href="<?php comments_link(); ?>">Comments</a>)
			</div>
		</summary>
	<?php if ( !get_settings('rss_use_excerpt') ) : ?>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<?php the_content('', 0, '') ?>
				<br /><br />
				(<a href="<?php comments_link(); ?>">Comments</a>)
			</div>
		</content>
	<?php endif; ?>
	<?php rss_enclosure(); ?>
	<?php do_action('atom_entry'); ?>
	</entry>
	<?php $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
</feed>
