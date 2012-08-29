<?php
/*
Plugin Name: Easy Facebook Like Button
Plugin URI: http://jonefox.com/blog
Description: The simplest way to add Facebook Like Buttons to your WordPress blog.  One click setup and start getting Facebook likes for posts on your blog.
Version: 1.02
Author: jonefox
Author URI: http://jonefox.com/blog
*/

define( 'FBLB_SERVICE_URL', 'http://jonefox.com/fb-like/rest.php' );

function fblb_save_option( $name, $value ) {
        global $wpmu_version;
        
        if ( false === get_option( $name ) && empty( $wpmu_version ) ) // Avoid WPMU options cache bug
                add_option( $name, $value, '', 'no' );
        else
                update_option( $name, $value );
}

function fblb_add_facebook_button( $content ) {
    global $post;
    
    $permalink = urlencode( get_permalink( $post->ID ) );
    
    $fb_html = '<iframe src="http://www.facebook.com/plugins/like.php?href=' . $permalink .'&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:80px;" allowTransparency="true"></iframe>';
    if ( get_option( 'fblb_add_before') )
        return $fb_html . $content;
    else
        return $content . $fb_html;
}

add_filter( "the_content", "fblb_add_facebook_button" );

function fblb_option_settings_api_init() {
        add_settings_field( 'fblb_setting', 'Facebook Like Button', 'fblb_setting_callback_function', 'reading', 'default' );
        register_setting( 'reading', 'fblb_setting' );
}

function fblb_setting_callback_function() {
    if ( get_option( 'fblb_add_before') ) {
        $fb_below = '';
        $fb_above = ' checked';
    } else {
        $fb_below = ' checked';
        $fb_above = '';
    }
    
    echo "Show Facebook like button: <input type='radio' name='opt_facebook_button' value='0' id='opt_facebook_button_below'$fb_below /> <label for='opt_facebook_button_below'>Below The Post</label> <input style='margin-left:15px' type='radio' name='opt_facebook_button' value='1' id='opt_facebook_button_above'$fb_above /> <label for='opt_facebook_button_above'>Above The Post</label>";
}

if ( isset( $_POST['opt_facebook_button'] ) ) {
        fblb_save_option( 'fblb_add_before', (bool) $_POST['opt_facebook_button'] );
}

add_action( 'admin_init',  'fblb_option_settings_api_init' );

function fblb_register_site() {
        global $current_user;
        
        $site = array( 'url' => get_option( 'siteurl' ), 'title' => get_option( 'blogname' ), 'user_email' => $current_user->user_email );
        
        $response = fblb_send_data( 'add-site', $site );
        if ( strpos( $response, '|' ) ) {
                // Success
                $vals = explode( '|', $response );
                $site_id = $vals[0];
                $site_key = $vals[1];
                if ( isset( $site_id ) && is_numeric( $site_id ) && strlen( $site_key ) > 0 ) {
                        fblb_save_option( 'fblb_site_id', $site_id );
                        fblb_save_option( 'fblb_site_key', $site_key );
                        return true;
                }
        }
        
        return $response;
}

function fblb_rest_handler() {
        // Basic ping
        if ( isset( $_GET['fblb_ping'] ) || isset( $_POST['fblb_ping'] ) )
                return fblb_ping_handler();
}

add_action( 'init', 'fblb_rest_handler' );

function fblb_ping_handler() {
        if ( !isset( $_GET['fblb_ping'] ) && !isset( $_POST['fblb_ping'] ) )
                return false;
        
        $ping = ( $_GET['fblb_ping'] ) ? $_GET['fblb_ping'] : $_POST['fblb_ping'];
        if ( strlen( $ping ) <= 0 )
                exit;
        
        if ( $ping != get_option( 'fblb_site_key' ) )
                exit;
        
        echo sha1( $ping );
        exit;
}

function fblb_notice() {
        if ( get_option( 'fblb_has_shown_notice') )
                return;
        
          
        fblb_save_option( 'fblb_has_shown_notice', true );
        return;
}

add_action( 'admin_notices', 'fblb_notice' );

function fblb_activate() {
        fblb_register_site();
}

register_activation_hook( __FILE__, 'fblb_activate' );

if ( !function_exists( 'wp_remote_get' ) && !function_exists( 'get_snoopy' ) ) {
        function get_snoopy() {
                include_once( ABSPATH . '/wp-includes/class-snoopy.php' );
                return new Snoopy;
        }
}

function fblb_http_query( $url, $fields ) {
        $results = '';
        if ( function_exists( 'wp_remote_get' ) ) {
                // The preferred WP HTTP library is available
                $url .= '?' . http_build_query( $fields );
                $response = wp_remote_get( $url );
                if ( !is_wp_error( $response ) )
                        $results = wp_remote_retrieve_body( $response );
        } else {
                // Fall back to Snoopy
                $snoopy = get_snoopy();
                $url .= '?' . http_build_query( $fields );
                if ( $snoopy->fetch( $url ) )
                        $results = $snoopy->results;
        }
        return $results;
}

function fblb_send_data( $action, $data_fields ) {
        $data = array( 'action' => $action, 'data' => base64_encode( json_encode( $data_fields ) ) );
        
        return fblb_http_query( FBLB_SERVICE_URL, $data );
}
?>