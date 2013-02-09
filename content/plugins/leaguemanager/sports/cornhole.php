<?php
/**
 * Cornhole Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerCornhole extends LeagueManager
{
	/**
	 * sports key
	 *
	 * @var string
	 */
	var $key = 'cornhole';


	/**
	 * load specific settings
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		add_filter( 'leaguemanager_sports', array(&$this, 'sports') );
		add_filter( 'team_points_'.$this->key, array(&$this, 'calculatePoints'), 10, 3 );
	}
	function LeagueManagerCornhole()
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
		$sports[$this->key] = __( 'Cornhole', 'leaguemanager');
		return $sports;
	}

	
	/**
	 * calculate Points: add match score
	 *
	 * @param array $points
	 * @param int $team_id
	 * @param array $rule
	 */
	function calculatePoints( $points, $team_id, $rule )
	{
		global $leaguemanager;

		$home = $leaguemanager->getMatches( "`home_team` = '".$team_id."'" );
		foreach ( $home AS $match ) {
			$points['plus'] += $match->home_points;
			$points['minus'] += $match->away_points;
		}

		$away = $leaguemanager->getMatches("`away_team` = '".$team_id."'" );
		foreach ( $away AS $match ) {
			$points['plus'] += $match->away_points;
			$points['minus'] += $match->home_points;
		}

		return $points;
	}
}

$cornhole = new LeagueManagerCornhole();
?>
