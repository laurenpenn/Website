<?php
/*
Plugin Name: Contact Form 7 - Campaign Monitor Addon
Plugin URI: http://www.bettigole.us/published-work/wordpress-contributions/campaign-monitor-addon-for-contact-form-7/
Description: Add the power of CampaignMonitor to Contact Form 7
Author: Joshua Bettigole
Author URI: http://www.bettigole.us
Version: 1.06
*/

/*  Copyright 2010 Joshua Bettigole (email: joshua at bettigole.us)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'WPCF7_CM_VERSION', '1.06' );

if ( ! defined( 'WPCF7_CM_PLUGIN_BASENAME' ) )
	define( 'WPCF7_CM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

add_action( 'wpcf7_after_save', 'wpcf7_cm_save_campaignmonitor' );

function wpcf7_cm_save_campaignmonitor($args)
{
	update_option( 'cf7_cm_'.$args->id, $_POST['wpcf7-campaignmonitor'] );
}

add_action( 'wpcf7_admin_before_subsubsub', 'add_cm_meta' );

function add_cm_meta (){
	if ( wpcf7_admin_has_edit_cap() ) {
		add_meta_box( 'cf7cmdiv', __( 'Campaign Monitor', 'wpcf7' ),
			'wpcf7_cm_add_campaignmonitor', 'cfseven', 'cf7_cm', 'core',
			array(
				'id' => 'wpcf7-cf7',
				'name' => 'cf7_cm',
				'use' => __( 'Use Campaign Monitor', 'wpcf7' ) ) );
	}
}

add_action( 'wpcf7_admin_after_mail_2', 'show_cm_metabox' );

function show_cm_metabox($cf){
	do_meta_boxes( 'cfseven', 'cf7_cm', $cf );
}
			
function wpcf7_cm_add_campaignmonitor($args)
{
				$cf7_cm_defaults = array();
				$cf7_cm = get_option( 'cf7_cm_'.$args->id, $cf7_cm_defaults );
			?>
				
<div class="mail-field">
	<input type="checkbox" id="wpcf7-campaignmonitor-active" name="wpcf7-campaignmonitor[active]" value="1"<?php echo ( $cf7_cm['active']==1 ) ? ' checked="checked"' : ''; ?> />
	<label for="wpcf7-campaignmonitor-active"><?php echo esc_html( __( 'Use CampaignMonitor', 'wpcf7' ) ); ?></label>
<div class="pseudo-hr"></div>
</div>

<br class="clear" />

<div class="mail-fields">
	<div class="half-left">
		<div class="mail-field">
			<label for="wpcf7-campaignmonitor-email"><?php echo esc_html( __( 'Subscriber Email:', 'wpcf7' ) ); ?></label><br />
			<input type="text" id="wpcf7-campaignmonitor-email" name="wpcf7-campaignmonitor[email]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['email'] ); ?>" />
		</div>
	
		<div class="mail-field">
		<label for="wpcf7-campaignmonitor-name"><?php echo esc_html( __( 'Subscriber Full Name:', 'wpcf7' ) ); ?></label><br />
		<input type="text" id="wpcf7-campaignmonitor-name" name="wpcf7-campaignmonitor[name]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['name'] ); ?>" />
		</div>
	
		<div class="mail-field">
		<label for="wpcf7-campaignmonitor-accept"><?php echo esc_html( __( 'Required Acceptance Field:', 'wpcf7' ) ); ?></label><br />
		<input type="text" id="wpcf7-campaignmonitor-accept" name="wpcf7-campaignmonitor[accept]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['accept'] ); ?>" />
		</div>
	
		<div class="mail-field"><br/>
		<input type="checkbox" id="wpcf7-campaignmonitor-cf-active" name="wpcf7-campaignmonitor[cfactive]" value="1"<?php echo ( $cf7_cm['cfactive'] ) ? ' checked="checked"' : ''; ?> />
		<label for="wpcf7-campaignmonitor-cfactive"><?php echo esc_html( __( 'Use Custom Fields', 'wpcf7' ) ); ?></label><br/><br/>
		</div>
	</div>
	
	<div class="half-right">
		<div class="mail-field">
		<label for="wpcf7-campaignmonitor-api"><?php echo esc_html( __( 'API Key:', 'wpcf7' ) ); ?></label><br />
		<input type="text" id="wpcf7-campaignmonitor-api" name="wpcf7-campaignmonitor[api]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['api'] ); ?>" />
		</div>

		<div class="mail-field">
		<label for="wpcf7-campaignmonitor-client"><?php echo esc_html( __( 'Client ID:', 'wpcf7' ) ); ?></label><br />
		<input type="text" id="wpcf7-campaignmonitor-client" name="wpcf7-campaignmonitor[client]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['client'] ); ?>" />
		</div>

		<div class="mail-field">
		<label for="wpcf7-campaignmonitor-list"><?php echo esc_html( __( 'List ID:', 'wpcf7' ) ); ?></label><br />
		<input type="text" id="wpcf7-campaignmonitor-list" name="wpcf7-campaignmonitor[list]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['list'] ); ?>" />
		</div>

		<div class="mail-field"><br/>
		<input type="checkbox" id="wpcf7-campaignmonitor-resubscribeoption" name="wpcf7-campaignmonitor[resubscribeoption]" value="1"<?php echo ( $cf7_cm['resubscribeoption'] ) ? ' checked="checked"' : ''; ?> />
		<label for="wpcf7-campaignmonitor-resubscribeoption"><?php echo esc_html( __( 'Allow Users to Resubscribe after being Deleted or Unsubscribed? (checked = true)', 'wpcf7' ) ); ?></label><br/><br/>
		</div>
	</div>
	
	<br class="clear" />

	<div class="campaignmonitor-custom-fields">
		<?php for($i=1;$i<=20;$i++){ ?>
			<div class="half-left">
				<div class="mail-field">
				<label for="wpcf7-campaignmonitor-CustomKey<?php echo $i; ?>"><?php echo esc_html( __( 'CustomKey'.$i.':', 'wpcf7' ) ); ?></label><br />
				<input type="text" id="wpcf7-campaignmonitor-CustomKey<?php echo $i; ?>" name="wpcf7-campaignmonitor[CustomKey<?php echo $i; ?>]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['CustomKey'.$i] ); ?>" />
				</div>
			</div>
			<div class="half-left">
				<div class="mail-field">
				<label for="wpcf7-campaignmonitor-CustomValue<?php echo $i; ?>"><?php echo esc_html( __( 'CustomValue'.$i.':', 'wpcf7' ) ); ?></label><br />
				<input type="text" id="wpcf7-campaignmonitor-CustomValue<?php echo $i; ?>" name="wpcf7-campaignmonitor[CustomValue<?php echo $i; ?>]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['CustomValue'.$i] ); ?>" />
				</div>
			</div>
			<br class="clear" />	
		<?php } ?>
		
	</div>
</div>

				<?php

}


add_action( 'admin_print_scripts', 'wpcf7_cm_admin_enqueue_scripts' );

function wpcf7_cm_admin_enqueue_scripts ()
{
	global $plugin_page;

	if ( ! isset( $plugin_page ) || 'wpcf7' != $plugin_page )
		return;

	wp_enqueue_script( 'wpcf7-cm-admin', wpcf7_cm_plugin_url( 'scripts.js' ),
		array( 'jquery', 'wpcf7-admin' ), WPCF7_CM_VERSION, true );
}


add_action( 'wpcf7_before_send_mail', 'wpcf7_cm_subscribe' );

function wpcf7_cm_subscribe($obj)
{
	$cf7_cm = get_option( 'cf7_cm_'.$obj->id );
	if( $cf7_cm )
	{
		$subscribe = false;

		$regex = '/\[\s*([a-zA-Z_][0-9a-zA-Z:._-]*)\s*\]/';
		$callback = array( &$obj, 'cf7_cm_callback' );
	
		$email = cf7_cm_tag_replace( $regex, $cf7_cm['email'], $obj->posted_data );
		$name = cf7_cm_tag_replace( $regex, $cf7_cm['name'], $obj->posted_data );
		
		$lists = cf7_cm_tag_replace( $regex, $cf7_cm['list'], $obj->posted_data );
		$listarr = explode(',',$lists);

		if( isset($cf7_cm['accept']) && strlen($cf7_cm['accept']) != 0 )
		{
			$accept = cf7_cm_tag_replace( $regex, $cf7_cm['accept'], $obj->posted_data );
			if($accept != $cf7_cm['accept'])
			{
				if(strlen($accept) > 0)
					$subscribe = true;
			}
		}
		else
		{
			$subscribe = true;
		}

		for($i=1;$i<=20;$i++){
		
			if( isset($cf7_cm['CustomKey'.$i]) && isset($cf7_cm['CustomValue'.$i]) && strlen(trim($cf7_cm['CustomValue'.$i])) != 0 )
			{
				$CustomFields[] = array('Key'=>trim($cf7_cm['CustomKey'.$i]), 'Value'=>cf7_cm_tag_replace( $regex, trim($cf7_cm['CustomValue'.$i]), $obj->posted_data ) );
			}

		}
		
		
		if( isset($cf7_cm['resubscribeoption']) && strlen($cf7_cm['resubscribeoption']) != 0 )
		{
			$ResubscribeOption = true;
		} 
			else
		{
			$ResubscribeOption = false;
		}
		
		if($subscribe && $email != $cf7_cm['email'])
		{
			
			require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'csrest_subscribers.php');
			
			$wrap = new CF7CM_CS_REST_Subscribers( trim($listarr[0]), $cf7_cm['api'] );
			foreach($listarr as $listid)
			{
				$wrap->set_list_id(trim($listid));
				$wrap->add(array(
					'EmailAddress' => $email,
					'Name' => $name,
					'CustomFields' => $CustomFields,
					'Resubscribe' => $ResubscribeOption
				));
			}
			
		}
		
	}
}

function cf7_cm_tag_replace( $pattern, $subject, $posted_data, $html = false ) {
	if( preg_match($pattern,$subject,$matches) > 0)
	{
	
		if ( isset( $posted_data[$matches[1]] ) ) {
			$submitted = $posted_data[$matches[1]];
	
			if ( is_array( $submitted ) )
				$replaced = join( ', ', $submitted );
			else
				$replaced = $submitted;
	
			if ( $html ) {
				$replaced = strip_tags( $replaced );
				$replaced = wptexturize( $replaced );
			}
	
			$replaced = apply_filters( 'wpcf7_mail_tag_replaced', $replaced, $submitted );
	
			return stripslashes( $replaced );
		}
	
		if ( $special = apply_filters( 'wpcf7_special_mail_tags', '', $matches[1] ) )
			return $special;
	
		return $matches[0];
	}
	return $subject;
}

function wpcf7_cm_plugin_url( $path = '' ) {
	return plugins_url( $path, WPCF7_CM_PLUGIN_BASENAME );
}


add_filter( 'plugin_action_links', 'wpcf7_cm_plugin_action_links', 10, 2 );


function wpcf7_cm_plugin_action_links( $links, $file ) {

	if ( $file != plugin_basename( __FILE__ ) )
		return $links;

	$url = wpcf7_admin_url( array( 'page' => 'wpcf7' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">' . esc_html( __( 'Settings', 'wpcf7' ) ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;

}
