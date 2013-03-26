<?php
/**
 * WPBakery Visual Composer Shortcode mapper
 *
 * @package WPBakeryVisualComposer
 *
 */



class WPBMap {
    protected static $sc = Array();
    protected static $layouts = Array();
    protected static $categories = Array();
    protected static $user_sc = false;
    protected static $user_categories = false;
    protected static $settings, $user_role;

    public static function layout($array) {
        self::$layouts[] = $array;
    }

    public static function getLayouts() {
        return self::$layouts;
    }

    public static function getSettings() {
        global $current_user;

        if(self::$settings=== null) {
            if(function_exists('get_currentuserinfo')) {
                get_currentuserinfo();
                /** @var $settings - get use group access rules */
                if(!empty($current_user->roles))
                    self::$user_role = $current_user->roles[0];
                else
                    self::$user_role = 'author';

            } else {
                self::$user_role = 'author';
            }
            self::$settings = WPBakeryVisualComposerSettings::get('groups_access_rules');

        }

        return self::$settings;
    }


    public static function map( $name, $attributes ) {


        if( empty($attributes['name']) ) {
            trigger_error( sprintf( __("Wrong name for shortcode:%s. Name required", "js_composer"), $name ) );
        } elseif( empty($attributes['base']) ) {
            trigger_error( sprintf( __("Wrong base for shortcode:%s. Base required", "js_composer"), $name ) );
        } else {
            self::$sc[$name] = $attributes;
            self::$sc[$name]['params'] = Array();
            // Here checks user access
            


            if($name != 'vc_column' && (!isset($attributes['content_element']) || $attributes['content_element'] === true)) {
                $category = isset(self::$sc[$name]['category'])  ? self::$sc[$name]['category'] : '_other_category_';
                // Category filter
                if(array_search($category, self::$categories)===false) {
                    self::$categories[] = $category;
                }
                self::$sc[$name]['_category_id'] =  array_search($category, self::$categories);
            }
            if(!empty($attributes['params'])) {
                $attributes_keys = Array();
                foreach($attributes['params'] as $attribute) {
                    $key = array_search($attribute['param_name'], $attributes_keys);
                    if( $key === false ) {
                        $attributes_keys[] = $attribute['param_name'];
                        self::$sc[$name]['params'][] = $attribute;
                    } else {
                        self::$sc[$name]['params'][$key] = $attribute;
                    }
                }
            }


            WPBakeryVisualComposer::getInstance()->addShortCode(self::$sc[$name]);
        }

    }
    
    public static function generateUserData() {
    	if(self::$user_sc!==false && self::$user_categories!==false) return true;
    	
        $settings = self::getSettings();
        self::$user_sc = self::$user_categories = array();
	    foreach(self::$sc as $name => $values) {
		    if(!isset($settings[self::$user_role]['shortcodes'])
                || ( isset($settings[self::$user_role]['shortcodes'][$name]) && (int)$settings[self::$user_role]['shortcodes'][$name] == 1 ) ) {
	                self::$user_sc[$name] = $values;
	                if($name != 'vc_column' && (!isset($values['content_element']) || $values['content_element'] === true)) {
	                	$category = isset($values['category'])  ? $values['category'] : '_other_category_';
	                	if(array_search($category, self::$user_categories)===false) self::$user_categories[] = $category;
	                }
           }
          
	    }
    }
    public static function getShortCodes() {
        return self::$sc;
    }
    public static function getUserShortCodes() {
    	self::generateUserData();
        return self::$user_sc;
    }
    public static function getShortCode($name) {
        return self::$sc[$name];
    }
    public static function getCategories() {
        return self::$categories;
    }

    public static function getUserCategories() {
    	self::generateUserData();
        return self::$user_categories;
    }

    public static function dropParam($name, $attribute_name) {
        foreach(self::$sc[$name]['params'] as $index => $param) {
            if($param['param_name']==$attribute_name) {
                unset(self::$sc[$name]['params'][$index]);
                return;
            }
        }
    }

    /* Extend params for settings */
    public static function addParam($name, $attribute = Array()) {
        if( !isset(self::$sc[$name]))
            return trigger_error( sprintf(__("Wrong name for shortcode:%s. Name required", "js_composer"), $name ) );
        elseif (!isset($attribute['param_name'])) {
            trigger_error( sprintf(__("Wrong attribute for '%s' shortcode. Attribute 'param_name' required", "js_composer"), $name ) );
        } else {

            $replaced = false;

            foreach(self::$sc[$name]['params'] as $index => $param) {
                if($param['param_name']==$attribute['param_name']) {
                   $replaced = true;
                   self::$sc[$name]['params'][$index] = $attribute;
                }
            }

            if($replaced === false) self::$sc[$name]['params'][] = $attribute;

            WPBakeryVisualComposer::getInstance()->addShortCode(self::$sc[$name]);
        }
    }

    public static function dropShortcode($name) {
        unset(self::$sc[$name]);
        WPBakeryVisualComposer::getInstance()->removeShortCode($name);

    }

    public static function showAllD() {
        $a = Array();
        foreach(self::$sc as $key => $params) {
            foreach($params['params'] as $p) {
                if(!isset($a[$p['type']])) {
                    $a[$p['type']] = $p;
                }
            }
        }

    }

}