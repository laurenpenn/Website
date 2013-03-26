<?php
/**
 * Loads attributes hooks.
 */
$dir = dirname(__FILE__);

require_once $dir.'/textarea_html/textarea_html.php';
require_once $dir.'/colorpicker/colorpicker.php';


global $vc_params_list;
$vc_params_list = array('textarea_html', 'colorpicker');
