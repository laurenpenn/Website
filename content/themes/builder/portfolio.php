<?php
// Template Name: Portfolio Template
$paged = 1;
if ( get_query_var('paged') ) $paged = get_query_var('paged');
if ( get_query_var('page') ) $paged = get_query_var('page');
query_posts( '&post_type=portfolio-type&paged=' . $paged );
?>
<?php
$title = get_the_title();
if ( $title == "2 Columns Portfolio")  $data['sl_portfolio_style'] = "2 Columns Portfolio";
if ( $title == "3 Columns Portfolio")  $data['sl_portfolio_style'] = "3 Columns Portfolio";
if ( $title == "4 Columns Portfolio")  $data['sl_portfolio_style'] = "4 Columns Portfolio";
if ( $title == "6 Columns Portfolio")  $data['sl_portfolio_style'] = "6 Columns Portfolio";
if ( $title == "2 Columns Portfolio")  query_posts( '&post_type=portfolio-type&posts_per_page=4&paged=' . $paged );
if ( $title == "4 Columns Portfolio")  query_posts( '&post_type=portfolio-type&posts_per_page=8&paged=' . $paged );
if ( $title == "6 Columns Portfolio")  query_posts( '&post_type=portfolio-type&posts_per_page=12&paged=' . $paged );
if ( $title == "3 Columns Portfolio")  query_posts( '&post_type=portfolio-type&posts_per_page=6&paged=' . $paged );


if ( $title == "Portfolio Right Sidebar")  $data['sl_portfolio_style'] = "Portfolio with Sidebar";
if ( $title == "Portfolio Right Sidebar")  $data['portfolio_sidebar_position'] = "Right Sidebar";
if ( $title == "Portfolio Left Sidebar")  $data['sl_portfolio_style'] = "Portfolio with Sidebar";
if ( $title == "Portfolio Left Sidebar")  $data['portfolio_sidebar_position'] = "Left Sidebar";

if ( $title == "Portfolio Left Sidebar")  query_posts( '&post_type=portfolio-type&posts_per_page=12&paged=' . $paged );
if ( $title == "Portfolio Right Sidebar")  query_posts( '&post_type=portfolio-type&posts_per_page=12&paged=' . $paged );

?>


			<?php get_header(); ?>
            
            <?php if (!is_front_page()){ ?>
				<?php if($data['revolution_index'] == true ) { ?>
                    <?php putRevSlider("main_slider") ?>
                <?php } ?>
            <?php } ?>
            
            <div class="main_content_area">
            <div class="container inner_content">
            <?php if ($data['sl_portfolio_style'] != "Portfolio with Sidebar") { ?>
                <div class="row hidden-phone">
                    <div class="span12">
                        <div id="filters">
                            <a href="#" data-filter="*" class="filter_button filter_current" >All Works</a><?php 
                                $categories = get_categories(array('type' => 'post', 'taxonomy' => 'portfolio-category')); 
                                foreach($categories as $category) {
                                $group = $category->slug;
                                  echo "<a href='#' data-filter='.$group' class='filter_button'>".$category->cat_name."</a>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
                
                <section style="padding-top:30px !important;">
                <div class="row">
                    <div class="span12">
                        <div id="portfolio" class="row">
							<?php if ($data['sl_portfolio_style'] == "4 Columns Portfolio") { ?>
								<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
                                <?php
                                    $custom = get_post_custom($post->ID);
                                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); 
                                     
                                    $cat = get_the_category($post->ID); 
                                ?>
                                <?php $cur_terms = get_the_terms( $post->ID, 'portfolio-category' ); 
										foreach($cur_terms as $cur_term){  
									};
									
									$catt = get_the_terms( $post->ID, 'portfolio-category' );
									$slugg = ''; 
									
									foreach($catt  as $vallue=>$key){
										$slugg .= strtolower($key->slug) . " ";
									}
								?>
                                
                                    <div class="span3 portfolio_item block <?php echo $slugg; ?>" data-filter="">
                                        <div class="view view-first <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                            <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"><img src="<?php echo $large_image_url[0]; ?>" alt="" /></a>
                                            <div class="mask">
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if($data['portfolio_descr_show'] == true ) { ?>
                                        <div class="descr">
	                                        <h5><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h5>
											<?php if($data['portfolio_descr_clo_text'] == true ) { ?><p class="clo"><?php echo get_post_meta($post->ID, 'port-descr', 1); ?></p><?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                
                                <?php endwhile; endif; ?>
                            <?php } ?>
        
                        
                        	<?php if ($data['sl_portfolio_style'] == "3 Columns Portfolio") { ?>
								<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
                                <?php
                                    $custom = get_post_custom($post->ID);
                                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); 
                                     
                                    $cat = get_the_category($post->ID); 
                                ?>
                                <?php $cur_terms = get_the_terms( $post->ID, 'portfolio-category' ); 
										foreach($cur_terms as $cur_term){  
									};
									
									$catt = get_the_terms( $post->ID, 'portfolio-category' );
									$slugg = ''; 
									
									foreach($catt  as $vallue=>$key){
										$slugg .= strtolower($key->slug) . " ";
									}
								?>
                                
                                    <div class="span4 portfolio_item block <?php echo $slugg; ?>"data-filter="">
                                        <div class="view view-first <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                            <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"><img src="<?php echo $large_image_url[0]; ?>" alt="" /></a>
                                            <div class="mask">
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if($data['portfolio_descr_show'] == true ) { ?>
                                        <div class="descr">
	                                        <h5><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h5>
											<?php if($data['portfolio_descr_clo_text'] == true ) { ?><p class="clo"><?php echo get_post_meta($post->ID, 'port-descr', 1); ?></p><?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                
                                <?php endwhile; endif; ?>
                            <?php } ?>
                        
                        	<?php if ($data['sl_portfolio_style'] == "2 Columns Portfolio") { ?>
								<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
                                <?php
                                    $custom = get_post_custom($post->ID);
                                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); 
                                     
                                    $cat = get_the_category($post->ID); 
                                ?>
                                <?php $cur_terms = get_the_terms( $post->ID, 'portfolio-category' ); 
										foreach($cur_terms as $cur_term){  
									};
									
									$catt = get_the_terms( $post->ID, 'portfolio-category' );
									$slugg = ''; 
									
									foreach($catt  as $vallue=>$key){
										$slugg .= strtolower($key->slug) . " ";
									}
								?>
                                
                                    <div class="span6 portfolio_item block <?php echo $slugg; ?>"data-filter="">
                                        <div class="view view-first <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                            <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"><img src="<?php echo $large_image_url[0]; ?>" alt="" /></a>
                                            <div class="mask">
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if($data['portfolio_descr_show'] == true ) { ?>
                                        <div class="descr">
	                                        <h5><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h5>
											<?php if($data['portfolio_descr_clo_text'] == true ) { ?><p class="clo"><?php echo get_post_meta($post->ID, 'port-descr', 1); ?></p><?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                
                                <?php endwhile; endif; ?>
                            <?php } ?>
                            
                            
                            <?php if ($data['sl_portfolio_style'] == "6 Columns Portfolio") { ?>
								<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
                                <?php
                                    $custom = get_post_custom($post->ID);
                                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); 
                                     
                                    $cat = get_the_category($post->ID); 
                                ?>
                                <?php $cur_terms = get_the_terms( $post->ID, 'portfolio-category' ); 
										foreach($cur_terms as $cur_term){  
									};
									
									$catt = get_the_terms( $post->ID, 'portfolio-category' );
									$slugg = ''; 
									
									foreach($catt  as $vallue=>$key){
										$slugg .= strtolower($key->slug) . " ";
									}
								?>
                                
                                    <div class="span2 portfolio_item block <?php echo $slugg; ?>"data-filter="">
                                        <div class="view view-first <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                            <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"><img src="<?php echo $large_image_url[0]; ?>" alt="" /></a>
                                            <div class="mask">
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if($data['portfolio_descr_show'] == true ) { ?>
                                        <div class="descr">
	                                        <h5><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h5>
											<?php if($data['portfolio_descr_clo_text'] == true ) { ?><p class="clo"><?php echo get_post_meta($post->ID, 'port-descr', 1); ?></p><?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                
                                <?php endwhile; endif; ?>
                            <?php } ?>
                            
                            
                            <?php if ($data['sl_portfolio_style'] == "Portfolio with Sidebar") { ?>
								<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
                                <?php
                                    $custom = get_post_custom($post->ID);
                                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); 
                                     
                                    $cat = get_the_category($post->ID); 
                                ?>
                                <?php $cur_terms = get_the_terms( $post->ID, 'portfolio-category' ); 
										foreach($cur_terms as $cur_term){  
									};
									
									$catt = get_the_terms( $post->ID, 'portfolio-category' );
									$slugg = ''; 
									
									foreach($catt  as $vallue=>$key){
										$slugg .= strtolower($key->slug) . " ";
									}
								?>
                                
                                    <div class="span3 portfolio_item block <?php echo $slugg; ?>"data-filter="">
                                        <div class="view view-first <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                            <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"><img src="<?php echo $large_image_url[0]; ?>" alt="" /></a>
                                            <div class="mask">
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if($data['portfolio_descr_show'] == true ) { ?>
                                        <div class="descr">
	                                        <h5><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h5>
											<?php if($data['portfolio_descr_clo_text'] == true ) { ?><p class="clo"><?php echo get_post_meta($post->ID, 'port-descr', 1); ?></p><?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                
                                <?php endwhile; endif; ?>
                            <?php } ?>
                            
                        
                        </div>
                    </div>
                </div>
                </section>
                <section style="padding:0px !important;">
                    <hr style="margin-top:0px;">
                    <?php if (function_exists('wp_corenavi')) { ?><div class="pride_pg"><?php wp_corenavi(); ?></div><?php }?>
                </section>
                <?php } ?>
                <?php if ($data['sl_portfolio_style'] == "Portfolio with Sidebar") { ?>
                	<div class="row">
                    	<?php if ($data['portfolio_sidebar_position'] == "Left Sidebar") { ?>
                        <div class="span3 portfolio_sidebar">
                        	<div class="well" id="filters_sidebar">
                            	<h5 style="text-transform: uppercase !important; font-weight:600; !important">Portfolio Filter</h5>
                                <hr>
                            	<a class="filter_sidebar filter_sidebar_current" href="#" data-filter="*">All Works</a><?php 
                                $categories = get_categories(array('type' => 'post', 'taxonomy' => 'portfolio-category')); 
                                foreach($categories as $category) {
                                $group = $category->slug;
                                  echo "<a href='#' data-filter='.$group'  class='filter_sidebar'>".$category->cat_name."</a>";
                                }?>
                            </div>
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Portfolio Sidebar") ) : ?>                
                            <?php endif; ?> 
                        </div>
						<?php } ?>
                    	<div class="span9">
                        	<div class="row" id="portfolio_sidebar">
                        	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
                                <?php
                                    $custom = get_post_custom($post->ID);
                                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); 
                                     
                                    $cat = get_the_category($post->ID); 
                                ?>
                                <?php $cur_terms = get_the_terms( $post->ID, 'portfolio-category' ); 
										foreach($cur_terms as $cur_term){  
									};
									
									$catt = get_the_terms( $post->ID, 'portfolio-category' );
									$slugg = ''; 
									
									foreach($catt  as $vallue=>$key){
										$slugg .= strtolower($key->slug) . " ";
									}
								?>
                                
                                    <div class="span3 portfolio_item block <?php echo $slugg; ?>"data-filter="">
                                        <div class="view view-first <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>nolink <?php } ?> <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>noinfo <?php } ?>">
                                            <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto"><img src="<?php echo $large_image_url[0]; ?>" alt="" /></a>
                                            <div class="mask">
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon + Link icon") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Zoom icon only") { ?>
                                                <a href="<?php echo $large_image_url[0]; ?>" rel="prettyPhoto" title="<?php the_title(); ?>" class="info"></a>
                                                <?php } ?>
                                                <?php if ($data['portfolio_image_hover_icons'] == "Link icon only") { ?>
                                                <a href="<?php echo get_permalink(); ?>" class="link"></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if($data['portfolio_descr_show'] == true ) { ?>
                                        <div class="descr">
	                                        <h5><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h5>
											<?php if($data['portfolio_descr_clo_text'] == true ) { ?><p class="clo"><?php echo get_post_meta($post->ID, 'port-descr', 1); ?></p><?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                
                                <?php endwhile; endif; ?>

                        	</div>
                        </div>
                        <?php if ($data['portfolio_sidebar_position'] == "Right Sidebar") { ?>
                        <div class="span3 portfolio_sidebar">
                        	<div class="well" id="filters_sidebar">
                            	<h5 style="text-transform: uppercase !important; font-weight:600; !important">Portfolio Filter</h5>
                                <hr>
                            	<a class="filter_sidebar filter_sidebar_current" href="#" data-filter="*">All Works</a><?php 
                                $categories = get_categories(array('type' => 'post', 'taxonomy' => 'portfolio-category')); 
                                foreach($categories as $category) {
                                $group = $category->slug;
                                  echo "<a href='#' data-filter='.$group'  class='filter_sidebar'>".$category->cat_name."</a>";
                                }?>
                            </div>
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Portfolio Sidebar") ) : ?>                
                            <?php endif; ?> 
                        </div>
						<?php } ?>
                    </div>
                <?php } ?>
            </div>
            </div>

        <?php get_footer(); ?>