<?php
/*
Template Name: Portfolio 1
*/
?>

<?php get_header(); ?>

<div id="page-entry-top">
    <h1><?php echo stripslashes(get_option('hp_portfolio_title')); ?></h1>
    <h3><?php echo stripslashes(get_option('hp_portfolio_description')); ?></h3>
</div>

<div class="content-topper"></div> <!--  TRANSPARENT DROP SHADOW  -->
<div id="content-wrapper"> <!--  BEGIN CONTENT WRAPPER  -->
	<div id="gallery-content2"> <!--  BEGIN MAIN CONTENT  -->

<?php
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$portfoliocat = get_option('hp_portfoliocat',true);
	$wp_query->query('category_name='.$portfoliocat.'&paged='.$paged.'');
?>
<?php 
$col = 1;
$cols_per_row = 3;
while ($wp_query->have_posts()) : $wp_query->the_post();
if($col == 1) echo '<div class="portfolio-row">';
?>


<div class="column-<?php echo $col; ?> portfolio-item" >
    <div id="post-<?php the_ID(); ?>">
    <?php 
        $videoimage = get_post_meta( $post->ID, "video_image", true);
    ?>
    
    <?php if ( $videoimage ) : ?>
    <a class="hover-zoom-video" href="<?php echo get_post_meta( $post->ID, "video", true ); ?>" rel="prettyPhoto[pp_gal]"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "video_image", true ); ?>&amp;w=280&amp;h=160&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" /></a>
    
    <?php  else : ?>
    
    <a class="hover-zoom" href="<?php echo get_post_meta( $post->ID, "zoom_image", true ); ?>" rel="prettyPhoto[pp_gal]"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?>&amp;w=280&amp;h=160&amp;zc=1&amp;q=100" alt="<?php the_title(); ?>" /></a>
    <?php endif; ?>
    
    <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
    <?php global $more; $more = FALSE; ?><?php the_content('Read more &rarr;'); ?>
    </div> <!--END POST ID-->
</div>
    
<?php
  if($col++ >= $cols_per_row){
	$col = 1;
	echo '</div>';
  }
endwhile;
?>

<?php include(TEMPLATEPATH . '/inc/wp-pagenavi.php'); if(function_exists('wp_pagenavi')) { wp_pagenavi();} ?>
<?php $wp_query = null; $wp_query = $temp;?>

<?php get_footer(); ?>