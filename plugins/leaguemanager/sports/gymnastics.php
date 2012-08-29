<?php
/**
 * Gymnastics Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerGymnastics extends LeagueManager
{
	/**
	 * sports key
	 *
	 * @var string
	 */
	var $key = 'gymnastics';


	/**
	 * load custom setings
	 *
	 * @param 
	 * @return void
	 */
	function __construct()
	{
		add_filter( 'leaguemanager_sports', array(&$this, 'sports') );
		add_filter( 'rank_teams_'.$this->key, array(&$this, 'rankTeams') );
		add_filter( 'team_points2_'.$this->key, array(&$this, 'calculateApparatusPoints') );

		add_filter( 'leaguemanager_export_matches_header_'.$this->key, array(&$this, 'exportMatchesHeader') );
		add_filter( 'leaguemanager_export_matches_data_'.$this->key, array(&$this, 'exportMatchesData'), 10, 2 );
		add_filter( 'leaguemanager_import_matches_'.$this->key, array(&$this, 'importMatches'), 10, 3 );

		add_action( 'matchtable_header_'.$this->key, array(&$this, 'displayMatchesHeader'), 10, 0 );
		add_action( 'matchtable_columns_'.$this->key, array(&$this, 'displayMatchesColumns') );
		add_action( 'leaguemanager_standings_header_'.$this->key, array(&$this, 'displayStandingsHeader') );
		add_action( 'leaguemanager_standings_columns_'.$this->key, array(&$this, 'displayStandingsColumns'), 10, 2 );
	}
	function LeagueManagerGymnastics()
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
		$sports[$this->key] = __( 'Gymnastics', 'leaguemanager' );
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
			$points2[$key] = $row->points2['plus'];
			$done[$key] = $row->done_matches;
		}

		array_multisort( $points, SORT_DESC, $points2, SORT_DESC, $done, SORT_ASC, $teams );
		return $teams;
	}


	/**
	 * extend header for Standings Table 
	 *
	 * @param none
	 * @return void
	 */
	function displayStandingsHeader()
	{
		echo '<th class="num">'._c( 'AP|apparatus points', 'leaguemanager' ).'</th><th>'.__( 'Diff', 'leaguemanager').'</th>';
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
		echo '<th>'.__( 'Apparatus Points', 'leaguemanager' ).'</th>';
	}


	/**
	 * display Table columns for Match Administration
	 *
	 * @param object $match
	 * @return void
	 */
	function displayMatchesColumns( $match )
	{
		echo '<td><input class="points" type="text" size="2" id="apparatus_points_plus_'.$match->id.'" name="custom['.$match->id.'][apparatus_points][plus]" value="'.$match->apparatus_points['plus'].'" /> : <input clas="points" type="text" size="2" id="apparatus_points_minus_'.$match->id.'" name="custom['.$match->id.'][apparatus_points][minus]" value="'.$match->apparatus_points['minus'].'" /></td>';
	}


	/**
	 * export matches header
	 *
	 * @param string $content
	 * @return the content
	 */
	function exportMatchesHeader( $content )
	{
		$content .= "\t"._c( 'AP|apparatus points', 'leaguemanager' );
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
		if ( isset($match->apparatus_points) )
			$content .= "\t".sprintf("%d-%d", $match->apparatus_points['plus'], $match->apparatus_points['minus']);
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
		$ap = explode("-", $line[8]);
		$custom[$match_id]['apparatus_points'] = array( 'plus' => $ap[0], 'minus' => $ap[1] );

		return $custom;
	}


	/**
	 * calculate apparatus points
	 *
	 * @param int $team_id
	 * @return array of points
	 */
	function calculateApparatusPoints( $team_id )
	{
		global $wpdb;

		$home = $wpdb->get_results( "SELECT `custom` FROM {$wpdb->leaguemanager_matches} WHERE `home_team` = '".$team_id."'" );
		$away = $wpdb->get_results( "SELECT `custom` FROM {$wpdb->leaguemanager_matches} WHERE `away_team` = '".$team_id."'" );

		$points = array( 'plus' => 0, 'minus' => 0);
		if ( count($home) > 0 ) {
			foreach ( $home AS $match ) {
				$custom = (array)maybe_unserialize($match->custom);

				$points['plus'] += intval($custom['apparatus_points']['plus']);
				$points['minus'] += intval($custom['apparatus_points']['minus']);
			}
		}
		
		if ( count($away) > 0 ) {
			foreach ( $away AS $match ) {
				$custom = (array)maybe_unserialize($match->custom);
				$points['plus'] += intval($custom['apparatus_points']['minus']);
				$points['minus'] += intval($custom['apparatus_points']['plus']);
			}
		}
		
		return $points;
	}
}

$gymnastics = new LeagueManagerGymnastics();
?>
