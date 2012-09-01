<?php
/**
 * @package WICE Contact for Wordpress
 * @author Hansj&ouml;rg Schmidt
 * @version 1.1
 */
 
/*
	Plugin Name: WICE Contact for Wordpress
	Plugin URI: http://www.wice.de/
	Description: WICE Contact for Wordpress
	Author: Hansj&ouml;rg Schmidt
	Version: 1.1
	License: GPL
	Author URI: http://www.wice.de/
	Last change: $LastChangedDate: 2011-03-24 17:10:05 +0100 (Do, 24 Mrz 2011) $
*/

global $wp_version;

if( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if(!function_exists('add_action') || version_compare($wp_version, "2.7", "<"))
{
	if(function_exists('add_action'))
		$exit_msg='The Plugin <em>Page Tags</em> requires Wordpress 2.7 or newer. 
		<a href="http://codex.wordpress.org/Upgrading_Wordpress"> Please Update Wordpress</a> or delete the plugin.';
		else
		$exit_msg='';
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit($exit_msg);
}

if(function_exists('add_action')){
	if(!defined('WP_CONTENT_URL'))
		define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
	if(!defined('WP_CONTENT_DIR'))
		define('WP_CONTENT_DIR', ABSPATH.'wp-content');
	if(!defined('WP_PLUGIN_URL'))
		define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	if(!defined('WP_PLUGIN_DIR'));
		define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
	if(!defined('PLUGINDIR'))
		define('PLUGINDIR', 'wp-content/plugins');
	if(!defined('WP_LANG_DIR'))
		define('WP_LANG_DIR', WP_CONTENT_DIR.'/languages');
		
	define('HS_WCWP', plugin_basename(__FILE__));
	define('HS_WCWP_TEXTDOMAIN', 'hs-wicecontact');
	define('WICE_PLUGIN_URL', '/plugin/wp_contact/contact.cgi');
	
}

if (!class_exists('WiceContact')) {
	class WiceContact {
		var $adminOptionsName = "WiceContactAdminOptions";
		var $error = NULL;
		
		function WiceContact () {
			
			add_action('init', array(&$this, 'hs_wice_contact'));
			add_action('admin_init', array(&$this, 'wice_contact_options_init'));
			add_action('admin_menu', array(&$this, 'wice_contact_options_add_page'));
			add_filter('the_content', array(&$this, 'FilterContent'));
			add_filter('wp_head', array(&$this, 'wice_contact_css'));	
		}
		
		function hs_wice_contact() {
			 // Load localization domain
			if (function_exists('load_plugin_textdomain'))
    			load_plugin_textdomain('hs-wicecontact', false, dirname(plugin_basename(__FILE__)));
		}
		
		function wice_contact_css()
		{
			echo '<!-- CSS Added By WICE Contact for Wordpress Plugin. Version {$ver} -->';
    		echo '<link href="'.WP_PLUGIN_URL.'/hs_wice_contact/wice_contact_css.css" rel="stylesheet" type="text/css" />';
		}
		
		function CheckInput() {
			if(!(isset($_POST['contactersubmit']))) {return false;} // Exit returning false, no input given
			$options = get_option('wice_contact_name');
			$bad_inputs = array("mime-version", "content-type", "cc:", "to:", "bcc:");
			foreach($_POST as $key => $value) {
				foreach($bad_inputs as $bad_input) {
					if(strpos(strtolower($value), strtolower($bad_input)) !== false) {
						$this->error .= __('Unwanted Input: ', HS_WCWP_TEXTDOMAIN).$value;
						return 0;
					}
				}
				if($key == 'EMAIL') {
					if(!(filter_var($value, FILTER_VALIDATE_EMAIL))) 
					{
						$this->error .= __('Not a valid email address: ', HS_WCWP_TEXTDOMAIN).$value;
						return 0;
					}
				}
				if(isset($options[$key]['required']) && (!($value))) 
				{	 
					
					$this->error .= __('Required field:', HS_WCWP_TEXTDOMAIN).' '.$options[$key]['name'];
					return 0;
				}
			}
			return 1;			
		}
		
		function FilterContent($content) {
			$search = '[contact]';
			
			if(strpos($content, $search) === false) {
				return $content;
			}
			if($this->CheckInput()) {
				$this->SendData($_POST);	
			}
			else 
			{
				$form = $this->contacter_buildform();
				$content = str_replace($search, $form, $content);
				return $content;
			}
		}
		
		function SendData($formdata) {
			$postdata;
	
			if($this->wice_contact_check_curl() == false)
			{
				_e('CURL library not detected on system.  Need to compile php with cURL in order to use this plug-in', HS_WCWP_TEXTDOMAIN);
				return false;
			}
			else
			{
				$options = get_option('wice_contact_name');
				$curl = curl_init();
				$server = $options['serveraddress']['text'].WICE_PLUGIN_URL;
				curl_setopt($curl, CURLOPT_URL, $server);
				curl_setopt($curl, CURLOPT_POST, 1);
				
				
				if(isset($formdata['NOCOMPANY']))
				{
					$postdata['COMPANY'] = $formdata['LASTNAME'].', '.$formdata['FIRSTNAME'];
				}
				foreach($formdata as $key => $value) {
					
					
					
					if($key == 'FORMSALUTATION')
					{
						if(isset($formdata['TITLE'])) $title = $formdata['TITLE'].' ';
						$gendersalutation = 'SALUTATION'.$value;
						$genderserialsalutation = 'SERIALSALUTATION'.$value;
						
						$postdata['SALUTATION'] = $options[$gendersalutation]['name'];
						
						$postdata['SERIAL_SALUTATION'] = $options[$genderserialsalutation]['name'].
							' '.$postdata[''].' '.$title.$formdata['LASTNAME'];						
					}
					if(($value) && ($value != 'process')) $postdata[$key]=iconv('UTF-8', 'ISO-8859-1', $value);
					
				}
				unset($postdata['FORMSALUTATION']);
			
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec($curl);
				curl_close ($curl); 
			
				
			}
			return true;
		}
		
		function wice_contact_check_curl() {
 			if (function_exists('curl_exec')) {
				return true;
			} 
			else {
				return false;
 			}
		}
				
		function contacter_buildform()
		{
			
			$theform = '<div><form method="POST" action="">';
			
			$theform .= '<p><table><tr><td colspan="2" align="left"><span class="contacterred">'
				.__('Fields in red are required', HS_WCWP_TEXTDOMAIN).'</span></td></tr>';
			if(isset($this->error)) 
			{
				$theform .= '<tr align="left"><td colspan="2">';
				$theform .= $this->error;
				$theform .= '</td></tr>';
			}
			$options = get_option('wice_contact_name');
			foreach($options as $key => $value)
			{	
				if(($key == 'COMPANY') and ($options[$key]['visible'] == FALSE))
				{
					$theform .= '<input type="hidden" name="NOCOMPANY" value="1">';
				}
				if($options[$key]['visible'] != 0) 
				{					
				  if(($key != 'MESSAGE') and ($key != 'SALUTATIONS') and ($key != 'SALUTATION') 
				  	and ($key != 'SALUTATIONMALE') and ($key != 'SALUTATIONFEMALE') and !(preg_match('/additionalfield/i', $key)))
					{
						$required='';
						if($options[$key]['required'] > 0) $required=' class="contacterred"';
						$theform .= '<tr align="right"><td><span'.$required.'>'.$options[$key]['name'].
							'</span></td><td><input type="text" name="'.$key.'" value="'.$_POST[$key].'"/></td></tr>';	
					}
					elseif($key == 'SALUTATION')
					{
						$required='';
						if($options[$key]['required'] > 0) $required=' class=contacterred"';
						$theform .='<tr align="right"><td><span'.$required.'>'.$options['SALUTATION']['name'].
							'</span></td><td><select name="FORMSALUTATION">';
						$theform.='<option value="MALE">'.$options['SALUTATIONMALE']['name'].'</option>';
						$theform.='<option value="FEMALE">'.$options['SALUTATIONFEMALE']['name'].'</option>';
						$theform .='</select>';				
					}
					elseif($key == 'MESSAGE')
					{
						$required='';
						if($options[$key]['required'] > 0) $required=' class="contacterred"';
						$theform .='<tr align="left"><td colspan="2"><span'.$required.'>'.$options[$key]['name'].
							'</span><br/><textarea name="TICKET_'.$key.'" rows="5" cols="35">'.$_POST[$key].'</textarea>';	
					}
					elseif(preg_match('/additionalfield/i', $key))
					{
						$required='';
						if($options[$key]['required'] > 0) $required=' class="contacterred"';
						$theform .='<tr align="left"><td colspan="2"><span'.$required.'>'.$options[$key]['name'].
							'</span><br/><textarea name="TICKET_'.$key.'" rows="5" cols="35">'.$_POST[$key].'</textarea>';
					}
				}
			}
			
			$theform .= '<input type="hidden" name="contactersubmit" value="process" />';
			$theform .= '<input type="hidden" name="form_id" value="'.$options['form']['id'].'" />';
			$theform .= '</table></p><p><input type="submit" value="'.$options['button']['text'].'"></form></p></div>';
			return $theform;
		}
		
		
		function wice_contact_options_init() {
			register_setting('wice_contact_options', 'wice_contact_name', array(&$this, 'wice_contact_options_validate'));		
		}
		
		function wice_contact_options_add_page() {
			add_options_page( __('WICE Contact', HS_WCWP_TEXTDOMAIN), 
				__('WICE Contact', HS_WCWP_TEXTDOMAIN),
				'manage_options', 'wice_contact_options',
				array(&$this, 'wice_contact_options_do_page'));
		}
		
		function wice_contact_options_do_page() {
			if (!current_user_can('manage_options'))
    		{
      			wp_die(__('You do not have sufficient permissions to access this page.', 
      				HS_WCWP_TEXTDOMAIN));
    		}
			
			?>
			<div class="wrap">
				<h2><?php _e('WICE Contact', HS_WCWP_TEXTDOMAIN);?></h2>
				<form method="post" action="options.php">
					<?php 
						settings_fields('wice_contact_options');
						$options = get_option('wice_contact_name');
					?>
					<table class="form-table">
						<tr valign="top">
							<th>
								<?php  _e('Field', HS_WCWP_TEXTDOMAIN); ?>
							</th>
							<th>
								<?php _e('Name on Website', HS_WCWP_TEXTDOMAIN); ?>
							</th>
							<th>
								<?php _e('Visible', HS_WCWP_TEXTDOMAIN); ?>
							</th>
							<th>
								<?php _e('Required?', HS_WCWP_TEXTDOMAIN); ?>
							</th>
							<th>
								<?php _e('Position', HS_WCWP_TEXTDOMAIN); ?>
							</th>
					   
						</tr>
						<tr>
							<td scope="row" align="center"><h3><?php _e('Companyfields', HS_WCWP_TEXTDOMAIN);?></h3></td>
						</tr>
						<tr>
							<td>
								<?php _e('Company', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[COMPANY][name]" type="text" value="<?php echo $options['COMPANY']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[COMPANY][visible]" type="checkbox" value="1" <?php checked('1', $options['COMPANY']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[COMPANY][required]" type="checkbox" value="1" <?php checked('1', $options['COMPANY']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[COMPANY][position]" size="2" maxlength="2" type="text" value="<?php echo $options['COMPANY']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Street', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[STREET][name]" type="text" value="<?php echo $options['STREET']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[STREET][visible]" type="checkbox" value="1" <?php checked('1', $options['STREET']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[STREET][required]" type="checkbox" value="1" <?php checked('1', $options['STREET']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[STREET][position]" size="2" maxlength="2" type="text" value="<?php echo $options['STREET']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Streetnumber', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[STREETNUMBER][name]" type="text" value="<?php echo $options['STREETNUMBER']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[STREETNUMBER][visible]" type="checkbox" value="1" <?php checked('1', $options['STREETNUMBER']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[STREETNUMBER][required]" type="checkbox" value="1" <?php checked('1', $options['STREETNUMBER']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[STREETNUMBER][position]" size="2" maxlength="2" type="text" value="<?php echo $options['STREETNUMBER']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Zipcode', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[ZIPCODE][name]" type="text" value="<?php echo $options['ZIPCODE']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[ZIPCODE][visible]" type="checkbox" value="1" <?php checked('1', $options['ZIPCODE']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[ZIPCODE][required]" type="checkbox" value="1" <?php checked('1', $options['ZIPCODE']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[ZIPCODE][position]" size="2" maxlength="2" type="text" value="<?php echo $options['ZIPCODE']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Town', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[TOWN][name]" type="text" value="<?php echo $options['TOWN']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[TOWN][visible]" type="checkbox" value="1" <?php checked('1', $options['TOWN']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[TOWN][required]" type="checkbox" value="1" <?php checked('1', $options['TOWN']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[TOWN][position]" size="2" maxlength="2" type="text" value="<?php echo $options['TOWN']['position']; ?>" />
							</td>
						</tr>
						
						<tr>
							<td>
								<?php _e('Country', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[COUNTRY][name]" type="text" value="<?php echo $options['COUNTRY']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[COUNTRY][visible]" type="checkbox" value="1" <?php checked('1', $options['COUNTRY']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[COUNTRY][required]" type="checkbox" value="1" <?php checked('1', $options['COUNTRY']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[COUNTRY][position]" size="2" maxlength="2" type="text" value="<?php echo $options['COUNTRY']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td scope="row" align="center"><h3><?php _e('Contactpersonfields', HS_WCWP_TEXTDOMAIN);?></h3></td>
						</tr>
						<tr>
							<td>
								<?php _e('Salutation', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[SALUTATION][name]" type="text" value="<?php echo $options['SALUTATION']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[SALUTATION][visible]" type="checkbox" value="1" <?php checked('1', $options['SALUTATION']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[SALUTATION][required]" type="checkbox" value="1" <?php checked('1', $options['SALUTATION']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[SALUTATION][position]" size="2" maxlength="2" type="text" value="<?php echo $options['SALUTATION']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Salutation Male', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[SALUTATIONMALE][name]" type="text" value="<?php echo $options['SALUTATIONMALE']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[SALUTATIONMALE][visible]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONMALE']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[SALUTATIONMALE][required]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONMALE']['required']); ?> />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Salutation Female', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[SALUTATIONFEMALE][name]" type="text" value="<?php echo $options['SALUTATIONFEMALE']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[SALUTATIONFEMALE][visible]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONFEMALE']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[SALUTATIONFEMALE][required]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONFEMALE']['required']); ?> />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Serial Salutation Male', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[SERIALSALUTATIONMALE][name]" type="text" value="<?php echo $options['SERIALSALUTATIONMALE']['name']; ?>" />
							</td>
							<!-- 
							<td>
								<input name="wice_contact_name[SERIALSALUTATIONS][visible]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONS']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[SERIALSALUTATIONS][required]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONS']['required']); ?> />
							</td>-->
						</tr>
						<tr>
							<td>
								<?php _e('Serial Salutation Female', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[SERIALSALUTATIONFEMALE][name]" type="text" value="<?php echo $options['SERIALSALUTATIONFEMALE']['name']; ?>" />
							</td>
							<!-- 
							<td>
								<input name="wice_contact_name[SERIALSALUTATIONS][visible]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONS']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[SERIALSALUTATIONS][required]" type="checkbox" value="1" <?php checked('1', $options['SALUTATIONS']['required']); ?> />
							</td>-->
						</tr>
						<tr>
							<td>
								<?php _e('Title', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[TITLE][name]" type="text" value="<?php echo $options['TITLE']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[TITLE][visible]" type="checkbox" value="1" <?php checked('1', $options['TITLE']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[TITLE][required]" type="checkbox" value="1" <?php checked('1', $options['TITLE']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[TITLE][position]" size="2" maxlength="2" type="text" value="<?php echo $options['TITLE']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Firstname', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[FIRSTNAME][name]" type="text" value="<?php echo $options['FIRSTNAME']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[FIRSTNAME][visible]" type="checkbox" value="1" <?php checked('1', $options['FIRSTNAME']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[FIRSTNAME][required]" type="checkbox" value="1" <?php checked('1', $options['FIRSTNAME']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[FIRSTNAME][position]" size="2" maxlength="2" type="text" value="<?php echo $options['FIRSTNAME']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Lastname', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[LASTNAME][name]" type="text" value="<?php echo $options['LASTNAME']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[LASTNAME][visible]" type="checkbox" value="1" <?php checked('1', $options['LASTNAME']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[LASTNAME][required]" type="checkbox" value="1" <?php checked('1', $options['LASTNAME']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[LASTNAME][position]" size="2" maxlength="2" type="text" value="<?php echo $options['LASTNAME']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Phone', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[PHONE][name]" type="text" value="<?php echo $options['PHONE']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[PHONE][visible]" type="checkbox" value="1" <?php checked('1', $options['PHONE']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[PHONE][required]" type="checkbox" value="1" <?php checked('1', $options['PHONE']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[PHONE][position]" size="2" maxlength="2" type="text" value="<?php echo $options['PHONE']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Fax', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[FAX][name]" type="text" value="<?php echo $options['FAX']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[FAX][visible]" type="checkbox" value="1" <?php checked('1', $options['FAX']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[FAX][required]" type="checkbox" value="1" <?php checked('1', $options['FAX']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[FAX][position]" size="2" maxlength="2" type="text" value="<?php echo $options['FAX']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e('Email', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[EMAIL][name]" type="text" value="<?php echo $options['EMAIL']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[EMAIL][visible]" type="checkbox" value="1" <?php checked('1', $options['EMAIL']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[EMAIL][required]" type="checkbox" value="1" <?php checked('1', $options['EMAIL']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[EMAIL][position]" size="2" maxlength="2" type="text" value="<?php echo $options['EMAIL']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td scope="row" align="center"><h3><?php _e('Textbox for a Message', HS_WCWP_TEXTDOMAIN);?></h3></td>
						</tr>
						<tr>
							<td>
								<?php _e('Message', HS_WCWP_TEXTDOMAIN);?>
							</td>
							<td>
								<input name="wice_contact_name[MESSAGE][name]" type="text" value="<?php echo $options['MESSAGE']['name']; ?>" />
							</td>
							<td>
								<input name="wice_contact_name[MESSAGE][visible]" type="checkbox" value="1" <?php checked('1', $options['MESSAGE']['visible']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[MESSAGE][required]" type="checkbox" value="1" <?php checked('1', $options['MESSAGE']['required']); ?> />
							</td>
							<td>
								<input name="wice_contact_name[MESSAGE][position]" size="2" maxlength="2" type="text" value="<?php echo $options['MESSAGE']['position']; ?>" />
							</td>
						</tr>
						<tr>
							<td scope="row" align="center"><h3><?php _e('Define additional Fields', HS_WCWP_TEXTDOMAIN);?></h3></td>
						</tr>
						<tr>
							<td><?php _e('Number of additional Fields', HS_WCWP_TEXTDOMAIN);?></td>
							<td><input name="wice_contact_name[numofadditionalfields][text]" type="text" size="1" maxlength="1" value="<?php echo $options['numofadditionalfields']['text']; ?>" /></td>
						</tr>
						<?php 
							if($options['numofadditionalfields']['text'] < 1)
							{
								echo '<tr><td scope="row" align="center">';
								_e('No additional Fields set. Please input first the amount of additional fields', HS_WCWP_TEXTDOMAIN);
								echo '</td></tr>';
							}
							else 
							{	
								$i=1;
								do
								{
									$field = 'additionalfield'.$i;
									echo '<tr>';
									echo '<td>';
									_e('Name of Field ', HS_WCWP_TEXTDOMAIN);
									echo ' '.$i;
									echo '</td>';
									echo '<td><input name="wice_contact_name['.$field.'][name]" type="text" value="'.$options[$field]['name'].'" /></td>';
									echo '<td>';
									echo '<input name="wice_contact_name['.$field.'][visible]" type="checkbox" value="1"';
									checked('1', $options[$field]['visible']);
									echo '/>';
									echo '</td>';
									echo '<td>';
									echo '<input name="wice_contact_name['.$field.'][required]" type="checkbox" value="1"';
									checked('1', $options[$field]['required']);
									echo '/>';
									echo '</td>';
									echo '<td>';
									echo '<input name="wice_contact_name['.$field.'][position]" size="2" maxlength="2" type="text" value="'.$options[$field]['position'].'" /></td>';
									echo '</tr>';
									$i++;
								
								} while($i<=$options['numofadditionalfields']['text']);
							}
						?>
						<tr>
							<td scope="row" align="center"><h3><?php _e('Additional Settings', HS_WCWP_TEXTDOMAIN);?></h3></td>
						</tr>
						<tr>
							<td scope="row"><?php _e('Address of your Server', HS_WCWP_TEXTDOMAIN);?><input name="wice_contact_name[serveraddress][text]" type="text" value="<?php echo $options['serveraddress']['text']; ?>" />
								<input type="hidden" name="wice_contact_name[serveraddress][visible]" value="0"></input>
							</td>
						</tr>
						<tr>
							<td scope="row"><?php _e('Text for Submitbutton', HS_WCWP_TEXTDOMAIN);?><input name="wice_contact_name[button][text]" type="text" value="<?php echo $options['button']['text']; ?>" />
								<input type="hidden" name="wice_contact_name[button][visible]" value="0"></input>
							</td>
						</tr>
						<tr>
							<td scope="row"><?php _e('Value of FormID', HS_WCWP_TEXTDOMAIN);?> <input name="wice_contact_name[form][id]" size="2" maxlength="2" type="text" value="<?php echo $options['form']['id']; ?>" />
								<input type="hidden" name="wice_contact_name[form][visible]" value="0"></input>
							</td>
							
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary"
							value="<?php _e('Save Changes', HS_WCWP_TEXTDOMAIN);?>"></input>
					</p>
				</form>
			</div>
			<?php
			
		}
		
		function sortier_funktion($a, $b) {
			print_r($a);
			print_r($b);
			return strnatcasecmp($a['position'],$b['position']);
		}
		
		function wice_contact_options_validate($input) {
			//$input['myoption1'] = ($input['myoption1'] == 1 ? 1 : 0);
			
			//$input['mytext'] = wp_filter_nohtml_kses($input['mytext']);
			foreach($input as $key => $value)
			{
				$position[$key] = $value['position'];
				$input[$key][name] = wp_filter_nohtml_kses($input[$key][name]);
			}
			// sort Input by Position
			array_multisort($position, SORT_ASC, $input);
			return $input;
		}	
			
	}
	
	$WiceContact = new WiceContact();
}



?>