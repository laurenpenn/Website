<?php
/**
 * WPBakery Visual Composer layout to display elements of administration iinterface
 *
 * @package WPBakeryVisualComposer
 *
 */

class WPBakeryVisualComposerLayoutButton implements WPBakeryVisualComposerTemplateInterface {
    protected $params = Array();

    public function setup($params) {
        if(empty($params['id']) || empty($params['title']))
            trigger_error( __("Wrong layout params", "js_composer"));
        $this->params = (array)$params;
        return $this;
    }

    public function output($post = null) {
        if(empty($this->params)) return '';
        $output = '<li class="category-layout wpb-layout-element-button not-column-inherit_o"><a id="'.$this->params['id'].'" data-element="vc_column" data-width="'.$this->params['id'].'" class="'.$this->params['id'].' dropable_el clickable_action" href="#"><span>'.__($this->params['title'], "js_composer").'</span></a></li>';
        return $output;
    }
}

class WPBakeryVisualComposerTemplateMenuButton implements WPBakeryVisualComposerTemplateInterface {
    protected $params = Array();
    protected $id;

    public function setID($id) {
        $this->id = (string)$id;
        return $this;
    }
    public function setup($params) {
        $this->params = (array)$params;
        return $this;
    }

    public function output($post = null) {
        if(empty($this->params)) return '';
        $output = '<li class="wpb_template_li"><a data-template_id="'.$this->id.'" href="#">'.__($this->params['name'], "js_composer").'</a> <span class="wpb_remove_template"><i class="icon-trash wpb_template_delete_icon"> </i> </span></li>';
        return $output;
    }
}

class WPBakeryVisualComposerElementButton implements WPBakeryVisualComposerTemplateInterface {
    protected $params = Array();
    protected $base;

    public function setBase($base) {
        $this->base = $base;
        return $this;
    }
    public function setup($params) {
        $this->params = $params;
        return $this;
    }
    protected function getIcon() {
        return !empty($this->params['icon']) ? '<i class="' . sanitize_title($this->params['icon']) . '"></i> ' : '';
    }
    public function output($post = null) {
        if(empty($this->params)) return '';
        $output = $class = $class_out = '';
        if ( $this->params["class"] != '' ) {
            $class_ar = $class_at_out = explode(" ", $this->params["class"]);
            for ($n=0; $n<count($class_ar); $n++) {
                $class_ar[$n] .= "_nav";
                $class_at_out[$n] .= "_o";
            }
            $class = ' ' . implode(" ", $class_ar);
            $class_out = ' '. implode(" ", $class_at_out);
        }
        if(isset($this->params["show_settings_on_create"]) && $this->params["show_settings_on_create"] === false) $class .= ' dont-show-settings-on-create';
        $output .= '<li class="category-'.$this->params['_category_id'].' wpb-layout-element-button'.$class_out.'"><a data-element="' . $this->base . '" id="' . $this->base . '" class="dropable_el clickable_action'.$class.'" href="#">' . $this->getIcon() . __($this->params["name"], "js_composer") .'</a></li>';
        return $output;
    }
}

class WPBakeryVisualComposerTemplateMenu implements WPBakeryVisualComposerTemplateInterface {
    protected $params = Array();

    public function setup($params) {
        $this->params = (array)$params;
        return $this;
    }

    public function output( $only_list = false ) {
        // if(empty($this->params)) return '';
        $output = '';
        if($only_list===false) {
            $output .=  '<li><ul><li class="nav-header">'.__('Save', 'js_composer').'</li>
                        <li id="wpb_save_template"><a href="#">'.__('Save current page as a Template', 'js_composer').'</a></li>
                        <li class="divider"></li>
                        <li class="nav-header">'.__('Load Template', 'js_composer').'</li>
                        </ul></li>
                        <li>
                        <ul class="wpb_templates_list">';
        }
        $is_empty = true;
        foreach($this->params as $id => $template) {
            if( is_array( $template) ) {
                $template_button = new WPBakeryVisualComposerTemplateMenuButton();
                $output .= $template_button->setup($template)->setID($id)->output();
               $is_empty = false;
            }
        }
        if($is_empty) $output .= '<li class="wpb_no_templates"><span>'.__('No custom templates yet.', 'js_composer').'</span></li>';
        if($only_list===false) {
            $output .= '</ul></li>';

        }
        return $output;
    }
}

class WPBakeryVisualComposerTemplate_r extends WPBakeryVisualComposerAbstract {

    protected $templates = Array();

    public function getMenu($only_list = false) {
        $template_menu = new WPBakeryVisualComposerTemplateMenu();
        return $template_menu->setup($this->getTemplatesList())->output($only_list);
    }
    protected function getTemplates() {
        if($this->templates==null)
            $this->templates = (array)get_option('wpb_js_templates');
        return $this->templates;
    }

    public function getTemplatesList() {
        return $this->getTemplates();
    }
}

class WPBakeryVisualComposerNavBar implements WPBakeryVisualComposerTemplateInterface {
    public function __construct() {

    }
    public function getColumnLayouts() {
        $output = '';
        foreach ( WPBMap::getLayouts() as $layout ) {
            $layout_button = new WPBakeryVisualComposerLayoutButton();
            $output .= $layout_button->setup($layout)->output();
        }
        return $output;
    }

    public function getContentCategoriesLayouts() {
        $output = '<li><ul class="isotope-filter"><li class="active"><a href="#" data-filter="*">'
                  .__('Show all', 'js_composer').'</a></li>';
        // $output .= '<li><a href="#" data-filter=".category-layout" class="category-layout-filer">'
        //           .__('Layout', 'js_composer').'</a></li>';
        $_other_category_index = 0;
        $show_other = false;
        foreach(WPBMap::getUserCategories() as $key => $name) {
            if($name === '_other_category_') {
                $_other_category_index  = $key;
                $show_other = true;
            } else {
                $output .='<li><a href="#" data-filter=".category-'.$key.'">'.__($name, "js_composer").'</a></li>';
            }
        }

        if($show_other) $output .= '<li><a href="#" data-filter=".category-'.$_other_category_index.'">'
                                    .__('Other', 'js_composer').'</a></li>';
        $output .= '</ul></li>';
        return $output;
    }

    public function getElementsModal() {
        $output = '<div class="wpb_bootstrap_modals">
        <div class="modal hide" id="wpb-elements-list-modal" >
          <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>'.__('Select element type', 'js_composer').'</h3>
          </div>
          <div class="modal-body wpb-elements-list">
            <ul class="wpb-content-layouts-container" style="position: relative;">
                '.$this->getContentCategoriesLayouts().'
                '.$this->getContentLayouts().'
            </ul>
          </div>
          <div class="modal-body wpb-edit-form">
            <div class="row-fluid wpb-edit-form-inner">
            </div>

          </div>
          <div class="modal-body wpb-image-gallery">
          </div>
          <div class="modal-footer hide">
            <button class="btn" data-dismiss="modal" aria-hidden="true">'.__('Close','js_composer').'</button>
          </div>
        </div></div>';

        return $output;
    }

    public function getContentLayouts() {

        $output = '<li><ul class="wpb-content-layouts">';
        // $output .= $this->getColumnLayouts();

        foreach (WPBMap::getUserShortCodes() as $sc_base => $el) {
            if(isset($el['content_element']) && $el['content_element'] === false) continue;
                $element_button = new WPBakeryVisualComposerElementButton();
                $output .= $element_button->setBase($sc_base)->setup($el) ->output();
        }
        $output .= '</ul></li>';
        return $output;
    }

    public function getTemplateMenu($only_list = false) {
        $template_r = new WPBakeryVisualComposerTemplate_r();
        return $template_r->getMenu($only_list);
    }


    public function output($post = null) {
        global $current_user;
        get_currentuserinfo();
        /** @var $settings - get use group access rules */
        $settings = WPBakeryVisualComposerSettings::get('groups_access_rules');
        $role = $current_user->roles[0];
        $show_role = isset($settings[$role]['show']) ? $settings[$role]['show'] : '';

        $output = '
            <div id="wpb_visual_composer-elements" class="navbar">
                <input type="hidden" name="wpb_js_composer_group_access_show_rule" class="wpb_js_composer_group_access_show_rule" value="'.$show_role.'" />
                <div class="navbar-inner">
                    <div class="container">
                        <a title="'.__('Visual Composer for WordPress', 'js_composer').'" href="http://wpbakery.com/vc/" class="brand" target="_blank"></a>
                        <div class="nav-collapse">
                            <ul class="nav">
                                <li class="dropdown wpb_deprecated_element">
                                    <a data-toggle="dropdown" class="dropdown-toggle wpb_popular_layouts" href="#">'.__("Popular Layouts", "js_composer").' <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        '.$this->getColumnLayouts().'
                                    </ul>
                                </li>
                                <li class="dropdown wpb_deprecated_element">
                                    <a data-toggle="dropdown" class="dropdown-toggle wpb_content_elements" href="#">'.__("Content Elements", "js_composer").' <b class="caret"></b></a>
                                    <ul class="dropdown-menu wpb-content-layouts-container">
                                        '.$this->getContentCategoriesLayouts().'
                                        '.$this->getContentLayouts().'
                                    </ul>
                                </li>
                                <li>
                                    <a class="wpb_add_new_element dropable_el button" id="wpb-add-new-element"><i class="icon"></i>'.__('Add element', 'js_composer').'</a>
                                </li>
                            </ul>
                            <ul class="nav"><li>
                                <a class="wpb_add_new_row dropable_row button" id="wpb-add-new-row" data-element="vc_row"><i class="icon"></i>'.__('Add row', 'js_composer').'</a>
                            </li></ul>
                            <ul class="nav">
                                <li class="dropdown">
                                    <a data-toggle="dropdown" class="wpb_templates button" href="#"><i class="icon"></i>'.__('Templates', 'js_composer').' <b class="caret"></b></a>
                                    <ul class="dropdown-menu wpb_templates_ul">
                                        '.$this->getTemplateMenu().'
                                    </ul>
                                </li>
                            </ul>
                            <ul class="nav pull-right wpb-update-button">
                                <li><a class="button" id="wpb-save-post">'.__('Update', 'jscomposer').'</a></li>
                            </ul>
                        </div><!-- /.nav-collapse -->
                    </div>
                </div>
            </div>
            <style type="text/css">#wpb_visual_composer {display: none;}</style>';

        return $output;
    }
}

class WPBakeryVisualComposerLayout implements  WPBakeryVisualComposerTemplateInterface {
    protected $navBar;
    public function __construct() {

    }
    public function getNavBar() {
        if($this->navBar==null) $this->navBar = new WPBakeryVisualComposerNavBar();
        return $this->navBar;
    }

	public function getContainerHelper() {
		// return '<div class="container-helper">' . __('<h2>No content yet! You should add some...</h2>', 'js_composer') . __("<p>Use the buttons under <a href='javascript:open_elements_dropdown();' class='open-dropdown-content-element'><i class='icon'></i> Content Elements</a> on the top or add <a href='#' class='add-text-block-to-content'><i class='icon'></i> Text block</a> with single click.", 'js_composer') . '</p></div>';
        //return '<div class="container-helper"><span>' . __('<h2>No content yet! You should add some...</h2>', 'js_composer') . __('<p>Click <a href="#" class="add-element-to-layout" title="Add to this column"><i class="icon"></i></a> to add new element inside this column.', 'js_composer') . '</p></span></div>';
        return '';
    }

    public function output($post = null) {

        $output = $this->getNavBar()->getElementsModal();

        $output .= $this->getNavBar()->output();

        $output .= '<div class="metabox-composer-content">
					<div id="visual_composer_edit_form" class="row-fluid"></div>
					<div id="wpb-convert-message"><div class="alert wpb_content_element alert-info"><div class="messagebox_text"><p>
                        '.__('Your page layout was created with previous Visual Composer version. Before converting your layout to the new version, make sure to <a target="_blank" href="http://kb.wpbakery.com/index.php?title=Update_Visual_Composer_from_3.4_to_3.5">read this page</a>.', 'js_composer').'
                        <div class="wpb-convert-buttons">
                            <a class="wpb_convert button" id="wpb-convert"><i class="icon"></i>'.__('Convert to new version', 'js_composer').'</a>
                        </div>
					</p></div></div></div>
					<div id="visual_composer_content" class="wpb_main_sortable main_wrapper row-fluid wpb_sortable_container">
						<img src="'.get_site_url().'/wp-admin/images/wpspin_light.gif" /> '.__("Loading, please wait...", "js_composer").'
					</div>
					<div id="wpb-empty-blocks">
						<h2>' . __("No content yet! You should add some...", "js_composer") .'</h2>
						<table class="helper-block">
							<tr>
								<td><span>1</span></td><td><p> '. __("This is a visual preview of your page. Currently, you don't have any content elements. Click or drag the button <a href='#' class='add-element-to-layout'><i class='icon'></i> Add element</a> on the top to add content elements on your page. Alternativly add <a href='#' class='add-text-block-to-content' parent-container='#visual_composer_content'><i class='icon'></i> Text block</a> with single click.", "js_composer") . '</p></td>
							</tr>
						</table>
						<table class="helper-block">
							<tr>
								<td><span>2</span></td><td><p class="one-line"> '. __("Click the pencil icon on the content elements to change their properties.", "js_composer") . '</p></td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="edit-picture"></div>
								</td>
							</tr>
						</table>
					</div>
				</div><div id="container-helper-block" style="display: none;">' . $this->getContainerHelper() . '</div>';

        $wpb_vc_status = get_post_meta($post->ID, '_wpb_vc_js_status', true);
        if ( $wpb_vc_status == "" || !isset($wpb_vc_status) ) {
            $wpb_vc_status = 'false';
        }
        $output .= '<input type="hidden" id="wpb_vc_js_status" name="wpb_vc_js_status" value="'. $wpb_vc_status .'" />';
        $output .= '<input type="hidden" id="wpb_vc_loading" name="wpb_vc_loading" value="'. __("Loading, please wait...", "js_composer") .'" />';
        $output .= '<input type="hidden" id="wpb_vc_loading_row" name="wpb_vc_loading_row" value="'. __("Crunching...", "js_composer") .'" />';

        $output .= '<input type="hidden" id="wpb_vc_js_interface_version" name="wpb_vc_js_interface_version" value="'. vc_get_initerface_version() .'" />';
        echo $output;
        require_once WPBakeryVisualComposer::config('COMPOSER').'templates/media_editor.php';
    }
}