<?php
class GFUpdate{
    public static function update_page(){
        if(!GFCommon::current_user_can_any("gravityforms_view_updates"))
           wp_die(__("You don't have permissions to view this page", "gravityforms"));

        if(!GFCommon::ensure_wp_version())
            return;

        echo GFCommon::get_remote_message();
        ?>
        <div class="wrap">
            <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" style="margin: 15px 7px 0pt 0pt; float: left;" src="<?php echo GFCommon::get_base_url() ?>/images/gravity-title-icon-32.png"/>
            <h2><?php echo _e("Updates", "alien") ?></h2>
            <?php
            $version_info = GFCommon::get_version_info(false);
            if(version_compare(GFCommon::$version, $version_info["version"], '<')) {
                $plugin_file = "gravityforms/gravityforms.php";
                $upgrade_url = wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin=' . urlencode($plugin_file), 'upgrade-plugin_' . $plugin_file);

                _e("There is a new version of Gravity Forms available.", "gravityforms");
                if( $version_info["is_valid_key"] )
                    echo sprintf(__(" You can upgrade to the latest version automatically or download the update and install it manually. %sUpgrade Automatically%s %sDownload Update%s", "gravityforms"), "<br/><br/><a class='button-primary' href='{$upgrade_url}'>", "</a>", "&nbsp;<a class='button' href='{$version_info["url"]}'>", "</a>");
                else
                    _e(' <a href="admin.php?page=gf_settings">Register</a> your copy of Gravity Forms to receive access to automatic upgrades and support. Need a license key? <a href="http://www.gravityforms.com">Purchase one now</a>.', 'gravityforms');

                echo "<br/><br/>";
                $changelog = RGForms::get_changelog();
                echo $changelog;
            }
            else{
                _e("Your version of Gravity Forms is up to date.", "alien");
            }
            ?>
        </div>


        <?php
    }


}
?>