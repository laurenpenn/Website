<?php
/*
Plugin Name: WP Export Users Plus
Plugin URI: http://greghetrick.com/wp-export-users-plus
Description: Export user's address, city, state, zip, country and phone of users.
Version: 1.0
Author: Greg Hetrick (modified from the original works of Matthew Price)
Author URI: http://greghetrick.com/wp-export-users-plus
License: GPL2
*/

if($_GET['action']); {

	switch ($_GET['action']) {
		
	case 'generate-custom-user-list': generate_custom_user_list(); break;

	}
}

function wp_export_users() {
	
global $wpdb;

if ($_GET['user_login'] != '') { $get_user_login = $_GET['user_login'] . ","; }
if ($_GET['disp_name'] != '') { $get_disp_name = $_GET['disp_name'] . ","; }
if ($_GET['user_email'] != '') { $get_user_email = $_GET['user_email'] . ","; }
if ($_GET['user_pass'] != '') { $get_user_pass = $_GET['user_pass'] . ","; }
if ($_GET['user_url'] != '') { $get_user_url = $_GET['user_url'] . ","; }
if ($_GET['user_role'] != '') { $get_user_role = $_GET['user_role'] . ","; }
if ($_GET['first_name'] != '') { $get_first_name = $_GET['first_name'] . ","; }
if ($_GET['last_name'] != '') { $get_last_name = $_GET['last_name'] . ","; }
if ($_GET['addr1'] != '') { $get_addr1 = $_GET['addr1'] . ","; }
if ($_GET['city'] != '') { $get_city = $_GET['city'] . ","; }
if ($_GET['thestate'] != '') { $get_thestate = $_GET['thestate'] . ","; }
if ($_GET['zip'] != '') { $get_zip = $_GET['zip'] . ","; }
if ($_GET['country'] != '') { $get_country = $_GET['country'] . ","; }
if ($_GET['phone1'] != '') { $get_phone1 = $_GET['phone1'] . ","; }

$encapsulator = $_GET['encapsulator'];
$separator = $_GET['separator'];
$headers = $_GET['headers'];

$select = "ID," . $get_user_login . $get_disp_name . $get_user_email . $get_user_pass . $get_user_url;
$select = substr($select, 0, -1);
// print_r($select);

$users = $wpdb->get_results("SELECT $select FROM {$wpdb->prefix}users");

?>
<div class="wrap">
<h2>WP Export Users Plus Settings</h2>
		<p>If you found this plugin useful, please consider making a small donation, thank you =)...
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="J2MM7GW9XZWQA">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	</p>Select the check boxes below for the items you'd like to appear in your exported data.
    
    <div id="left">
<form action="?action=generate-custom-user-list" method="post">

<div class="options">
<label for="user_login">User Login</label>
<input type="checkbox" id="user_login" name="user_login" <?php if ($_GET['user_login'] != '') { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="user_email">First Name</label>
<input type="checkbox" id="first_name" name="first_name" <?php if ($_GET['first_name'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="user_email">Last Name</label>
<input type="checkbox" id="last_name" name="last_name" <?php if ($_GET['last_name'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="user_email">Email</label>
<input type="checkbox" id="user_email" name="user_email" <?php if ($_GET['user_email'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="user_pass">Password (Encrypted)</label>
<input type="checkbox" id="user_pass" name="user_pass" <?php if ($_GET['user_pass'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="disp_name">Display Name</label>
<input type="checkbox" id="disp_name" name="disp_name" <?php if ($_GET['disp_name'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="user_email">User Url</label>
<input type="checkbox" id="user_url" name="user_url" <?php if ($_GET['user_url'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="addr1">Address</label>
<input type="checkbox" id="addr1" name="addr1" <?php if ($_GET['addr1'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="city">City</label>
<input type="checkbox" id="city" name="city" <?php if ($_GET['city'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="thestate">State</label>
<input type="checkbox" id="thestate" name="thestate" <?php if ($_GET['thestate'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="zip">Zip</label>
<input type="checkbox" id="zip" name="zip" <?php if ($_GET['zip'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="country">Country</label>
<input type="checkbox" id="country" name="country" <?php if ($_GET['country'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="phone1">Phone</label>
<input type="checkbox" id="phone1" name="phone1" <?php if ($_GET['phone1'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="user_email">User Role</label>
<input type="checkbox" id="user_role" name="user_role" <?php if ($_GET['user_role'] != "") { echo 'checked="checked"'; } ?>>
</div>

<div class="options">
<label for="separator">Field Ecapsulation<br>
<span>
Please select the field separator needed for your output file. Typical separators are "comma", "semi-colon", "pipe".<br>
</span>
</label>
<select name="encapsulator">
<option value="comma" <?php if ($encapsulator == "comma") { echo 'selected="selected"'; } ?>>Comma (,)</option>
<option value="semicolon" <?php if ($encapsulator == "semicolon") { echo 'selected="selected"'; } ?>>Semi-Colon (;)</option>
<option value="pipe" <?php if ($encapsulator == "pipe") { echo 'selected="selected"'; } ?>>Pipe (|)</option>
<option value="newline" <?php if ($encapsulator == "newline") { echo 'selected="selected"'; } ?>>Line Break</option>
<option value="squote" <?php if ($encapsulator == "squote") { echo 'selected="selected"'; } ?>>Single Quote (')</option>
<option value="dquote" <?php if ($encapsulator == "dquote") { echo 'selected="selected"'; } ?>>Double Quote (")</option>
<option value="none" <?php if ($encapsulator == "none") { echo 'selected="selected"'; } ?>>None</option>
</select>
</div>

<div class="options">
<label for="separator">Field Separator<br>
<span>
Please select the record/user separator needed for your output file.<br>
</span>
</label>
<select name="separator">
<option value="comma" <?php if ($separator == "comma") { echo 'selected="selected"'; } ?>>Comma (,)</option>
<option value="semicolon" <?php if ($separator == "semicolon") { echo 'selected="selected"'; } ?>>Semi-Colon (;)</option>
<option value="pipe" <?php if ($separator == "pipe") { echo 'selected="selected"'; } ?>>Pipe (|)</option>
<option value="newline" <?php if ($separator == "newline") { echo 'selected="selected"'; } ?>>Line Break</option>
<option value="squote" <?php if ($separator == "squote") { echo 'selected="selected"'; } ?>>Single Quote (')</option>
<option value="dquote" <?php if ($separator == "dquote") { echo 'selected="selected"'; } ?>>Double Quote (")</option>
<option value="none" <?php if ($separator == "none") { echo 'selected="selected"'; } ?>>None</option>
</select>
</div>

<div class="options">
Here is an explanation of both options above...<br><br>
<span class="red">Field Separator</span><br>
<span class="blue">Field Encapsulator</span>
<br><br>
example:  <span class="blue">"</span>Field Value #1<span class="blue">"</span><span class="red">,</span><span class="blue">"</span>Field Value #2<span class="blue">"</span>
</div>

<div class="options">
<label for="headers">Add field headers? (if yes, check box)</label>
<input type="checkbox" id="headers" name="headers" <?php if ($_GET['headers'] != '') { echo 'checked="checked"'; } ?>
</div>

<br />
<h3>Generate List:<br />
  <input type="submit" value="Click to Generate Custom List" id="sumit" name="submit">
</h3></form>

</div>

<div id="right">

<?php if ($_GET['separator'] == '') { ?>
<h3>Listing of Generated Records Below:<br />
</h3>
<div class="directions">
1) Review the records to make sure they are what you want.  If not, then revise your options.<br>
2) Copy these records and paste into a text file, then save as and change to a .csv (ie; "file_name.csv")<br>
3) You can then import this file into a newsletter application or where ever you need to use this data<br>
</div>
<textarea cols="75" rows="25">
<?php
foreach ($users as $user) {
echo $user->user_email . "\n";
	}
?>
</textarea>

<?php } else { ?>

<h3>Your Custom List</h3>
<strong>Directions</strong><br>
<div class="directions">
1) Review the records to make sure they are what you want.  If not, then revise your options.<br>
2) You can copy these records and paste into a text file and save as "my_users.csv"<br>
3) Then you can import this file into a newsletter application or where ever you need this data<br>
</div>
<br>
<textarea cols="75" rows="25">
<?php

if ($encapsulator == 'comma') { $encap = ","; } 
elseif ($encapsulator == 'semicolon') { $encap = ";"; } 
elseif ($encapsulator == 'pipe') { $encap = "|"; } 
elseif ($encapsulator == 'newline') { $encap = "\n"; } 
elseif ($encapsulator == 'squote') { $encap = "'"; } 
elseif ($encapsulator == 'dquote') { $encap = "\""; } 
elseif ($encapsulator == 'none') { $encap = ""; } 

if ($separator == 'comma') { $sep = ","; } 
elseif ($separator == 'semicolon') { $sep = ";"; } 
elseif ($separator == 'pipe') { $sep = "|"; } 
elseif ($separator == 'newline') { $sep = "\n"; } 
elseif ($separator == 'squote') { $sep = "'"; } 
elseif ($separator == 'dquote') { $sep = "\""; } 
elseif ($separator == 'none') { $sep = " "; } 

if ($_GET['headers'] != '') {

if ($_GET['user_login'] == '' ) { $ul = ''; } else { $ul = "user_login, "; }
if ($_GET['disp_name'] == '' ) { $dn = ''; } else { $dn = "disp_name, "; }
if ($_GET['user_email'] == '' ) { $em = ''; } else { $em = "user_email, "; }
if ($_GET['user_pass'] == '' ) { $up = ''; } else { $up = "user_pass, "; }
if ($_GET['user_url'] == '' ) { $uu = ''; } else { $uu = "user_url, "; }
if ($_GET['user_role'] == '' ) { $ur = ''; } else { $ur = "user_role, "; }
if ($_GET['first_name'] == '' ) { $fn = ''; } else { $fn = "first_name, "; }
if ($_GET['last_name'] == '' ) { $ln = ''; } else { $ln = "last_name, "; }
if ($_GET['addr1'] == '' ) { $ad = ''; } else { $ad = "addr1, "; }
if ($_GET['city'] == '' ) { $ci = ''; } else { $ci = "city, "; }
if ($_GET['thestate'] == '' ) { $st = ''; } else { $st = "thestate, "; }
if ($_GET['zip'] == '' ) { $zp = ''; } else { $zp = "zip, "; }
if ($_GET['country'] == '' ) { $co = ''; } else { $co = "country, "; }
if ($_GET['phone1'] == '' ) { $ph = ''; } else { $ph = "phone1, "; }

$getArray = $ul . $dn . $em . $up . $uu . $ur . $fn . $ln . $ad . $ci . $st . $zp . $co . $ph;
$getArray = explode(', ', $getArray);
$countArray = count($getArray);
$i = 1;

foreach ($getArray as $arr) {
	
	if ($i == $countArray) {
	echo "";
	} elseif ($i == $countArray - 1) {
	echo $encap . $arr . $encap;
	} else {
	echo $encap . $arr . $encap . $sep;
	}
	$i++;
}	

echo "\n";

}

foreach ($users as $user) {

if ($get_user_login == '' ) { $gul = ''; } else { $gul = $user->user_login .  ", "; }
if ($get_disp_name == '' ) { $gdn = ''; } else { $gdn = $user->display_name . ", "; }
if ($get_user_email == '' ) { $gem = ''; } else { $gem = $user->user_email . ", "; }
if ($get_user_pass == '' ) { $gup = ''; } else { $gup = $user->user_pass . ", "; }
if ($get_user_url == '' ) { $guu = ''; } else { $guu = $user->user_url . ", "; }

if ($get_user_role) { 
	
	$user = get_userdata( $user->ID );
	$capabilities = $user->{$wpdb->prefix . 'capabilities'};

	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	foreach ( $wp_roles->role_names as $role => $name ) {

		if ( array_key_exists( $role, $capabilities ) )
			$roleM = $role;
	}
	
}

if ($get_user_role == '') { $gur = ''; } else { $gur = $roleM . ", "; }

$user_first_name = get_usermeta($user->ID,'first_name');
if ($get_first_name == '') { $gfn = ''; } else { $gfn = $user_first_name . ", "; }

$user_last_name = get_usermeta($user->ID,'last_name');
if ($get_last_name == '') { $gln = ''; } else { $gln = $user_last_name . ", "; }

$user_addr1 = get_usermeta($user->ID,'addr1');
if ($get_addr1 == '') { $gad = ''; } else { $gad = $user_addr1 . ", "; }

$user_city = get_usermeta($user->ID,'city');
if ($get_city == '') { $gci = ''; } else { $gci = $user_city . ", "; }

$user_thestate = get_usermeta($user->ID,'thestate');
if ($get_thestate == '') { $gst = ''; } else { $gst = $user_thestate . ", "; }

$user_zip = get_usermeta($user->ID,'zip');
if ($get_zip == '') { $gzp = ''; } else { $gzp = $user_zip . ", "; }

$user_country = get_usermeta($user->ID,'country');
if ($get_country == '') { $gco = ''; } else { $gco = $user_country . ", "; }

$user_phone1 = get_usermeta($user->ID,'phone1');
if ($get_phone1 == '') { $gph = ''; } else { $gph = $user_phone1 . ", "; }

$getArrayData = $gul . $gdn . $gem . $gup . $guu . $gur . $gfn . $gln . $gad . $gci . $gst . $gzp . $gco . $gph;
$getArrayData = explode(', ', $getArrayData);
$countArrayData = count($getArrayData);
$iData = 1;

foreach ($getArrayData as $arrData) {
	
	if ($iData == $countArrayData) {
	echo "";
	} elseif ($iData == $countArrayData - 1) {
	echo $encap . $arrData . $encap;
	} else {
	echo $encap . $arrData . $encap . $sep;
	}
	$iData++;
}




echo "\n";
	}
	
?>
</textarea>

<?php } ?>

</div>

</div>
<?php
}	

function generate_custom_user_list() {

if ($_POST['user_login'] == 'on') { $user_login = "user_login"; } else { $user_login = ""; }
if ($_POST['disp_name'] == 'on') { $disp_name = "display_name"; } else { $disp_name = ""; } 
if ($_POST['user_email'] == 'on') { $user_email = "user_email"; } else { $user_email = ""; }
if ($_POST['user_pass'] == 'on') { $user_pass = "user_pass"; } else { $user_pass = ""; }
if ($_POST['user_url'] == 'on') { $user_url = "user_url"; } else { $user_url = ""; }
if ($_POST['user_role'] == 'on') { $user_role = "user_role"; } else { $user_role = ""; }
if ($_POST['first_name'] == 'on') { $first_name = "first_name"; } else { $first_name = ""; }
if ($_POST['last_name'] == 'on') { $last_name = "last_name"; } else { $last_name = ""; }
if ($_POST['addr1'] == 'on') { $addr1 = "addr1"; } else { $addr1 = ""; }
if ($_POST['city'] == 'on') { $city = "city"; } else { $city = ""; }
if ($_POST['thestate'] == 'on') { $thestate = "thestate"; } else { $thestate = ""; }
if ($_POST['zip'] == 'on') { $zip = "zip"; } else { $zip = ""; }
if ($_POST['country'] == 'on') { $country = "country"; } else { $country = ""; }
if ($_POST['phone1'] == 'on') { $phone1 = "phone1"; } else { $phone1 = ""; }

$encapsulator = $_POST['encapsulator'];
$separator = $_POST['separator'];
$headers = $_POST['headers'];

header("Location: " . $_SERVER['PHP_SELF'] . "?page=wp-export-users&user_login=".$user_login."&disp_name=".$disp_name."&user_email=".$user_email."&user_pass=".$user_pass."&user_url=".$user_url."&user_role=".$user_role."&first_name=".$first_name."&last_name=".$last_name."&addr1=".$addr1."&city=".$city."&thestate=".$thestate."&zip=".$zip."&country=".$country."&phone1=".$phone1."&encapsulator=".$encapsulator."&separator=".$separator."&headers=".$headers);

}
	
function wp_export_users_add_to_users_menu() {
add_users_page(__('WP Export Users Plus','wp export users plus'), __('WP Export Users Plus','wp export users plus'), 'manage_options', 'wp-export-users', 'wp_export_users', '', '');
}

function wp_export_users_styles() {
?>
<style type="text/css">
.options {width: 300px; padding: 5px; background: #EFEFEF; color: #333333; margin: 5px; border: 1px solid #cccccc;}
.options label {float: left; width: 285px;}
.options label span {font-size: 12px; color: #666666; width: 200px;}
#left {float: left;}
#right {float: left;}
.red {color: #FF0000; font-weight: bold;}
.blue {color: #0048ff; font-weight: bold;}
.directions {width: 375px;}
</style>
<?php
}

add_action( 'admin_head', 'wp_export_users_styles' );	
add_action( 'admin_menu', 'wp_export_users_add_to_users_menu' );
?>