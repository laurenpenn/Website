<?php


class JWP6_Admin_Page_Licensing extends JWP6_Admin_Page {

    public function __construct() {

        parent::__construct();
        $license_version_field =  new JWP6_Form_Field_Select(
            'license_version',
            array(
                'options' => JWP6_Plugin::$license_versions,
                'default' => 'free',
                'description_is_value' => true,
                'help_text' => 'Select which edition of JW Player you own to unlock additional template settings and to hide the player watermark.',
            )
        );
        $license_key_field = new JWP6_Form_Field(
            'license_key', 
            array(
                'validation' => array($this, "license_key_validation"),
                'help_text' => 'A license key is required for the Pro, Premium and Ads edition.',
            )
        );

        if ( JWP6_USE_CUSTOM_SHORTCODE_FILTER ) {
            $default_config_options = array(
                'label' => 'Category pages',
                'options' => array(
                    'excerpt' => 'Use excerpt',
                    'content' => 'Use content',
                    'disable' => 'Strip shortcode',
                ),
                'default' => 'content',
                'single_line' => true,
            );

            $category_config_options = $default_config_options;
            $category_config_options['label'] = 'Category pages';
            $category_config_field = new JWP6_Form_Field_Radio(
                'category_config',
                $category_config_options
            );        

            $search_config_options = $default_config_options;
            $search_config_options['label'] = 'Search pages';
            $search_config_field = new JWP6_Form_Field_Radio(
                'search_config',
                $search_config_options
            );        

            $tag_config_options = $default_config_options;
            $tag_config_options['label'] = 'Tag pages';
            $tag_config_field = new JWP6_Form_Field_Radio(
                'tag_config',
                $tag_config_options
            );        

            $home_config_options = $default_config_options;
            $home_config_options['label'] = 'Home page';
            $home_config_field = new JWP6_Form_Field_Radio(
                'home_config',
                $home_config_options
            );        
        }

        $tracking_field = new JWP6_Form_Field_Toggle(
            'allow_anonymous_tracking',
            array(
                'label' => 'Anonymous tracking',
                'text' => 'Allow anonymous tracking of plugin feature usage',
                'help_text' => 'We track which overall features (player edition, external urls, playlists, etc.) you use. This will help us improve the plugin in the future.',
                'default' => true,
            )
        );
        
        $purge_field = new JWP6_Form_Field_Toggle(
            'purge_settings_at_deactivation',
            array(
                'label' => 'Purge settings',
                'text' => 'Purge all plugin settings when I deactivate the plugin.',
                'default' => false,
                'help_text' => 'Note. This process is irreversible. If you ever decide to reactivate the plugin all your settings will be gone. Use with care!',
            )
        );

        $this->license_fields = array(
            $license_version_field, 
            $license_key_field, 
        );

        $this->other_fields = array(
            $tracking_field,
            $purge_field,
        );

        if ( JWP6_USE_CUSTOM_SHORTCODE_FILTER ) {
            $this->shortcode_fields = array(
                $category_config_field,
                $search_config_field,
                $tag_config_field,
                $home_config_field,
            );
        } else {
            $this->shortcode_fields = array();
        }

        $this->form_fields = array_merge(
            $this->license_fields,
            $this->shortcode_fields,
            $this->other_fields
        );

    }

    public function license_key_validation($value) {
        return ( preg_match('/^\S*$/', $value) ) ? $value : NULL;
    }

    public function render() {
        $this->render_page_start('License and Location');
        $this->render_all_messages();
        ?>
        <form method="post" action="<?php echo $this->page_url(); ?>">
            <?php settings_fields(JWP6 . 'menu_licensing'); ?>

            <h3>License Settings</h3>

            <p>
                By default this plugin uses the JW Player 6 Free Edition. If you operate a commercial
                site, you are required to <a href="<?php echo JWP6_Plugin::$urls['player_pricing']; ?>">
                purchase a license key</a> for JW Player. In addition to removing the player watermark, 
                a license key unlocks features like a custom logo, premium skins, Facebook/Twitter
                sharing, Google Analytics integrations and Apple HLS streaming support.
            </p>

            <table class="form-table">
                <?php foreach ($this->license_fields as $field) { $this->render_form_row($field); } ?>
            </table>

            <div class="divider"></div>


            <?php if ( JWP6_USE_CUSTOM_SHORTCODE_FILTER ): ?>
            <h3>Shortcode settings</h3>

            <p>
                Configure here wether you want JW Player to embed in overview pages (home, tags, etc). Depending
                upon your Wordpress theme, the JW Player plugin must render the shortcodes from either 
                <code>the_excerpt</code> or <code>the_content</code>. The third option is to disable player embeds
                on a specific page type. This will strip out the shortcode.
            </p>

            <table class="form-table">
                <?php foreach ($this->shortcode_fields as $field) { $this->render_form_row($field); } ?>
            </table>

            <div class="divider"></div>
            <?php endif; ?>

            <h3>Other settings</h3>

            <table class="form-table">
                <?php if ( is_null(JWP6_PLAYER_LOCATION) ): ?>
                <tr>
                    <th>
                        Player Version
                    </th>
                    <td>
                        <strong> <?php echo JWP6_Plugin::$player_version; ?></strong>
                        <p class="description">
                            JW Player itself will automatically get updated through updates of
                            the Wordpress plugin. Player binaries are CDN hosted by Longtail Video.
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($this->other_fields as $field) { $this->render_form_row($field); } ?>
            </table>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  />
            </p>
        </form>

        <script type="text/javascript">
            jQuery(function () {
                var $ = jQuery;
                function check_key(e) {
                    var
                        version = $('#license_version').val();
                        key = $('#license_key').val();

                    alert('We have version ' + version + ' with key ' + key);

                }
                $('#license_version').bind('change', check_key);
            });
        </script>
        <?php
        $this->render_page_end();
    }

}
