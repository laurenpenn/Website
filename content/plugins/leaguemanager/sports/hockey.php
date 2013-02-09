<?php
/**
 * Hockey Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerHockey extends LeagueManager
{

	/**
	 * sports keys
	 *
	 * @var array
	 */
	var $keys = array();

	/**
	 * load specifif settings
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		$this->keys = array( 'del' => __( 'German Icehockey League (DEL)', 'leaguemanager' ), 'nhl' => __( 'National Hockey League (NHL)', 'leaguemanager' ) );
		add_filter( 'leaguemanager_sports', array(&$this, 'sports') );
		add_filter( 'leaguemanager_point_rules_list', array(&$this, 'getPointRuleList') );
		add_filter( 'leaguemanager_point_rules',  array(&$this, 'getPointRules') );
		add_action( 'leaguemanager_doc_point_rules', array(&$this, 'pointRuleDocumentation') );
		foreach ( $this->keys AS $key => $name ) {
			add_filter( 'rank_teams_'.$key, array(&$this, 'rankTeams') );
			add_filter( 'team_points_'.$key, array(&$this, 'calculatePoints'), 10, 3 );
			add_filter( 'team_points2_'.$key, array(&$this, 'calculateGoalStatistics') );

			add_filter( 'leaguemanager_export_matches_header_'.$key, array(&$this, 'exportMatchesHeader') );
			add_filter( 'leaguemanager_export_matches_data_'.$key, array(&$this, 'exportMatchesData'), 10, 2 );
			add_filter( 'leaguemanager_import_matches_'.$key, array(&$this, 'importMatches'), 10, 3 );

			add_action( 'matchtable_header_'.$key, array(&$this, 'displayMatchesHeader'), 10, 0 );
			add_action( 'matchtable_columns_'.$key, array(&$this, 'displayMatchesColumns') );
			add_action( 'leaguemanager_standings_header_'.$key, array(&$this, 'displayStandingsHeader') );
			add_action( 'leaguemanager_standings_columns_'.$key, array(&$this, 'displayStandingsColumns'), 10, 2 );
		}
	}
	function LeagueManagerHockey()
	{
		$this->__construct();
	}


	/**
	 * add sports to list
	 *
	 * @param array $sports
	 * @return array
	 */
	function sports( $sports )
	{
		foreach ( $this->keys AS $key => $name )
			$sports[$key] = $name;

		return $sports;
	}


	/**
	 * get Point Rule list
	 *
	 * @param array $rules
	 * @return array
	 */
	function getPointRuleList( $rules )
	{
		foreach ( $this->keys AS $key => $name )
			$rules[$key] = $name;

		return $rules;
	}


	/**
	 * get Point rules
	 *
	 * @param array $rules
	 * @return array
	 */
	function getPointRules( $rules )
	{
		// DEL rule
		$rules['del'] = array( 'forwin' => 3, 'fordraw' => 1, 'forloss' => 0, 'forwin_overtime' => 2, 'forloss_overtime' => 1 );
		// NHL rule
		$rules['nhl'] = array( 'forwin' => 2, 'fordraw' => 0, 'forloss' => 0, 'forwin_overtime' => 2, 'forloss_overtime' => 1 );

		return $rules;
	}


	/**
	 * rank Teams
	 *
	 * @param array $teams
	 * @return array of teams
	 */
	function rankTeams( $teams )
	{
		foreach ( $teams AS $key => $row ) {
			$points[$key] = $row->points['plus']+$row->add_points;
			$done[$key] = $row->done_matches;
			$diff[$key] = $row->diff;
		}

		array_multisort( $points, SORT_DESC, $diff, SORT_DESC, $done, SORT_ASC, $teams );
		return $teams;
	}


	/**
	 * calculate goals. Penalty is not counted in statistics
	 *
	 * @param int $team_id
	 * @param string $option
	 * @return int
	 */
	function calculateGoalStatistics( $team_id )
	{
		global $wpdb, $leaguemanager;
		
		$goals = array( 'plus' => 0, 'minus' => 0 );
				
		$matches = $wpdb->get_results( "SELECT `home_points`, `away_points`, `custom` FROM {$wpdb->leaguemanager_matches} WHERE `home_team` = '".$team_id."'" );
		if ( $matches ) {
			foreach ( $matches AS $match ) {
				$custom = maybe_unserialize($match->custom);
				if ( !empty($custom['overtime']['home']) && !empty($custom['overtime']['away']) ) {
					$home_goals = $custom['overtime']['home'];
					$away_goals = $custom['overtime']['away'];
				} else {
					$home_goals = $match->home_points;
					$away_goals = $match->away_points;
				}
				
				$goals['plus'] += $home_goals;
				$goals['minus'] += $away_goals;
			}
		}
		
		$matches = $wpdb->get_results( "SELECT `home_points`, `away_points`, `custom` FROM {$wpdb->leaguemanager_matches} WHERE `away_team` = '".$team_id."'" );
		if ( $matches ) {
			foreach ( $matches AS $match ) {
				$custom = maybe_unserialize($match->custom);
				if ( !empty($custom['overtime']['home']) && !empty($custom['overtime']['away']) ) {
					$home_goals = $custom['overtime']['home'];
					$away_goals = $custom['overtime']['away'];
				} else {
					$home_goals = $match->home_points;
					$away_goals = $match->away_points;
				}
				
				$goals['plus'] += $away_goals;
				$goals['minus'] += $home_goals;
			}
		}
		
		return $goals;
	}


	/**
	 * display point  rule documentation
	 *
	 * @param none
	 * @return void
	 */
	function pointRuleDocumentation()
	{
		echo '<h4>'.__( 'German Icehockey League (DEL)', 'leaguemanager' ).'</h4>';
		echo '<p>'.__( 'The DEL uses a more complicated form of the Three-Point-Rule. The winner after regular time gets three points, the loser none. The winner after overtime gets two points and the loser one. This rule was also applied at the Icehockey Worldchampionchip in 2008.', 'leaguemanager' ).'</p>';
	
		echo '<h4>'.__( 'National Hockey League (NHL)', 'leaguemanager' ).'</h4>';
		echo '<p>'.__( 'The NHL uses a derivative of the Two-Point-Rule. The winner after regular time and overtime gains two points whereas the loser after overtime and penalty gets one.', 'leaguemanager' ).'</p>';
	}


	/**
	 * re-calculate points
	 *
	 * @param array $points
	 * @param int $team_id
	 * @param array $rule
	 * @return array with modified points
	 */
	function calculatePoints( $points, $team_id, $rule )
	{
		extract($rule);

		$num_won_overtime = $this->getNumWonMatchesOvertime( $team_id );
		$num_lost_overtime = $this->getNumLostMatchesOvertime( $team_id );

		$points['plus'] = $points['plus'] - $num_won_overtime * $forwin + $num_won_overtime * $forwin_overtime + $num_lost_overtime * $forloss_overtime;
		$points['minus'] = $points['minus'] - $num_lost_overtime * $forwin + $num_won_overtime * $forloss_overtime + $num_lost_overtime * $forwin_overtime;

		return $points;
	}


	/**
	 * get number of won matches after overtime
	 *
	 * @param int $team_id
	 * @return int
	 */
	function getNumWonMatchesOvertime( $team_id )
	{
		global $wpdb;
		$matches = $wpdb->get_results( "SELECT `custom` FROM {$wpdb->leaguemanager_matches} WHERE `winner_id` = '".$team_id."'" );
		$num = 0;
		foreach ( $matches AS $match ) {
			$custom = maybe_unserialize($match->custom);
			if ( isset($custom['overtime']) && $custom['overtime']['home'] != '' && $custom['overtime']['away'] != '' )
				$num++;
		}
		return $num;
	}


	/**
	 * get number of lost matches after overtime
	 *
	 * @param int $team_id
	 * @return int
	 */
	function getNumLostMatchesOvertime( $team_id )
	{
		global $wpdb;
		$matches = $wpdb->get_results( "SELECT `custom` FROM {$wpdb->leaguemanager_matches} WHERE `loser_id` = '".$team_id."'" );
		$num = 0;
		foreach ( $matches AS $match ) {
			$custom = maybe_unserialize($match->custom);
			if ( isset($custom['overtime']) && $custom['overtime']['home'] != '' && $custom['overtime']['away'] != '' )
				$num++;
		}
		return $num;
	}


	/**
	 * extend header for Standings Table
	 *
	 * @param none
	 * @return void
	 */
	function displayStandingsHeader()
	{
		echo '<th class="num">'._c( 'Goals', 'leaguemanager' ).'</th><th>'.__( 'Diff', 'leaguemanager').'</th>';
	}


	/**
	 * extend columns for Standings Table
	 *
	 * @param object $team
	 * @param string $rule
	 * @return void
	 */
	function displayStandingsColumns( $team, $rule )
	{
		global $leaguemanager;
		$league = $leaguemanager->getCurrentLeague();

		echo '<td class="num">';
		if ( is_admin() && $rule == 'manual' ) 
			echo '<input type="text" size="2" name="custom['.$team->id.'][points2][plus]" value="'.$team->points2_plus.'" /> : <input type="text" size="2" name="custom['.$team->id.'][points2][minus]" value="'.$team->points2_minus.'" />';
		else
			printf($league->point_format2, $team->points2_plus, $team->points2_minus);

		echo '</td>';
		echo '<td class="num">'.$team->diff.'</td>';
	}


	/**
	 * display Table Header for Match Administration
	 *
	 * @param none
	 * @return void
	 */
	function displayMatchesHeader()
	{
		echo '<th>'.__( 'Thirds', 'leaguemanager' ).'</th><th>'.__( 'Overtime', 'leaguemanager' ).'</th><th>'.__( 'Penalty', 'leaguemanager' ).'</th>';
	}


	/**
	 * display Table columns for Match Administration
	 *
	 * @param object $match
	 * @return void
	 */
	function displayMatchesColumns( $match )
	{
		echo '<td>';
		for ( $i = 1; $i <= 3; $i++ )
			echo '<input class="points" type="text" size="2" id="thirds_plus_'.$i.'_'.$match->id.'" name="custom['.$match->id.'][thirds]['.$i.'][plus]" value="'.$match->thirds[$i]['plus'].'" /> : <input clas="points" type="text" size="2" id="thirds_minus_'.$i.'_'.$match->id.'" name="custom['.$match->id.'][thirds]['.$i.'][minus]" value="'.$match->thirds[$i]['minus'].'" /><br />';
		echo '</td>';

		echo '<td><input class="points" type="text" size="2" id="overtime_home_'.$match->id.'" name="custom['.$match->id.'][overtime][home]" value="'.$match->overtime['home'].'" /> : <input class="points" type="text" size="2" id="overtime_away_'.$match->id.'" name="custom['.$match->id.'][overtime][away]" value="'.$match->overtime['away'].'" /></td>';
		echo '<td><input class="points" type="text" size="2" id="penalty_home_'.$match->id.'" name="custom['.$match->id.'][penalty][home]" value="'.$match->penalty['home'].'" /> : <input class="points" type="text" size="2" id="penalty_away_'.$match->id.'" name="custom['.$match->id.'][penalty][away]" value="'.$match->penalty['away'].'" /></td>';
	}


	/**
	 * export matches header
	 *
	 * @param string $content
	 * @return the content
	 */
	function exportMatchesHeader( $content )
	{
		$content .= "\t".__( 'Thirds', 'leaguemanager' )."\t\t\t".__('Overtime', 'leaguemanager')."\t".__('Penalty', 'leaguemanager');
		return $content;
	}


	/**
	 * export matches data
	 *
	 * @param string $content
	 * @param object $match
	 * @return the content
	 */
	function exportMatchesData( $content, $match )
	{
		if ( isset($match->thirds) ) {
			for ( $i = 1; $i <= 3; $i++ )
				$content .= "\t".sprintf("%d-%d", $match->thirds[$i]['plus'], $match->thirds[$i]['minus']);
		} else {
			$content .= "\t\t\t";
		}

		if ( isset($match->overtime) )
			$content .= "\t".sprintf("%d-%d", $match->overtime['home'], $match->overtime['away'])."\t".sprintf("%d-%d", $match->penalty['home'], $match->penalty['away']);
		else
			$content .= "\t\t";

		return $content;
	}

	
	/**
	 * import matches
	 *
	 * @param array $custom
	 * @param array $line elements start at index 8
	 * @param int $match_id
	 * @return array
	 */
	function importMatches( $custom, $line, $match_id )
	{
		$thirds = array( explode("-", $line[8]), explode("-", $line[9]), explode("-", $line[10]) );
		$overtime = explode("-", $line[11]);
		$penalty = explode("-", $line[12]);

		foreach ( $thirds AS $i => $third ) {
			$x = $i+1;
			$custom[$match_id]['thirds'][$x]['plus'] = $third[0];
			$custom[$match_id]['thirds'][$x]['minus'] = $third[1];
		}

		$custom[$match_id]['overtime'] = array( 'home' => $overtime[0], 'away' => $overtime[1] );
		$custom[$match_id]['penalty'] = array( 'home' => $penalty[0], 'away' => $penalty[1] );

		return $custom;
	}
}

$hockey = new LeagueManagerHockey();
?>
