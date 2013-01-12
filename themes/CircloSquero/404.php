<?php get_header(); ?>
<div id="topinfobar">
    <div id="<?php if (get_option_tree( 'cs_search_d' ) == 'No' ) {echo 'pageinfoFW';} else {echo 'pageinfo'; };?>">
    <h2>404 Error</h2>
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
    <div class="separatorDots hcsdt separatorDotsPage2"></div>
    </div><div class="clear"></div>
    <div class="contentWrap">
            <div class="singlePostCS_FW">
                <h1>404 Error</h1>
                <h4>Sorry, but the page you are looking for doesn't exist. You can try to go to the <a href="<?php bloginfo('url'); ?>">Homepage</a> and find your way.</h4>
                <br/>
            </div>           
            <div class="clear"></div>          

</div>                                         

<?php get_footer(); ?>
