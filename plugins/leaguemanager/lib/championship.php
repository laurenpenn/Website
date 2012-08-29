<?php
/**
 * Core class for the WordPress plugin LeagueManager
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerChampionship extends LeagueManager
{
	/**
	 * league object
	 *
	 * @var object
	 */
	var $league;


	/**
	 * preliminary groups
	 *
	 * @var array
	 */
	var $groups = array();


	/**
	 * number of final rounds
	 *
	 * @var int
	 */
	var $num_rounds;


	/**
	 * number of teams in first round
	 *
	 * @var int
	 */
	var $num_teams_first_round;


	/**
	 * final keys indexed by round
	 *
	 * @var array
	 */
	var $keys = array();


	/**
	 * finals indexed by key
	 *
	 * @var array
	 */
	var $finals = array();


	/**
	 * initialize Champtionchip Mode
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		add_filter( 'leaguemanager_modes', array(&$this, 'modes') );
		add_action( 'league_settings_championship', array(&$this, 'settingsPage') );

		if ( isset($_GET['league_id']) )
			$this->initialize((int)$_GET['league_id']);
	}
	function LeagueManagerchampionship()
	{
		$this->__construct();
	}


	/**
	 * initialize basic settings
	 *
	 * @param int $league_id
	 * @return void
	 */
	function initialize( $league_id )
	{
		global $leaguemanager;
		$this->league = $leaguemanager->getLeague($league_id);
		$this->groups = explode(";", $this->league->groups);

		$this->num_teams_first_round = count($this->getGroups()) * $this->league->num_advance;
		$num_rounds = log($this->num_teams_first_round, 2);
		$num_teams = 2;

		$i = $num_rounds;
		while ( $num_teams <= $this->num_teams_first_round ) {
			$finalkey = $this->getFinalKey($num_teams);
			$this->finals[$finalkey] = array( 'key' => $finalkey, 'name' => $this->getFinalName($finalkey), 'num_matches' => $num_teams/2, 'num_teams' => $num_teams, 'round' => $i );

			// Separately add match for third playce
			if ( $num_teams == 2 ) {
				$finalkey = 'third';
				$this->finals[$finalkey] = array( 'key' => $finalkey, 'name' => $this->getFinalName($finalkey), 'num_matches' => $num_teams/2, 'num_teams' => $num_teams, 'round' => $i );
			}
				
			$this->keys[$i] = $finalkey;

			$i--;
			$num_teams = $num_teams * 2;
		}
		$this->num_rounds = $num_rounds;
	}


	/**
	 * get league object
	 *
	 * @param int $league_id
	 * @return void
	 */
	function getLeague( ) {
		return $this->league;
	}


	/**
	 * get groups
	 *
	 * @param none
	 * @return array
	 */
	function getGroups()
	{
		return $this->groups;
	}


	/**
	 * get final key
	 *
	 * @param int $round
	 * @return string
	 */
	function getFinalKeys( $round )
	{
		if ( $round )
			return $this->keys[$round];

		return $this->keys;
	}


	/**
	 * get final data
	 *
	 * @param int $round
	 * @return mixed
	 */
	function getFinals( $key = false )
	{
		if ( $key )
			return $this->finals[$key];
	
		return $this->finals;
	}


	/**
	 * get number of final rounds
	 *
	 * @param none
	 * @return int
	 */
	function getNumRounds()
	{
		return $this->num_rounds;
	}


	/**
	 * get number of teams in first final round
	 *
	 * @param none
	 * @return int
	 */
	function getNumTeamsFirstRound()
	{
		return $this->num_teams_first_round;
	}


	/**
	 * add championship mode
	 *
	 * @param array $modes
	 * @return array
	 */
	function modes( $modes )
	{
		$modes['championship'] = __( 'Championship', 'leaguemanager' );
		return $modes;
	}


	/**
	 * add settings
	 *
	 * @param object $league
	 * @return void
	 */
	function settingsPage( $league )
	{
		echo '<tr valign="top">';
		echo '<th scope="row"><label for="groups">'.__( 'Groups', 'leaguemanager' ).'</label></th>';
		echo '<td valign="top"><input type="text" name="settings[groups]" id="groups" size="20" value="'.$league->groups.'" />&#160;<span class="setting-description">'.__( 'Separate Groups by semicolon ;', 'leaguemanager' ).'</span></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<th scope="row"><label for="num_advance">'.__('Teams Advance', 'leaguemanager').'</label></th>';
		echo '<td><input type="text" size="3" id="num_advance" name="settings[num_advance]" value="'.$league->num_advance.'" /></td>';
		echo '</tr>';
	}


	/**
	 * get name of final depending on number of teams
	 *
	 * @param string $key
	 * @return the name
	 */
	function getFinalName( $key )
	{
		if ( 'final' == $key )
			return __( 'Final', 'leaguemanager' );
		elseif ( 'third' == $key )
			return __( 'Third Place', 'leaguemanager' );
		elseif ( 'semi' == $key )
			return __( 'Semi Final', 'leaguemanager' );
		elseif ( 'quarter' == $key )
			return __( 'Quarter Final', 'leaguemanager' );
		else {
			$tmp = explode("-", $key);
			return sprintf(__( 'Last-%d', 'leaguemanager'), $tmp[1]);
		}
	}
	
	
	/**
	 * get key of final depending on number of teams
	 *
	 * @param int $num_teams
	 * @return the key
	 */
	function getFinalKey( $num_teams )
	{
		if ( 2 == $num_teams )
			return 'final';
		elseif ( 4 == $num_teams )
			return 'semi';
		elseif ( 8 == $num_teams )
			return 'quarter';
		else
			return 'last-'.$num_teams;
	}
	
	
	/**
	 * get number of matches
	 *
	 * @param string $key
	 * @return int
	 */
	function getNumMatches( $key )
	{
		if ( 'final' == $key || 'third' == $key )
			return 1;
		elseif ( 'semi' == $key )
			return 2;
		elseif ( 'quarter' == $key )
			return 4;
		else {
			$tmp = explode("-", $key);
			return $tmp[1]/2;
		}
	}


	/**
	 * get array of teams for finals
	 *
	 * @param array $final
	 * @param boolean $start true if first round of finals
	 * @param string $round 'prev' | 'current'
	 * @return array of teams
	 */
	function getFinalTeams( $final, $output = 'OBJECT' )
	{
		$current = $final;
		// Set previous final or false if first round
		$final = ( $final['round'] > 1 ) ? $this->getFinals($this->getFinalKeys($final['round']-1)) : false;

		$teams = array();
		if ( $final ) {
			for ( $x = 1; $x <= $final['num_matches']; $x++ ) {
				if ( $current['key'] == 'third' ) {
					$title = sprintf(__('Looser %s %d', 'leaguemanager'), $final['name'], $x);
					$key = '2_'.$final['key'].'_'.$x;
				} else {
					$title = sprintf(__('Winner %s %d', 'leaguemanager'), $final['name'], $x);
					$key = '1_'.$final['key'].'_'.$x;
				}

				if( $output == 'ARRAY' ) {
					$teams[$key] = $title;
				} else {
					$data = array( 'id' => $key, 'title' => $title );
					$teams[] = (object) $data;
				}
			}
		} else {
			foreach ( (array)explode(";",$this->league->groups) AS $group ) {
				for ( $a = 1; $a <= $this->league->num_advance; $a++ ) {
					$title = sprintf(__('%d Group %s', 'leaguemanager'), $a, $group);
					if( $output == 'ARRAY' ) {
						$teams[$a.'_'.$group] =	$title;
					} else {
						$data = array( 'id' => $a.'_'.$group, 'title' => $title );
						$teams[] = (object) $data;
					}
				}
			}
		}
		return $teams;
	}


	/**
	 * update final rounds results
	 *
	 * @param int $league_id
	 * @param array $matches
	 * @param array $home_poinsts
	 * @param array $away_points
	 * @param array $home_team
	 * @param array $away_team
	 * @param array $custom
	 * @param int $round
	 */
	function updateResults( $league_id, $matches, $home_points, $away_points, $home_team, $away_team, $custom, $round )
	{
		global $lmLoader, $leaguemanager;
		$admin = $lmLoader->getAdminPanel();
		$admin->updateResults($league_id, $matches, $home_points, $away_points, $home_team, $away_team, $custom, true);

		if ( $round < $this->getNumRounds() )
			$this->proceed($this->getFinalKeys($round), $this->getFinalKeys($round+1));
			
		//$leaguemanager->printMessage();

	}


	/**
	 * proceed to next final round
	 *
	 * @param string|false $last
	 * @param string $current
	 * @return void
	 */
	function proceed( $last, $current )
	{
		global $leaguemanager, $wpdb;

		$league = $this->getLeague();
		$season = $leaguemanager->getSeason( $league );

		$search = "`league_id` = '".$league->id."' AND `season` = '".$season['name']."'";
		$matches = $leaguemanager->getMatches( $search . " AND `final` = '".$current."'" );
		foreach ( $matches AS $match ) {
			$update = true;
			$home = explode("_", $match->home_team);
			$away = explode("_", $match->away_team);
			// First Final round. Previous results are from preliminary round
			if ( !$last ) {
				$home = array( 'rank' => $home[0], 'group' => $home[1] );
				$away = array( 'rank' => $away[0], 'group' => $away[1] );

				$home_team = $wpdb->get_results( "SELECT `id` FROM {$wpdb->leaguemanager_teams} WHERE $search AND `rank` = '".$home['rank']."' AND `group` = '".$home['group']."'" );
				$away_team = $wpdb->get_results( "SELECT `id` FROM {$wpdb->leaguemanager_teams} WHERE $search AND `rank` = '".$away['rank']."' AND `group` = '".$away['group']."'" );

				if ( $home_team && $away_team ) {
					$home['team'] = $home_team[0]->id;
					$away['team'] = $away_team[0]->id;
				} else {
					$update = false;
				}
			} else {
				$col = ( $home[0] == 1 ) ? 'winner_id' : 'loser_id';
				$home = array( 'col' => $col, 'finalkey' => $home[1], 'no' => $home[2] );
				$col = ( $away[0] == 1 ) ? 'winner_id' : 'loser_id';
				$away = array( 'col' => $col, 'finalkey' => $away[1], 'no' => $away[2] );

				// get matches of previous round
				$prev = $leaguemanager->getMatches( $search . " AND `final` = '".$last."'" );
				if ( $prev[$home['no']-1] && $prev[$away['no']-1] ) {
					$prev_home = $prev[$home['no']-1];
					$prev_away = $prev[$away['no']-1];

					$home['team'] = $prev_home->{$home['col']};
					$away['team'] = $prev_away->{$away['col']};
				} else {
					$update = false;
				}
			}

			if ( $update ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_matches} SET `home_team` = %d, `away_team` = %d WHERE `id` = %d", $home['team'], $away['team'], $match->id ) );
				// Set winners on final
				if ( $current == 'third' ) {
					$match = $leaguemanager->getMatches( $search . " AND `final` = 'final'" );
					$match = $match[0];
					$home_team = $prev_home->winner_id;
					$away_team = $prev_away->winner_id;
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_matches} SET `home_team`= %d, `away_team`= %d WHERE `id` = %d", $home_team, $away_team, $match->id ) );
				}

			}
		}
	}
}
?>
