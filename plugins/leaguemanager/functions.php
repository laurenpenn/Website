<?php
/**
 * display widget statically
 *
 * @param int $number
 * @param array $instance
 */
function leaguemanager_display_widget( $number, $instance ) {
	echo "<ul id='leaguemanger-widget-".$instance['league']."' class='leaguemanager_widget'>";
	$widget = new LeagueManagerWidget(true);
	$widget->widget( array('number' => $number), $instance );
	echo "</ul>";
}


/**
 * display next match box
 *
 * @param int $number
 * @param array $instance
 */
function leaguemanager_display_next_match_box( $number, $instance ) {
	$widget = new LeagueManagerWidget(true);
	$widget->showNextMatchBox( $number, $instance );
}


/**
 * display previous match box
 *
 * @param int $number
 * @param array $instance
 */
function leaguemanager_display_prev_match_box( $number, $instance ) {
	$widget = new LeagueManagerWidget(true);
	$widget->showPrevMatchBox( $number, $instance );
}


/**
 * display standings table manually
 *
 * @param int $league_id League ID
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function leaguemanager_standings( $league_id, $args = array() ) {
	global $lmShortcodes;
	$defaults = array( 'season' => false, 'template' => 'extend', 'logo' => 'true', 'group' => false, 'home' => false );
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $lmShortcodes->showStandings( array('league_id' => $league_id, 'logo' => $logo, 'season' => $season, 'template' => $template, 'group' => $group, 'home' => $home) );
}


/**
 * display crosstable table manually
 *
 * @param int $league_id
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function leaguemanager_crosstable( $league_id, $args = array() ) {
	global $lmShortcodes;
	$defaults = array('season' => false, 'template' => '', 'mode' => '');
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $lmShortcodes->showCrosstable( array('league_id' => $league_id, 'mode' => $mode, 'template' => $temaplate, 'season' => $season) );
}


/**
 * display matches table manually
 *
 * @param int $league_id
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function leaguemanager_matches( $league_id, $args = array() ) {
	global $lmShortcodes;
	$defaults = array('season' => false, 'template' => '', 'mode' => '', 'archive' => false, 'match_day' => false, 'group' => false, 'roster' => false, 'order' => false);
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $lmShortcodes->showMatches( array('league_id' => $league_id, 'mode' => $mode, 'season' => $season, 'archive' => $archive, 'template' => $template, 'roster' => $roster, 'order' => $order, 'match_day' => $match_day, 'group' => $group) );
}


/**
 * display one match manually
 *
 * @param int $match_id
 * @param array $args additional arguments as assoziative array (optional)
 * @return void
 */
function leaguemanager_match( $match_id, $args = array() ) {
	global $lmShortcodes;
	$defaults = array('template' => '');
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);

	echo $lmShortcodes->showMatch( array('id' => $match_id, 'template' => $template) );
}


/**
 * display team list manually
 *
 * @param int|string $league_id
 * @param array $args additional arguments as assoziative array (optional)
 * @return void
 */
function leaguemanager_teams( $league_id, $args = array() ) {
	global $lmShortcodes;
	$defaults = array('season' => false, 'template' => '');
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);

	echo $lmShortcodes->showTeams( array('league_id' => $league_id, 'season' => $season, 'template' => $template) );
}


/**
 * display one team manually
 *
 * @param int $team_id
 * @param array $args additional arguments as assoziative array (optional)
 * @return void
 */
function leaguemanager_team( $team_id, $args = array() ) {
	global $lmShortcodes;
	$defaults = array('template' => '');
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);

	echo $lmShortcodes->showTeam( array('id' => $team_id, 'template' => $template) );
}


/**
 * display championchip manually
 *
 * @param int $league_id
 * @param array $args additional arguments as assoziative array (optional)
 * @return void
 */
function leaguemanager_championship( $league_id, $args = array() ) {
	global $lmShortcodes;
	$defaults = array('template' => '', 'season' => false);
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);

	echo $lmShortcodes->showChampionship( array('league_id' => $league_id, 'template' => $template, 'season' => $season) );
}


/**
 * helper function to allocate matches and teams of a league to a aseason and maybe other league
 *
 * @param int $league_id ID of current league
 * @param string $season season to set
 * @param int $new_league_id ID of different league to add teams and matches to (optionl)
 * @param int $old_season (optional) old season if you want to re-allocate teams and matches
 */
function move_league_to_season( $league_id, $season, $new_league_id = false, $old_season = false ) {
	global $leaguemanager, $wpdb;
	if ( !$new_league_id ) $new_league_id = $league_id;
	
	$search = "`league_id` = '".$league_id."'";
	if ( $old_season ) $search .= " AND `season` = '".$old_season."'";

	if ( $teams = $leaguemanager->getTeams($search) ) {
		foreach ( $teams AS $team ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `season` = '%d', `league_id` = '%d' WHERE `id` = '%d'", $season, $new_league_id, $team->id ) );
		}
	}
	if ( $matches = $leaguemanager->getMatches($search) ) {
		foreach ( $matches AS $match ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_matches} SET `season` = '%d', `league_id` = '%d' WHERE `id` = '%d'", $season, $new_league_id, $match->id ) );
		}
	}
}

?>
