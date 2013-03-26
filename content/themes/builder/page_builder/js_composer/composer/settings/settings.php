<?php
/**
 * WPBakery Visual Composer Plugin
 *
 * @package VPBakeryVisualComposer
 *
 */


class WPBakeryVisualComposerSettings {

    protected $option_group = 'wpb_js_composer_settings';
    protected $page = "wpb_vc_settings";
    protected static $field_prefix = 'wpb_js_';
    protected $composer;

    public function __construct($composer) {
        $this->composer = $composer;
    }
    /**
     * Init settings page && menu item
     */
    public function init() {
        $page = add_options_page(__("Visual Composer Settings", "js_composer"),
            __("Visual Composer", "js_composer"),
            'install_plugins',
            $this->page,
            array(&$this, 'output'));

        add_action("load-$page", array(&$this, 'admin_load'));

        if( WPBakeryVisualComposer::getInstance()->isPlugin() ) {
            register_setting($this->option_group, self::$field_prefix.'content_types', array($this, 'sanitize_post_types_callback'));
        } else {
            register_setting($this->option_group, self::$field_prefix.'theme_content_types', array($this, 'sanitize_post_types_callback'));
        }
        register_setting($this->option_group, self::$field_prefix.'groups_access_rules', array($this, 'sanitize_group_access_rules_callback'));

        register_setting($this->option_group, self::$field_prefix.'not_responsive_css', array($this, 'sanitize_not_responsive_css_callback'));
        register_setting($this->option_group, self::$field_prefix.'row_css_class', array($this, 'sanitize_row_css_class_callback'));
        register_setting($this->option_group, self::$field_prefix.'column_css_classes', array($this, 'sanitize_column_css_classes_callback'));

        add_settings_section($this->option_group.'_default',
            __('General settings', 'js_composer'),
            array(&$this, 'setting_section_callback_function'),
            $this->page);
        if( WPBakeryVisualComposer::getInstance()->isPlugin() ) {
            add_settings_field(self::$field_prefix.'content_types', __("Content types", "js_composer"), array(&$this, 'content_types_field_callback'), $this->page, $this->option_group.'_default');
        } else {
            add_settings_field(self::$field_prefix.'theme_content_types', __("Themes content types", "js_composer"), array(&$this, 'theme_content_types_field_callback'), $this->page, $this->option_group.'_default');
        }
        add_settings_field(self::$field_prefix.'groups_access_rules', __("Rules for users groups", "js_composer"), array(&$this, 'groups_access_rules_callback'), $this->page, $this->option_group.'_default');
        add_settings_field(self::$field_prefix.'not_responsive_css', __("Disable responsive content elements", "js_composer"), array(&$this, 'not_responsive_css_field_callback'), $this->page, $this->option_group.'_default');
        add_settings_field(self::$field_prefix.'row_css_class', __("Row CSS class name", "js_composer"), array(&$this, 'row_css_class_callback'), $this->page, $this->option_group.'_default');
        add_settings_field(self::$field_prefix.'column_css_classes', __("Columns CSS class names", "js_composer"), array(&$this, 'column_css_classes_callback'), $this->page, $this->option_group.'_default');

    }

    public static function get($option_name) {
       return get_option(self::$field_prefix.$option_name);
    }


    /**
     * Set up the enqueue for the CSS & JavaScript files.
     *
     */

    function admin_load() {
        /*
        get_current_screen()->add_help_tab( array(
            'id'      => 'overview',
            'title'   => __('Overview'),
            'content' =>
            ''
        ) );

        get_current_screen()->set_help_sidebar(
            '<p><strong>' . __( 'For more information:' ) . '</strong></p>'
        );
        */

        wp_register_script('wpb_js_composer_settings', $this->composer->assetURL( 'js_composer_settings.js' ), array('jquery'), WPB_VC_VERSION, true);

        wp_enqueue_style('bootstrap');
        wp_enqueue_style('ui-custom-theme');
        wp_enqueue_style('js_composer');

        wp_enqueue_script('jquery-ui-accordion');

        wp_enqueue_script('bootstrap-js');

        wp_enqueue_script('wpb_js_composer_settings');
    }

    /**
     * Access groups
     *
     */
    public function groups_access_rules_callback() {
        $groups = get_editable_roles();

        $settings = ( $settings = get_option(self::$field_prefix.'groups_access_rules')) ?  $settings : array();
        $show_types = array(
            'all' => __('Show visual composer & default editor', 'js_composer'),
            'only' => __('Show only visual composer', 'js_composer'),
            'no' => __("Don't allow to use visual composer", 'js_composer')
        );
        $shortcodes = WPBMap::getShortCodes();
        $size_line = ceil(count(array_keys($shortcodes))/3);
        ?>
        <div class="wpb_settings_accordion" id="wpb_js_settings_access_groups">
        <?php
        foreach($groups as $key => $params):
            if(isset($params['capabilities']['edit_posts']) && $params['capabilities']['edit_posts']===true):
            $allowed_setting = isset($settings[$key]['show']) ? $settings[$key]['show'] : 'all';
            $shortcode_settings =  isset($settings[$key]['shortcodes']) ? $settings[$key]['shortcodes'] : array();
            ?>
                <h3 id="wpb-settings-group-<?php echo $key ?>-header">
                    <a href="#wpb-settings-group-<?php echo $key ?>">
                        <?php echo $params['name'] ?>
                    </a>
                </h3>
                <div id="wpb-settings-group-<?php echo $key ?>" class="accordion-body">
                    <div class="visibility settings-block">
                        <label for="wpb_composer_access_<?php echo $key ?>"><b><?php _e('Visual composer access', 'js_composer') ?></b></label>
                        <select id="wpb_composer_access_<?php echo $key ?>" name="<?php echo self::$field_prefix.'groups_access_rules['.$key.'][show]' ?>">
                            <?php foreach($show_types as $i_key => $name): ?>
                            <option value="<?php echo $i_key ?>"<?php echo $allowed_setting==$i_key ? ' selected="true"' : '' ?>><?php echo $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="shortcodes settings-block">
                        <div class="title"><b><?php echo _e('Allowed shortcodes', 'js_composer') ?></b> </div>
                        <?php $z=1;foreach ($shortcodes as $sc_base => $el): ?>
                        <?php if (!isset($el['content_element']) || $el['content_element']==true): ?>
                        <?php if($z==1): ?><div class="pull-left"><?php endif; ?>
                        <label>
                            <input type="checkbox" <?php if(isset($shortcode_settings[$sc_base]) && (int)$shortcode_settings[$sc_base]==1): ?>checked="true" <?php endif; ?>name="<?php echo self::$field_prefix.'groups_access_rules['.$key.'][shortcodes]['.$sc_base.']' ?>" value="1" />
                            <?php _e($el["name"], "js_composer") ?>
                        </label>
                        <?php if($z==$size_line): ?></div><?php $z=0; endif; $z+=1; ?>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if($z!=1): ?></div><?php endif; ?>
                        <div class="clearfix"></div>
                        <div class="select-all">
                            <a href="#" class="wpb-settings-select-all-shortcodes"><?php echo __('Select All', 'js_composer') ?></a> | <a href="#" class="wpb-settings-select-none-shortcodes"><?php echo __('Select none', 'js_composer') ?></a>
                        </div>
                    </div>
                </div>
        <?php
        endif;
        endforeach;
        ?>
        </div>
        <?php
    }

    /**
     * Content types checkboxes list callback function
     */
    public function content_types_field_callback() {
        $pt_array = ($pt_array = get_option('wpb_js_content_types')) ? ($pt_array) : WPBakeryVisualComposer::getInstance()->config('default_post_types');
        foreach ($this->getPostTypes() as $pt) {
            if (!in_array($pt, $this->getExcluded())) {
                $checked = (in_array($pt, $pt_array)) ? ' checked="checked"' : '';
                ?>
                <label>
                    <input type="checkbox"<?php echo $checked; ?> value="<?php echo $pt; ?>" id="wpb_js_post_types_<?php echo $pt; ?>" name="<?php echo self::$field_prefix.'content_types' ?>[]">
                    <?php echo $pt; ?>
                </label><br>
                <?php }
        }
        ?>
        <p class="description indicator-hint"><?php _e("Select for which content types Visual Composer should be available during post creation/editing.", "js_composer"); ?></p>
        <?php
    }


    /**
     * Themes Content types checkboxes list callback function
     */
    public function theme_content_types_field_callback() {
        $pt_array = ($pt_array = get_option('wpb_js_theme_content_types')) ? $pt_array : WPBakeryVisualComposer::getInstance()->config('default_post_types');
        foreach ($this->getPostTypes() as $pt) {
            if (!in_array($pt, $this->getExcluded())) {
                $checked = (in_array($pt, $pt_array)) ? ' checked="checked"' : '';
                ?>
            <label>
                <input type="checkbox"<?php echo $checked; ?> value="<?php echo $pt; ?>" id="wpb_js_post_types_<?php echo $pt; ?>" name="<?php echo self::$field_prefix.'theme_content_types' ?>[]">
                <?php echo $pt; ?>
            </label><br>
            <?php }
        }
        ?>
    <p class="description indicator-hint"><?php _e("Select for which content types Visual Composer should be available during post creation/editing.", "js_composer"); ?></p>
    <?php
    }

    /**
     * Not responsive checkbox callback function
     */
    public function not_responsive_css_field_callback() {
        $checked = ($checked = get_option(self::$field_prefix.'not_responsive_css')) ? $checked : false;
        ?>
            <label>
                <input type="checkbox"<?php echo ($checked ? ' checked="checked";' : '') ?> value="1" id="wpb_js_not_responsive_css" name="<?php echo self::$field_prefix.'not_responsive_css' ?>">
                <?php _e('Disable', "js_composer") ?>
            </label><br/>
    <p class="description indicator-hint"><?php _e('Check this checkbox to prevent content elements from "stacking" one on top other (on small media screens, eg. mobile).', "js_composer"); ?></p>
    <?php
    }
    /**
     * Row css class callback
     */
    public function row_css_class_callback() {
        $value = ($value = get_option(self::$field_prefix.'row_css_class')) ? $value : '';
        echo '<input type="text" name="'.self::$field_prefix.'row_css_class'.'" value="'.$value.'">';
        echo '<p class="description indicator-hint">'.__('To change class name for the row element, enter it here. By default vc_row is used.', 'js_composer').'</p>';

    }

    /**
     * Content types checkboxes list callback function
     */
    public function column_css_classes_callback() {
        $classes = ($classes = get_option(self::$field_prefix.'column_css_classes')) ? $classes : array();
        for($i=1;$i<=12;$i++) {
            $id = self::$field_prefix.'column_css_classes_span_'.$i;
            echo '<div class="column_css_class">';
            echo '<label for="'.$id.'">'.sprintf(__('Span %d:','js_composer'), $i).'</label>';
            echo '<input type="text" name="'.self::$field_prefix.'column_css_classes'.'[span'.$i.']" id="'.$id.'" value="'.(!empty($classes['span'.$i]) ? $classes['span'.$i]: '').'">';
            echo '</div>';
        }
        ?>
    <p class="description indicator-hint"><?php _e("To change class names for the columns elements, enter them here. Bu default vc_spanX are used, where X number from 1 to 12.", "js_composer"); ?></p>
    <?php
    }
    /**
     * Callback function for settings section
     *
     *
     */
    public function setting_section_callback_function() {
        echo '<p></p>';
    }

    protected function getExcluded() {
        return array('attachment', 'revision', 'nav_menu_item', 'mediapage');
    }

    protected function getPostTypes() {
        return get_post_types(array('public'   => true));
    }

    /**
     * Sanitize functions
     *
     */

    // {{

    /**
     * Access rules for user's groups
     *
     * @param $rules - Array of selected rules for each user's group
     */

    public function sanitize_group_access_rules_callback($rules) {
        $groups = get_editable_roles();
        foreach($groups as $key => $params) {
            if(isset($rules[$key])) $sanitize_rules[$key] = $rules[$key];
        }
        return $sanitize_rules;
    }

    public function sanitize_not_responsive_css_callback($rules) {
        return $rules;
    }

    public function sanitize_row_css_class_callback($value) {
        return $value; // return preg_match('/^[a-z_]\w+$/i', $value) ? $value : '';
    }
    public function sanitize_column_css_classes_callback($classes) {
        $sanitize_rules = array();
        for($i=1; $i<=12; $i++) {
            if(isset($classes['span'.$i])) {
                $sanitize_rules['span'.$i] = $classes['span'.$i];
            }
        }
        return $sanitize_rules;
    }
    /**
     * Post types fields sanitize
     *
     * @param $post_types - Post types array selected by user
     */

    public function sanitize_post_types_callback($post_types) {
        $pt_array = array();
        if(isset($post_types) && is_array($post_types)) {
            foreach ( $post_types as $pt ) {
                if ( !in_array($pt, $this->getExcluded()) && in_array($pt, $this->getPostTypes()) ) {
                    $pt_array[] = $pt;
                }
            }
        }

        return $pt_array;
    }

    // }}

    /**
     * Process options data from form and add to js_composer option parameters
     *
     *
     */
    public function take_action() {
        // if this fails, check_admin_referer() will automatically print a "failed" page and die.
        if ( !empty($_POST) && check_admin_referer('wpb_js_settings_save_action', 'wpb_js_nonce_field') ) {

            if ( isset($_POST['post_types']) && is_array($_POST['post_types']) ) {
                update_option('wpb_js_content_types', $_POST['post_types']);
            } else {
                delete_option('wpb_js_content_types');
            }

            wp_redirect(admin_url().'options-general.php?page=wpb_vc_settings'); exit();
        }
    }

    /**
     *  HTML template
     */
    public function output() {
        ?>
<div class="wrap" id="wpb-js-composer-settings">
    <?php screen_icon(); ?>
    <h2><?php _e('WPBakery Visual Composer Settings', 'js_composer'); ?></h2>

    <form action="options.php" method="post">
        <?php settings_fields( $this->option_group ) ?>
        <?php do_settings_sections($this->page) ?>
        <?php wp_nonce_field('wpb_js_settings_save_action', 'wpb_js_nonce_field'); ?>
        <?php submit_button( __( 'Save Changes', 'js_composer' )); ?>
    </form>

    <div>
        <h2><?php _e("Thank you", "js_composer"); ?></h2>
        <p><?php _e("Visual Composer will save you a lot of time while working with your sites content.", "js_composer"); ?></p>
        <p><?php _e('If you have comments or simply want to say "Hi", please check <a href="http://wpbakery.com/vc/" title="" target="_blank">plugins homepage</a>.', "js_composer"); ?></p>
    </div>

</div>
<?php
    }
}
?>