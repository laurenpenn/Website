<?php
/*
Plugin Name: WP Nivo Slider
Plugin URI: http://cirolini.com.br/wp-nivo-slider-en/
Description: Creates a slider using js created by Gilbert Pellegrom. WordPress plugin develop by Rafael Cirolini
Version: 3.1
Author: Rafael Cirolini
Author URI: http://cirolini.com.br/
License: GPL2
*/

/*  Copyright 2010  WP Nivo Slider - Rafael Cirolini  (email : rafael@nerdhead.com.br)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Adds actions
add_action('admin_menu', 'wpns_add_menu');
add_action('admin_init', 'wpns_reg_function' );
add_action('wp_enqueue_scripts', 'wpns_add_scripts' );

//register of default values in plugin activation
register_activation_hook( __FILE__, 'wpns_activate' );

//add post tumbnails
add_theme_support('post-thumbnails');

//create the menu panel
function wpns_add_menu() {
    $page = add_options_page('WP Nivo Slider', 'WP Nivo Slider', 'administrator', 'wpns_menu', 'wpns_menu_function');
}

//create group of variables
function wpns_reg_function() {
	register_setting( 'wpns-settings-group', 'wpns_category' );
	register_setting( 'wpns-settings-group', 'wpns_effect' );
	register_setting( 'wpns-settings-group', 'wpns_slices' );
	register_setting( 'wpns-settings-group', 'wpns_width' );
	register_setting( 'wpns-settings-group', 'wpns_height' );
	register_setting( 'wpns-settings-group', 'wpns_theme' );
}

//add default value to variables
function wpns_activate() {
	add_option('wpns_category','1');
	add_option('wpns_effect','random');
	add_option('wpns_slices','5');
	add_option('wpns_theme','default');
}

/**
 * Enqueue plugin style-file
 */
function wpns_add_scripts() {
    //Main css file
    wp_register_style( 'wpns-style', plugins_url('nivo-slider.css', __FILE__));

    //Theme css file
    $wpns_theme = get_option('wpns_theme');
    if ($wpns_theme == "bar") {
    	wp_register_style( 'wpns-style-theme', plugins_url('/themes/bar/bar.css', __FILE__));
    }
    elseif ($wpns_theme == "dark") {
	    wp_register_style( 'wpns-style-theme', plugins_url('/themes/dark/dark.css', __FILE__));
    }
    elseif ($wpns_theme == "light") {
	    wp_register_style( 'wpns-style-theme', plugins_url('/themes/light/light.css', __FILE__));
    }
    else {
	    wp_register_style( 'wpns-style-theme', plugins_url('/themes/default/default.css', __FILE__));
    }

    //enqueue css
    wp_enqueue_style( 'wpns-style' );
    wp_enqueue_style( 'wpns-style-theme' );

    wp_enqueue_script('wpns-js', plugins_url('jquery.nivo.slider.pack.js', __FILE__), array('jquery'), '3.2' );
}

function show_nivo_slider() {
?>

<?php
	$wpns_theme = get_option('wpns_theme');
	$wpns_width = get_option('wpns_width');
?>
<style>
.slider-wrapper {
    width:<?php echo get_option('wpns_width'); ?>px; /* Change this to your images width */
    height:<?php echo get_option('wpns_height'); ?>px; /* Change this to your images height */
}
#wpns_slider {
    width:<?php echo get_option('wpns_width'); ?>px; /* Change this to your images width */
    height:<?php echo get_option('wpns_height'); ?>px; /* Change this to your images height */
}
.nivoSlider {
    position:relative;
}
.nivoSlider img {
    position:absolute;
    top:0px;
    left:0px;
    display:none;
}
.nivoSlider a {
    border:0;
    display:block;
}
</style>

<script type="text/javascript">
jQuery(window).load(function() {
	jQuery('#wpns_slider').nivoSlider({
		effect:'<?php echo get_option('wpns_effect'); ?>',
		slices:<?php echo get_option('wpns_slices'); ?>,
	});
});
</script>

<div class="slider-wrapper theme-<?php echo $wpns_theme; ?>">
	<div id="wpns_slider" class="nivoSlider">
	<?php
		$category = get_option('wpns_category');
		$n_slices = get_option('wpns_slices');
	?>
	<?php query_posts( 'cat='.$category.'&posts_per_page=$n_slices' ); if( have_posts() ) : while( have_posts() ) : the_post(); ?>
		<?php if ( '' != get_the_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
				<?php the_post_thumbnail(); ?>
			</a>
		<?php endif ?>
	<?php endwhile; endif;?>
	<?php wp_reset_query();?>
	</div>
</div>
<?php }

function wpns_menu_function() {

?>

<div class="wrap">
<h2>WP Nivo Slider</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'wpns-settings-group' ); ?>
    <table class="form-table">

        <tr valign="top">
        <th scope="row">Category</th>
        <td>
        <select name="wpns_category" id="wpns_category">
			 <option value="">Select a Category</option>
 			<?php
 				$category = get_option('wpns_category');
  				$categories=  get_categories();
  				foreach ($categories as $cat) {
  					$option = '<option value="'.$cat->term_id.'"';
  					if ($category == $cat->term_id) $option .= ' selected="selected">';
  					else { $option .= '>'; }
					$option .= $cat->cat_name;
					$option .= ' ('.$cat->category_count.')';
					$option .= '</option>';
					echo $option;
  				}
 			?>
		</select>

        </tr>

    	<tr valign="top">
        <th scope="row">Number of slices</th>
        <td>
        <label>
        <input type="text" name="wpns_slices" id="wpns_slices" size="7" value="<?php echo get_option('wpns_slices'); ?>" />
        </label>
        </tr>

        <tr valign="top">
        <th scope="row">Type of Animation</th>
        <td>
        <label>
        <?php $effect = get_option('wpns_effect'); ?>
        <select name="wpns_effect" id="wpns_effect">
        	<option value="random" <?php if($effect == 'random') echo 'selected="selected"'; ?>>Random</option>
        	<option value="sliceDown" <?php if($effect == 'sliceDown') echo 'selected="selected"'; ?> >sliceDown</option>
        	<option value="sliceDownLeft" <?php if($effect == 'sliceDownLeft') echo 'selected="selected"'; ?> >sliceDownLeft</option>
        	<option value="sliceUp" <?php if($effect == 'sliceUp') echo 'selected="selected"'; ?> >sliceUp</option>
        	<option value="sliceUpLeft" <?php if($effect == 'sliceUpLeft') echo 'selected="selected"'; ?> >sliceUpLeft</option>
        	<option value="sliceUpDown" <?php if($effect == 'sliceUpDown') echo 'selected="selected"'; ?> >sliceUpDown</option>
        	<option value="sliceUpDownLeft" <?php if($effect == 'sliceUpDownLeft') echo 'selected="selected"'; ?> >sliceUpDownLeft</option>
        	<option value="fold" <?php if($effect == 'fold') echo 'selected="selected"'; ?> >fold</option>
        	<option value="fade" <?php if($effect == 'fade') echo 'selected="selected"'; ?> >fade</option>
        	<option value="slideInRight" <?php if($effect == 'slideInRight') echo 'selected="selected"'; ?> >slideInRight</option>
        	<option value="slideInLeft" <?php if($effect == 'slideInLeft') echo 'selected="selected"'; ?> >slideInLeft</option>
        	<option value="boxRandom" <?php if($effect == 'boxRandom') echo 'selected="selected"'; ?> >boxRandom</option>
        	<option value="boxRain" <?php if($effect == 'boxRain') echo 'selected="selected"'; ?> >boxRain</option>
        	<option value="boxRainReverse" <?php if($effect == 'boxRainReverse') echo 'selected="selected"'; ?> >boxRainReverse</option>
        	<option value="boxRainGrow" <?php if($effect == 'boxRainGrow') echo 'selected="selected"'; ?> >boxRainGrow</option>
        	<option value="boxRainGrowReverse" <?php if($effect == 'boxRainGrowReverse') echo 'selected="selected"'; ?> >boxRainGrowReverse</option>
        	
        </select>
        </label>
        </tr>

        <tr valign="top">
        <th scope="row">Theme</th>
        <td>
        <label>
        <?php $wpns_theme = get_option('wpns_theme'); ?>
        <select name="wpns_theme" id="wpns_theme">
        	<option value="bar" <?php if($wpns_theme == 'bar') echo 'selected="selected"'; ?>>Bar</option>
        	<option value="dark" <?php if($wpns_theme == 'dark') echo 'selected="selected"'; ?> >Dark</option>
        	<option value="default" <?php if($wpns_theme == 'default') echo 'selected="selected"'; ?> >Default</option>
        	<option value="light" <?php if($wpns_theme == 'sliceUp') echo 'selected="selected"'; ?> >Light</option>
        </select>
        </label>
        </tr>

		<tr valign="top">
			<td>This is size of yours images. This plugin do not resize images.</td>
        </tr>

		<tr valign="top">
        <th scope="row">Width</th>
        <td>
        <label>
        <input type="text" name="wpns_width" id="wpns_width" size="7" value="<?php echo get_option('wpns_width'); ?>" />px
        </label>
        </tr>

		<tr valign="top">
        <th scope="row">Height</th>
        <td>
        <label>
        <input type="text" name="wpns_height" id="wpns_height" size="7" value="<?php echo get_option('wpns_height'); ?>" />px
        </label>
        </tr>

    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>

<?php } ?>
