<?php

define('JWP6_LINK_JWP6_INFO',               'http://www.longtailvideo.com/jw-player/');
define('JWP6_LINK_UPGRADE_WORDPRESS_GUIDE', 'http://www.longtailvideo.com/support/jw-player/28834/migrating-from-jw5-to-jw6');
define('JWP6_LINK_UPGRADE_TO_JWP6',         'http://www.longtailvideo.com/jw-player/pricing/');


class JWP6_Migrate {
    public static function upgrade_notice() {
        $jwp6_hideupgradeto6notice = get_option(JWP6 . 'hideupgradeto6notice');
        if ( ! $jwp6_hideupgradeto6notice ) {
            add_option( JWP6 . 'hideupgradeto6notice', 'no', '', true);
            $jwp6_hideupgradeto6notice = 'no';
        }
        if ( 'no' == $jwp6_hideupgradeto6notice ) {
            if ( 
                ( isset($_GET['page']) && 0 == strpos($_GET['page'], 'jwplayer') && 'jwplayer-update' != $_GET['page'] )
                ||
                'plugins.php' == basename($_SERVER['SCRIPT_FILENAME']) 
            ) {
            ?>
            <div class="fade updated">
                <p>
                    Please note. You can now upgrade to the new JW Player version 6.
                    <a href="<?php echo admin_url('admin.php?page=jwplayer-update'); ?>">Activate it now</a>
                    or
                    <a href="<?php echo admin_url('admin.php?page=jwplayer-update&' . JWP6 . 'hide_migration_notice=1'); ?>">hide this message</a>.
                </p>
            </div>
            <?php
            }
        }
    }

    public static function migrate_section() {
        ?>
        <form name="<?php echo LONGTAIL_KEY . "form"; ?>" method="post" action="<?php echo admin_url('admin.php?page=jwplayer-update&noheader=true'); ?>">
            <div class="stuffbox">
                <h3 class="hndle"><span>Upgrade to JW Player 6</span></h3>
                <div class="inside">
                    <p style="margin: 15px;">
                        Your WordPress site currently uses JW Player 5. A newer version, <a href="<?php echo JWP6_LINK_JWP6_INFO; ?>">JW 
                        player 6</a>, is available at present. You can automatically upgrade to this new version, receiving the following benefits:
                    </p>
                    <ul style="margin: 15px; list-style: disc inside;">
                        <li>
                            A much slicker interface, better HTML5 support and HTML5/Flash performance.
                        </li>
                        <li>
                            CDN hosted player assets, so upgrades are easier and asset loading is faster.
                        </li>
                        <li>
                            Pro (custom logo), Premium (skins, sharing, HLS) and Ads (VAST/IMA) features.
                        </li>
                    </ul>
                    <p style="margin:15px;">
                        Unfortunately, not all functionality of the JW5 player is offered with JW6. Most notable are the library
                        of skins and plugins and the playback of images. See <a href="<?php echo JWP6_LINK_UPGRADE_WORDPRESS_GUIDE; ?>">upgrading 
                        Wordpress from JW5 to JW6</a> for a full list.
                    </p>
                    <p style="margin:15px;">
                        If after upgrading, you discover you miss a critical feature in JW6, you can always 
                        <strong>revert to JW5</strong>
                        again, retrieving all of your original configurations.
                    </p>
                    <p style="margin: 25px 15px;">
                        <input type="hidden" name="noheader" value="true" />
                        <input class="button button-primary" type="submit" name="migrate_to_jwp6" value="<?php _e("Upgrade to JW Player 6", 'jw-player-plugin-for-wordpress'); ?>" />
                    </p>
                    <p style="margin:15px;">
                        Note the JW Player Plugin for Wordpress no longer supports commercial JW5 upgrades. If you are interested in using a commercial
                        version of the JW Player, you must <a href="<?php echo JWP6_LINK_UPGRADE_TO_JWP6; ?>">upgrade to JW Player 6</a>.
                    </p>
                </div>
            </div>
        </form>
        <?php
    }

    public static function hide_migration_notice() {
        update_option(JWP6 . 'hideupgradeto6notice', 'yes');
        ?>
        <div class="updated fade">
            <p>
                <strong>The migration notice will no longer be showed.</strong>
                You can always upgrade to JW Player Version 6 on this page!
            </p>
        </div>
        <?php
    }

    public static function migrate() {
        update_option(JWP6 . 'plugin_version', 6);
        add_option(JWP6 . 'previous_version', 5, '', true);
        delete_option(JWP6 . 'hideupgradeto6notice');
        wp_redirect( JWPLAYER_PLUGIN_URL . "/jwp6/jwp6-import.php?a=migrate");
        //wp_redirect(admin_url('admin.php?page=' . JWP6 . 'menu_import&show_migration_notice=true'));
    }
}

add_action('admin_notices', array('JWP6_Migrate', 'upgrade_notice'));
