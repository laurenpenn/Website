<?php

class JWP6_Form_Field {

    // The name of the field
    // Needs to be set when instantiating the class
    public $name;

    // The name to display e.g. in the label
    public $label;

    // The value of the field
    public $value;

    // A closure / lambda function to validate form input
    public $validation;

    // The help text below the field
    public $help_text = '';

    // The text to display when the input is wrong
    public $error_help = '';

    // The text to display as a placeholder.
    public $placeholder = '';

    // Text to be added after the text field. Normally used to indicate the unit
    public $unit;

    // The class for the field
    public $class = '';

    // The type of the field
    public $type = 'text';

    // If the value has an error
    public $error = false;

    // The value that the method was given in the post request.
    protected $post_value = NULL;

    public static function dummy_validation($value) {
        return $value;
    }

    public function __construct($name, $settings = array()) {
        $this->name = $name;
        $this->label = $this->nice_name();
        $this->validation = array("JWP6_Form_Field", "dummy_validation");
        foreach ($settings as $key => $value) {
            if ( property_exists($this, $key) ) {
                $this->{$key} = $value;
            }
        }
        if ( !isset($this->value) ) {
            $this->value = get_option(JWP6 . $this->name);
        }
    }

    public function nice_name() {
        $name = ( strpos($this->name,'__') ) ? end(explode('__', $this->name)) : $this->name;
        return ucwords( str_replace( '_', ' ', $name ) );
    }

    public function validate($post_data) {
        if ( !is_callable($this->validation) ) {
            die('Make sure to use a callable validation!');
        }
        if ( array_key_exists($this->name, $post_data) ) {
            $this->post_value = $post_data[$this->name];
            $this->value = call_user_func($this->validation, $this->post_value);
            if ( is_null($this->value) ) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    public function save() {
        update_option(JWP6 . $this->name, $this->value);
    }

    public function render_field($value = NULL) {
        $this->class = ( $this->error ) ? $this->class . " error": $this->class;
        $this->value = ( is_null($value) ) ? $this->value : $value;
        echo '<input name="' . $this->name . '" type="' . $this->type . '" ';
        echo 'id="' . $this->name . '" placeholder="' . $this->placeholder . '" ';
        echo 'class="' . $this->class . '" value="' . esc_attr($this->value) . '" />' . "\n";
        if ( $this->unit ) {
            echo "<span class='unit'>{$this->unit}</span>";
        }
        $this->render_help();
    }

    public function render_label() {
        echo '<label for="' . esc_attr($this->name) . '">' . $this->label . "</label>\n";
    }

    public function render_help() {
        if ( $this->error ) {
            echo '<p class="description error">' . $this->error_help . '</p>';
        }
        echo '<p class="description">' . $this->help_text . '</p>';
    }

}

class JWP6_Form_Field_Uneditable extends JWP6_Form_Field {

    public $why_not = 'You cannot edit the value for this setting.';

    public function render_field($value = '') {
        echo "<strong>{$this->value}</strong>";
        if ( $this->unit ) echo "<span class='unit'>{$this->unit}</span>";
        if ( $this->why_not ) echo '<p class="description">' . $this->why_not . '</p>';
    }

    public function save() {
        // does nothing, it's uneditable...
    }

    public function validate() {
        return true;
    }

}

class JWP6_Form_Field_Select extends JWP6_Form_Field {

    public $options = array();

    public $description_is_value = false;

    public $default;

    public function validate($post_data) {
        if ( array_key_exists($this->name, $post_data) ) {
            $this->post_value = $post_data[$this->name];
            if ( 
                ( $this->description_is_value && in_array($this->post_value, $this->options) )
                ||
                ( !$this->description_is_value && array_key_exists($this->post_value, $this->options) )
            ) {
                $this->value = $this->post_value;
                return true;
            }
        }
        return false;
    }

    public function render_field() {
        $selected_value = ( is_null($this->post_value) ) ? $this->value : $this->post_value;
        echo "<select id='id_{$this->name}' name='{$this->name}' class='{$this->class}'>\n";
        foreach ($this->options as $value => $description) {
            $value = ( $this->description_is_value ) ? $description : $value;
            if ( $selected_value )  {
                $selected = ( $selected_value == $value ) ? 'selected="selected"' : "";
            } else {
                $selected = ( $this->default == $value ) ? 'selected="selected"' : "";
            }
            echo "\t<option $selected value='" . esc_attr($value) . "'>";
            echo ucfirst($description);
            echo "</option>\n";
        }
        echo "</select>";
        $this->render_help();
    }

}

class JWP6_Form_Field_Radio extends JWP6_Form_Field {
    
    public $options = array();

    public $description_is_value = false;

    public $single_line = false;

    public $default;

    public function validate($post_data) {
        if ( array_key_exists($this->name, $post_data) ) {
            $this->post_value = $post_data[$this->name];
            if ( 
                ( $this->description_is_value && in_array($this->post_value, $this->options) )
                ||
                ( !$this->description_is_value && array_key_exists($this->post_value, $this->options) )
            ) {
                $this->value = $this->post_value;
                return true;
            }
        }
        return false;
    }

    public function render_field() {
        $checked_value = ( is_null($this->post_value) ) ? $this->value : $this->post_value;
        $last_value = array_pop(array_keys($this->options));
        echo "<fieldset>";
        foreach ($this->options as $value => $description) {
            $value = ( $this->description_is_value ) ? $description : $value;
            if ( $checked_value )  {
                $checked = ( $checked_value == $value ) ? 'checked="checked"' : "";
            } else {
                $checked = ( $this->default == $value ) ? 'checked="checked"' : "";
            }
            echo "\t<label title='" . esc_attr($value) . "'>";
            echo "\t\t<input type='radio' value='". esc_attr($value) . "' name='{$this->name}' {$checked} value='{$value}' />";
            echo "\t\t" . '<span>&nbsp;&nbsp;' . ucfirst($description) . '</span>';
            echo "\t</label>";
            if ( $value != $last_value ) {
                echo ( $this->single_line ) ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : "\t<br />";
            }
        }
        echo "</fieldset>";
        $this->render_help();
    }

}

class JWP6_Form_Field_Toggle extends JWP6_Form_Field {

    public $text = 'Toggle on or off';

    public function render_field() {
        $log = ( $this->value ) ? 'yes' : 'no';
        $checked = ( $this->value ) ? "checked='checked'" : '';
        echo "<fieldset><label for='{$this->name}'>";
        echo "<input type='checkbox' {$checked} name='{$this->name}' value='1' /> ";
        echo $this->text;
        echo "</label></fieldset>";
        $this->render_help();
    }

    public function validate($post_data) {
        $this->post_value = ( array_key_exists($this->name, $post_data) ) ? true : false;
        $this->value = $this->post_value;
        return true;
    }

}
