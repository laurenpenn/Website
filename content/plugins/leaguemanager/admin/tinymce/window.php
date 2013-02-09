<?php

$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));

if (file_exists($root.'/wp-load.php')) {
	// WP 2.6
	require_once($root.'/wp-load.php');
} else {
	// Before 2.6
	if (!file_exists($root.'/wp-config.php'))  {
		echo "Could not find wp-config.php";	
		die;
	}// stop when wp-config is not there
	require_once($root.'/wp-config.php');
}

require_once(ABSPATH.'/wp-admin/admin.php');

// check for rights
if(!current_user_can('edit_posts')) die;

global $wpdb;

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('Leaguemanager', 'leaguemanager') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo LEAGUEMANAGER_URL ?>/admin/tinymce/tinymce.js"></script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('table_tag').focus();" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
<form name="LeagueManager" action="#">
	<div class="tabs">
		<ul>
			<li id="table_tab" class="current"><span><a href="javascript:mcTabs.displayTab('table_tab', 'table_panel');" onmouseover="return false;"><?php _e( "Table", 'leaguemanager' ); ?></a></span></li>
			<li id="crosstable_tab"><span><a href="javascript:mcTabs.displayTab('crosstable_tab', 'crosstable_panel');" onmouseover="return false;"><?php _e( "Crosstable", 'leaguemanager' ); ?></a></span></li>
			<li id="matches_tab"><span><a href="javascript:mcTabs.displayTab('matches_tab', 'matches_panel');" onmouseover="return false;"><?php _e( "Matches", 'leaguemanager' ); ?></a></span></li>
			<li id="match_tab"><span><a href="javascript:mcTabs.displayTab('match_tab', 'match_panel');" onmouseover="return false;"><?php _e( "Match", 'leaguemanager' ); ?></a></span></li>
			<li id="teams_tab"><span><a href="javascript:mcTabs.displayTab('teams_tab', 'teams_panel');" onmouseover="return false;"><?php _e( "Teams", 'leaguemanager' ); ?></a></span></li>
			<li id="team_tab"><span><a href="javascript:mcTabs.displayTab('team_tab', 'team_panel');" onmouseover="return false;"><?php _e( "Team", 'leaguemanager' ); ?></a></span></li>
			<li id="archive_tab"><span><a href="javascript:mcTabs.displayTab('archive_tab', 'archive_panel');" onmouseover="return false;"><?php _e( "Archive", 'leaguemanager' ); ?></a></span></li>
		</ul>
	</div>

	<div class="panel_wrapper">
	<!-- table panel -->
	<div id="table_panel" class="panel current"><br />
	<table style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="table_tag"><?php _e("League", 'leaguemanager'); ?></label></td>
		<td>
		<select id="table_tag" name="table_tag" style="width: 200px">
        	<option value="0"><?php _e("No League", 'leaguemanager'); ?></option>
		<?php
			$leagues = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager} ORDER BY `id` DESC");
			if( $leagues ) {
			foreach( $leagues as $league )
				echo '<option value="'.$league->id.'" >'.$league->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td><label for="standings_display"><?php _e( "Display", 'leaguemanager' ) ?></label></td>
		<td>
			<select size="1" name="standings_display" id="standings_display">
				<option value="extend"><?php _e( 'Extend', 'leaguemanager' ) ?></option>
				<option value="compact"><?php _e( 'Compact', 'leaguemanager' ) ?></option>
				<option value="slim"><?php _e( 'Slim', 'leaguemanager' ) ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="show_logo"><?php _e( 'Show Logos', 'leaguemanager' ) ?></label></td>
		<td><input type="checkbox" id="show_logo" name="show_logo" value="1" /></td>
	</tr>
	</table>
	</div>
	
	<!-- matches panel -->
	<div id="matches_panel" class="panel"><br/>
	<table  style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="matches_tag"><?php _e("League", 'leaguemanager'); ?></label></td>
		<td>
		<select id="matches_tag" name="matches_tag" style="width: 200px">
        	<option value="0"><?php _e("No League", 'leaguemanager'); ?></option>
		<?php
			$leagues = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager} ORDER BY `id` DESC");
			if( $leagues ) {
			foreach( $leagues as $league )
				echo '<option value="'.$league->id.'" >'.$league->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td><label for="matches_display"><?php _e( "Display", 'leaguemanager' ) ?></label></td>
		<td>
			<select size="1" name="matches_display" id="matches_display">
				<option value=""><?php _e( 'Match day based', 'leaguemanager' ) ?></option>
				<option value="all"><?php _e( 'All', 'leaguemanager' ) ?></option>
				<option value="home"><?php _e( 'Only Home Team', 'leaguemanager' ) ?></option>
			</select>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- match panel -->
	<div id="match_panel" class="panel"><br/>
	<table  style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="match_tag"><?php _e("Match", 'leaguemanager'); ?></label></td>
		<td>
		<select id="match_tag" name="match_tag" style="width: 200px">
        	<option value="0"><?php _e("No Match", 'leaguemanager'); ?></option>
		<?php
			$matches = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager_matches} ORDER BY `id` DESC");
			$teams_sql = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager_teams} ORDER BY `id` DESC");			
			if( $matches ) {
				if ( $teams_sql ) {
					$teams = array();
					foreach ( $teams_sql AS $team ) {
						$teams[$team->id] = $team->title;
					}
				}
				foreach( $matches as $match ) {
					$title = isset($match->title) ? $match->title : $teams[$match->home_team] . "&#8211;" . $teams[$match->away_team];
					echo '<option value="'.$match->id.'" >'.$title.'</option>'."\n";
				}
			}
		?>
        	</select>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- teams panel -->
	<div id="teams_panel" class="panel"><br/>
	<table  style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="teams_tag"><?php _e("League", 'leaguemanager'); ?></label></td>
		<td>
		<select id="teams_tag" name="teams_tag" style="width: 200px">
        	<option value="0"><?php _e("No League", 'leaguemanager'); ?></option>
		<?php
			$leagues = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager} ORDER BY `id` DESC");
			if( $leagues ) {
			foreach( $leagues AS $league )
				echo '<option value="'.$league->id.'" >'.$league->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- team panel -->
	<div id="team_panel" class="panel"><br/>
	<table  style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="team_tag"><?php _e("Team", 'leaguemanager'); ?></label></td>
		<td>
		<select id="team_tag" name="team_tag" style="width: 200px">
        	<option value="0"><?php _e("No Team", 'leaguemanager'); ?></option>
		<?php
			$teams = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager_teams} ORDER BY `title` ASC");	
			if( $teams ) {
				foreach ( $teams AS $team ) {
					$league = $wpdb->get_results( "SELECT `title` FROM {$wpdb->leaguemanager} WHERE `id` = {$team->league_id}" );
					echo '<option value="'.$team->id.'" >'.$team->title.' ('.$league[0]->title.' Saison '.$team->season.')</option>'."\n";
				}
			}
		?>
        	</select>
		</td>
	</tr>
	</table>
	</div>

	<!-- crosstable panel -->
	<div id="crosstable_panel" class="panel"><br/>
	<table>
	<tr>
		<td><label for="crosstable_tag"><?php _e("League", 'leaguemanager'); ?></label></td>
		<td>
		<select id="crosstable_tag" name="crosstable_tag" style="width: 200px">
        	<option value="0"><?php _e("No League", 'leaguemanager'); ?></option>
		<?php
			$leagues = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager} ORDER BY `id` DESC");
			if( $leagues ) {
			foreach( $leagues as $league )
				echo '<option value="'.$league->id.'" >'.$league->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label><?php _e( 'Display', 'leaguemanager' ) ?></label></td>
		<td>
			<input type="radio" name="crosstable_showtype" id="crosstable_showtype_embed" value="embed" checked="ckecked" /><label for="crosstable_showtype_embed"><?php _e( 'Embed', 'leaguemanager' ) ?></label><br />
			<input type="radio" name="crosstable_showtype" id="crosstable_showtype_popup" value="popup" /><label for="crosstable_showtype_popup"><?php _e( 'Popup', 'leaguemanager' ) ?></label>
		</td>
   	</tr>
	</table>
	</div>

	<!-- archive panel -->
	<div id="archive_panel" class="panel"><br/>
	<table  style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="archive_tag"><?php _e("League", 'leaguemanager'); ?></label></td>
		<td>
		<select id="archive_tag" name="archive_tag" style="width: 200px">
        	<option value="0"><?php _e("No League", 'leaguemanager'); ?></option>
		<?php
			$leagues = $wpdb->get_results("SELECT * FROM {$wpdb->leaguemanager} ORDER BY `id` DESC");
			if( $leagues ) {
			foreach( $leagues AS $league )
				echo '<option value="'.$league->id.'" >'.$league->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	</table>
	</div>
	
	</div>
	
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'leaguemanager'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'leaguemanager'); ?>" onclick="insertLeagueManagerLink();" />
		</div>
	</div>

</form>
</body>
</html>
