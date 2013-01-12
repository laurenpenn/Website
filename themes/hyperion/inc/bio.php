       <!-- Show Author Bio-->
		<?php $show_bio = get_option('hp_show_bio'); ?>
         <?php if ($show_bio=='true') : ?>
        <div id="author-info"> <!-- Author Bio Area -->
            <?php echo get_avatar( get_the_author_email(), '65' ); ?>
            <h3>Written by: <?php the_author_posts_link(); ?> | <a href="<?php the_author_meta('user_url'); ?>">Visit Website</a></h3>
            <p><?php the_author_meta('description'); ?></p>
        </div>
        <?php endif; ?>
