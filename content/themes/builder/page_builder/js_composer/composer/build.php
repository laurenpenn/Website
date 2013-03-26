<?php
/**
 * WPBakery Visual Composer build plugin
 *
 * @package WPBakeryVisualComposer
 *
 */


if (!defined('ABSPATH')) die('-1');

class WPBakeryVisualComposerSetup extends WPBakeryVisualComposerAbstract {
    protected $composer;

    public function __construct() {
    }

    public function init($settings) {
        parent::init($settings);
        $this->composer = WPBakeryVisualComposer::getInstance();

        $this->composer->createColumnShortCode(); // Refactored
        $this->addAction('init', 'setUp');
    }
    
    public function setUp() {
        global $vc_as_theme;
        //  old version: if ( preg_match('/'.preg_quote(VC_THEME_DIR, '/').'/', preg_replace('/\\\/', '/', self::$config['APP_ROOT']) ) )
        if ( $vc_as_theme ) {
            $this->composer->setTheme();
            $this->setUpTheme();
            load_theme_textdomain('js_composer', locate_template(self::$config['APP_DIR'].'locale/'));
        } else {
            $this->composer->setPlugin();
            $this->setUpPlugin();
            load_plugin_textdomain( 'js_composer', false,  self::$config['APP_DIR'].'locale/' );
        }
        if ( function_exists( 'add_theme_support' ) ) {
            add_theme_support( 'post-thumbnails');
        }
        add_post_type_support( 'page', 'excerpt' );
        $this->composer->addFilter('the_excerpt', 'excerptFilter');
    }

    public function setUpPlugin() {
        if (!is_admin()) {
            $this->addAction('template_redirect', 'frontCss');
            $this->addAction('template_redirect', 'frontJsRegister');
            $this->addAction('wp_enqueue_scripts', 'frontendJsLoad');
            $this->addFilter('the_content', 'fixPContent', 11); //If weight is higher then 11 it doesn work... why?
            $this->addFilter('body_class', 'jsComposerBodyClass');
        }
    }

    public function fixPContent($content = null) {
        //$content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );
        $s = array(
                    '/'.preg_quote('</div>', '/').'[\s\n\f]*'.preg_quote('</p>', '/').'/i',
                    '/'.preg_quote('<p>', '/').'[\s\n\f]*'.preg_quote('<div ', '/').'/i'
                  );
        $r = array("</div>", "<div ");
        $content = preg_replace($s, $r, $content);
        return $content;
    }
    public function frontendJsLoad() {
        wp_enqueue_script( 'jquery' );
    }

    public function frontCss() {

        //wp_register_style( 'bootstrap', $this->composer->assetURL( 'bootstrap/css/bootstrap.css' ), false, WPB_VC_VERSION, 'screen' );
        wp_register_style( 'ui-custom-theme', $this->composer->assetURL( 'ui-custom-theme/jquery-ui-' . WPB_JQUERY_UI_VERSION . '.custom.css' ), false, WPB_VC_VERSION, 'screen');
        wp_register_style( 'flexslider', $this->composer->assetURL( 'js/flexslider/flexslider.css' ), false, WPB_VC_VERSION, 'screen' );
        wp_register_style( 'nivo-slider-css', $this->composer->assetURL( 'nivoslider/nivo-slider.css' ), false, WPB_VC_VERSION, 'screen' );
        wp_register_style( 'prettyphoto', $this->composer->assetURL( 'js/prettyphoto/css/prettyPhoto.css' ), false, WPB_VC_VERSION, 'screen' );
        wp_register_style( 'isotope-css', $this->composer->assetURL( 'isotope.css' ), false, WPB_VC_VERSION, 'screen' );
        wp_register_style( 'js_composer_front', $this->composer->assetURL( 'css/js_composer_front.css' ), false, WPB_VC_VERSION, 'screen' );
    }

    public function frontJsRegister() {
        wp_register_script( 'jquery_ui_tabs_rotate', $this->composer->assetURL( 'jquery-ui-tabs-rotate/jquery-ui-tabs-rotate.js' ), array( 'jquery', 'jquery-ui-tabs' ), WPB_VC_VERSION, true);
        wp_register_script( 'wpb_composer_front_js', $this->composer->assetURL( 'js_composer_front.js' ), array( 'jquery' ), WPB_VC_VERSION, true);

        wp_register_script( 'tweet', $this->composer->assetURL( 'js/jquery.tweet.js' ), array( 'jquery' ), WPB_VC_VERSION, true);
        wp_register_script( 'isotope', $this->composer->assetURL( 'isotope/jquery.isotope.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true);
        wp_register_script( 'jcarousellite', $this->composer->assetURL( 'js/jcarousellite_1.0.1.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true);

        //wp_register_script( 'cycle', $this->composer->assetURL( 'js/jquery.cycle.all.js' ), array( 'jquery' ));
        wp_register_script( 'nivo-slider', $this->composer->assetURL( 'nivoslider/jquery.nivo.slider.pack.js' ), array( 'jquery' ), WPB_VC_VERSION, true);
        wp_register_script( 'flexslider', $this->composer->assetURL( 'flexslider/jquery.flexslider-min.js' ), array( 'jquery' ), WPB_VC_VERSION, true);
        wp_register_script( 'prettyphoto', $this->composer->assetURL( 'js/prettyphoto/js/jquery.prettyPhoto.js' ), array( 'jquery' ), WPB_VC_VERSION, true);

        //wp_register_script( 'jcarousellite', $this->composer->assetURL( 'js/jcarousellite_1.0.1.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true);
        //wp_register_script( 'anythingslider', $this->composer->assetURL( 'js/jquery.anythingslider.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true);
    }

    public function setUpTheme() {
        $this->setUpPlugin();
    }


    public function jsComposerBodyClass($classes) {
        $classes[] = 'wpb-js-composer js-comp-ver-'.WPB_VC_VERSION;
        $disable_responsive = WPBakeryVisualComposerSettings::get('not_responsive_css');
        if($disable_responsive!=='1')$classes[] = 'vc_responsive';
        return $classes;
    }
}

/* Setup for admin */

class WPBakeryVisualComposerSetupAdmin extends WPBakeryVisualComposerSetup {
    public function __construct() {
        parent::__construct();
    }

    public function setupNotifications() {
        // New version notification.
        $new_version = get_option('wpb_js_composer_show_new_version_message');
        if($new_version!==false && !empty($new_version) && $new_version != WPB_VC_VERSION) {
            $this->addACtion('admin_notices', 'adminNoticeNewVersion');
        }
    }
    public function adminNoticeNewVersion() {
        $version = get_option('wpb_js_composer_show_new_version_message');
        if(!empty($version))
            echo '<div class="updated"><p>'.sprintf(__('There is a new version of Visual Composer available. <a href="http://bit.ly/vcomposer" target="_blank">Download version <b>%s</b></a>.', 'js_composer'), $version).'</p></div>';
    }
    /* Setup plugin composer */

    public function setUpPlugin() {
    	global $current_user;
    	get_currentuserinfo();
    	
        /** @var $settings - get use group access rules */
        $settings = WPBakeryVisualComposerSettings::get('groups_access_rules');

        parent::setUpPlugin();
        $show = true;
        foreach($current_user->roles as $role) {
	        if(isset($settings[$role]['show']) && $settings[$role]['show']==='no') {
		        $show = false;
		        break;
	        }
        }
        
        if ($show) {
            $this->composer->addAction( 'edit_post', 'saveMetaBoxes' );
            $this->composer->addAction( 'wp_ajax_wpb_get_element_backend_html', 'elementBackendHtmlJavascript_callback' );
            $this->composer->addAction( 'wp_ajax_wpb_get_convert_elements_backend_html', 'Convert2NewVersionJavascript_callback' );
            $this->composer->addAction( 'wp_ajax_wpb_get_row_element_backend_html', 'elementRowBackendHtmlJavascript_callback' );
            $this->composer->addAction( 'wp_ajax_wpb_shortcodes_to_visualComposer', 'shortCodesVisualComposerJavascript_callback' );
            $this->composer->addAction( 'wp_ajax_wpb_show_edit_form', 'showEditFormJavascript_callback' );
            $this->composer->addAction('wp_ajax_wpb_save_template', 'saveTemplateJavascript_callback');
            $this->composer->addAction('wp_ajax_wpb_load_template', 'loadTemplateJavascript_callback');
            $this->composer->addAction('wp_ajax_wpb_delete_template', 'deleteTemplateJavascript_callback');
            $this->addAction( 'admin_init', 'jsComposerEditPage', 5 );
        }
        // Add specific CSS class by filter
        $this->addFilter('body_class', 'jsComposerBodyClass');
        $this->addFilter( 'get_media_item_args', 'jsForceSend' );

        $this->addAction( 'admin_init', 'composerRedirect' );


        $this->addAction( 'admin_init', 'registerCss' );
        $this->addAction( 'admin_init', 'registerJavascript' );

        $this->addAction( 'admin_menu','composerSettings' );

        $this->addAction( 'admin_print_scripts-post.php', 'editScreen_js' );
        $this->addAction( 'admin_print_scripts-post-new.php', 'editScreen_js' );

        /* Create Media tab for images */
        $this->composer->createImagesMediaTab();


    }

    /*
     * Set up theme filters and actions
     *
     */
    public function setUpTheme() {
        $this->setUpPlugin();
        $this->addAction('admin_init', 'themeScreen_js');
    }


    public function jsForceSend($args) {
        $args['send'] = true;
        return $args;
    }

    public function themeScreen_js() {
        wp_enqueue_script('wpb_js_theme_admin');
    }

    public function editScreen_js() {

        if(in_array(get_post_type(), $this->composer->getPostTypes())) {
        	$this->setupNotifications();

            wp_enqueue_style('farbtastic');
            wp_enqueue_style('bootstrap');
            wp_enqueue_style('bootstrap_modals');
            wp_enqueue_style('ui-custom-theme');
            wp_enqueue_style('isotope-css');
            wp_enqueue_style('animate-css');
            wp_enqueue_style('js_composer');
            WPBakeryShortCode_Settings::enqueueCss();

            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('farbtastic');

            wp_enqueue_script('bootstrap-js');
            wp_enqueue_script('isotope');
            wp_enqueue_script('wpb_bootstrap_modals_js');
            wp_enqueue_script('wpb_scrollTo_js');
            wp_enqueue_script('wpb_php_js');
            WPBakeryShortCode_Settings::enqueueJs();
            // js composer js app {{
            wp_enqueue_script('wpb_jscomposer_composer_js');
            wp_enqueue_script('wpb_jscomposer_shortcode_js');
            wp_enqueue_script('wpb_jscomposer_modal_js');
            wp_enqueue_script('wpb_jscomposer_templates_js');
            wp_enqueue_script('wpb_jscomposer_stage_js');
            wp_enqueue_script('wpb_jscomposer_layout_js');
            wp_enqueue_script('wpb_jscomposer_row_js');
            wp_enqueue_script('wpb_jscomposer_settings_js');
            wp_enqueue_script('wpb_jscomposer_media_editor_js');
            // }}
            wp_enqueue_script('wpb_js_composer_js');
        }
    }

    public function registerJavascript() {
        wp_register_script( 'isotope', $this->composer->assetURL( 'isotope/jquery.isotope.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true);
        wp_register_script('wpb_bootstrap_modals_js', $this->composer->assetURL( 'bootstrap_modals/js/bootstrap.min.js' ), array('jquery'), WPB_VC_VERSION, true);
        wp_register_script('wpb_scrollTo_js', $this->composer->assetURL( 'scrollTo/jquery.scrollTo.min.js' ), array('jquery'), WPB_VC_VERSION, true);
        wp_register_script('wpb_php_js', $this->composer->assetURL( 'js/php.default.min.js' ), array('jquery'), WPB_VC_VERSION, true);

        wp_register_script('wpb_jscomposer_composer_js', $this->composer->assetURL( 'js/jscomposer/composer.js' ), array('jquery'), WPB_VC_VERSION, true);
        wp_register_script('wpb_jscomposer_shortcode_js', $this->composer->assetURL( 'js/jscomposer/shortcode.js' ), array('wpb_jscomposer_composer_js', 'wpb_bootstrap_modals_js', 'isotope', 'wpb_scrollTo_js'), WPB_VC_VERSION, true);

        wp_register_script('wpb_jscomposer_modal_js', $this->composer->assetURL( 'js/jscomposer/modal.js' ), array('wpb_jscomposer_shortcode_js'), WPB_VC_VERSION, true);
        wp_register_script('wpb_jscomposer_templates_js', $this->composer->assetURL( 'js/jscomposer/templates.js' ), array('wpb_jscomposer_modal_js'), WPB_VC_VERSION, true);
        wp_register_script('wpb_jscomposer_stage_js', $this->composer->assetURL( 'js/jscomposer/stage.js' ), array('wpb_jscomposer_modal_js'), WPB_VC_VERSION, true);
        wp_register_script('wpb_jscomposer_layout_js', $this->composer->assetURL( 'js/jscomposer/layout.js' ), array('wpb_jscomposer_stage_js'), WPB_VC_VERSION, true);
        wp_register_script('wpb_jscomposer_row_js', $this->composer->assetURL( 'js/jscomposer/row.js' ), array('wpb_jscomposer_layout_js'), WPB_VC_VERSION, true);
        wp_register_script('wpb_jscomposer_settings_js', $this->composer->assetURL( 'js/jscomposer/settings.js' ), array('wpb_jscomposer_layout_js'), WPB_VC_VERSION, true);
        wp_register_script('wpb_jscomposer_media_editor_js', $this->composer->assetURL( 'js/jscomposer/media-editor.js' ), false, WPB_VC_VERSION, true);
        wp_register_script('wpb_js_composer_js', $this->composer->assetURL( 'js_composer.js' ), array('wpb_jscomposer_layout_js'), WPB_VC_VERSION, true);

        wp_localize_script( 'wpb_js_composer_js', 'i18nLocale', array(
            'add_remove_picture' => __( 'Add/remove picture', 'js_composer' ),
            'finish_adding_text' => __( 'Finish Adding Images', 'js_composer' ),
            'add_image' => __( 'Add Image', 'js_composer' ),
            'add_images' => __( 'Add Images', 'js_composer' ),
            'main_button_title' => __( 'Visual Composer', 'js_composer' ),
            'main_button_title_revert' => __( 'Classic editor', 'js_composer' ),
            'please_enter_templates_name' => __('Please enter templates name', 'js_composer'),
            'confirm_deleting_template' => __('Confirm deleting "{template_name}" template, press Cancel to leave. This action cannot be undone.', 'js_composer'),
            'press_ok_to_delete_section' => __('Press OK to delete section, Cancel to leave', 'js_composer'),
            'press_ok_to_pop_section' => __('Press OK to pop (move) section to the top level, Cancel to leave', 'js_composer'),
            'drag_drop_me_in_column' => __('Drag and drop me in the column', 'js_composer'),
            'press_ok_to_delete_tab' => __('Press OK to delete "{tab_name}" tab, Cancel to leave', 'js_composer'),
            'slide' => __('Slide', 'js_composer'),
            'tab' => __('Tab', 'js_composer'),
            'please_enter_new_tab_title' => __('Please enter new tab title', 'js_composer'),
            'press_ok_delete_section' => __('Press OK to delete "{tab_name}" section, Cancel to leave', 'js_composer'),
            'section_default_title' => __('Section', 'js_composer'),
            'please_enter_section_title' => __('Please enter new section title', 'js_composer'),
            'error_please_try_again' => __('Error. Please try again.', 'js_composer'),
            'if_close_data_lost' => __('If you close this window all shortcode settings will be lost. Close this window?', 'js_composer'),
            'header_select_element_type' => __('Select element type', 'js_composer'),
            'header_media_gallery' => __('Media gallery', 'js_composer'),
            'header_element_settings' => __('Element settings', 'js_composer'),
            'add_tab' => __('Add tab', 'js_composer'),
            'are_you_sure_convert_to_new_version' => __('Are you sure you want to convert to new version?', 'js_composer'),
            // Media editor
            'set_image' => __('Set Image', 'js_composer')
        ) );

        wp_register_script('bootstrap-js', $this->composer->assetURL( 'bootstrap/js/bootstrap.min.js' ), false, WPB_VC_VERSION, true);
        wp_register_script('wpb_js_theme_admin', $this->composer->assetURL( 'theme_admin.js' ), array('jquery'), WPB_VC_VERSION, true);

    }

    public function registerCss() {
        wp_register_style( 'bootstrap', $this->composer->assetURL( 'bootstrap/css/bootstrap.css' ), false, WPB_VC_VERSION, false );
        wp_register_style( 'bootstrap_modals', $this->composer->assetURL( 'bootstrap_modals/css/bootstrap.modals.css' ), false, WPB_VC_VERSION, false );

        wp_register_style( 'ui-custom-theme', $this->composer->assetURL( 'ui-custom-theme/jquery-ui-' . WPB_JQUERY_UI_VERSION . '.custom.css' ), false, WPB_VC_VERSION, false );
        wp_register_style( 'isotope-css', $this->composer->assetURL( 'isotope.css' ), false, WPB_VC_VERSION, 'screen' );
        wp_register_style( 'animate-css', $this->composer->assetURL( 'css/animate.css' ), false, WPB_VC_VERSION, 'screen' );

        wp_register_style( 'js_composer', $this->composer->assetURL( 'js_composer.css' ), array('isotope-css', 'animate-css', 'bootstrap_modals'), WPB_VC_VERSION, false );

    }
    /* Call to generate main template editor */

    public function jsComposerEditPage() {
        $pt_array = $this->composer->getPostTypes();
        foreach ($pt_array as $pt) {
            add_meta_box( 'wpb_visual_composer', __('Visual Composer', "js_composer"), Array($this->composer->getLayout(), 'output'), $pt, 'normal', 'high');
        }
    }

    /* Add option to Settings menu */
    public function composerSettings() {
        if ( current_user_can('manage_options') && $this->composer->isPlugin()) {
            // add_options_page(__("Visual Composer Settings", "js_composer"), __("Visual Composer", "js_composer"), 'install_plugins', "wpb_vc_settings", array($this, "composerSettingsMenuHTML"));
            $this->composer->settings = new WPBakeryVisualComposerSettings($this->composer);
            $this->composer->settings->init();
        } elseif($this->composer->isTheme() && current_user_can('edit_theme_options')) {
            $this->composer->settings = new WPBakeryVisualComposerSettings($this->composer);
            $this->composer->settings->init();
        }
    }




    /** Include plugin settings page
     *
     * @deprecated
     *
     * */

    public static function composerSettingsMenuHTML() {
        include_once( self::$config['COMPOSER'] . 'settings/' . 'js_composer_settings_menu.php' );
    }


    public function composerRedirect() {
        if ( get_option('wpb_js_composer_do_activation_redirect', false) ) {
            delete_option('wpb_js_composer_do_activation_redirect');
            wp_redirect(admin_url().'options-general.php?page=wpb_vc_settings');
        }
    }
}


