<?php
portfolioScripts();
?>

<?php get_header(); ?>
<div id="topinfobar">
    <div id="<?php if (get_option_tree( 'cs_search_d' ) == 'No' ) {echo 'pageinfoFW';} else {echo 'pageinfo'; };?>">
    <?php $posttitle = get_post_meta($post->ID, 'post_title', true);  ?>
    <?php if ($posttitle) {echo '<h2>'.$posttitle.'</h2>';} else { echo '<h2>'; single_post_title(); echo '</h2>'; }; ?>
    </div>
    <?php if (get_option_tree( 'cs_search_d' ) == 'Yes' ) { ?> 
    <div id="searchTB">
        <form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" >
            <div><input type="text" value="<?php echo get_option_tree( 't_search' );?>" name="s" id="s" class="CS_searchform" onfocus="if (this.value == '<?php echo get_option_tree( 't_search' );?>') {this.value = '';}";" /><input type="submit" id="searchsubmit" value="" class="CS_searchform_button" />
            </div>
        </form>
    </div>
    <?php }; ?>
    <div class="clear"></div>
</div>
</div>
<div id="separator22"></div>
<div class="shadowBg">
    <div class="breadcrumbs">
        <?php
        if(function_exists('bcn_display'))
        {
            bcn_display();
        }
        ?>
    
    </div>

<div class="contentWrap">
<?php $playout = 'list'; ?>
    <?php if (get_option_tree( 'portfolio_layout' )=='3-Columns Sortable') { ?>
<div class="clear"></div><div class="separatorDots hcsdb"></div>
    <ul class="portfolio-list">
    <?php
    $counter = 1;
    while ( have_posts() ) : the_post(); ?>
    <?php $portfoliothumbnail = get_post_meta($post->ID, 'thumbnailurl', true); ?>
    <?php $portfolioimage = get_post_meta($post->ID, 'fullsizeurl', true); ?>
    <?php $portfoliodescription = get_post_meta($post->ID, 'portfoliodesc', true); ?>
    <li class="portfolio_wrap_small <?php $cs_terms = get_the_term_list( $post->ID, 'types', '', ' ' ); echo strip_tags($cs_terms); ?> ">
        <a href="<?php the_permalink() ?>"><h1><?php the_title();?></h1></a>

        <a rel="prettyPhoto" href="<?php echo $portfolioimage; ?>" > <img src="<?php echo $portfoliothumbnail; ?>" alt="<?php echo the_title(); ?>" width="280" height="140" /></a>
        <div class="shadowp2"></div>
        <div class="potfolioDescDesc">
            <h6><?php $cs_terms = get_the_term_list( $post->ID, 'types', '', ', ' ); echo strip_tags($cs_terms);  ?> </h6>
            <?php echo $portfoliodescription; ?><br/>
            <a href="<?php the_permalink() ?>" class="mediumbutton">View Project</a>
        </div>
    </li>
    <?php if ($counter % 3 == 0) echo '';?>
    <?php $counter++; endwhile; ?>

     </ul>                    
    
    
    
    <?php } elseif (get_option_tree( 'portfolio_layout' )=='One Column') { ?>
        <?php
        while ( have_posts() ) : the_post(); ?>
        <?php $portfoliothumbnail = get_post_meta($post->ID, 'thumbnailurl', true); ?>
        <?php $portfolioimage = get_post_meta($post->ID, 'fullsizeurl', true); ?>
        <?php $portfoliodescription = get_post_meta($post->ID, 'portfoliodesc', true); ?> 
        <div class="portfolio_wrap">
            <div class="portfolioThumbnail">
                <a rel="prettyPhoto" href="<?php echo $portfolioimage; ?>" > <img src="<?php echo $portfoliothumbnail; ?>" alt="<?php echo the_title(); ?>" width="591" height="267" /></a>
                <div class="shadow_portfolio"></div>
            </div>
            <div class="portfolioDescription">
                <a href="<?php the_permalink() ?>"><h1><?php the_title();?></h1></a>
                <h5><?php $cs_terms = get_the_term_list( $post->ID, 'types', '', ', ' ); echo strip_tags($cs_terms);  ?> </h5>
                <?php echo $portfoliodescription;   ?><br/>
                <a href="<?php the_permalink() ?>" class="mediumbutton">View Project</a>
            </div><div class="clear"></div>
        </div>
        <div class="separatorDots"></div>
        <?php endwhile; ?>    
    <?php }; ?>

<div class="clear"></div>
</div>
    <?php wp_reset_query(); ?> 
<?php get_footer(); ?>