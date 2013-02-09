<?php
if ( !current_user_can( 'manage_leagues' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
	
else :
	$options = get_option('leaguemanager');
	$league = $leaguemanager->getCurrentLeague();
	if ( isset($_POST['updateSettings']) ) {
		check_admin_referer('leaguemanager_manage-league-options');

		$settings = (array)$_POST['settings'];

		// Set textdomain
		$options['textdomain'] = (string)$settings['sport'];
		update_option('leaguemanager', $options);
		
		if ( isset($_POST['forwin']) )
			$settings['point_rule'] = array( 'forwin' => $_POST['forwin'], 'fordraw' => $_POST['fordraw'], 'forloss' => $_POST['forloss'], 'forwin_overtime' => $_POST['forwin_overtime'], 'forloss_overtime' => $_POST['forloss_overtime'] );

		$this->editLeague( $_POST['league_title'], $settings, $_POST['league_id'] );
		$this->printMessage();
	}
	
	$options = get_option('leaguemanager');
	$league = $leaguemanager->getLeague( $_GET['league_id'] );

	$forwin = $fordraw = $forloss = $forwin_overtime = $forloss_overtime = 0;
	// Manual point rule
	if ( is_array($league->point_rule) ) {
		$forwin = $league->point_rule['forwin'];
		$forwin_overtime = $league->point_rule['forwin_overtime'];
		$fordraw = $league->point_rule['fordraw'];
		$forloss = $league->point_rule['forloss'];
		$forloss_overtime = $league->point_rule['forloss_overtime'];
		$league->point_rule = 'user';
	}
?>

<div class="wrap">
	<p class="leaguemanager_breadcrumb"><a href="admin.php?page=leaguemanager"><?php _e( 'Leaguemanager', 'leaguemanager' ) ?></a> &raquo; <a href="admin.php?page=leaguemanager&amp;subpage=show-league&amp;league_id=<?php echo $league->id ?>"><?php echo $league->title ?></a> &raquo; <?php _e( 'League Preferences', 'leaguemanager' ) ?></p>
			
	<h2><?php _e( 'League Preferences', 'leaguemanager' ) ?></h2>
	<form action="" method="post">
		<?php wp_nonce_field( 'leaguemanager_manage-league-options' ) ?>
			
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="league_title"><?php _e( 'Title', 'leaguemanager' ) ?></label></th><td><input type="text" name="league_title" id="league_title" value="<?php echo $league->title ?>" size="30" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sport"><?php _e( 'Sport', 'leaguemanager' ) ?></label></th>
				<td>
					<select size="1" name="settings[sport]" id="sport">
						<?php foreach ( $leaguemanager->getLeagueTypes() AS $id => $title ) : ?>
							<option value="<?php echo $id ?>"<?php if ( $id == $league->sport ) echo ' selected="selected"' ?>><?php echo $title ?></option>
						<?php endforeach; ?>
					</select>
					<span class="setting-description"><?php printf( __( "Check the <a href='%s'>Documentation</a> for details", 'leaguemanager'), admin_url() . 'admin.php?page=leaguemanager-doc' ) ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="point_rule"><?php _e( 'Point Rule', 'leaguemanager' ) ?></label></th>
				<td>
					<select size="1" name="settings[point_rule]" id="point_rule" onchange="Leaguemanager.checkPointRule(<?php echo $forwin ?>, <?php echo $forwin_overtime ?>, <?php echo $fordraw ?>, <?php echo $forloss ?>, <?php echo $forloss_overtime ?>)">
					<?php foreach ( $this->getPointRules() AS $id => $point_rule ) : ?>
					<option value="<?php echo $id ?>"<?php if ( $id == $league->point_rule ) echo ' selected="selected"'; ?>><?php echo $point_rule ?></option>
					<?php endforeach; ?>
					</select>
					<span class="setting-description"><?php printf( __("For details on point rules see the <a href='%s'>Documentation</a>", 'leaguemanager'), admin_url() . 'admin.php?page=leaguemanager-doc' ) ?></span>
					<div id="point_rule_manual" style="display: block;">
					<?php if ( $league->point_rule == 'user' ) : ?>
						<div id="point_rule_manual_content">
							<input type='text' name='forwin' id='forwin' value='<?php echo $forwin ?>' size='2' />
							<input type='text' name='forwin_overtime' id='forwin_overtime' value='<?php echo $forwin_overtime ?>' size='2' />
							<input type='text' name='fordraw' id='fordraw' value='<?php echo $fordraw ?>' size='2' />
							<input type='text' name='forloss' id='forloss' value='<?php echo $forloss ?>' size='2' />
							<input type='text' name='forloss_overtime' id='forloss_overtime' value='<?php echo $forloss_overtime ?>' size='2' />
							&#160;<span class='setting-description'><?php _e( 'Order: win, win overtime, tie, loss, loss overtime', 'leaguemanager' ) ?></span>
						</div>
					<?php endif; ?>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="point_format"><?php _e( 'Point Format', 'leaguemanager' ) ?></label></th>
				<td>
					<select size="1" name="settings[point_format]" id="point_format" >
					<?php foreach ( $this->getPointFormats() AS $id => $format ) : ?>
					<option value="<?php echo $id ?>"<?php selected ( $id, $league->point_format ) ?>><?php echo $format ?></option>
					<?php endforeach; ?>
					</select>
					<select size="1" name="settings[point_format2]" id="point_format2" >
					<?php foreach ( $this->getPointFormats() AS $id => $format ) : ?>
					<option value="<?php echo $id ?>"<?php selected ( $id, $league->point_format2 ); ?>><?php echo $format ?></option>
					<?php endforeach; ?>
					</select>
					&#160;<span class="setting-description"><?php _e( 'Point formats for primary and seconday points (e.g. Goals)', 'leaguemanager' ) ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="team_ranking"><?php _e( 'Team Ranking', 'leaguemanager' ) ?></label></th>
				<td>
					<select size="1" name="settings[team_ranking]" id="team_ranking" >
						<option value="auto"<?php if ( 'auto' == $league->team_ranking  ) echo ' selected="selected"'; ?>><?php _e( 'Automatic', 'leaguemanager' ) ?></option>
						<option value="manual"<?php if ( 'manual' == $league->team_ranking  ) echo ' selected="selected"'; ?>><?php _e( 'Manual', 'leaguemanager' ) ?></option>
					</select>
					&#160;<span class="setting-description"><?php _e( 'Team Ranking via Drag & Drop probably will only work in Firefox', 'leaguemanager' ) ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mode"><?php _e( 'Mode', 'leaguemanager' ) ?></label></th>
				<td>
					<select size="1" name="settings[mode]" id="mode">
					<?php foreach ( $this->getModes() AS $id => $mode ) : ?>
						<option value="<?php echo $id ?>"<?php if ( $id == $league->mode ) echo ' selected="selected"' ?>><?php echo $mode ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="upload_dir"><?php _e( 'Upload Directory', 'leaguemanager' ) ?></label></th>
				<td><input type="text" size="40" name="settings[upload_dir]" id="upload_dir" value="<?php echo $league->upload_dir ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="default_start_time"><?php _e( 'Default Match Start Time', 'leaguemanager' ) ?></label></th>
				<td>
					<select size="1" name="settings[default_match_start_time][hour]">
					<?php for ( $hour = 0; $hour <= 23; $hour++ ) : ?>
						<option value="<?php echo str_pad($hour, 2, 0, STR_PAD_LEFT) ?>"<?php selected( $hour, $league->default_match_start_time['hour'] ) ?>><?php echo str_pad($hour, 2, 0, STR_PAD_LEFT) ?></option>
					<?php endfor; ?>
					</select>
					<select size="1" name="settings[default_match_start_time][minutes]">
					<?php for ( $minute = 0; $minute <= 60; $minute++ ) : ?>
						<?php if ( 0 == $minute % 5 && 60 != $minute ) : ?>
						<option value="<?php  echo str_pad($minute, 2, 0, STR_PAD_LEFT) ?>"<?php selected( $minute, $league->default_match_start_time['minutes'] ) ?>><?php echo str_pad($minute, 2, 0, STR_PAD_LEFT) ?></option>
					<?php endif; ?>
					<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="standings_table"><?php _e( 'Standings Table Display', 'leaguemanager' ) ?></label></th>
				<td>
					<p><input type="checkbox" name="settings[standings][pld]" id="standings_pld" value="1" <?php checked(1, $league->standings['pld']) ?> /><label for="standings_pld" style="margin-left: 0.5em;"><?php _e( 'Played Games', 'leaguemanager' ) ?></label></p>
					<p><input type="checkbox" name="settings[standings][won]" id="standings_won" value="1" <?php checked(1, $league->standings['won']) ?> /><label for="standings_won" style="margin-left: 0.5em;"><?php _e( 'Won Games', 'leaguemanager' ) ?></label></p>
					<p><input type="checkbox" name="settings[standings][tie]" id="standings_tie" value="1" <?php checked(1, $league->standings['tie']) ?> /><label for="standings_tie" style="margin-left: 0.5em;"><?php _e('Tie Games', 'leaguemanager' ) ?></label></p>
					<p><input type="checkbox" name="settings[standings][lost]" id="standings_lost" value="1" <?php checked(1, $league->standings['lost']) ?> /><label for="standings_lost" style="margin-left: 0.5em;"><?php _e( 'Lost Games', 'leaguemanager' ) ?></label></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="teams_ascend"><?php _e( 'Teams Ascend', 'leaguemanager' ) ?></label></th>
				<td><input type="text" name="settings[num_ascend]" id="teams_ascend" value="<?php echo $league->num_ascend ?>" size="2" />&#160;<span class="setting-description"><?php _e( 'Number of Teams that ascend into higher league', 'leaguemanager' ) ?></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="teams_descend"><?php _e( 'Teams Descend', 'leaguemanager' ) ?></label></th>
				<td><input type="text" name="settings[num_descend]" id="teams_descend" value="<?php echo $league->num_descend ?>" size="2" />&#160;<span class="setting-description"><?php _e( 'Number of Teams that descend into lower league', 'leaguemanager' ) ?></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="teams_relegation"><?php _e( 'Teams Relegation', 'leaguemanager' ) ?></label></th>
				<td><input type="text" name="settings[num_relegation]" id="teams_relegation" value="<?php echo $league->num_relegation ?>" size="2" />&#160;<span class="setting-description"><?php _e( 'Number of Teams that need to go into relegation', 'leaguemanager' ) ?></span></td>
			</tr>

			<?php do_action( 'league_settings_'.$league->sport, $league ); ?> 
			<?php do_action( 'league_settings_'.$league->mode, $league ); ?> 
			<?php do_action( 'league_settings', $league ); ?> 
		</table>
		
		<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />
		<p class="submit"><input type="submit" name="updateSettings" value="<?php _e( 'Save Preferences', 'leaguemanager' ) ?> &raquo;" class="button" /></p>
	</form>
</div>



<?php endif; ?>
