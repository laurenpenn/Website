<?php
	// Template Name: With Rigth SideBar
?>
<?php get_header(); ?>
<?php 
	global $more;
	$more = 0;	 
?>


<?php if (!is_front_page()){ ?>
	<?php if($data['revolution_index'] == true ) { ?>
		<?php putRevSlider("main_slider") ?>
	<?php } ?>
<?php } ?>


<div class="main_content_area">
	<div class="container">
		<div class="row">
			<div class="span9">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
				<?php endwhile;  ?> 
				<?php endif; ?>
			</div>
            
            <div class="span3 page_sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Right Sidebar") ) : ?>                
                <?php endif; ?> 
            </div>
            
		</div>
	</div>
</div>
<?php get_footer(); ?>