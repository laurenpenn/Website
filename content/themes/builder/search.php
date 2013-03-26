<?php get_header(); ?>
<?php   global $more; $more = 0;
?>


	<div class="main_content_area">
    <div class="container">
        <div class="row">
        	<?php if ($data['blog_sidebar_position'] == "Left Sidebar") { ?>
            <!--Sidebar-->
            <div class="span4 blog_sidebar">
                <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?>                
                <?php endif; ?> 
            </div>
            <!--/Sidebar-->
            <?php } ?>
            <!--Page contetn-->
            <div class="span8">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="blockquote4" style="border-top:none!important;">
                	<a href="<?php the_permalink(); ?>"><h4 class="colored" style="margin-bottom:0px;"><?php the_title(); ?></h4></a><?php the_time('l, F j, Y'); ?>
					<br><br>
					<?php the_content('<h6 class="read_more"><a style="margin-top:15px;" href="'. get_permalink($post->ID) . '">'. __("Read More","builder") .'</a></h6>'); ?>
                </div>
                <?php endwhile; else: ?>
				<div class="alert">
                	<strong><?php __("Nothing was found!","builder"); ?></strong> <?php __("Change a few things up and try submitting again.","builder"); ?>
                </div>
                <?php endif; ?>
                <section style="padding:0px !important;">
                    <hr style="margin-top:0px;">
                    <?php if (function_exists('wp_corenavi')) { ?><div class="pride_pg"><?php wp_corenavi(); ?></div><?php }?>
                </section>
        	</div>
            <?php if ($data['blog_sidebar_position'] == "Right Sidebar") { ?>
            <!--Sidebar-->
            <div class="span4 blog_sidebar">
                <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?>                
                <?php endif; ?> 
            </div>
            <!--/Sidebar-->
            <?php } ?>
        </div>
    </div>
    </div>


<?php get_footer(); ?>