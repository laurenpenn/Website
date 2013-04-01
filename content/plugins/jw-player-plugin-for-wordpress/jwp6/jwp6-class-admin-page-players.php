<?php

class JWP6_Admin_Page_Players extends JWP6_Admin_Page {

    private $overview_or_edit = 'overview';

    private $players = array();

    private $player;

    public function render() {
        if ( 'edit' == $this->overview_or_edit ) {
            return $this->render_edit_page();
        }
        return $this->render_overview();
    }

    public function __construct() {
        parent::__construct();
        $this->imported_players = get_option(JWP6 . 'imported_jwp5_players');
        if ( isset($_GET['player_id']) && isset($_GET['action']) ) {
            $this->process_action();
        } else if ( isset($_GET['player_id']) ) {
            $this->player = new JWP6_Player($_GET['player_id']);
            if ( ! $this->player->is_existing() ) {
                $this->add_message('Please note that you need save changes to definitely save this player.');
            }
            $this->overview_or_edit = 'edit';
            return $this->_init_edit_page();
        }
        return $this->_init_overview_page();
    }


    protected function process_action() {
        if ( 'delete' == $_GET['action'] ) {
            $player = new JWP6_Player($_GET['player_id']);
            $player->purge();
            $this->add_message("Player {$player->get_id()} has been deleted.");
            unset($player);
        }
    }

    public function process_post_data($post_data) {
        parent::process_post_data($post_data, false);
        if ( isset($_GET['player_id']) ) {
            return $this->process_edit_post_data($post_data);
        } else {
            return $this->process_overview_post_data($post_data);
        }
    }

    protected function process_overview_post_data($post_data) {
        $new_player = false;
        if ( !count($this->form_error_fields) ) {
            $new_player_id = JWP6_Player::next_player_id();
            if ( isset($_POST['copy_from_player']) && $_POST['copy_from_player'] ) {
                $from = intval($_POST['copy_from_player']);
                $players = get_option(JWP6 . "players");
                if ( in_array($from, $players) ) {
                    $from = new JWP6_Player($from);
                    $new_player = new JWP6_Player($new_player_id, $from->get_config());
                    $new_description = "Copy of \"{$new_player->get('description')}\"";
                    $new_player->set('description', $new_description);
                    $new_player->save();
                }
            } else {
                $new_player = new JWP6_Player($new_player_id);
                wp_redirect($new_player->admin_url($this));
                exit();
            }
            //$new_player->save();
        }
        wp_redirect($this->page_url());
        exit();
    }

    protected function process_edit_post_data($post_data) {
        foreach ($this->form_fields as $field) {
            $ok = $this->player->set($field->name, $field->value);
        }
        if ( !count($this->form_error_fields) ) {
            $this->player->save();
            wp_redirect($this->page_url(array('player_saved' => $this->player->get_id())));
            exit();
        } else {
            wp_head();
        }
    }

    private function _init_overview_page() {
        $players = get_option(JWP6 . 'players');
        foreach ($players as $key => $player_name) {
            $this->players[$player_name] = new JWP6_Player($player_name);
        }
        ksort($this->players);
        update_option(JWP6 . 'players', array_keys($this->players));
        if ( isset($_GET['player_saved']) && array_key_exists($_GET['player_saved'], $this->players) ) {
            $this->add_message("Changes for <strong>player {$_GET['player_saved']}</strong> have been saved successfully.");
        }
    }

    private function _init_edit_page() {
        // Basic settings
        $cannot_edit = false;
        if ( $this->imported_players && array_key_exists($this->player->get('description'), $this->imported_players) ) {
            $cannot_edit = 'You cannot edit the description of this player, because this is an imported JW5 player configuration and the description is used to map your old shortcodes to this player.';
        } else if ( ! $this->player->get_id() ) {
            $cannot_edit = 'You cannot edit the description of the default editor.';
        }
        if ( $cannot_edit ) {
            $description_field = new JWP6_Form_Field_Uneditable(
                'description',
                array(
                    'value' => $this->player->get('description'),
                    'why_not' => $cannot_edit,
                )
            );
        } else {
            $description_field = new JWP6_Form_Field(
                'description',
                array(
                    'value' => $this->player->get('description'),
                    'validation' => "sanitize_text_field",
                    'placeholder'  => "Use this short description as a reminder to identify the player.",
                    'class' => 'wide',
                )
            );
        }
        $width_field = new JWP6_Form_Field(
            'width',
            array(
                'value' => $this->player->get('width'),
                'validation' => array('JWP6_Plugin', 'validate_int'),
                'unit' => 'pixels wide.',
                'class' => 'small right',
            )
        );
        $height_field = new JWP6_Form_Field(
            'height',
            array(
                'value' => $this->player->get('height'),
                'validation' => array('JWP6_Plugin', 'validate_int'),
                'unit' => 'pixels high.',
                'class' => 'small right',
            )
        );

        // LAYOUT

        $skin_field = ( JWP6_Plugin::option_available('skin') ) ? new JWP6_Form_Field_Select(
            'skin',
            array(
                'value' => $this->player->get('skin'),
                'options' => JWP6_Plugin::$player_options['skin']['options'],
                'default' => JWP6_Plugin::$player_options['skin']['default'],
            )
        ) : null;


        $controls_field = new JWP6_Form_Field_Toggle(
            'controls',
            array(
                'value' => $this->player->get('controls'),
                'text' => JWP6_Plugin::$player_options['controls']['text'],
                'help_text' => JWP6_Plugin::$player_options['controls']['help_text'],
            )
        );

        $stretching_field = new JWP6_Form_Field_Select(
            'stretching',
            array(
                'value' => $this->player->get('stretching'),
                'options' => JWP6_Plugin::$player_options['stretching']['options'],
                'default' => JWP6_Plugin::$player_options['stretching']['default'],
                'help_text' => JWP6_Plugin::$player_options['stretching']['help_text'],
            )
        );

        // PLAYBACK

        $autostart_field = new JWP6_Form_Field_Toggle(
            'autostart',
            array(
                'value' => $this->player->get('autostart'),
                'text' => JWP6_Plugin::$player_options['autostart']['text'],
                'help_text' => JWP6_Plugin::$player_options['autostart']['help_text'],
            )
        );

        $fallback_field = new JWP6_Form_Field_Toggle(
            'fallback',
            array(
                'value' => $this->player->get('fallback'),
                'text' => JWP6_Plugin::$player_options['fallback']['text'],
            )
        );

        $mute_field = new JWP6_Form_Field_Toggle(
            'mute',
            array(
                'value' => $this->player->get('mute'),
                'text' => JWP6_Plugin::$player_options['mute']['text'],
            )
        );

        $primary_field = new JWP6_Form_Field_Radio(
            'primary',
            array(
                'value' => $this->player->get('primary'),
                'label' => 'Default rendering mode',
                'options' => JWP6_Plugin::$player_options['primary']['options'],
                'default' => JWP6_Plugin::$player_options['primary']['default'],
                'help_text' => JWP6_Plugin::$player_options['primary']['help_text'],
                'description_is_value' => true,
            )
        );

        $repeat_field = new JWP6_Form_Field_Toggle(
            'repeat',
            array(
                'value' => $this->player->get('repeat'),
                'text' => JWP6_Plugin::$player_options['repeat']['text'],
            )
        );

        // TODO: STARTPARAM
        $listbar_position_field = new JWP6_Form_Field_Select(
            'listbar__position',
            array(
                'value' => $this->player->get('listbar__position'),
                'options' => JWP6_Plugin::$player_options['listbar__position']['options'],
                'default' => JWP6_Plugin::$player_options['listbar__position']['default'],
                'help_text' => JWP6_Plugin::$player_options['listbar__position']['help_text'],
            )
        );

        $listbar_size_field = new JWP6_Form_Field(
            'listbar__size',
            array(
                'value' => $this->player->get('listbar__size'),
                'validation' => array('JWP6_Plugin', 'validate_int'),
                'default' => JWP6_Plugin::$player_options['listbar__size']['default'],
                'unit' => 'pixels.',
                'class' => 'small right',
                'help_text' => JWP6_Plugin::$player_options['listbar__size']['help_text'],
            )
        );


        // LISTBAR

        // LOGO & RIGHTCLICK

        if ( JWP6_Plugin::option_available('logo') ) {

            $logo_file_field = new JWP6_Form_Field(
                'logo__file',
                array(
                    'value' => $this->player->get('logo__file'),
                    'validation' => array('JWP6_Plugin', 'validate_empty_or_url'),
                    'placeholder'  => "URL to the file to use for the image",
                    'class' => 'wide',
                    'help_text' => JWP6_Plugin::$player_options['logo__file']['help_text'],
                )
            );

            $logo_hide_field = new JWP6_Form_Field_Toggle(
                'logo__hide',
                array(
                    'value' => $this->player->get('logo__hide'),
                    'text' => JWP6_Plugin::$player_options['logo__hide']['text'],
                )
            );

            $logo_link_field = new JWP6_Form_Field(
                'logo__link',
                array(
                    'value' => $this->player->get('logo__link'),
                    'validation' => array('JWP6_Plugin', 'validate_empty_or_url'),
                    'placeholder'  => "URL you want the logo to link to.",
                    'class' => 'wide',
                    'help_text' => JWP6_Plugin::$player_options['logo__link']['help_text'],
                )
            );

            $logo_margin_field = new JWP6_Form_Field(
                'logo__margin',
                array(
                    'value' => $this->player->get('logo__margin'),
                    'validation' => array('JWP6_Plugin', 'validate_int'),
                    'unit' => 'pixels.',
                    'class' => 'small right',
                    'help_text' => JWP6_Plugin::$player_options['logo__margin']['help_text'],
                )
            );


            $logo_position_field = new JWP6_Form_Field_Select(
                'logo__position',
                array(
                    'value' => $this->player->get('logo__position'),
                    'options' => JWP6_Plugin::$player_options['logo__position']['options'],
                    'default' => JWP6_Plugin::$player_options['logo__position']['default'],
                    'help_text' => JWP6_Plugin::$player_options['logo__position']['help_text'],
                    'description_is_value' => true,
                )
            );

            // RIGHCLICK
            // $abouttext_field = new JWP6_Form_Field(
            //     'abouttext',
            //     array(
            //         'value' => $this->player->get('abouttext'),
            //         'label' => 'Text in rightclick menu',
            //         'validation' => "sanitize_text_field",
            //         'placeholder'  => "URL you want the right-click to link to.",
            //         'class' => 'wide',
            //         'help_text' => JWP6_Plugin::$player_options['abouttext']['help_text'],
            //     )
            // );

            // $aboutlink_field = new JWP6_Form_Field(
            //     'aboutlink',
            //     array(
            //         'value' => $this->player->get('aboutlink'),
            //         'label' => 'Link',
            //         'validation' => array('JWP6_Plugin', 'validate_empty_or_url'),
            //         'placeholder'  => "URL you want the right-click to link to.",
            //         'class' => 'wide',
            //         'help_text' => JWP6_Plugin::$player_options['aboutlink']['help_text'],
            //     )
            // );

            // FIELDSETS

            $this->logo_settings_fields = array(
                'logo_file_field' => $logo_file_field,
                'logo_hide_field' => $logo_hide_field,
                'logo_link_field' => $logo_link_field,
                'logo_margin_field' => $logo_margin_field,
                'logo_position_field' => $logo_position_field,
            );

            // $this->rightclick_settings_fields = array(
            //     'abouttext_field' => $abouttext_field,
            //     'aboutlink_field' => $aboutlink_field,
            // );

        }

        // Advertising

        if ( JWP6_Plugin::option_available('advertising') ) {

            $advertising_client_field = new JWP6_Form_Field_Select(
                'advertising__client',
                array(
                    'value' => $this->player->get('advertising__client'),
                    'options' => JWP6_Plugin::$player_options['advertising__client']['options'],
                    'default' => JWP6_Plugin::$player_options['advertising__client']['default'],
                    //'help_text' => JWP6_Plugin::$player_options['advertising__client']['help_text'],
                )
            );

            $advertising_tag_field = new JWP6_Form_Field(
                'advertising__tag',
                array(
                    'value' => $this->player->get('advertising__tag'),
                    'validation' => array('JWP6_Plugin', 'validate_empty_or_url'),
                    'placeholder'  => "URL of your vast/googima tag",
                    'class' => 'wide',
                    'help_text' => JWP6_Plugin::$player_options['advertising__tag']['help_text'],
                )
            );

            $this->advertising_fields = array(
                'advertising_client_field' => $advertising_client_field,
                'advertising_tag_field' => $advertising_tag_field,
            );

        }

        // GOOGLE ANALYTICS & SHARING

        if ( JWP6_Plugin::option_available('ga') ) {

            $ga_field = new JWP6_Form_Field_Toggle(
                'ga',
                array(
                    'value' => $this->player->get('ga'),
                    'text' => JWP6_Plugin::$player_options['ga']['text'],
                    'label' => 'Google Analytics',
                )
            );

            $sharing_field = new JWP6_Form_Field_Toggle(
                'sharing',
                array(
                    'value' => $this->player->get('sharing'),
                    'text' => JWP6_Plugin::$player_options['sharing']['text'],
                    'label' => 'Sharing',
                )
            );

            $this->other_settings_fields = array(
                'ga_field' => $ga_field,
                'sharing_field' => $sharing_field,
            );

        }

        if ( $this->player->get('streamer') ) {
            $this->other_settings_fields['streamer_field'] = new JWP6_Form_Field_Uneditable(
                'streamer',
                array(
                    'value' => $this->player->get('streamer'),
                    'why_not' => "This is an imported JW Player 5 that had a custom streamer setting. JW player 6 only supports this setting as a legacy setting.",
                )
            );
        }


        $this->basic_settings_fields = array(
            'description_field' => $description_field,
            'width_field' => $width_field,
            'height_field' => $height_field
        );

        $this->layout_settings_fields = array(
            'skin_field' => $skin_field,
            'controls_field' => $controls_field,
            'stretching_field' => $stretching_field,
        );

        $this->playback_settings_fields = array(
            'autostart_field' => $autostart_field,
            'fallback_field' => $fallback_field,
            'mute_field' => $mute_field,
            'primary_field' => $primary_field,
            'repeat_field' => $repeat_field,
        );

        $this->listbar_settings_fields = array(
            'listbar_position_field' => $listbar_position_field,
            'listbar_size_field' => $listbar_size_field,
        );

        $this->form_fields = array_merge(
            $this->basic_settings_fields, 
            $this->layout_settings_fields,
            $this->playback_settings_fields,
            $this->listbar_settings_fields
        );

        if ( JWP6_Plugin::option_available('logo') ) {
            $this->form_fields = array_merge(
                $this->form_fields, 
                $this->logo_settings_fields
                // $this->rightclick_settings_fields
            );
        }

        if ( JWP6_Plugin::option_available('advertising') ) {
            $this->form_fields = array_merge(
                $this->form_fields, 
                $this->advertising_fields
            );
        }

        if ( JWP6_Plugin::option_available('ga') ) {
            $this->form_fields = array_merge(
                $this->form_fields, 
                $this->other_settings_fields
            );
        }

    }

    protected function render_edit_page() {
        $this->render_page_start('Edit player: <strong>' . $this->player->full_description() . '</strong>.');
        ?>
        <div class="backlink">
            <a href="<?php echo $this->page_url(); ?>">← Back to the player overview</a>
        </div>
        <?php
        $this->render_all_messages();
        ?>

        <form method="post" action="<?php echo $this->page_url(array('noheader' => 'true', 'player_id' => $this->player->get_id())) ?>">
            <?php settings_fields(JWP6 . 'menu'); ?>

            <h3>Basic Settings</h3>
            <table class="form-table">
                <?php foreach ($this->basic_settings_fields as $field) { if ( $field ) $this->render_form_row($field); } ?>
            </table>

            <h3>Layout Settings</h3>
            <table class="form-table">
                <?php foreach ($this->layout_settings_fields as $field) { if ( $field ) { $this->render_form_row($field); } } ?>
            </table>

            <h3>Playback Settings</h3>
            <table class="form-table">
                <?php foreach ($this->playback_settings_fields as $field) { if ( $field ) { $this->render_form_row($field); } } ?>
            </table>

            <h3>Listbar/Playlist Settings</h3>
            <table class="form-table">
                <?php foreach ($this->listbar_settings_fields as $field) { if ( $field ) { $this->render_form_row($field); } } ?>
            </table>

            <?php if ( isset($this->logo_settings_fields) ): ?>
            <h3>Logo/Watermark Settings</h3>
            <table class="form-table">
                <?php foreach ($this->logo_settings_fields as $field) { $this->render_form_row($field); } ?>
            </table>
            <?php endif; ?>

            <?php /*if ( isset($this->rightclick_settings_fields) ): ?>
            <h3>Rightclick Settings</h3>
            <table class="form-table">
                <?php foreach ($this->rightclick_settings_fields as $field) { $this->render_form_row($field); } ?>
            </table>
            <?php endif; */?>

            <?php if ( isset($this->advertising_fields) ): ?>
            <h3>Advertising</h3>
            <table class="form-table">
                <?php foreach ($this->advertising_fields as $field) { $this->render_form_row($field); } ?>
            </table>
            <?php endif; ?>

            <?php if ( isset($this->other_settings_fields) ): ?>
            <h3>Other Settings</h3>
            <table class="form-table">
                <?php foreach ($this->other_settings_fields as $field) { $this->render_form_row($field); } ?>
            </table>
            <?php endif; ?>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button-primary" value="Save changes"  />
            </p>
        </form>
        <?php
        $this->render_page_end();
    }

    protected function render_overview() {
        $this->render_page_start('JW Players');
        $this->render_all_messages();
        ?>
        <h3>Your Players</h3>
        <table class="wp-list-table widefat player-table" cellspacing="0">
            <?php $this->render_overview_header('thead'); ?>
            <tbody>
            <?php foreach ($this->players as $player) { $this->render_overview_row($player); } ?>
            </tbody>
            <?php $this->render_overview_header('tfoot'); ?>
        </table>

        <form method="post" id="add_player_form" name="add_player_form" action="<?php echo $this->page_url(array('noheader'=>'true')) ?>">
            <?php settings_fields(JWP6 . 'menu'); ?>
            <p class="submit">
                <input type="hidden" name="noheader" value="true" />
                <input type="hidden" name="copy_from_player" id="copy_from_player" value="" />
                <input type="submit" name="submit_form" id="submit_form" class="button-primary" value="Create a new player"  />
            </p>
        </form>
        <script type="text/javascript">
        jQuery(function () {
            var jwp6 = new JWP6Admin();
            jwp6.player_copy();
            jwp6.player_delete();
        });
        </script>
        <?php
        $this->render_page_end();
    }

    protected function render_overview_header($type = 'thead') {
        $type = ( 'tfoot' == $type ) ? 'tfoot' : 'thead';
        echo "<$type>";
        ?>
            <tr>
                <th>ID</th>
                <th>Decription</th>
                <th>Dimensions</th>
                <th>Mode</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        <?php
        echo "</$type>";
    }

    protected function render_overview_row($player) {
        $description = $player->get('description');
        if ( $description ) {
            if ( $this->imported_players && array_key_exists($description, $this->imported_players) ) {
                $description .= " <em>(imported JW5 player)</em>";
            }
        } else {
            $description = "<em>no description</em>";
        }
        ?>
        <tr valign="middle">
            <td align="center">
                <strong>
                    <?php echo $player->get_id(); ?>
                </strong>
            </td>
            <td><?php echo $description; ?></td>
            <td><?php echo $player->get('width'); ?> x <?php echo $player->get('height'); ?></td>
            <td><?php echo $player->get('primary'); ?></td>
            <td><a href="<?php echo $player->admin_url($this, 'edit'); ?>" class="button jwp6_edit">Edit</a></td>
            <td><a href="<?php echo $player->admin_url($this, 'copy'); ?>" class="button jwp6_copy">Copy</a></td>
            <td>
                <?php if ( $player->get_id() ): ?>
                <a href="<?php echo $player->admin_url($this, 'delete'); ?>" class="button jwp6_delete">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

}
