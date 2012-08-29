<?php
/**
 * Pool Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerPool extends LeagueManager
{

	/**
	 * sports key
	 *
	 * @var string
	 */
	var $key = 'pool';


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

		add_action( 'leaguemanager_save_standings', array(&$this, 'saveStandings') );
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
		$sports[$this->key] = __( 'Pool Billiard', 'leaguemanager' );
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
			$diff[$key] = $row->forScore - $row->againstScore;
			$won[$key] = $row->won_matches;
		}

		array_multisort( $points, SORT_DESC, $diff, SORT_DESC, $won, SORT_DESC, $teams );
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
		global $wpdb;

		$team = $wpdb->get_results( "SELECT `custom` FROM {$wpdb->leaguemanager_teams} WHERE `id` = {$team_id}" );
		$custom = maybe_unserialize($team->custom);

		$custom['forScore'] = $this->getScore($team_id, 'for');
		$custom['againstScore'] = $this->getScore($team_id, 'against');

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `custom` = '%s' WHERE `id` = '%d'", maybe_serialize($custom), $team_id ) );
	}


	/**
	 * get score for left balls
	 *
	 * @param int $team_id
	 * @return array number of runs for and against as assoziative array
	 */
	function getScore( $team_id, $index )
	{
		global $leaguemanager;
		
		$score = array( 'for' => 0, 'against' => 0 );
		$home = $leaguemanager->getMatches( "`home_team` = {$team_id}" );
		foreach ( $home AS $match ) {
			$score['for'] += $match->forScore;
			$score['against'] += $match->againstScore;
		}

		$away = $leaguemanager->getMatches( "`away_team` = {$team_id}" );
		foreach ( $away AS $match ) {
			$score['for'] += $match->againstScore;
			$score['against'] += $match->forScore;
		}
		
		return $score[$index];
	}
	

	/**
	 * extend header for Standings Table 
	 *
	 * @param none
	 * @return void
	 */
	function displayStandingsHeader()
	{
		echo '<th class="num">'.__( 'For', 'leaguemanager' ).'</th><th>'.__( 'Against', 'leaguemanager' ).'</th>';
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
		if ( is_admin() && $rule == 'manual' )
			echo '<td><input type="text" size="2" name="custom['.$team->id.'][forScore]" value="'.$team->forScore.'" /></td><td><input type="text" size="2" name="custom['.$team->id.'][againstScore]" value="'.$team->againstScore.'" /></td>';
		else
			echo '<td class="num">'.$team->forScore.'</td><td class="num">'.$team->againstScore.'</td>';
	}


	/**
	 * display hidden fields in team form
	 *
	 * @param object $team
	 * @return void
	 */
	function editTeam( $team )
	{
		echo '<input type="hidden" name="custom[forScore]" value="'.$team->forScore.'" /><input type="hidden" name="custom[againstScore]" value="'.$team->againstScore.'" />';
	}


	/**
	 * display Table Header for Match Administration
	 *
	 * @param none
	 * @return void
	 */
	function displayMatchesHeader()
	{
		echo '<th>'.__( 'For', 'leaguemanager' ).'</th><th>'.__( 'Against', 'leaguemanager' ).'</th>';
	}


	/**
	 * display Table columns for Match Administration
	 *
	 * @param object $match
	 * @return void
	 */
	function displayMatchesColumns( $match )
	{
		echo '<td><input class="points" type="text" size="2" id="forscore_'.$match->id.'" name="custom['.$match->id.'][forScore]" value="'.$match->forScore.'" /></td><td><input clas="points" type="text" size="2" id="againstscore_'.$match->id.'" name="custom['.$match->id.'][againstScore]" value="'.$match->againstScore.'" /></td>';
	}


	/**
	 * export matches header
	 *
	 * @param string $content
	 * @return the content
	 */
	function exportMatchesHeader( $content )
	{
		$content .= "\t".__( 'For', 'leaguemanager' )."\t".__('Against', 'leaguemanager');
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
		if ( isset($match->forScore) )
			$content .= "\t".$match->forScore."\t".$match->againstScore;
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
		$custom[$match_id]['forScore'] = $line[8];
		$custom[$match_id]['againstScore'] = $line[9];
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
		$content .= "\t".__( 'For', 'leaguemanager' )."\t".__('Against', 'leaguemanager');
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
		if ( isset($team->forScore) )
			$content .= "\t".$team->forScore."\t".$team->againstScore;
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
		$custom['forScore'] = $line[8];
		$custom['againstScore'] = $line[9];
		return $custom;
	}
}

$pool = new LeagueManagerPool();
?>
