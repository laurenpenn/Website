<?php
/**
 * Get the list of styles
 */

function _get_style_options()
{
    global $addthis_new_styles;
    return apply_filters('addthis_style_options', $addthis_new_styles );
}

/**
 * AddThis replacement for kses
 *
 */
function addthis_kses($string, $customstyles)
{
	global $allowedposttags;
    $mytags = $allowedposttags;
    $mytags['a'][ 'gplusonesize' ] = array();
    $mytags['a'][ 'gplusonecount' ]= array();
    $mytags['a'][ 'gplusoneannotation' ]= array();
    $mytags['a'][ 'fblikelayout' ]= array();
    $mytags['a'][ 'fblikesend' ]= array();
    $mytags['a'][ 'fblikeshow_faces' ]= array();
    $mytags['a'][ 'fblikewidth' ]= array();
    $mytags['a'][ 'fblikeaction' ]= array();
    $mytags['a'][ 'fblikefont' ]= array();
    $mytags['a'][ 'fblikecolorscheme' ]= array();
    $mytags['a'][ 'fblikeref' ]= array();
    $mytags['a'][ 'fblikehref' ]= array();
    $mytags['a'][ 'twcount' ]= array();
    $mytags['a'][ 'twurl' ]= array();
    $mytags['a'][ 'twvia' ]= array();
    $mytags['a'][ 'twtext' ]= array();
    $mytags['a'][ 'twrelated' ]= array();
    $mytags['a'][ 'twlang' ]= array();
    $mytags['a'][ 'twhashtags' ]= array();
    $mytags['a'][ 'twcounturl' ]= array();
    $mytags['a'][ 'pipinitlayout' ]= array();
    $mytags['a'][ 'pipiniturl' ]= array();
    $mytags['a'][ 'pipinitmedia' ]= array();
    $mytags['a'][ 'pipinitdescription' ]= array();
    
    $pretags = array( 'g:plusone:', 'fb:like:', 'tw:', 'pi:pinit:');
    $posttags = array('gplusone', 'fblike', 'tw', 'pipinit');

    foreach($pretags as $i => $attr)
    {
        $pre_pattern[] = '/'.$attr.'/';
        $pretags[$i] = ' '.$attr;
    }
    foreach($posttags as $i => $attr)
    {
        $post_pattern[] = '/[^_]'.$attr.'/';
        $posttags[$i] = ' '.$attr;
    }
    $temp_string = preg_replace( $pre_pattern, $posttags, $string);
    $new_temp_string = wp_kses($temp_string, $mytags);
    $new_string = preg_replace( $post_pattern, $pretags, $new_temp_string);
    // Add in our %s so that the url and title get added properly

    $new_string = substr_replace($new_string, $customstyles, 4, 0);
    
    return $new_string;
}
/**
 * Add this version notification message
 * @param int $atversion_update_status
 * @param int $atversion
 */
function _addthis_version_notification($atversion_update_status, $atversion)
{
    //Fresh install Scenario. ie., atversion = 300 without reverting back. 
    if($atversion_update_status == ADDTHIS_ATVERSION_AUTO_UPDATE && $atversion >= ADDTHIS_ATVERSION) {
            return;
    }
    $imgLocationBase = apply_filters( 'addthis_files_uri',  plugins_url( '' , basename(dirname(__FILE__)))) . '/addthis/img/'  ;
    ob_start();
    // In the automatic update by the system the $atversion_update_status is 0
    // On subsequent update using notification link the  $atversion_update_status = -1
    // In both cases display the revert link
    if ($atversion_update_status == ADDTHIS_ATVERSION_AUTO_UPDATE || $atversion_update_status == ADDTHIS_ATVERSION_MANUAL_UPDATE) {
        ?>
        <div class="addthis-notification addthis-success-message">
            <div style="float:left">Your AddThis sharing plugin has been updated.</div>
            <div style="float:right">
                <a href="#" class="addthis-revert-atversion">Revert back to previous version</a>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="addthis-notification addthis-warning-message">
            <div style="float:left">Update AddThis to activate new features that will make sharing even easier.</div>
            <div style="float:right">
                <a href="#" class="addthis-update-atversion"><img src="<?php echo $imgLocationBase . 'update.png';?>" /></a>
            </div>
        </div>       
        <?php
    }
    $notification_content = ob_get_contents();
    ob_end_clean();
    return $notification_content;
}
/**
 * Swap the order of occurrence of two keys in an associative array
 * @param type $array
 * @param type $key1
 * @param type $key2
 */
function _addthis_swap_first_two_elements (&$array, $key)
{
   $temp        = array($key => $array[$key]);
   unset($array[$key]);
   $array = $temp + $array;
}
/**
 * The icon choser row.  Should be made to look a bit prettier
 */
 function _addthis_choose_icons($name, $options)
 {
     $addthis_new_styles = _get_style_options();
     global $addthis_default_options;
     extract($options);
     if ($name == 'above')
     {
        _addthis_swap_first_two_elements($addthis_new_styles, 'large_toolbox');
        $legend = 'Top';
        $option = $above;
        $custom_size = $above_custom_size;
        $do_custom_services  = ( isset( $above_do_custom_services ) && $above_do_custom_services  ) ? 'checked="checked"' : '';
        $do_custom_preferred = ( isset( $above_do_custom_preferred ) &&  $above_do_custom_preferred ) ? 'checked="checked"' : '';
        $custom_services = $above_custom_services;
        $custom_preferred  = $above_custom_preferred;
        $custom_more = $above_custom_more;
        $custom_string = $above_custom_string;
     }
     else
     {
        $legend = 'Bottom';
        $option = $below;
        $custom_size =  $below_custom_size;
        $do_custom_services  = ( isset( $below_do_custom_services ) && $below_do_custom_services  ) ? 'checked="checked"' : '';
        $do_custom_preferred = ( isset( $below_do_custom_preferred ) &&  $below_do_custom_preferred ) ? 'checked="checked"' : '';
        $custom_services = $below_custom_services;
        $custom_preferred  = $below_custom_preferred;
        $custom_more = $below_custom_more;
        $custom_string = $below_custom_string;
     }
?>
        <tr>
            <td id="<?php echo $name ?>" colspan="2">
              <fieldset>  
		<legend>&nbsp;<strong><?php _e("$legend Sharing Tool", 'addthis_trans_domain') ?></strong> &nbsp;</legend>
                
		<?php 
                    $sharing_checked = $option == 'none' ? '' : 'checked="checked"';
                    echo "<div class='enable-sharing-tool'><input type='checkbox' {$sharing_checked} value='{$option}' id='enable_{$name}' name='addthis_settings[enable_{$name}]' />
                <label for='enable_{$name}'>";
                echo _e('Enable the following sharing tool at the ', 'addthis_trans_domain' );
                echo '<strong>';
                echo _e($legend, 'addthis_trans_domain');
                echo '</strong> of posts:</label></div>';
                    
                 $imgLocationBase = apply_filters( 'at_files_uri',  plugins_url( '' , basename(dirname(__FILE__)))) . '/addthis/img/'  ;
                 $imgLocationBase = apply_filters( 'addthis_files_uri',  plugins_url( '' , basename(dirname(__FILE__)))) . '/addthis/img/'  ;
                
                foreach ($addthis_new_styles as $k => $v)
                {
                    $checked = '';
                    if ($option == $k || ($option == 'none' && $k == $addthis_default_options[$name]  ) ){
                        $checked = 'checked="checked"';
                    }
                    if ($checked === '' && isset($v['defaultHide']) &&  $v['defaultHide'] == true)
                        continue;
                    echo "<div class='$name"."_option select_row'><span class='radio'><input $checked type='radio' value='".$k."' id='{$k}_{$name}' name='addthis_settings[$name]' /></span><label for='{$k}_{$name}'> <img alt='".$k."'  src='". $imgLocationBase  .  $v['img'] ."' align='left' /></label><div class='clear'></div></div>";
                }
                
                $class = 'hidden';
                $checked = '';
                if ($option == 'custom' || ($option == 'none' && 'custom' == $addthis_default_options[$name]  ) ){
                    $checked = 'checked="checked"';
                    $class = '';

                    echo "<div class='$name"."_option select_row $class mt20'><span class='radio mt4'><input $checked type='radio' value='custom' name='addthis_settings[$name]' id='$name"."_custom_button' /></span> Build your own<div class='clear'></div></div>";

                    echo "<ul class='$name"."_option_custom hidden'>";
                    $custom_16 = ($custom_size == 16) ? 'selected="selected"' : '' ;
                    $custom_32 = ($custom_size == 32) ? 'selected="selected"' : '' ;

                    echo "<li class='nocheck'><span class='at_custom_label'>Size:</span><select name='addthis_settings[$name"."_custom_size]'><option value='16' $custom_16 >16x16</option><option value='32' $custom_32 >32x32</option></select><br/><span class='description'>The size of the icons to display</span></li>";
                    echo "<li><input $do_custom_services class='at_do_custom'  type='checkbox' name='addthis_settings[$name"."_do_custom_services]' value='true' /><span class='at_custom_label'>Services to always show:</span><input class='at_custom_input' name='addthis_settings[$name"."_custom_services]' value='$custom_services'/><br/><span class='description'>Enter a comma-separated list of <a href='//addthis.com/services'>service codes</a> </span></li>";
                    echo "<li><input type='checkbox' $do_custom_preferred class='at_do_custom'  name='addthis_settings[$name"."_do_custom_preferred]' value='true' /><span class='at_custom_label'>Automatically personalized:</span>
                        <select name='addthis_settings[$name"."_custom_preferred]' class='at_custom_input'>";
                        for($i=0; $i <= 11; $i++)
                        {
                            $selected = '';
                            if ($custom_preferred == $i)
                                $selected = 'selected="selected"';
                            echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';

                        }
                    echo "</select><br/><span class='description'>Enter the number of automatically user-personalized items you want displayed</span></li>";
                   $custom_more = ( $custom_more ) ? 'checked="checked"' : '';
                    
                    echo "<li><input $custom_more type='checkbox' class='at_do_custom' name='addthis_settings[$name"."_custom_more]' value='true' /><span class='at_custom_label'>More</span><br/><span class='description'>Display our iconic logo that offers sharing to over 330 destinations</span></li>";
                    echo "</ul></div>";
                }
               
                    $checked = '';
                    if ($option == 'custom_string' || $option == 'none' && 'custom_strin' == $addthis_default_options[$name] )
                    {
                        $checked = 'checked="checked"';
                    }

                    echo "<div class='$name"."_option select_row'><span class='radio mt4'><input $checked type='radio' value='custom_string' name='addthis_settings[$name]' id='$name"."_custom_string' /></span> <label for='{$name}_custom_string'>Custom button</label><div class='clear'></div></div>";
                    _e( sprintf("<div style='max-width: 748px;margin-left:20px' class='%s_custom_string_input'> This text box allows you to enter any AddThis markup that you wish. To see examples of what you can do, visit <a href='https://www.addthis.com/get/sharing'>AddThis.com Sharing Tools</a> and select any sharing tool. You can also check out our <a href='http://support.addthis.com/customer/portal/articles/381263-addthis-client-api#rendering-decoration'>Client API</a>. For any help you may need, please visit <a href='http://support.addthis.com'>AddThis Support</a></div>", $name ),'addthis_trans_domain');
                    echo "<textarea style='max-width:748px;margin-left:20px'  rows='5' cols='120' name='addthis_settings[$name"."_custom_string]' class='$name"."_custom_string_input' />".esc_textarea($custom_string)."</textarea>";

                    echo '</div>';
                ?>
                               				
			  </fieldset>	
            </td>
        </tr>

<?php
 }


?>
