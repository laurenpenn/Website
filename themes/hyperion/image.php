<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div id="entry-top">
    <h1><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> : <?php the_title(); ?></h1>
    <div class="post-meta"><p>By <?php  the_author_posts_link(''); ?> in <?php the_category(', ') ?> | <?php comments_number('0','1','%'); ?> <a href="#comments" class="anchorLink">Comments</a> <span class="edit"><?php edit_post_link('Edit', '| ', ''); ?></span></p></div>
    <div class="date">
        <span class="day"><?php the_time('j'); ?></span>
        <span class="month"><?php the_time('F'); ?></span>
        <span class="year"><?php the_time('Y'); ?></span>
    </div>
</div>

<div class="content-topper"></div> <!--  TRANSPARENT DROP SHADOW  -->
<div id="content-wrapper"> <!--  BEGIN CONTENT WRAPPER  -->
	<div id="content"> <!--  BEGIN MAIN CONTENT  --> 

		<div class="post" id="post-<?php the_ID(); ?>">
			<p><a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a></p>
			<?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the "caption" ?>
			<?php the_content('<p>Read the rest of this entry &raquo;</p>'); ?>
			<?php previous_image_link() ?> | <?php next_image_link() ?>
            
	<?php comments_template('', true); ?> <!--DISPLAYS COMMENTS TEMPLATE-->

	<?php endwhile; else: ?>

		<p>Sorry, no attachments matched your criteria.</p>

<?php endif; ?>
</div> <!--END OF POST --> 

<?php get_sidebar('Sidebar'); ?>
<?php get_footer(); ?>