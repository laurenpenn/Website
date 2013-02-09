<?php get_header(); ?>

	<div class="simple_page_title">
				<?php the_title(); ?>
			</div>
			<?php include (TEMPLATEPATH . '/includes/get_shortcut.php'); ?>
			
			<div class="white_line"></div>
					
					<!-- begin page content -->
					<div class="page_contact">
					
					<?php if (have_posts()) : ?>
					
					<div class="navigation" style="padding-bottom:10px;">
						<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
						<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
					</div>
					
					<?php while (have_posts()) : the_post(); ?>
					
					<div style="padding-bottom:25px;">
						<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						<small><?php the_time('l, F jS, Y') ?></small>

						<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
					</div>
					
					<div class="page_line"></div>
					
					<?php endwhile; ?>
					
					<div class="navigation" style="padding-top:10px; padding-bottom:10px;">
						<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
						<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
					</div>
					
					<?php else : ?>
					<br/>
					<br/>
					<br/>
					<div class="page_content" style="text-align:center;">
						<h1><?php echo get_option('notfound', 'No posts found. Try a different search?'); ?></h1>
					</div>
					<br/>
					<br/>
					<br/>
					<?php endif; ?>
					
					</div>
					<!-- end page content -->

<?php get_footer(); ?>
