				<?php get_header(); ?>
				<?php 
					global $more;
					$more = 0;	 
				?>

				
                <?php if (!is_front_page()){ ?>
					<?php if($data['revolution_index'] == true ) { ?>
                        <?php putRevSlider("main_slider") ?>
                    <?php } ?>
                    
                    <?php if (get_post_meta($post->ID, 'sliderr', true)) { ?>
                        <?php putRevSlider(get_post_meta($post->ID, 'sliderr', 1)) ?>
                    <?php } ?>
                    
                <?php } ?>
                
                
                <div class="main_content_area">
                    <div class="container">
                        <div class="row">
                            <div class="span12">
                                <?php if (!(have_posts())) { ?><div class="span12"><h2 class="colored uppercase"><?php __("There are no posts","builder"); ?></h2></div><?php }  ?>   
                                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                                <?php the_content(); ?>
                                <?php endwhile;  ?> 
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
				<?php get_footer(); ?>