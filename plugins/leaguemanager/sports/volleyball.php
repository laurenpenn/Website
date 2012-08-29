<?php
/**
 * Volleyball Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerVolleyball extends LeagueManager
{

	/**
	 * sports key
	 *
	 * @var string
	 */
	var $key = 'volleyball';


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

		add_filter( 'leaguemanager_export_matches_header_'.$this->key, array(&$this, 'exportMatchesHeader') );
		add_filter( 'leaguemanager_export_matches_data_'.$this->key, array(&$this, 'exportMatchesData'), 10, 2 );
		add_filter( 'leaguemanager_import_matches_'.$this->key, array(&$this, 'importMatches'), 10, 3 );
		add_filter( 'leaguemanager_export_teams_header_'.$this->key, array(&$this, 'exportTeamsHeader') );
		add_filter( 'leaguemanager_export_teams_data_'.$this->key, array(&$this, 'exportTeamsData'), 10, 2 );
		add_filter( 'leaguemanager_import_teams_'.$this->key, array(&$this, 'importTeams'), 10, 2 );

		add_action( 'matchtable_header_'.$this->key, array(&$this, 'displayMatchesHeader'), 10, 0);
		add_action( 'matchtable_columns_'.$this->key, array(&$this, 'displayMatchesColumns') );
		add_action( 'leaguemanager_standings_header_'.$this->key, array(&$this, 'displayStandingsHeader') );
		add_action( 'leaguemanager_standings_columns_'.$this->key, array(&$this, 'displayStandingsColumns'), 10, 2 );
		add_action( 'team_edit_form_'.$this->key, array(&$this, 'editTeam') );

		add_action( 'leaguemanager_save_standings_'.$this->key, array(&$this, 'saveStandings') );
	}
	function LeagueManagerSoccer()
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
		$sports[$this->key] = __( 'Volleyball', 'leaguemanager' );
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
			$set_diff[$key] = $row->sets['plus']-$row->sets['minus'];
			$won_sets[$key] = $row->sets['plus'];
			$ballpoints_diff[$key] = $row->ballpoints['plus']-$row->ballpoints['minus'];
			$ballpoints[$key] = $row->ballpoints['plus'];
		}

		array_multisort( $points, SORT_DESC, $set_diff, SORT_DESC, $won_sets, SORT_DESC, $ballpoints_diff, SORT_DESC, $ballpoints, SORT_DESC, $teams );
		return $teams;
	}


	/**
	 * save custom standings
	 *
	 * @param int $team_id
	 * @return void
	 */
	function saveStandings( $team_id )
	{
		global $wpdb, $leaguemanager;

		$team = $wpdb->get_results( "SELECT `custom` FROM {$wpdb->leaguemanager_teams} WHERE `id` = {$team_id}" );
		$custom = maybe_unserialize($team->custom);
		$custom = $this->getStandingsData($team_id, $custom);

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `custom` = '%s' WHERE `id` = '%d'", maybe_serialize($custom), $team_id ) );
	}


	/**
	 * get standings data for given team
	 *
	 * @param int $team_id
	 * @param array $data
	 * @return array number of runs for and against as assoziative array
	 */
	function getStandingsData( $team_id, $data = array() )
	{
		global $leaguemanager;
		
		$data['sets'] = array( "won" => 0, "lost" => 0 );
		$data['ballpoints'] = array( 'plus' => 0, 'minus' => 0 );

		$matches = $leaguemanager->getMatches( "(`home_team` = {$team_id} OR `away_team` = {$team_id})" );
		foreach ( $matches AS $match ) {
			// Home Match
			if ( $team_id == $match->home_team ) {
				$data['sets']['won'] += $match->home_points;
				$data['sets']['lost'] += $match->away_points;
	
				foreach ( $match->sets AS $s => $set ) {
					$data['ballpoints']['plus'] += $set['home'];
					$data['ballpoints']['minus'] += $set['away'];
				}
			} else {
				$data['sets']['won'] += $match->away_points;
				$data['sets']['lost'] += $match->home_points;
	
				foreach ( $match->sets AS $s => $set ) {
					$data['ballpoints']['plus'] += $set['away'];
					$data['ballpoints']['minus'] += $set['home'];
				}
			}
		}
		
		return $data;
	}


	/**
	 * extend header for Standings Table in Backend
	 *
	 * @param none
	 * @return void
	 */
	function displayStandingsHeader()
	{
		echo '<th class="num">'.__( 'Sets', 'leaguemanager' ).'</th><th class="num">'.__( 'Ballpoints', 'leaguemanager' ).'</th>';
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

		if ( is_admin() && $rule == 'manual' )
			echo '<td><input type="text" size="2" name="custom['.$team->id.'][sets][won]" value="'.$team->sets['won'].'" />:<input type="text" size="2" name="custom['.$team->id.'][sets][lost]" value="'.$team->sets['lost'].'" /></td><td><input type="text" size="2" name="custom['.$team->id.'][ballpoints][plus]" value="'.$team->ballpoints['plus'].'" />:<input type="text" size="2" name="custom['.$team->id.'][ballpoints][minus]" value="'.$team->ballpoints['minus'].'" /></td>';
		else
			echo '<td class="num">'.sprintf($league->point_format2, $team->sets['won'], $team->sets['lost']).'</td><td class="num">'.sprintf($league->point_format2, $team->ballpoints['plus'], $team->ballpoints['minus']).'</td>';
	}


	/**
	 * display hidden fields in team edit form
	 *
	 * @param object $team
	 * @return void
	 */
	function editTeam( $team )
	{
		echo '<input type="hidden" name="custom[sets][won]" value="'.$team->sets['won'].'" /><input type="hidden" name="custom[sets][lost]" value="'.$team->sets['lost'].'" /><input type="hidden" name="custom[ballpoints][plus]" value="'.$team->ballpoints['plus'].'" /><input type="hidden" name="custom[ballpoints][minus]" value="'.$team->ballpoints['minus'].'" />';
	}


	/**
	 * display Table Header for Match Administration
	 *
	 * @param none
	 * @return void
	 */
	function displayMatchesHeader()
	{
		echo '<th colspan="5" style="text-align: center;">'.__( 'Sets', 'leaguemanager' ).'</th>';
	}


	/**
	 * display Table columns for Match Administration
	 *
	 * @param object $match
	 * @return void
	 */
	function displayMatchesColumns( $match )
	{
		for ( $i = 1; $i <= 5; $i++ ) {
			echo '<td><input class="points" type="text" size="2" id="set_'.$match->id.'_'.$i.'_home" name="custom['.$match->id.'][sets]['.$i.'][home]" value="'.$match->sets[$i]['home'].'" /> : <input class="points" type="text" size="2" id="set_'.$match->id.'_'.$i.'_away" name="custom['.$match->id.'][sets]['.$i.'][away]" value="'.$match->sets[$i]['away'].'" /></td>';
		}
	}


	/**
	 * export matches header
	 *
	 * @param string $content
	 * @return the content
	 */
	function exportMatchesHeader( $content )
	{
		$content .= "\t"._c( 'Sets', 'leaguemanager' )."\t\t\t\t";
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
		if ( isset($match->sets) ) {
			foreach ( $match->sets AS $j => $set ) {
				$content .= "\t".implode(":", $set);
			}
		} else {
			$content .= "\t\t\t";
		}

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
		for( $x = 8; $x <= 12; $x++ ) {
			$set = explode(":",$line[$x]);
			$custom[$match_id]['sets'][] = array( 'home' => $set[0], 'away' => $set[1] );
		}

		return $custom;
	}


	/**
	 * export teams header
	 *
	 * @param string $content
	 * @return the content
	 */
	function exportTeamsHeader( $content )
	{
		$content .= "\t".__( 'Sets', 'leaguemanager' )."\t".__('Ballpoints', 'leaguemanager');
		return $content;
	}


	/**
	 * export teams data
	 *
	 * @param string $content
	 * @param object $team
	 * @return the content
	 */
	function exportTeamsData( $content, $team )
	{
		if ( isset($team->sets) )
			$content .= "\t".sprintf(":",$team->sets['won'], $team->sets['lost'])."\t".sprintf(":", $team->ballpoints['plus'], $team->ballpoints['minus']);
		else
			$content .= "\t\t";

		return $content;
	}

	
	/**
	 * import teams
	 *
	 * @param array $custom
	 * @param array $line elements start at index 8
	 * @return array
	 */
	function importTeams( $custom, $line )
	{
		$sets = explode(":", $line[8]);
		$ballpoints = explode(":", $line[9]);
		$custom['sets'] = array( 'won' => $sets[0], 'lost' => $sets[1] );
		$custom['ballpoints'] = array( 'plus' => $ballpoints[0], 'minus' => $ballpoints[1] );

		return $custom;
	}
}

$volleyball = new LeagueManagerVolleyball();
?>
