<?php
	// Template Name: Contact Page
?>
<?php get_header(); ?>

	<!--PAGE CONTENT-->
    <div class="container" style="margin-bottom:50px;">
    	<div class="row">
        	<div class="span6">
            	<div class="slider_area">
            		<div id="map"></div>
            	</div>
            </div>
            <div class="span6">
            	<?php if (!(have_posts())) { ?><div class="span12"><h2 class="colored uppercase">There is no posts</h2></div><?php }  ?>   
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    	<?php the_content(); ?>
					<?php endwhile;  ?> 
                <?php endif; ?>
                <h4 class="sep_bg">Do you need help, support or advise?</h4>
                <div id="note"></div>
                <div id="fields">
                <form class="form" id="ajax-contact-form" action="javascript:alert('Was send!');">
                    <!--[if IE]><label for="name">Name</label><![endif]--><input type="text" id="name" name="name" class="span3" style="margin-right:25px;" placeholder="Name" />
                    <!--[if IE]><label for="email">E-mail</label><![endif]--><input id="email" type="text"  class="span3" name="email" placeholder="Email" />
                    <!--[if IE]><label for="message">Message</label><![endif]--><textarea id="message" type="text" name="message" placeholder="Message" rows="8" class="span6"></textarea>
                    <button type="submit"  class="btn btn-info btn-small">Send message</button>
                </form>
                </div>
            </div>
        </div>
    </div>
    <!--PAGE CONTENT-->

<?php get_footer(); ?>