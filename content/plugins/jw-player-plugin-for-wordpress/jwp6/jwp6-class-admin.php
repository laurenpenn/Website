<?php

class JWP6_Admin {

    public function __construct() {
        global $wp_version;
        $this->previous_version = get_option(JWP6 . 'previous_version');
        // if ( version_compare($wp_version, '3.5', '<') ) add_action('media_buttons', array($this, 'media_button'), 99);
        add_action("init", array($this, 'enqueue_scripts_and_styles'));
        add_action('admin_menu', array($this, 'admin_menu'));
        JWP6_Media::actions_and_filters();
    }

    // public function media_button() {
    //     $url = esc_url(JWP6_PLUGIN_URL . 'jwp6-media.php?TB_iframe=1');
    //     echo "<a href='$url' id='jwp6-media-button' class='thickbox' " .
    //         "title='Add a JW Player to your post'>Add a JW Player</a>";
    // }

    public static function enqueue_scripts_and_styles() {
        wp_register_script('jwp6-admin-js', JWP6_PLUGIN_URL.'js/jwp6-admin.js');
        wp_enqueue_script('jwp6-admin-js');
        wp_register_style('jwp6-admin-css', JWP6_PLUGIN_URL.'css/jwp6-admin.css');
        wp_enqueue_style('jwp6-admin-css');
    }

    public function admin_menu() {
        $admin = add_menu_page(
            "JW Players Title",                    // $page_title
            "JW Players",                          // $menu_title
            "administrator",                       // $capability
            JWP6 . "menu",                         // $menu_slug
            null,
            //array($this, 'admin_pages'),            // $function
            JWP6_PLUGIN_URL . "/img/wordpress.png"  // $icon_url
        );
        add_submenu_page(
            JWP6 . "menu",
            "JW Player Configurations", 
            "Player management", 
            "administrator", 
            JWP6 . "menu", 
            array($this, 'admin_pages')
        );
        add_submenu_page(
            JWP6 . "menu",
            "JW Player Plugin", 
            "Plugin Settings", 
            "administrator", 
            JWP6 . "menu_licensing", 
            array($this, 'admin_pages')
        );
        $purged = get_option(JWP6 . 'jwp5_purged');
        if ( $this->previous_version && $this->previous_version < 6 && ! $purged) {
            add_submenu_page(
                JWP6 . "menu",
                "Revert your plugin back to JW Player 5", 
                "Revert to JW5", 
                "administrator", 
                JWP6 . "menu_import", 
                array($this, 'admin_pages')
            );
        }
        //add_action("admin_print_scripts-$admin", "add_admin_js");
        //add_action("admin_print_scripts-$media", "add_admin_js");
    }

    public function admin_pages() {
        require_once(JWP6_PLUGIN_DIR . '/jwp6-class-admin-page.php');
        require_once( JWP6_PLUGIN_DIR . '/jwp6-class-form-field.php');
        switch ($_GET["page"]) {
            case JWP6 . "menu_import" :
                require_once (JWP6_PLUGIN_DIR . '/jwp6-class-admin-page-import.php');
                $page = new JWP6_Admin_Page_Import();
                break;
            case JWP6 . "menu_licensing" :
                require_once (JWP6_PLUGIN_DIR . '/jwp6-class-admin-page-settings.php');
                $page = new JWP6_Admin_Page_Licensing();
                break;
            default:
                require_once (JWP6_PLUGIN_DIR . '/jwp6-class-admin-page-players.php');
                $page = new JWP6_Admin_Page_Players();
                break;
        }
        $page->page_slug = $_GET["page"];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page->process_post_data($_POST);
        }
        $page->head_assets();
        $page->render();
    }

}
