<?php
/*
	Template Name: Full Width Page
*/
?>
<?php get_header(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="simple_page_title">
				<?php the_title(); ?>
			</div>
			<?php include (TEMPLATEPATH . '/includes/get_shortcut.php'); ?>
			
			<div class="white_line"></div>
			
			<div class="simple_page_content">

					<?php the_content(); ?>
					
					
						
						
         
						<div class="entry-utility">
							<?php if ( ('open' == $post->comment_status) && ('open' == $post->ping_status) ) :  ?>
							
							<br/><div class="page_line"></div>
							
							<?php endif; ?>
						</div>                                                                                                   
						<?php comments_template('', true); ?>                   

					

					</div>


<?php endwhile; endif;  ?>

<?php get_footer(); ?>
