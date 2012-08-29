<?php
class addthis_post_metabox{

    function admin_init()
    {
        $screens = apply_filters('addthis_post_metabox_screens', array('post', 'page') );
        foreach($screens as $screen)
        {
        add_meta_box('addthis', 'AddThis', array($this, 'post_metabox'), $screen, 'side', 'default'  );
        }
        add_action('save_post', array($this, 'save_post') );
        
        add_filter('default_hidden_meta_boxes', array($this,  'default_hidden_meta_boxes' )  );
    }

    function default_hidden_meta_boxes($hidden)
    {
        $hidden[] = 'addthis';
        return $hidden;
    }

    function post_metabox(){
        global $post_id;

        if ( is_null($post_id) )
            $checked = '';
        else
        {
            $custom_fields = get_post_custom($post_id);
            $checked = ( isset ($custom_fields['addthis_exclude'])   ) ? 'checked="checked"' : '' ;
        }

        wp_nonce_field('addthis_postmetabox_nonce', 'addthis_postmetabox_nonce');
        echo '<label for="addthis_show_option">';
        _e("Remove AddThis:", 'myplugin_textdomain' );
        echo '</label> ';
        echo '<input type="checkbox" id="addthis_show_option" name="addthis_show_option" value="1" '.$checked.'>';
    }

    function save_post($post_id)
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

        if ( ! isset($_POST['addthis_postmetabox_nonce'] ) ||  !wp_verify_nonce( $_POST['addthis_postmetabox_nonce'], 'addthis_postmetabox_nonce' ) ) 
            return;

        if ( ! isset($_POST['addthis_show_option']) )
        {
            delete_post_meta($post_id, 'addthis_exclude');
        }
        else
        {
            $custom_fields = get_post_custom($post_id);
            if (! isset ($custom_fields['addthis_exclude'][0])  )
            {
                add_post_meta($post_id, 'addthis_exclude', 'true');
            }
            else
            {
                update_post_meta($post_id, 'addthis_exclude', 'true' , $custom_fields['addthis_exclude'][0]  ); 
            }
        }

    }

}

$addthis_post_metabox = new addthis_post_metabox;
add_action('admin_init', array($addthis_post_metabox, 'admin_init'));

