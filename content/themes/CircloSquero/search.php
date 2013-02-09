<?php get_header(); ?>
<div id="topinfobar">
    <div id="<?php if (get_option_tree( 'cs_search_d' ) == 'No' ) {echo 'pageinfoFW';} else {echo 'pageinfo'; };?>">
    <h2><?php echo get_option_tree( 't_search_results' );?> "<?php the_search_query(); ?>"</h2>
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
<?php
global $query_string;

$query_args = explode("&", $query_string);
$search_query = array();

foreach($query_args as $key => $string) {
	$query_split = explode("=", $string);
	$search_query[$query_split[0]] = $query_split[1];
} // foreach

$search = new WP_Query($search_query);
?>

    <div class="separatorDots hcsdt separatorDotsPage2"></div>
    </div><div class="clear"></div>
    <div class="contentWrap">
            <div class="singlePostCS_FW">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                  
                                <h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>                                                                                                                                                                                    
                                <?php the_excerpt(); ?>
                                <div class="separatorDots hcsdt separatorDotsPage2"></div>
                    <div class="clear"></div>
                    <?php endwhile; else: ?>
                    <p><?php echo get_option_tree( 't_no_results' );?></p>
                <?php endif; ?>
            </div>           
            <div class="clear"></div>          
                <div id="paginationCS">

                        <?php if (  $wp_query->max_num_pages > 1 ) { 
                            circlosquero_pagination();
                        }; ?>

                </div>
</div>                                         

<?php get_footer(); ?>
