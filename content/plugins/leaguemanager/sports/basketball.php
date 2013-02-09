<?php
/**
 * Basketball Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerBasketball extends LeagueManager
{

	/**
	 * sports keys
	 *
	 * @var array
	 */
	var $key = 'basketball';

	/**
	 * load specifif settings
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		add_filter( 'leaguemanager_sports', array(&$this, 'sports') );
		add_filter( 'rank_teams_'.$this->key, array(&$this, 'rankTeams') );
		add_filter( 'team_points2_'.$this->key, array(&$this, 'calculateBasketStatistics') );

		add_filter( 'leaguemanager_export_matches_header_'.$this->key, array(&$this, 'exportMatchesHeader') );
		add_filter( 'leaguemanager_export_matches_data_'.$this->key, array(&$this, 'exportMatchesData'), 10, 2 );
		add_filter( 'leaguemanager_import_matches_'.$this->key, array(&$this, 'importMatches'), 10, 3 );

		add_action( 'matchtable_header_'.$this->key, array(&$this, 'displayMatchesHeader'), 10, 0 );
		add_action( 'matchtable_columns_'.$this->key, array(&$this, 'displayMatchesColumns') );
		add_action( 'leaguemanager_standings_header_'.$this->key, array(&$this, 'displayStandingsHeader') );
		add_action( 'leaguemanager_standings_columns_'.$this->key, array(&$this, 'displayStandingsColumns'), 10, 2 );
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
		$sports[$this->key] = __( 'Basketball', 'leaguemanager' );

		return $sports;
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
	function calculateBasketStatistics( $team_id )
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
	 * extend header for Standings Table in Backend
	 *
	 * @param none
	 * @return void
	 */
	function displayStandingsHeader()
	{
		echo '<th class="num">'._c( 'Baskets', 'leaguemanager' ).'</th><th class="num">'.__( 'Diff', 'leaguemanager').'</th>';
	}


	/**
	 * extend columns for Standings Table in Backend
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
		echo '<th>'.__( 'Quarters', 'leaguemanager' ).'</th><th>'.__( 'Overtime', 'leaguemanager' ).'</th>';
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
		for ( $i = 1; $i <= 4; $i++ )
			echo '<input class="points" type="text" size="2" id="quarters_plus_'.$i.'_'.$match->id.'" name="custom['.$match->id.'][quarters]['.$i.'][plus]" value="'.$match->quarters[$i]['plus'].'" /> : <input clas="points" type="text" size="2" id="quarters_minus_'.$i.'_'.$match->id.'" name="custom['.$match->id.'][quarters]['.$i.'][minus]" value="'.$match->quarters[$i]['minus'].'" /><br />';
		echo '</td>';

		echo '<td><input class="points" type="text" size="2" id="overtime_home_'.$match->id.'" name="custom['.$match->id.'][overtime][home]" value="'.$match->overtime['home'].'" /> : <input class="points" type="text" size="2" id="overtime_away_'.$match->id.'" name="custom['.$match->id.'][overtime][away]" value="'.$match->overtime['away'].'" /></td>';
	}


	/**
	 * export matches header
	 *
	 * @param string $content
	 * @return the content
	 */
	function exportMatchesHeader( $content )
	{
		$content .= "\t".__( 'Quarters', 'leaguemanager' )."\t\t\t\t".__('Overtime', 'leaguemanager');
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
		if ( isset($match->quarters) ) {
			for ( $i = 1; $i <= 4; $i++ )
				$content .= "\t".sprintf("%d-%d", $match->quarters[$i]['plus'], $match->quarters[$i]['minus']);
		} else {
			$content .= "\t\t\t\t";
		}

		if ( isset($match->overtime) )
			$content .= "\t".sprintf("%d-%d", $match->overtime['home'], $match->overtime['away']);
		else
			$content .= "\t";

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
		$quarters = array( explode("-", $line[8]), explode("-", $line[9]), explode("-", $line[10]), explode("-", $line[11]) );
		$overtime = explode("-", $line[12]);

		foreach ( $quarterss AS $i => $quarter ) {
			$x = $i+1;
			$custom[$match_id]['quarters'][$x]['plus'] = $quarter[0];
			$custom[$match_id]['quarters'][$x]['minus'] = $quarter[1];
		}

		$custom[$match_id]['overtime'] = array( 'home' => $overtime[0], 'away' => $overtime[1] );

		return $custom;
	}
}

$basketball = new LeagueManagerBasketball();
?>
