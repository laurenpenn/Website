<?php
/**
 * WPBakery Visual Composer shortcodes attributes class.
 *
 * This class and functions represents ability which will allow you to create attributes settings fields to
 * control new attributes.
 * New attributes can be added to shortcode settings by using param array in wp_map function
 *
 * @package WPBakeryVisualComposer
 *
 */


/**
 *
 */
class WpbakeryShortcodeParams
{
    /**
     * @var array - store shortcode attributes types
     */
    protected static $params = array();
    /**
     * @var array - store shortcode javascript files urls
     */
    protected static $scripts = array();

    /**
     * Create new attribute type
     *
     * @static
     * @param $name - attribute name
     * @param $form_field_callback - hook, will be called when settings form is shown and attribute added to shortcode param list
     * @param $script_url - javascript file url which will be attached at the end of settings form.
     *
     * @return bool - return true if attribute type created
     */
    public static function addField($name, $form_field_callback, $script_url = null)
    {

        $result = false;
        if (!empty($name) && !empty($form_field_callback)) {
            self::$params[$name] = array(
                'callbacks' => array(
                    'form' => $form_field_callback
                )
            );
            $result = true;

            if(is_string($script_url) && !in_array($script_url, self::$scripts)) {
                self::$scripts[] = $script_url;
            }
        }
        return $result;
    }

    /**
     * Calls hook for attribute type
     *
     * @static
     * @param $name - attribute name
     * @param $param_settings - attribute settings from shortcode
     * @param $param_value - attribute value
     * @return mixed|string - returns html which will be render in hook
     */
    public static function renderSettingsField($name, $param_settings, $param_value) {
        if (isset(self::$params[$name]['callbacks']['form'])) {
            return call_user_func(self::$params[$name]['callbacks']['form'], $param_settings, $param_value);
        }
        return '';
    }

    /**
     * List of javascript files urls for shortcode attributes.
     *
     * @static
     * @return array - list of js scripts
     */

    public static function getScripts() {
        return self::$scripts;
    }
}

/**
 * Helper function to register new shortcode attribute hook.
 *
 * @param $name - attribute name
 * @param $form_field_callback - hook, will be called when settings form is shown and attribute added to shortcode param list
 * @param $script_url - javascript file url which will be attached at the end of settings form.
 * @return bool
 */
function add_shortcode_param($name, $form_field_callback, $script_url = null) {
    return WpbakeryShortcodeParams::addField($name, $form_field_callback, $script_url);
}


/**
 * Call hook for attribute.
 * @param $name - attribute name
 * @param $param_settings - attribute settings from shortcode
 * @param $param_value - attribute value
 * @return mixed|string - returns html which will be render in hook
 */
function do_shortcode_param_settings_field($name, $param_settings, $param_value) {
    return WpbakeryShortcodeParams::renderSettingsField($name, $param_settings, $param_value);
}

/**
 * Helper function to create tag attributes string for linked attributes of shortcode.
 * @param $settings
 * @return string
 */
function vc_generate_dependencies_attributes($settings) {
    if( !empty($settings['dependency']) && isset($settings['dependency']['element']) ) {
        return ' data-dependency-element="true"';
    }
    return '';
}
