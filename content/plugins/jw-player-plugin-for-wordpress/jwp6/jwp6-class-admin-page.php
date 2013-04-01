<?php

class JWP6_Admin_Page {

    // Holds the vars that can be posted
    protected  $form_fields = array();

    // Holds the fields that do not validate
    protected  $form_error_fields = array();

    // Messages to be displayed on top of the page
    protected $messages = array();

    // The baseurl of the Wordpress admin page
    protected $base_url = "admin.php";

    // The id of the admin page, used in the url.
    public $page_slug;

    public function __construct() {
        $notification = get_option(JWP6 . 'notification');
        if ( $notification ) {
            $this->add_message($notification);
            delete_option(JWP6 . 'notification');
        }
    }

    // Python's .get() dictionary method.
    protected function _get($array, $key, $default = false) {
        return ( isset($array[$key]) ) ? $array[$key] : $default;
    }

    public function process_post_data($post_data, $save_to_option = true) {
        foreach($this->form_fields as $field) {
            if ( $field ) {
                $validated = $field->validate($post_data);
                if ( !$validated ) {
                    $field->error = true;
                    array_push($this->form_error_fields, $field);
                }
            }
        }

        if ( $save_to_option && !count($this->form_error_fields) ) {
            foreach ($this->form_fields as $field) {
                $field->save();
            }
            $this->add_message('Your input has been saved successfully.');
        }
    }

    public function render() {
        echo "Should be implemented by child class";
    }

    public function page_url($additional_params = NULL) {
        $params = array();
        if ( $this->page_slug ) {
            $params['page'] = $this->page_slug;
        }
        if ( is_array($additional_params) ) {
            $params = array_merge($params, $additional_params);
        }
        return admin_url($this->base_url . '?' . http_build_query($params));
    }

    public function head_assets() {
    }

    public function add_message($msg, $type = 'updated') {
        array_push($this->messages, array(
            'message'   => $msg,
            'type'      => $type
        ));
    }

    protected function render_all_messages() {
        // Check for post error messages
        if ( count($this->form_error_fields) ) {
            $msg = 'Watch out you gave invalid input for <em>"';
            $errors = array();
            foreach ($this->form_error_fields as $field) {
                $errors[] = $field->label;
            }
            $msg .= implode ('"</em>, <em>"', $errors);
            $msg .= '"</em>. Please correct the input and resubmit to save your input.';
            $this->add_message($msg, 'error');
        }
        if ( count($this->messages) ) {
            foreach ($this->messages as $msg) {
                $this->render_message($msg['message'], $msg['type']);
            }
        } else {
            echo "<div class='divider'></div>";
        }
    }

    protected function form_error_message() {
    }

    protected function render_message($msg, $type = 'updated') {
        $type = ( 'error' == $type ) ? 'error': 'updated fade';
        echo "<div class='$type'><p>";
        if ( 'error' == $type ) {
            echo '<strong>ERROR:</strong> ';
        }
        echo $msg;
        echo '</p></div>';
    }

    protected function render_page_start($title)  {
        ?>
        <div id="wpbody-content" class="jwp6">
            <div class="wrap">
                <div id="icon-jwp6-main" class="icon32"></div>
                <h2><?php echo $title; ?></h2>
        <?php
    }

    protected function render_page_end() {
        ?>
            </div>
            <div class="clear"></div>
        </div><!-- wpbody-content -->
        <div class="clear"></div>
        <?php
    }

    protected function render_form_row($field) {
        ?>
        <tr valign="top">
            <th scope="row">
                <?php $field->render_label(); ?>
            </th>
            <td>
                <?php $field->render_field(); ?>
            </td>
        </tr>
        <?php
    }
}