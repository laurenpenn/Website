<?php
/**
 * Tabletennis Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerTabletennis extends LeagueManager
{
	/**
	 * sports key
	 *
	 * @var string
	 */
	var $key = 'tabletennis';


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
		add_filter( 'leaguemanager_point_rules_list', array(&$this, 'getPointRuleList') );
		add_filter( 'leaguemanager_point_rules',  array(&$this, 'getPointRules') );
	}
	function LeagueManagerTabletennis()
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
		$sports[$this->key] = __( 'Tabletennis', 'leaguemanager');
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
		$rules[$this->key] = __('Tabletennis', 'leaguemanager');

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
		$rules['tabletennis'] = array( 'forwin' => 0, 'fordraw' => 0, 'forloss' => 0 );

		return $rules;
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

		$points = array( 'plus' => 0, 'minus' => 0 );
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

//$tabletennis = new LeagueManagerTabletennis();
?>
