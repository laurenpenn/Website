<?php

function vc_textarea_html_form_field($settings, $value) {
    $settings_line = '';
    $dependency = vc_generate_dependencies_attributes($settings);
    if ( function_exists('wp_editor') ) {
        $default_content = $value;
        $output_value = '';
        // WP 3.3+
        ob_start();
        wp_editor($default_content, 'wpb_tinymce_'.$settings['param_name'], array('editor_class' => 'wpb_vc_param_value wpb-textarea visual_composer_tinymce '.$settings['param_name'].' '.$settings['type'], 'media_buttons' => true ) );
        $output_value = ob_get_contents();
        ob_end_clean();
        $settings_line .= $output_value;
    }
    // $settings_line = '<textarea name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-textarea visual_composer_tinymce '.$settings['param_name'].' '.$settings['type'].' '.$settings['param_name'].'_tinymce"' . $dependency . '>'.$settings_value.'</textarea>';
    return $settings_line;
}