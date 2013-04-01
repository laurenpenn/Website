<?php


class JWP6_Admin_Page_Import extends JWP6_Admin_Page {

    public function __construct() {
        parent::__construct();
    }

    public function render() {
        global $jwp6_admin;
        $purged = get_option(JWP6 . 'jwp5_purged');
        if ($jwp6_admin->previous_version && $jwp6_admin->previous_version < 6 && ! $purged ) {
            return $this->do_render();
        }
        return $this->do_not_render();
    }

    public function do_not_render() {
        return '';
    }

    public function do_render() {
        $this->render_page_start('Revert to JW5');
        $this->render_all_messages();

        ?>
        <p>
            This section allows you to revert your JW Player to version 5.
        </p>
        <div class="spacing"></div>
        <p>
            Your Wordpress site currently uses JW Player 6, the latest version. Unfortunately, not all functionality
            of your previously used JW5 player is offered with JW6. Most notable are the library of skins & plugins
            and playback of images. See <a href="<?php echo JWP6_Plugin::$urls['migration_guide']; ?>">Upgrading 
            Wordpress from JW5 to JW6</a> for a full list.
        </p>
        <p>
            If you discover or miss a critical feature in JW6, you can revert to JW5 here. All previous settings and
            options will be restored (including the option to upgrade to 6).
        </p>
        <div class="spacing"></div>
        <p>
            <a href="<?php echo JWP6_PLUGIN_URL . "jwp6-import.php?a=revert"; ?>" class="button" id="revertjwp5button">
                Revert back to JW Player 5 Plugin
            </a>
        </p>
        <div class="spacing"></div>
        <p class="description">
            Note the JW Player Plugin for Wordpress no longer support commercial JW5 upgrades. If you are interested
            in using a commercial version of the JW Player, you must upgrade to JW Player 6.
        </p>

        <script type="text/javascript">
        jQuery(function(){
            jQuery('#revertjwp5button').bind('click', function (e) {
                var c = confirm(
                    'Please note:\n\n' +
                    '1. Player embeds that you have made with this version of the plugin can be buggy after you revert.\n' +
                    '2. All settings for version 6 of the plugin will be deleted.\n'
                );
                if (!c) {
                    return false;
                }
            });
        });
        </script>
        <?php
        $this->render_page_end();
    }

}
