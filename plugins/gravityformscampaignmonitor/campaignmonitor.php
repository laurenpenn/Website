<?php
/*
Plugin Name: Gravity Forms Campaign Monitor Add-On
Plugin URI: http://www.gravityforms.com
Description: Integrates Gravity Forms with Campaign Monitor allowing form submissions to be automatically sent to your Campaign Monitor account
Version: 1.9
Author: rocketgenius
Author URI: http://www.rocketgenius.com

------------------------------------------------------------------------
Copyright 2009 rocketgenius

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

add_action('init',  array('GFCampaignMonitor', 'init'));

register_activation_hook( __FILE__, array("GFCampaignMonitor", "add_permissions"));

class GFCampaignMonitor {

    private static $path = "gravityformscampaignmonitor/campaignmonitor.php";
    private static $url = "http://www.gravityforms.com";
    private static $slug = "gravityformscampaignmonitor";
    private static $version = "1.9";
    private static $min_gravityforms_version = "1.3.9";

    //Plugin starting point. Will load appropriate files
    public static function init(){


        if(RG_CURRENT_PAGE == "plugins.php"){
            //loading translations
            load_plugin_textdomain('gravityformscampaignmonitor', FALSE, '/gravityformscampaignmonitor/languages' );

            add_action('after_plugin_row_' . self::$path, array('GFCampaignMonitor', 'plugin_row') );

            //force new remote request for version info on the plugin page
            self::flush_version_info();
        }

        if(!self::is_gravityforms_supported()){
           return;
        }

        if(is_admin()){
            //loading translations
            load_plugin_textdomain('gravityformscampaignmonitor', FALSE, '/gravityformscampaignmonitor/languages' );

            add_filter("transient_update_plugins", array('GFCampaignMonitor', 'check_update'));
            add_filter("site_transient_update_plugins", array('GFCampaignMonitor', 'check_update'));

            add_action('install_plugins_pre_plugin-information', array('GFCampaignMonitor', 'display_changelog'));

            //creates a new Settings page on Gravity Forms' settings screen
            if(self::has_access("gravityforms_campaignmonitor")){
                RGForms::add_settings_page("Campaign Monitor", array("GFCampaignMonitor", "settings_page"), self::get_base_url() . "/images/campaignmonitor_wordpress_icon_32.png");
            }
        }

        //integrating with Members plugin
        if(function_exists('members_get_capabilities'))
            add_filter('members_get_capabilities', array("GFCampaignMonitor", "members_get_capabilities"));

        //creates the subnav left menu
        add_filter("gform_addon_navigation", array('GFCampaignMonitor', 'create_menu'));

        if(self::is_campaignmonitor_page()){

            //enqueueing sack for AJAX requests
            wp_enqueue_script(array("sack"));

            //loading data lib
            require_once(self::get_base_path() . "/data.php");

            //loading upgrade lib
            if(!class_exists("RGCampaignMonitorUpgrade"))
                require_once("plugin-upgrade.php");

            //loading Gravity Forms tooltips
            require_once(GFCommon::get_base_path() . "/tooltips.php");
            add_filter('gform_tooltips', array('GFCampaignMonitor', 'tooltips'));

            //runs the setup when version changes
            self::setup();

         }
         else if(in_array(RG_CURRENT_PAGE, array("admin-ajax.php"))){

            //loading data class
            require_once(self::get_base_path() . "/data.php");

            add_action('wp_ajax_rg_update_feed_active', array('GFCampaignMonitor', 'update_feed_active'));
            add_action('wp_ajax_gf_select_campaignmonitor_form', array('GFCampaignMonitor', 'select_form'));
            add_action('wp_ajax_gf_select_campaignmonitor_client', array('GFCampaignMonitor', 'select_client'));

        }
        else{
             //handling post submission.
            add_action("gform_post_submission", array('GFCampaignMonitor', 'export'), 10, 2);
        }
    }


    public static function update_feed_active(){
        check_ajax_referer('rg_update_feed_active','rg_update_feed_active');
        $id = $_POST["feed_id"];
        $feed = GFCampaignMonitorData::get_feed($id);
        GFCampaignMonitorData::update_feed($id, $feed["form_id"], $_POST["is_active"], $feed["meta"]);
    }

    //--------------   Automatic upgrade ---------------------------------------------------
    public static function flush_version_info(){
        if(!class_exists("RGCampaignMonitorUpgrade"))
            require_once("plugin-upgrade.php");

        RGCampaignMonitorUpgrade::set_version_info(false);
    }


    public static function plugin_row(){
        if(!self::is_gravityforms_supported()){
            $message = sprintf(__("Gravity Forms " . self::$min_gravityforms_version . " is required. Activate it now or %spurchase it today!%s"), "<a href='http://www.gravityforms.com'>", "</a>");
            RGCampaignMonitorUpgrade::display_plugin_message($message, true);
        }
        else{
            $version_info = RGCampaignMonitorUpgrade::get_version_info(self::$slug, self::get_key(), self::$version);

            if(!$version_info["is_valid_key"]){
                $new_version = version_compare(self::$version, $version_info["version"], '<') ? __('There is a new version of Gravity Forms Campaign Monitor Add-On available.', 'gravityformscampaignmonitor') .' <a class="thickbox" title="Gravity Forms Campaign Monitor Add-On" href="plugin-install.php?tab=plugin-information&plugin=' . self::$slug . '&TB_iframe=true&width=640&height=808">'. sprintf(__('View version %s Details', 'gravityformscampaignmonitor'), $version_info["version"]) . '</a>. ' : '';
                $message = $new_version . sprintf(__('%sRegister%s your copy of Gravity Forms to receive access to automatic upgrades and support. Need a license key? %sPurchase one now%s.', 'gravityformscampaignmonitor'), '<a href="admin.php?page=gf_settings">', '</a>', '<a href="http://www.gravityforms.com">', '</a>') . '</div></td>';
                RGCampaignMonitorUpgrade::display_plugin_message($message);
            }
        }
    }

    //Displays current version details on Plugin's page
    public static function display_changelog(){
        if($_REQUEST["plugin"] != self::$slug)
            return;

        //loading upgrade lib
        if(!class_exists("RGCampaignMonitorUpgrade"))
            require_once("plugin-upgrade.php");

        RGCampaignMonitorUpgrade::display_changelog(self::$slug, self::get_key(), self::$version);
    }

    public static function check_update($update_plugins_option){
        if(!class_exists("RGCampaignMonitorUpgrade"))
            require_once("plugin-upgrade.php");

        return RGCampaignMonitorUpgrade::check_update(self::$path, self::$slug, self::$url, self::$slug, self::get_key(), self::$version, $update_plugins_option);
    }

    private static function get_key(){
        if(self::is_gravityforms_supported())
            return GFCommon::get_key();
        else
            return "";
    }
    //---------------------------------------------------------------------------------------

    //Returns true if the current page is an Feed pages. Returns false if not
    private static function is_campaignmonitor_page(){
        $current_page = trim(strtolower(RGForms::get("page")));
        $campaignmonitor_pages = array("gf_campaignmonitor");

        return in_array($current_page, $campaignmonitor_pages);
    }

    //Creates or updates database tables. Will only run when version changes
    private static function setup(){

        if(get_option("gf_campaignmonitor_version") != self::$version)
            GFCampaignMonitorData::update_table();

        update_option("gf_campaignmonitor_version", self::$version);
    }

    //Adds feed tooltips to the list of tooltips
    public static function tooltips($tooltips){
        $campaignmonitor_tooltips = array(
            "campaignmonitor_client" => "<h6>" . __("Client", "gravityformscampaignmonitor") . "</h6>" . __("Select the Campaign Monitor client you would like to add your contacts to.", "gravityformscampaignmonitor"),
            "campaignmonitor_contact_list" => "<h6>" . __("Contact List", "gravityformscampaignmonitor") . "</h6>" . __("Select the Campaign Monitor list you would like to add your contacts to.", "gravityformscampaignmonitor"),
            "campaignmonitor_gravity_form" => "<h6>" . __("Gravity Form", "gravityformscampaignmonitor") . "</h6>" . __("Select the Gravity Form you would like to integrate with Campaign Monitor. Contacts generated by this form will be automatically added to your Campaign Monitor account.", "gravityformscampaignmonitor"),
            "campaignmonitor_map_fields" => "<h6>" . __("Map Fields", "gravityformscampaignmonitor") . "</h6>" . __("Associate your Campaign Monitor custom fields to the appropriate Gravity Form fields by selecting the appropriate form field from the list.", "gravityformscampaignmonitor"),
            "campaignmonitor_optin_condition" => "<h6>" . __("Opt-In Condition", "gravityformscampaignmonitor") . "</h6>" . __("When the opt-in condition is enabled, form submissions will only be exported to Campaign Monitor when the condition is met. When disabled all form submissions will be exported.", "gravityformscampaignmonitor"),
            "campaignmonitor_resubscribe" => "<h6>" . __("Resubscribe", "gravityformscampaignmonitor") . "</h6>" . __("When this option is enabled, if the subscriber is in an inactive state or has previously been unsubscribed, they will be re-added to the active list. Therefore, this option should be used with caution and only when appropriate.", "gravityformscampaignmonitor")
        );
        return array_merge($tooltips, $campaignmonitor_tooltips);
    }

    //Creates CampaignMonitor left nav menu under Forms
    public static function create_menu($menus){

        // Adding submenu if user has access
        $permission = self::has_access("gravityforms_campaignmonitor");
        if(!empty($permission))
            $menus[] = array("name" => "gf_campaignmonitor", "label" => __("Campaign Monitor", "gravityformscampaignmonitor"), "callback" =>  array("GFCampaignMonitor", "campaignmonitor_page"), "permission" => $permission);

        return $menus;
    }

    public static function settings_page(){

        if(!class_exists("RGCampaignMonitorUpgrade"))
            require_once("plugin-upgrade.php");

        if(isset($_POST["uninstall"])){
            check_admin_referer("uninstall", "gf_campaignmonitor_uninstall");
            self::uninstall();

            ?>
            <div class="updated fade" style="padding:20px;"><?php _e(sprintf("Gravity Forms Campaign Monitor Add-On have been successfully uninstalled. It can be re-activated from the %splugins page%s.", "<a href='plugins.php'>","</a>"), "gravityformscampaignmonitor")?></div>
            <?php
            return;
        }
        else if(isset($_POST["gf_campaignmonitor_submit"])){
            check_admin_referer("update", "gf_campaignmonitor_update");
            $settings = array("api_key" => $_POST["gf_campaignmonitor_api_key"], "client_id" => $_POST["gf_campaignmonitor_client_id"]);
            update_option("gf_campaignmonitor_settings", $settings);
        }
        else{
            $settings = get_option("gf_campaignmonitor_settings");
        }

        $is_valid = self::is_valid_key();

        $message = "";
        if($is_valid)
            $message = "Valid API Key.";
        else if(!empty($settings["api_key"]))
            $message = "Invalid API Key. Please try another.";

        ?>
        <style>
            .valid_credentials{color:green;}
            .invalid_credentials{color:red;}
            .size-1{width:400px;}
        </style>

        <form method="post" action="">
            <?php wp_nonce_field("update", "gf_campaignmonitor_update") ?>

            <h3><?php _e("Campaign Monitor Account Information", "gravityformscampaignmonitor") ?></h3>
            <p style="text-align: left;">
                <?php _e(sprintf("Campaign Monitor is an email marketing software for designers and their clients. Use Gravity Forms to collect customer information and automatically add them to your client's Campaign Monitor subscription list. If you don't have a Campaign Monitor account, you can %ssign up for one here%s", "<a href='http://www.campaignmonitor.com' target='_blank'>" , "</a>"), "gravityformscampaignmonitor") ?>
            </p>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="gf_campaignmonitor_username"><?php _e("API Key", "gravityformscampaignmonitor"); ?></label> </th>
                    <td width="88%">
                        <input type="password" class="size-1" id="gf_campaignmonitor_api_key" name="gf_campaignmonitor_api_key" value="<?php echo esc_attr($settings["api_key"]) ?>" />
                        <img src="<?php echo self::get_base_url() ?>/images/<?php echo $is_valid ? "tick.png" : "stop.png" ?>" border="0" alt="<?php $message ?>" title="<?php echo $message ?>" style="display:<?php echo empty($message) ? 'none;' : 'inline;' ?>" />
                        <br/>
                        <small><?php _e("You can find your unique API key by clicking on the 'Account Settings' link at the top of your Campaign Monitor screen", "gravityformscampaignmonitor") ?></small>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gf_campaignmonitor_username"><?php _e("API Client ID", "gravityformscampaignmonitor"); ?></label> </th>
                    <td width="88%">
                        <input type="text" class="size-1" id="gf_campaignmonitor_client_id" name="gf_campaignmonitor_client_id" value="<?php echo esc_attr($settings["client_id"]) ?>" />
                        <br/>
                        <small><?php _e("(Optional) Enter a <strong>API Client ID</strong> to limit this Add-On to the specified client", "gravityformscampaignmonitor") ?></small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" ><input type="submit" name="gf_campaignmonitor_submit" class="button-primary" value="<?php _e("Save Settings", "gravityformscampaignmonitor") ?>" /></td>
                </tr>

            </table>
            <div>

            </div>
        </form>

         <form action="" method="post">
            <?php wp_nonce_field("uninstall", "gf_campaignmonitor_uninstall") ?>
            <?php if(GFCommon::current_user_can_any("gravityforms_campaignmonitor_uninstall")){ ?>
                <div class="hr-divider"></div>

                <h3><?php _e("Uninstall Campaign Monitor Add-On", "gravityformscampaignmonitor") ?></h3>
                <div class="delete-alert"><?php _e("Warning! This operation deletes ALL Campaign Monitor Feeds.", "gravityformscampaignmonitor") ?>
                    <?php
                    $uninstall_button = '<input type="submit" name="uninstall" value="' . __("Uninstall Campaign Monitor Add-On", "gravityformscampaignmonitor") . '" class="button" onclick="return confirm(\'' . __("Warning! ALL Campaign Monitor Feeds will be deleted. This cannot be undone. \'OK\' to delete, \'Cancel\' to stop", "gravityformscampaignmonitor") . '\');"/>';
                    echo apply_filters("gform_campaignmonitor_uninstall_button", $uninstall_button);
                    ?>
                </div>
            <?php } ?>
        </form>

        <?php
    }

    public static function campaignmonitor_page(){
        $view = rgar($_GET, "view");
        if($view == "edit")
            self::edit_page();
        else
            self::list_page();
    }

    //Displays the campaignmonitor feeds list page
    private static function list_page(){
        if(!self::is_gravityforms_supported()){
            die(__(sprintf("Campaign Monitor Add-On requires Gravity Forms %s. Upgrade automatically on the %sPlugin page%s.", self::$min_gravityforms_version, "<a href='plugins.php'>", "</a>"), "gravityformscampaignmonitor"));
        }

        if(rgpost("action") == "delete"){
            check_admin_referer("list_action", "gf_campaignmonitor_list");

            $id = absint($_POST["action_argument"]);
            GFCampaignMonitorData::delete_feed($id);
            ?>
            <div class="updated fade" style="padding:6px"><?php _e("Feed deleted.", "gravityformscampaignmonitor") ?></div>
            <?php
        }
        else if (!empty($_POST["bulk_action"])){
            check_admin_referer("list_action", "gf_campaignmonitor_list");
            $selected_feeds = $_POST["feed"];
            if(is_array($selected_feeds)){
                foreach($selected_feeds as $feed_id)
                    GFCampaignMonitorData::delete_feed($feed_id);
            }
            ?>
            <div class="updated fade" style="padding:6px"><?php _e("Feeds deleted.", "gravityformscampaignmonitor") ?></div>
            <?php
        }

        ?>
        <div class="wrap">
            <img alt="<?php _e("Campaign Monitor Feeds", "gravityformscampaignmonitor") ?>" src="<?php echo self::get_base_url()?>/images/campaignmonitor_wordpress_icon_32.png" style="float:left; margin:15px 7px 0 0;"/>
            <h2><?php _e("Campaign Monitor Feeds", "gravityformscampaignmonitor"); ?>
            <a class="button add-new-h2" href="admin.php?page=gf_campaignmonitor&view=edit&id=0"><?php _e("Add New", "gravityformscampaignmonitor") ?></a>
            </h2>

            <form id="feed_form" method="post">
                <?php wp_nonce_field('list_action', 'gf_campaignmonitor_list') ?>
                <input type="hidden" id="action" name="action"/>
                <input type="hidden" id="action_argument" name="action_argument"/>

                <div class="tablenav">
                    <div class="alignleft actions" style="padding:8px 0 7px; 0">
                        <label class="hidden" for="bulk_action"><?php _e("Bulk action", "gravityformscampaignmonitor") ?></label>
                        <select name="bulk_action" id="bulk_action">
                            <option value=''> <?php _e("Bulk action", "gravityformscampaignmonitor") ?> </option>
                            <option value='delete'><?php _e("Delete", "gravityformscampaignmonitor") ?></option>
                        </select>
                        <?php
                        echo '<input type="submit" class="button" value="' . __("Apply", "gravityformscampaignmonitor") . '" onclick="if( jQuery(\'#bulk_action\').val() == \'delete\' && !confirm(\'' . __("Delete selected feeds? ", "gravityformscampaignmonitor") . __("\'Cancel\' to stop, \'OK\' to delete.", "gravityformscampaignmonitor") .'\')) { return false; } return true;"/>';
                        ?>
                    </div>
                </div>
                <table class="widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                            <th scope="col" id="active" class="manage-column check-column"></th>
                            <th scope="col" class="manage-column"><?php _e("Form", "gravityformscampaignmonitor") ?></th>
                            <th scope="col" class="manage-column"><?php _e("Campaign Monitor Client", "gravityformscampaignmonitor") ?></th>
                            <th scope="col" class="manage-column"><?php _e("Campaign Monitor List", "gravityformscampaignmonitor") ?></th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                            <th scope="col" id="active" class="manage-column check-column"></th>
                            <th scope="col" class="manage-column"><?php _e("Form", "gravityformscampaignmonitor") ?></th>
                            <th scope="col" class="manage-column"><?php _e("Campaign Monitor Client", "gravityformscampaignmonitor") ?></th>
                            <th scope="col" class="manage-column"><?php _e("Campaign Monitor List", "gravityformscampaignmonitor") ?></th>
                        </tr>
                    </tfoot>

                    <tbody class="list:user user-list">
                        <?php

                        $settings = GFCampaignMonitorData::get_feeds();
                        if(is_array($settings) && sizeof($settings) > 0){
                            foreach($settings as $setting){
                                ?>
                                <tr valign="top">
                                    <th scope="row" class="check-column"><input type="checkbox" name="feed[]" value="<?php echo $setting["id"] ?>"/></th>
                                    <td><img src="<?php echo self::get_base_url() ?>/images/active<?php echo intval($setting["is_active"]) ?>.png" alt="<?php echo $setting["is_active"] ? __("Active", "gravityformscampaignmonitor") : __("Inactive", "gravityformscampaignmonitor");?>" title="<?php echo $setting["is_active"] ? __("Active", "gravityformscampaignmonitor") : __("Inactive", "gravityformscampaignmonitor");?>" onclick="ToggleActive(this, <?php echo $setting['id'] ?>); " /></td>
                                    <td class="column-title">
                                        <a href="admin.php?page=gf_campaignmonitor&view=edit&id=<?php echo $setting["id"] ?>" title="<?php _e("Edit", "gravityformscampaignmonitor") ?>"><?php echo $setting["form_title"] ?></a>
                                        <div class="row-actions">
                                            <span class="edit">
                                            <a href="admin.php?page=gf_campaignmonitor&view=edit&id=<?php echo $setting["id"] ?>" title="<?php _e("Edit", "gravityformscampaignmonitor") ?>"><?php _e("Edit", "gravityformscampaignmonitor") ?></a>
                                            |
                                            </span>

                                            <span class="trash">
                                            <a title="<?php _e("Delete", "gravityformscampaignmonitor") ?>" href="javascript: if(confirm('<?php _e("Delete this feed? ", "gravityformscampaignmonitor") ?> <?php _e("\'Cancel\' to stop, \'OK\' to delete.", "gravityformscampaignmonitor") ?>')){ DeleteSetting(<?php echo $setting["id"] ?>);}"><?php _e("Delete", "gravityformscampaignmonitor")?></a>

                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo $setting["meta"]["client_name"] ?></td>
                                    <td><?php echo $setting["meta"]["contact_list_name"] ?></td>
                                </tr>
                                <?php
                            }
                        }
                        else if(self::is_valid_key()){
                            ?>
                            <tr>
                                <td colspan="5" style="padding:20px;">
                                    <?php _e(sprintf("You don't have any Campaign Monitor feeds configured. Let's go %screate one%s!", '<a href="admin.php?page=gf_campaignmonitor&view=edit&id=0">', "</a>"), "gravityformscampaignmonitor"); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        else{
                            ?>
                            <tr>
                                <td colspan="5" style="padding:20px;">
                                    <?php _e(sprintf("To get started, please configure your %sCampaign Monitor Settings%s.", '<a href="admin.php?page=gf_settings&addon=Campaign Monitor">', "</a>"), "gravityformscampaignmonitor"); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
        <script type="text/javascript">
            function DeleteSetting(id){
                jQuery("#action_argument").val(id);
                jQuery("#action").val("delete");
                jQuery("#feed_form")[0].submit();
            }
            function ToggleActive(img, feed_id){
                var is_active = img.src.indexOf("active1.png") >=0
                if(is_active){
                    img.src = img.src.replace("active1.png", "active0.png");
                    jQuery(img).attr('title','<?php _e("Inactive", "gravityformscampaignmonitor") ?>').attr('alt', '<?php _e("Inactive", "gravityformscampaignmonitor") ?>');
                }
                else{
                    img.src = img.src.replace("active0.png", "active1.png");
                    jQuery(img).attr('title','<?php _e("Active", "gravityformscampaignmonitor") ?>').attr('alt', '<?php _e("Active", "gravityformscampaignmonitor") ?>');
                }

                var mysack = new sack("<?php echo admin_url("admin-ajax.php")?>" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "rg_update_feed_active" );
                mysack.setVar( "rg_update_feed_active", "<?php echo wp_create_nonce("rg_update_feed_active") ?>" );
                mysack.setVar( "feed_id", feed_id );
                mysack.setVar( "is_active", is_active ? 0 : 1 );
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() { alert('<?php _e("Ajax error while updating feed", "gravityformscampaignmonitor" ) ?>' )};
                mysack.runAJAX();

                return true;
            }
        </script>
        <?php
    }

    public static function edit_page(){
        ?>
        <style>
            .campaignmonitor_col_heading{padding-bottom:2px; border-bottom: 1px solid #ccc; font-weight: bold;}
            .campaignmonitor_field_cell {padding: 6px 17px 0 0; margin-right:15px;}
            .left_header{float:left; width:200px;}
            .margin_vertical_10{margin: 10px 0;}
            #campaignmonitor_resubscribe_warning{padding-left: 5px; padding-bottom:4px; font-size: 10px;}
            .gfield_required{color:red;}
            .feeds_validation_error{ background-color:#FFDFDF;}
            .feeds_validation_error td{ margin-top:4px; margin-bottom:6px; padding-top:6px; padding-bottom:6px; border-top:1px dotted #C89797; border-bottom:1px dotted #C89797}
        </style>
        <script type="text/javascript">
            var form = Array();
        </script>
        <div class="wrap">
            <img alt="<?php _e("Campaign Monitor", "gravityformsmailchimp") ?>" style="margin: 15px 7px 0pt 0pt; float: left;" src="<?php echo self::get_base_url() ?>/images/campaignmonitor_wordpress_icon_32.png"/>
            <h2><?php _e("Campaign Monitor Feed", "gravityformsmailchimp") ?></h2>

        <?php

        //ensures valid credentials were entered in the settings page
        if(!self::is_valid_key()){
            ?>
            <tr><td colspan="2"><?php echo sprintf(__("We are unable to login to Campaign Monitor with the provided API key. Please make sure you have entered a valid API key in the %sSettings Page%s", "gravityformscampaignmonitor"), "<a href='?page=gf_settings&addon=Email+Marketing'>", "</a>"); ?></td></tr>
            <?php
            return;
        }

        //getting setting id (0 when creating a new one)
        $id = !empty($_POST["campaignmonitor_setting_id"]) ? $_POST["campaignmonitor_setting_id"] : absint($_GET["id"]);
        $config = empty($id) ? array("is_active" => true) : GFCampaignMonitorData::get_feed($id);
        if(!isset($config["meta"]))
            $config["meta"] = array();

        //updating meta information
        if(rgpost("gf_campaignmonitor_submit")){

            list($client_id, $client_name) = explode("|:|", stripslashes($_POST["gf_campaignmonitor_client"]));
            $config["meta"]["client_id"] = $client_id;
            $config["meta"]["client_name"] = $client_name;

            list($list_id, $list_name) = explode("|:|", stripslashes($_POST["gf_campaignmonitor_list"]));
            $config["meta"]["contact_list_id"] = $list_id;
            $config["meta"]["contact_list_name"] = $list_name;
            $config["form_id"] = absint($_POST["gf_campaignmonitor_form"]);

            $merge_vars = self::get_custom_fields($list_id);
            $field_map = array();
            foreach($merge_vars as $var){
                $field_name = "campaignmonitor_map_field_" . self::get_field_key($var);
                $mapped_field = stripslashes($_POST[$field_name]);
                if(!empty($mapped_field))
                    $field_map[self::get_field_key($var)] = $mapped_field;
            }
            $config["meta"]["field_map"] = $field_map;
            $config["meta"]["resubscribe"] = rgpost("campaignmonitor_resubscribe") ? true : false;

            $config["meta"]["optin_enabled"] = rgpost("campaignmonitor_optin_enable") ? true : false;
            if($config["meta"]["optin_enabled"]){
                $config["meta"]["optin_field_id"] = rgpost("campaignmonitor_optin_field_id");
                $config["meta"]["optin_operator"] = rgpost("campaignmonitor_optin_operator");
                $config["meta"]["optin_value"] = rgpost("campaignmonitor_optin_value");
            }

            $is_valid = !empty($field_map["email"]);
            if($is_valid){
                $id = GFCampaignMonitorData::update_feed($id, $config["form_id"], $config["is_active"], $config["meta"]);
                ?>
                <div class="updated fade" style="padding:6px"><?php echo sprintf(__("Feed Updated. %sback to list%s", "gravityformscampaignmonitor"), "<a href='?page=gf_campaignmonitor'>", "</a>") ?></div>
                <input type="hidden" name="campaignmonitor_setting_id" value="<?php echo $id ?>"/>
                <?php
            }
            else{
                ?>
                <div class="error" style="padding:6px"><?php echo __("Feed could not be updated. Please enter all required information below.", "gravityformscampaignmonitor") ?></div>
                <?php
            }
        }

        if(empty($merge_vars)){
            //getting merge vars from selected list (if one was selected)
            $merge_vars = empty($config["meta"]["contact_list_id"]) ? array() : self::get_custom_fields($config["meta"]["contact_list_id"]);
        }
        ?>
        <form method="post" action="">
            <input type="hidden" name="campaignmonitor_setting_id" value="<?php echo $id ?>"/>
            <div class="margin_vertical_10">

                <?php

                self::include_api();
                $api = new CS_REST_General(self::get_api_key());

                //getting all clients
                $response = $api->get_clients();
                if (!$response->was_successful()){
                    _e("Could not get client list from Campaign Monitor.", "gravityformscampaignmonitor");
                }
                else{
                    $clients = $response->response;
                    $client_id = self::get_client_id();
                    if(empty($client_id)){
                        $client_id = isset($config["meta"]["client_id"]) ? $config["meta"]["client_id"] : "";
                        ?>
                        <label for="gf_campaignmonitor_client" class="left_header"><?php _e("Client", "gravityformscampaignmonitor"); ?> <?php gform_tooltip("campaignmonitor_client") ?></label>
                        <select id="gf_campaignmonitor_client" name="gf_campaignmonitor_client" onchange="SelectClient(jQuery(this).val());">
                            <option value=""><?php _e("Select a Client", "gravityformscampaignmonitor"); ?></option>
                        <?php
                        foreach ($clients as $client){
                            $selected = $client->ClientID == $client_id ? "selected='selected'" : "";
                            ?>
                            <option value="<?php echo esc_attr($client->ClientID) . "|:|" . esc_attr($client->Name) ?>" <?php echo $selected ?>><?php echo esc_html($client->Name) ?></option>
                            <?php
                        }
                        ?>
                        </select>
                        &nbsp;&nbsp;
                        <img src="<?php echo self::get_base_url() ?>/images/loading.gif" id="campaignmonitor_wait_client" style="display: none;"/>
                    <?php
                    }
                    else{
                        $client_name = "";
                        foreach($clients as $client){
                            if($client->ClientID == $client_id)
                                $client_name = $client->Name;
                        }

                        if(empty($client_name)){
                            echo sprintf(__("Your API Client ID is invalid. You can change it in the %ssettings page%s", "gravityformscampaignmonitor"), "<a href='?page=gf_settings&addon=Campaign+Monitor'>", "</a>");
                            $client_id = 0;
                        }
                        else{
                            ?>
                            <input type="hidden" id="gf_campaignmonitor_client" name="gf_campaignmonitor_client" value="<?php echo $client_id . "|:|" . $client_name ?>" />
                            <?php
                        }
                    }
                }
                ?>
            </div>

            <div id="gf_campaignmonitor_list_container" class="margin_vertical_10" <?php echo empty($client_id) ? "style='display:none;'" : "" ?>>
                <label for="gf_campaignmonitor_list" class="left_header"><?php _e("Contact list", "gravityformscampaignmonitor"); ?> <?php gform_tooltip("campaignmonitor_contact_list") ?></label>

                <select id="gf_campaignmonitor_list" name="gf_campaignmonitor_list" onchange="SelectList(jQuery(this).val());">
                    <?php
                    if(!empty($client_id)){
                        $lists = self::get_lists($client_id, $config["meta"]["contact_list_id"]);
                        echo $lists;
                    }
                    ?>
                </select>
            </div>

            <div id="campaignmonitor_form_container" valign="top" class="margin_vertical_10" <?php echo empty($client_id) || empty($config["meta"]["contact_list_id"]) ? "style='display:none;'" : "" ?>>
                <label for="gf_campaignmonitor_form" class="left_header"><?php _e("Gravity Form", "gravityformscampaignmonitor"); ?> <?php gform_tooltip("campaignmonitor_gravity_form") ?></label>

                <select id="gf_campaignmonitor_form" name="gf_campaignmonitor_form" onchange="SelectForm(jQuery('#gf_campaignmonitor_list').val(), jQuery(this).val());">
                <option value=""><?php _e("Select a Form", "gravityformscampaignmonitor"); ?></option>
                <?php
                $forms = RGFormsModel::get_forms();
                foreach($forms as $form){
                    $selected = absint($form->id) == $config["form_id"] ? "selected='selected'" : "";
                    ?>
                    <option value="<?php echo absint($form->id) ?>"  <?php echo $selected ?>><?php echo esc_html($form->title) ?></option>
                    <?php
                }
                ?>
                </select>
                &nbsp;&nbsp;
                <img src="<?php echo self::get_base_url() ?>/images/loading.gif" id="campaignmonitor_wait_form" style="display: none;"/>
            </div>

            <div id="campaignmonitor_field_group" valign="top" <?php echo empty($client_id) || empty($config["meta"]["contact_list_id"]) || empty($config["form_id"]) ? "style='display:none;'" : "" ?>>

                <div id="campaignmonitor_field_container" valign="top" class="margin_vertical_10" >
                    <label for="campaignmonitor_fields" class="left_header"><?php _e("Map Fields", "gravityformscampaignmonitor"); ?> <?php gform_tooltip("campaignmonitor_map_fields") ?></label>

                    <div id="campaignmonitor_field_list">
                    <?php
                    if(!empty($config["form_id"])){

                        //getting list of all Campaign Monitor merge variables for the selected contact list
                        if(empty($merge_vars))
                            $merge_vars = $api->listMergeVars($list_id);

                        //getting field map UI
                        echo self::get_field_mapping($config, $config["form_id"], $merge_vars);

                        //getting list of selection fields to be used by the optin
                        $form_meta = RGFormsModel::get_form_meta($config["form_id"]);
                        $selection_fields = GFCommon::get_selection_fields($form_meta, rgar($config["meta"],"optin_field_id"));
                    }
                    ?>
                    </div>
                </div>

                <div id="campaignmonitor_optin_container" valign="top" class="margin_vertical_10">
                    <label for="campaignmonitor_optin" class="left_header"><?php _e("Opt-In Condition", "gravityformscampaignmonitor"); ?> <?php gform_tooltip("campaignmonitor_optin_condition") ?></label>
                    <div id="campaignmonitor_optin">
                        <table>
                            <tr>
                                <td>
                                    <input type="checkbox" id="campaignmonitor_optin_enable" name="campaignmonitor_optin_enable" value="1" onclick="if(this.checked){jQuery('#campaignmonitor_optin_condition_field_container').show('slow');} else{jQuery('#campaignmonitor_optin_condition_field_container').hide('slow');}" <?php echo rgar($config["meta"],"optin_enabled") ? "checked='checked'" : ""?>/>
                                    <label for="campaignmonitor_optin_enable"><?php _e("Enable", "gravityformscampaignmonitor"); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div id="campaignmonitor_optin_condition_field_container" <?php echo !rgar($config["meta"],"optin_enabled") ? "style='display:none'" : ""?>>
                                        <div id="campaignmonitor_optin_condition_fields" <?php echo empty($selection_fields) ? "style='display:none'" : ""?>>
                                            <?php _e("Export to Campaign Monitor if ", "gravityformscampaignmonitor") ?>

                                            <select id="campaignmonitor_optin_field_id" name="campaignmonitor_optin_field_id" class='optin_select' onchange='jQuery("#campaignmonitor_optin_value").html(GetFieldValues(jQuery(this).val(), "", 20));'><?php echo $selection_fields ?></select>
                                            <select id="campaignmonitor_optin_operator" name="campaignmonitor_optin_operator" />
                                                <option value="is" <?php echo rgar($config["meta"],"optin_operator") == "is" ? "selected='selected'" : "" ?>><?php _e("is", "gravityformscampaignmonitor") ?></option>
                                                <option value="isnot" <?php echo rgar($config["meta"],"optin_operator") == "isnot" ? "selected='selected'" : "" ?>><?php _e("is not", "gravityformscampaignmonitor") ?></option>
                                            </select>
                                            <select id="campaignmonitor_optin_value" name="campaignmonitor_optin_value" class='optin_select'>
                                            </select>

                                        </div>
                                        <div id="campaignmonitor_optin_condition_message" <?php echo !empty($selection_fields) ? "style='display:none'" : ""?>>
                                            <?php _e("To create an Opt-In condition, your form must have a drop down, checkbox or multiple choice field.", "gravityform") ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <script type="text/javascript">
                        <?php
                        if(!empty($config["form_id"])){
                            ?>
                            //creating Javascript form object
                            form = <?php echo GFCommon::json_encode($form_meta)?>;

                            //initializing drop downs
                            jQuery(document).ready(function(){
                                var selectedField = "<?php echo str_replace('"', '\"', rgar($config["meta"], "optin_field_id")) ?>";
                                var selectedValue = "<?php echo str_replace('"', '\"', rgar($config["meta"], "optin_value")) ?>";
                                SetOptin(selectedField, selectedValue);
                            });
                        <?php
                        }
                        ?>
                    </script>
                </div>

                <div id="campaignmonitor_options_container" valign="top" class="margin_vertical_10">
                    <label for="campaignmonitor_options" class="left_header"><?php _e("Options", "gravityformscampaignmonitor"); ?></label>
                    <div id="campaignmonitor_options">
                        <table>
                            <tr><td><input type="checkbox" name="campaignmonitor_resubscribe" id="campaignmonitor_resubscribe" value="1" <?php echo rgar($config["meta"],"resubscribe") ? "checked='checked'" : "" ?> onclick="var element = jQuery('#campaignmonitor_resubscribe_warning'); if(this.checked){element.show('slow');} else{element.hide('slow');}"/> <?php _e("Resubscribe" , "gravityformscampaignmonitor") ?>  <?php gform_tooltip("campaignmonitor_resubscribe") ?> <br/><span id='campaignmonitor_resubscribe_warning' <?php echo !rgar($config["meta"],"resubscribe") ? "style='display:none'" : "" ?>>(<?php _e("This option will re-subscribe users that have been unsubscribed. Use with caution and only when appropriate.", "gravityformscampaignmonitor") ?>)</span></td></tr>
                        </table>
                    </div>
                </div>

                <div id="campaignmonitor_submit_container" class="margin_vertical_10">
                    <input type="submit" name="gf_campaignmonitor_submit" value="<?php echo empty($id) ? __("Save", "gravityformscampaignmonitor") : __("Update", "gravityformscampaignmonitor"); ?>" class="button-primary"/>
                    <input type="button" value="<?php _e("Cancel", "gravityformscampaignmonitor"); ?>" class="button" onclick="javascript:document.location='admin.php?page=gf_campaignmonitor'" />
                </div>
            </div>
        </form>
        </div>
        <script type="text/javascript">

            function SelectClient(clientId){
                jQuery("#gf_campaignmonitor_list_container").slideUp();
                SelectList();

                if(!clientId)
                    return;

                jQuery("#campaignmonitor_wait_client").show();

                var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "gf_select_campaignmonitor_client" );
                mysack.setVar( "gf_select_campaignmonitor_client", "<?php echo wp_create_nonce("gf_select_campaignmonitor_client") ?>" );
                mysack.setVar( "client_id", clientId);
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() {jQuery("#campaignmonitor_wait_client").hide(); alert('<?php _e("Ajax error while selecting a client", "gravityformscampaignmonitor") ?>' )};
                mysack.runAJAX();

                return true;
            }

            function EndSelectClient(lists){
                if(lists){

                    jQuery("#gf_campaignmonitor_list").html(lists);
                    jQuery("#gf_campaignmonitor_list_container").slideDown();

                }
                else{
                    jQuery("#gf_campaignmonitor_list_container").slideUp();
                    jQuery("#campaignmonitor_list").html("");
                }
                jQuery("#campaignmonitor_wait_client").hide();
            }


            function SelectList(listId){

                EndSelectForm("");

                if(listId){
                    jQuery("#campaignmonitor_form_container").slideDown();
                    jQuery("#gf_campaignmonitor_form").val("");
                }
                else{
                    jQuery("#campaignmonitor_form_container").slideUp();
                }

            }

            function SelectForm(listId, formId){
                if(!formId){
                    jQuery("#campaignmonitor_field_group").slideUp();
                    return;
                }

                jQuery("#campaignmonitor_wait_form").show();
                jQuery("#campaignmonitor_field_group").slideUp();

                var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "gf_select_campaignmonitor_form" );
                mysack.setVar( "gf_select_campaignmonitor_form", "<?php echo wp_create_nonce("gf_select_campaignmonitor_form") ?>" );
                mysack.setVar( "list_id", listId);
                mysack.setVar( "form_id", formId);
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() {jQuery("#campaignmonitor_wait_form").hide(); alert('<?php _e("Ajax error while selecting a form", "gravityformscampaignmonitor") ?>' )};
                mysack.runAJAX();

                return true;
            }

            function SetOptin(selectedField, selectedValue){

                //load form fields
                jQuery("#campaignmonitor_optin_field_id").html(GetSelectableFields(selectedField, 20));

                var optinConditionField = jQuery("#campaignmonitor_optin_field_id").val();

                if(optinConditionField){
                    jQuery("#campaignmonitor_optin_condition_message").hide();
                    jQuery("#campaignmonitor_optin_condition_fields").show();

                    jQuery("#campaignmonitor_optin_value").html(GetFieldValues(optinConditionField, selectedValue, 20));

                }
                else{
                    jQuery("#campaignmonitor_optin_condition_message").show();
                    jQuery("#campaignmonitor_optin_condition_fields").hide();
                }

            }

            function EndSelectForm(fieldList, form_meta){

                //setting global form object
                form = form_meta;

                if(fieldList){

                    SetOptin("","");

                    jQuery("#campaignmonitor_field_list").html(fieldList);
                    jQuery("#campaignmonitor_field_group").slideDown();

                }
                else{
                    jQuery("#campaignmonitor_field_group").slideUp();
                    jQuery("#campaignmonitor_field_list").html("");
                }
                jQuery("#campaignmonitor_wait_form").hide();
            }

            function GetFieldValues(fieldId, selectedValue, labelMaxCharacters){
                if(!fieldId)
                    return "";

                var str = "";
                var field = GetFieldById(fieldId);
                if(!field || !field.choices)
                    return "";

                var isAnySelected = false;

                for(var i=0; i<field.choices.length; i++){
                    var fieldValue = field.choices[i].value ? field.choices[i].value : field.choices[i].text;
                    var isSelected = fieldValue == selectedValue;
                    var selected = isSelected ? "selected='selected'" : "";
                    if(isSelected)
                        isAnySelected = true;

                    str += "<option value='" + fieldValue.replace(/'/g, "&#039;") + "' " + selected + ">" + TruncateMiddle(field.choices[i].text, labelMaxCharacters) + "</option>";
                }

                if(!isAnySelected && selectedValue){
                    str += "<option value='" + selectedValue.replace("'", "&#039;") + "' selected='selected'>" + TruncateMiddle(selectedValue, labelMaxCharacters) + "</option>";
                }

                return str;
            }

            function GetFieldById(fieldId){
                for(var i=0; i<form.fields.length; i++){
                    if(form.fields[i].id == fieldId)
                        return form.fields[i];
                }
                return null;
            }

            function TruncateMiddle(text, maxCharacters){
                if(text.length <= maxCharacters)
                    return text;
                var middle = parseInt(maxCharacters / 2);
                return text.substr(0, middle) + "..." + text.substr(text.length - middle, middle);
            }

            function GetSelectableFields(selectedFieldId, labelMaxCharacters){
                var str = "";
                var inputType;
                for(var i=0; i<form.fields.length; i++){
                    fieldLabel = form.fields[i].adminLabel ? form.fields[i].adminLabel : form.fields[i].label;
                    inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
                    if(inputType == "checkbox" || inputType == "radio" || inputType == "select"){
                        var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : "";
                        str += "<option value='" + form.fields[i].id + "' " + selected + ">" + TruncateMiddle(fieldLabel, labelMaxCharacters) + "</option>";
                    }
                }
                return str;
            }

        </script>

        <?php

    }


    public static function get_lists($client_id, $list_id = ""){
        self::include_api();
        $api = new CS_REST_Clients($client_id, self::get_api_key());

        //getting list of all Campaign Monitor merge variables for the selected contact list
        $response = $api->get_lists();

        //$lists = $lists["anyType"]["List"];
        //if(!empty($lists["ListID"]))
        //    $lists = array($lists);

        $str = "<option value=''>" . __("Select a List", "gravityformscampaignmonitor") . "</option>";
        if(!$response->was_successful())
            return $str;

        $lists = $response->response;
        if(is_array($lists)){
            foreach($lists as $list){
                $selected = $list->ListID == $list_id ? "selected='selected'" : "";
                $str .= "<option value='" . esc_attr($list->ListID) . "|:|" . esc_attr($list->Name) . "' " . $selected . " >" . esc_html($list->Name) . "</option>";
            }
        }
        return $str;
    }

    public static function select_client(){

        check_ajax_referer("gf_select_campaignmonitor_client", "gf_select_campaignmonitor_client");
        list($client_id, $client_name) =  explode("|:|", $_POST["client_id"]);

        $lists = self::get_lists($client_id);

        die("EndSelectClient(\"" . $lists . "\");");
    }

    private static function get_custom_fields($list_id){
        self::include_api();
        $api = new CS_REST_Lists($list_id, self::get_api_key());

        //getting list of all Campaign Monitor merge variables for the selected contact list
        $response = $api->get_custom_fields();
        if(!$response->was_successful())
            return array();

        $custom_field_objects = $response->response;

        $custom_fields = array(array("FieldName" => "Email Address", "Key" => "[email]"), array("FieldName" => "Full Name", "Key" => "[fullname]"));

        foreach($custom_field_objects as $custom_field)
            $custom_fields[] = get_object_vars($custom_field);

        return $custom_fields;
    }

    private static function get_field_key($custom_field){
        $key = str_replace("]", "",str_replace("[", "", $custom_field["Key"]));
        return $key;
    }

    public static function select_form(){

        check_ajax_referer("gf_select_campaignmonitor_form", "gf_select_campaignmonitor_form");
        $form_id =  intval(rgpost("form_id"));
        list($list_id, $dummy) =  explode("|:|", rgpost("list_id"));
        $setting_id =  intval(rgpost("setting_id"));

        if(!self::is_valid_key())
            die("EndSelectForm();");

        $custom_fields = self::get_custom_fields($list_id);

        //getting configuration
        $config = GFCampaignMonitorData::get_feed($setting_id);

        //getting field map UI
        $str = self::get_field_mapping($config, $form_id, $custom_fields);


        //fields meta
        $form = RGFormsModel::get_form_meta($form_id);
        //$fields = $form["fields"];
        die("EndSelectForm(\"$str\", " . GFCommon::json_encode($form) . ");");
    }

    private static function get_field_mapping($config, $form_id, $merge_vars){

        //getting list of all fields for the selected form
        $form_fields = self::get_form_fields($form_id);

        $str = "<table cellpadding='0' cellspacing='0'><tr><td class='campaignmonitor_col_heading'>" . __("List Fields", "gravityformscampaignmonitor") . "</td><td class='campaignmonitor_col_heading'>" . __("Form Fields", "gravityformscampaignmonitor") . "</td></tr>";
        foreach($merge_vars as $var){
            $meta = rgar($config, "meta");
            if(!is_array($meta))
                $meta = array("field_map"=>"");

            $selected_field = rgar($meta["field_map"], self::get_field_key($var));
            $required = self::get_field_key($var) == "email" ? "<span class='gfield_required'>*</span> " : "";
            $error_class = self::get_field_key($var) == "email" && empty($selected_field) && !rgempty("gf_campaignmonitor_submit") ? " feeds_validation_error" : "";
            $str .= "<tr class='$error_class'><td class='campaignmonitor_field_cell'>" . esc_html($var["FieldName"]) . " $required</td><td class='campaignmonitor_field_cell'>" . self::get_mapped_field_list(self::get_field_key($var), $selected_field, $form_fields) . "</td></tr>";
        }
        $str .= "</table>";

        return $str;
    }

    public static function get_form_fields($form_id){
        $form = RGFormsModel::get_form_meta($form_id);
        $fields = array();

        //Adding default fields
        array_push($form["fields"],array("id" => "date_created" , "label" => __("Entry Date", "gravityformscampaignmonitor")));
        array_push($form["fields"],array("id" => "ip" , "label" => __("User IP", "gravityformscampaignmonitor")));
        array_push($form["fields"],array("id" => "source_url" , "label" => __("Source Url", "gravityformscampaignmonitor")));

        if(is_array($form["fields"])){
            foreach($form["fields"] as $field){
                if(is_array(rgar($field,"inputs"))){

                    //If this is a name or checkbox field, add full name to the list
                    if(RGFormsModel::get_input_type($field) == "name")
                        $fields[] =  array($field["id"], GFCommon::get_label($field) . " (" . __("Full" , "gravityformscampaignmonitor") . ")");
                    else if(RGFormsModel::get_input_type($field) == "checkbox")
                        $fields[] =  array($field["id"], GFCommon::get_label($field));

                    foreach($field["inputs"] as $input)
                        $fields[] =  array($input["id"], GFCommon::get_label($field, $input["id"]));
                }
                else if(!rgar($field,"displayOnly")){
                    $fields[] =  array($field["id"], GFCommon::get_label($field));
                }
            }
        }
        return $fields;
    }

    public static function get_mapped_field_list($variable_name, $selected_field, $fields){
        $field_name = "campaignmonitor_map_field_" . $variable_name;
        $str = "<select name='$field_name' id='$field_name'><option value=''></option>";
        foreach($fields as $field){
            $field_id = $field[0];
            $field_label = esc_html(GFCommon::truncate_middle($field[1], 40));

            $selected = $field_id == $selected_field ? "selected='selected'" : "";
            $str .= "<option value='" . $field_id . "' ". $selected . ">" . $field_label . "</option>";
        }
        $str .= "</select>";
        return $str;
    }

    public static function export($entry, $form){

        if(!self::is_valid_key())
            return;

        //Login to CampaignMonitor
        self::include_api();

        //loading data class
        require_once(self::get_base_path() . "/data.php");

        //getting all active feeds
        $feeds = GFCampaignMonitorData::get_feed_by_form($form["id"], true);
        foreach($feeds as $feed){
            //only export if user has opted in
            if(self::is_optin($form, $feed))
                self::export_feed($entry, $form, $feed);
        }
    }

    public static function export_feed($entry, $form, $feed){

        $resubscribe = $feed["meta"]["resubscribe"] ? true : false;
        $email = $entry[$feed["meta"]["field_map"]["email"]];
        $name = "";
        if(!empty($feed["meta"]["field_map"]["fullname"]))
            $name = self::get_name($entry, $feed["meta"]["field_map"]["fullname"]);

        $merge_vars = array();
        foreach($feed["meta"]["field_map"] as $var_key => $field_id){
            $field = RGFormsModel::get_field($form, $field_id);
            if(GFCommon::is_product_field($field["type"]) && rgar($field, "enablePrice")){
                $ary = explode("|", $entry[$field_id]);
                $name = count($ary) > 0 ? $ary[0] : "";
                $merge_vars[] = array("Key" => $var_key, "Value" => $name);
            }
            else if(RGFormsModel::get_input_type($field) == "checkbox"){
                foreach($field["inputs"] as $input){
                    $index = (string)$input["id"];
                    if(!rgempty($index, $entry)){
                        $merge_vars[] = array("Key" => $var_key, "Value" => $entry[$index]);
                    }
                }
            }
            else if(!in_array($var_key, array('email', 'fullname'))){
                $merge_vars[] = array("Key" => $var_key, "Value" => $entry[$field_id]);
            }

        }

        $subscriber = array (
              'EmailAddress' => $email,
              'Name' => $name,
              'CustomFields' => $merge_vars,
              'Resubscribe' => $resubscribe
        );

        $api = new CS_REST_Subscribers($feed["meta"]["contact_list_id"], self::get_api_key());
        $api->add($subscriber);
    }

    private static function get_name($entry, $field_id){

        //If field is simple (one input), simply return full content
        $name = rgar($entry,$field_id);
        if(!empty($name))
            return $name;

        //Complex field (multiple inputs). Join all pieces and create name
        $prefix = trim(rgar($entry,$field_id . ".2"));
        $first = trim(rgar($entry,$field_id . ".3"));
        $last = trim(rgar($entry,$field_id . ".6"));
        $suffix = trim(rgar($entry,$field_id . ".8"));

        $name = $prefix;
        $name .= !empty($name) && !empty($first) ? " $first" : $first;
        $name .= !empty($name) && !empty($last) ? " $last" : $last;
        $name .= !empty($name) && !empty($suffix) ? " $suffix" : $suffix;
        return $name;
    }

    public static function is_optin($form, $settings){
        $config = $settings["meta"];
        $operator = isset($config["optin_operator"]) ? $config["optin_operator"] : "";

        $field = RGFormsModel::get_field($form, rgar($config,"optin_field_id"));
        $field_value = RGFormsModel::get_field_value($field, array());

        $is_value_match = RGFormsModel::is_value_match($field_value, rgar($config,"optin_value"));

        return  !$config["optin_enabled"] || empty($field) || ($operator == "is" && $is_value_match) || ($operator == "isnot" && !$is_value_match);
    }

    public static function add_permissions(){
        global $wp_roles;
        $wp_roles->add_cap("administrator", "gravityforms_campaignmonitor");
        $wp_roles->add_cap("administrator", "gravityforms_campaignmonitor_uninstall");
    }

    //Target of Member plugin filter. Provides the plugin with Gravity Forms lists of capabilities
    public static function members_get_capabilities( $caps ) {
        return array_merge($caps, array("gravityforms_campaignmonitor", "gravityforms_campaignmonitor_uninstall"));
    }

    public static function uninstall(){

        //loading data lib
        require_once(self::get_base_path() . "/data.php");

        if(!GFCampaignMonitor::has_access("gravityforms_campaignmonitor_uninstall"))
            die(__("You don't have adequate permission to uninstall the Campaign Monitor Add-On.", "gravityformscampaignmonitor"));

        //droping all tables
        GFCampaignMonitorData::drop_tables();

        //removing options
        delete_option("gf_campaignmonitor_settings");
        delete_option("gf_campaignmonitor_version");

        //Deactivating plugin
        $plugin = "gravityformscampaignmonitor/campaignmonitor.php";
        deactivate_plugins($plugin);
        update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
    }

    private static function is_valid_key(){
        self::include_api();
        $api = new CS_REST_General(self::get_api_key());
        $result = $api->get_systemdate();
        return $result->was_successful();
    }

    private static function get_api_key(){
        $settings = get_option("gf_campaignmonitor_settings");
        $api_key = $settings["api_key"];
        return $api_key;
    }

    private static function get_client_id(){
        $settings = get_option("gf_campaignmonitor_settings");
        $client_id = $settings["client_id"];
        return $client_id;
    }

    private static function include_api(){

        if(!class_exists('CS_REST_Clients'))
        	require_once self::get_base_path() . "/api/csrest_clients.php";

        if(!class_exists('CS_REST_General'))
        	require_once self::get_base_path() . "/api/csrest_general.php";

        if(!class_exists('CS_REST_Lists'))
        	require_once self::get_base_path() . "/api/csrest_lists.php";

        if(!class_exists('CS_REST_Subscribers'))
        	require_once self::get_base_path() . "/api/csrest_subscribers.php";

    }

    private static function is_gravityforms_installed(){
        return class_exists("RGForms");
    }

    private static function is_gravityforms_supported(){
        if(class_exists("GFCommon")){
            $is_correct_version = version_compare(GFCommon::$version, self::$min_gravityforms_version, ">=");
            return $is_correct_version;
        }
        else{
            return false;
        }
    }

    protected static function has_access($required_permission){
        $has_members_plugin = function_exists('members_get_capabilities');
        $has_access = $has_members_plugin ? current_user_can($required_permission) : current_user_can("level_7");
        if($has_access)
            return $has_members_plugin ? $required_permission : "level_7";
        else
            return false;
    }

    //Returns the url of the plugin's root folder
    protected function get_base_url(){
        return plugins_url(null, __FILE__);
    }

    //Returns the physical path of the plugin's root folder
    protected function get_base_path(){
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . "/" . $folder;
    }


}

if(!function_exists("rgget")){
function rgget($name, $array=null){
    if(!isset($array))
        $array = $_GET;

    if(isset($array[$name]))
        return $array[$name];

    return "";
}
}

if(!function_exists("rgpost")){
function rgpost($name, $do_stripslashes=true){
    if(isset($_POST[$name]))
        return $do_stripslashes ? stripslashes_deep($_POST[$name]) : $_POST[$name];

    return "";
}
}

if(!function_exists("rgar")){
function rgar($array, $name){
    if(isset($array[$name]))
        return $array[$name];

    return '';
}
}


if(!function_exists("rgempty")){
function rgempty($name, $array = null){
    if(!$array)
        $array = $_POST;

    $val = rgget($name, $array);
    return empty($val);
}
}


if(!function_exists("rgblank")){
function rgblank($text){
    return empty($text) && strval($text) != "0";
}
}


if(!function_exists("rgobj")){
function rgobj($obj, $name){
    if(isset($obj->$name))
        return $obj->$name;

    return '';
}
}
?>
