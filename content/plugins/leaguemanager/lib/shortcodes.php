<?php
/**
* Shortcodes class for the WordPress plugin LeagueManager
* 
* @author 	Kolja Schleich
* @package	LeagueManager
* @copyright 	Copyright 2008-2009
*/

class LeagueManagerShortcodes extends LeagueManager
{
	/**
	 * checks if bridge is active
	 *
	 * @var boolean
	 */
	var $bridge = false;
	
	
	/**
	 * initialize shortcodes
	 *
	 * @param boolean $bridge
	 * @return void
	 */
	function __construct($bridge = false)
	{
		global $lmLoader;
		
		$this->addShortcodes();
		if ( $bridge ) {
			global $lmBridge;
			$this->bridge =  true;
			$this->lmBridge = $lmBridge;
		}
	}
	function LeagueManagerShortcodes($bridge = false)
	{
		$this->__construct($bridge);
	}
	

	/**
	 * Adds shortcodes
	 *
	 * @param none
	 * @return void
	 */
	function addShortcodes()
	{
		add_shortcode( 'standings', array(&$this, 'showStandings') );
		add_shortcode( 'matches', array(&$this, 'showMatches') );
		add_shortcode( 'match', array(&$this, 'showMatch') );
		add_shortcode( 'championship', array(&$this, 'showChampionship') );
		add_shortcode( 'crosstable', array(&$this, 'showCrosstable') );
		add_shortcode( 'teams', array(&$this, 'showTeams') );
		add_shortcode( 'team', array(&$this, 'showTeam') );
		add_shortcode( 'leaguearchive', array(&$this, 'showArchive') );

		add_action( 'leaguemanager_teampage', array(&$this, 'showTeam') );
	}
	
	
	/**
	 * Function to display League Standings
	 *
	 *	[standings league_id="1" mode="extend|compact" template="name"]
	 *
	 * - league_id is the ID of league
	 * - league_name (optional) get league by name and not id
	 * - season: display specific season (optional). default is current season
	 * - template is the template used for displaying. Replace name appropriately. Templates must be named "standings-template.php" (optional)
	 * - group: optional
	 *
	 * @param array $atts
	 * @param boolean $widget (optional)
	 * @return the content
	 */
	function showStandings( $atts, $widget = false )
	{
		global $wpdb, $leaguemanager;
		
		extract(shortcode_atts(array(
			'league_id' => 0,
			'league_name' => '',
			'logo' => 'true',
			'template' => 'extend',
			'season' => false,
			'group' => false,
			'home' => 0
		), $atts ));
		
		$search = !empty($league_name) ? $league_name : $league_id;
		$league = $leaguemanager->getLeague( $search );
		if (!$season) {
			$season = $leaguemanager->getSeason( $league );
			$season = $season['name'];
		}

		$search = "`league_id` = '".$league->id."' AND `season` = '".$season."'";
		if ( $group ) $search .= " AND `group` = '".$group."'";
		$teams = $leaguemanager->getTeams( $search );
	
		if ( !empty($home) ) {
			$teamlist = array();
			foreach ( $teams AS $offset => $team ) {
				if ( $team->home == 1 ) {
					$low = $offset-$home;
					$high = $offset+$home;

					if ( $low < 0 ) {
						$high -= $low;
						$low = 0;
					} elseif ( $high > count($teams)-1 ) {
						$low -= $high - count($teams)+1;
						$high = count($teams)-1;
					}

					for ( $x = $low; $x <= $high; $x++ ) {
						if ( !array_key_exists($teams[$x]->rank, $teamlist) ) 
							$teamlist[$teams[$x]->rank] = $teams[$x];
					}
				}
			}
			
			$teams = array_values($teamlist);
		}

		$i = 0; $class = array();
		foreach ( $teams AS $team ) {
			$class = ( in_array('alternate', $class) ) ? array() : array('alternate');
			// Add classes for ascend or descend
			if ( $team->rank <= $league->num_ascend ) $class[] = 'ascend';
			elseif ( count($teams)-$team->rank < $league->num_descend ) $class[] =  'descend';

			// Add class for relegation
			if ( $team->rank >  count($teams)-$league->num_descend-$league->num_relegation && $team->rank <= count($teams)-$league->num_descend ) $class[] = 'relegation';

			// Add class for home team
			if ( 1 == $team->home ) $class[] = 'homeTeam';
			
			$url = get_permalink();
			$url = add_query_arg( 'team', $team->id, $url );

			$teams[$i]->pageURL = $url;
			//if ( $league->team_ranking == 'auto' ) $teams[$i]->rank = $i+1;
			$teams[$i]->class = implode(' ', $class);
			$teams[$i]->logoURL = $leaguemanager->getThumbnailUrl($team->logo);
			if ( 1 == $team->home ) $teams[$i]->title = '<strong>'.$team->title.'</strong>';
			if ( $team->website != '' ) $teams[$i]->title = '<a href="http://'.$team->website.'" target="_blank">'.$team->title.'</a>';
			
			$team->points_plus += $team->add_points; // add or substract points
			$teams[$i]->points = sprintf($league->point_format, $team->points_plus, $team->points_minus);
			$teams[$i]->points2 = sprintf($league->point_format2, $team->points2_plus, $team->points2_minus);
			$i++;
		}
		
		$league->show_logo = ( $logo == 'true' ) ? true : false;

		if ( !$widget && $this->checkTemplate('standings-'.$league->sport) )
			$filename = 'standings-'.$league->sport;
		else
			$filename = 'standings-'.$template;

		$out = $this->loadTemplate( $filename, array('league' => $league, 'teams' => $teams, 'widget' => $widget) );
			
		return $out;
	}
	
	
	/**
	 * Function to display League Matches
	 *
	 *	[matches league_id="1" mode="all|home|racing" template="name" roster=ID]
	 *
	 * - league_id is the ID of league
	 * - league_name: get league by name and not ID (optional)
	 * - mode can be either "all" or "home". For racing it must be "racing". If it is not specified the matches are displayed on a weekly basis
	 * - season: display specific season (optional)
	 * - template is the template used for displaying. Replace name appropriately. Templates must be named "matches-template.php" (optional)
	 * - archive: true or false, check if archive page
	 * - roster is the ID of individual team member (currently only works with racing)
	 * - match_day: specific match day (integer)
	 *
	 * @param array $atts
	 * @return the content
	 */
	function showMatches( $atts )
	{
		global $leaguemanager, $championship;
		
		extract(shortcode_atts(array(
			'league_id' => 0,
			'league_name' => '',
			'team' => 0,
			'template' => '',
			'mode' => '',
			'season' => '',
			'limit' => false,
			'archive' => false,
			'roster' => false,
			'order' => false,
			'match_day' => false,
			'group' => false,
			'time' => false
		), $atts ));
		
		$search = !empty($league_name) ? $league_name : $league_id;
		$league = $leaguemanager->getLeague( $search );
		$league_id = $this->league_id = $league->id;
		$leaguemanager->setLeagueId($league_id);
		
		if ( $league->mode == 'championship' ) $championship->initialize($league->id);

		if ( !$group && isset($_GET['group']) ) $group = $_GET['group'];

		if ( !isset($_GET['match']) ) {
			$season = $leaguemanager->getSeason($league, $season);
			$league->num_match_days = $season['num_match_days'];
			$season = $season['name'];
			$leaguemanager->setSeason($season);

			$league->match_days = ( ( !$match_day && empty($mode) || $mode == 'racing' ) && !$time && $league->num_match_days > 0 ) ? true : false;
			$league->isCurrMatchDay = ( $archive ) ? false : true;
				
			$teams = $leaguemanager->getTeams( "`league_id` = ".$league_id." AND `season` = '".$season."'", "`title` ASC", 'ARRAY' );

			$search = "`league_id` = '".$league_id."' AND `season` = '".$season."' AND `final` = ''";
			if ( $mode != 'racing' ) {
				// Standard is match day based with team dropdown
				if ( empty($mode) ) {
					if ( !empty($team) || (isset($_GET['team_id']) && !empty($_GET['team_id'])) )
						$team_id = !empty($team) ? $team : (int)$_GET['team_id'];

					$match_day = $match_day ? $match_day : $leaguemanager->getMatchDay(true);

					if ( $team_id )
						$search .= " AND ( `home_team`= {$team_id} OR `away_team` = {$team_id} )";
					elseif ( $group )
						$search .= " AND `group` = '".$group."'";
					elseif ( $league->mode != 'championship' && !$time )
						$search .= " AND `match_day` = '".$match_day."'";
					
				}
				
				if ( $time ) {
					if ( $time == 'next' )
						$search .= " AND DATEDIFF(NOW(), `date`) <= 0";
					elseif ( $time == 'prev' )
						$search .= " AND DATEDIFF(NOW(), `date`) > 0";
				}

				// Only get Home Teams
				if ( $mode == 'home' )
					$search .= parent::buildHomeOnlyQuery($league_id);
			} else {
				if ( isset($_GET['match_day']) && !empty($_GET['match_day']) ) {
					$match_day = (int)$_GET['match_day'];
					$search .= " AND `match_day` = '".$match_day."'";
				} elseif ( $match_day ) {
					$search .= " AND `match_day` = '".$match_day."'";
				}
			}
			$matches = $leaguemanager->getMatches( $search , $limit, $order );
			$i = 0;
			foreach ( $matches AS $match ) {
				$class = ( 'alternate' == $class ) ? '' : 'alternate';
				
				$matches[$i]->class = $class;
				$matches[$i]->hadPenalty = $match->hadPenalty = ( isset($match->penalty) && $match->penalty['home'] != '' && $match->penalty['away'] != '' ) ? true : false;
				$matches[$i]->hadOvertime = $match->hadOvertime = ( isset($match->overtime) && $match->overtime['home'] != '' && $match->overtime['away'] != '' ) ? true : false;

				$url = get_permalink();
				$url = add_query_arg( 'match', $match->id, $url );
				$matches[$i]->pageURL = $url;

				$matches[$i]->start_time = ( '00' == $match->hour && '00' == $match->minutes ) ? '' : mysql2date(get_option('time_format'), $match->date);
				$matches[$i]->date = ( substr($match->date, 0, 10) == '0000-00-00' ) ? 'N/A' : mysql2date(get_option('date_format'), $match->date); 

				$matches[$i]->title = ( isset($matches[$i]->title) && !empty($matches[$i]->title) ) ? $match->title : $teams[$match->home_team]['title'].' &#8211; '. $teams[$match->away_team]['title'];
				$matches[$i]->title = apply_filters( 'leaguemanager_matchtitle_'.$league->sport, $matches[$i]->title, $match, $teams ); 
				if ( parent::isHomeTeamMatch( $match->home_team, $match->away_team, $teams ) )
					$matches[$i]->title = '<strong>'.$matches[$i]->title.'</strong>';
				
				$matches[$i]->report = ( $match->post_id != 0 ) ? '(<a href="'.get_permalink($match->post_id).'">'.__('Report', 'leaguemanager').'</a>)' : '';
	
				if ( $match->hadPenalty )
					$matches[$i]->score = sprintf("%s - %s", $match->penalty['home']+$match->overtime['home'], $match->penalty['away']+$match->overtime['away'])." "._c( 'o.P.|on penalty', 'leaguemanager' );
				elseif ( $match->hadOvertime )
					$matches[$i]->score = sprintf("%s - %s", $match->overtime['home'], $match->overtime['away'])." "._c( 'AET|after extra time', 'leaguemanager' );
				elseif ( $match->home_points != NULL && $match->away_points != NULL ) 
					$matches[$i]->score = sprintf("%s - %s", $matches[$i]->home_points, $matches[$i]->away_points);
				else
					$matches[$i]->score = "-:-";

				$i++;
			}
		}
		
		if ( empty($template) && $this->checkTemplate('matches-'.$league->sport) )
			$filename = 'matches-'.$league->sport;
		else
			$filename = ( !empty($template) ) ? 'matches-'.$template : 'matches';

		$out = $this->loadTemplate( $filename, array('league' => $league, 'matches' => $matches, 'teams' => $teams, 'season' => $season, 'roster' => $roster ) );

		return $out;
	}
	
	
	/**
	 * Function to display single match
	 *
	 * [match id="1" template="name"]
	 *
	 * - id is the ID of the match to display
	 * - template is the template used for displaying. Replace name appropriately. Templates must be named "match-template.php" (optional)
	 *
	 * @param array $atts
	 * @return the content
	 */
	function showMatch( $atts )
	{
		global $leaguemanager, $lmStats;
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => '',
		), $atts ));
		
		$match = $leaguemanager->getMatch($id);
		$league = $leaguemanager->getLeague($match->league_id);
		$home = $leaguemanager->getTeam($match->home_team);
		$away = $leaguemanager->getTeam($match->away_team);
		
		$match->hadPenalty = ( isset($match->penalty) && $match->penalty['home'] != '' && $match->penalty['away'] != '' ) ? true : false;
		$match->hadOvertime = ( isset($match->overtime) && $match->overtime['home'] != '' && $match->overtime['away'] != '' ) ? true : false;

		$match->home_points = ( NULL == $match->home_points ) ? '-' : $match->home_points;
		$match->away_points = ( NULL == $match->away_points ) ? '-' : $match->away_points;

		$match->homeTeam = $home->title;
		$match->awayTeam = $away->title;
		$match->title = $match->homeTeam . "&#8211;" . $match->awayTeam;

		$match->homeLogo = $home->logo;
		$match->awayLogo = $away->logo;

		$match->start_time = ( '00' == $match->hour && '00' == $match->minutes ) ? '' : mysql2date(get_option('time_format'), $match->date);
		$match->date = ( substr($match->date, 0, 10) == '0000-00-00' ) ? 'N/A' : mysql2date(get_option('date_format'), $match->date); 

		$match->report = ( $match->post_id != 0 ) ? '(<a href="'.get_permalink($match->post_id).'">'.__('Report', 'leaguemanager').'</a>)' : '';

		if ( $match->hadPenalty )
			$match->score = sprintf("%s - %s", $match->penalty['home'], $match->penalty['away'])." "._c( 'o.P.|on penalty', 'leaguemanager' );
		elseif ( $match->hadOvertime )
			$match->score = sprintf("%s - %s", $match->overtime['home'], $match->overtime['away'])." "._c( 'AET|after extra time', 'leaguemanager' );
		else
			$match->score = sprintf("%s - %s", $match->home_points, $match->away_points);
		
		if ( empty($template) && $this->checkTemplate('match-'.$league->sport) )
			$filename = 'match-'.$league->sport;
		else
			$filename = ( !empty($template) ) ? 'match-'.$template : 'match';

		$out = $this->loadTemplate( $filename, array('league' => $league, 'match' => $match) );

		return $out;
	}
	
	
	/**
	 * Function to display championship
	 *
	 *	[championship league_id="1" template="name"]
	 *
	 * - league_id is the ID of league
	 * - league_name: get league by name and not ID (optional)
	 * - season: display specific season (optional)
	 * - template is the template used for displaying. Replace name appropriately. Templates must be named "matches-template.php" (optional)
	 *
	 * @param array $atts
	 * @return the content
	 */
	function showChampionship( $atts )
	{
		global $leaguemanager, $championship;
		
		extract(shortcode_atts(array(
			'league_id' => 0,
			'league_name' => '',
			'template' => '',
			'season' => false,
		), $atts ));
		
		$search = !empty($league_name) ? $league_name : $league_id;
		$league = $leaguemanager->getLeague( $search );
		if ( !$season ) {
			$season = $leaguemanager->getSeason($league);
			$season = $season['name'];
		}
		$league->season = $season;
		$league_id = $this->league_id = $league->id;
		
		$championship->initialize($league->id);

		$finals = array();
		foreach ( $championship->getFinals() AS $final ) {
			$class = ( 'alternate' == $class ) ? '' : 'alternate';
			$data['class'] = $class;
			
			$data['key'] = $final['key'];
			$data['name'] = $final['name'];
			$data['num_matches'] = $final['num_matches'];
			$data['colspan'] = ( $championship->getNumTeamsFirstRound()/2 >= 4 ) ? ceil(4/$final['num_matches']) : ceil(($championship->getNumTeamsFirstRound()/2)/$final['num_matches']);

			$matches_raw = $leaguemanager->getMatches("`league_id` = '".$league->id."' AND `season` = '".$season."' AND `final` = '".$final['key']."'", false, "`id` ASC");
			$teams = $leaguemanager->getTeams( "`league_id` = '".$league->id."' AND `season` = '".$season."'", "`id` ASC", 'ARRAY' );
			$teams2 = $championship->getFinalTeams($final, 'ARRAY');
			
			$matches = array();
			for ( $i = 1; $i <= $final['num_matches']; $i++ ) {
				$match = $matches_raw[$i-1];
				if ( $match ) {
					if ( is_numeric($match->home_team) && is_numeric($match->away_team) ) {
						$match->title = $match->title2 = sprintf("%s &#8211; %s", $teams[$match->home_team]['title'], $teams[$match->away_team]['title']);
					} else {
						$match->title = sprintf("%s &#8211; %s", $teams2[$match->home_team], $teams2[$match->away_team]);
						$match->title2 = "&#8211;";
					}

					$match->hadPenalty = $match->hadPenalty = ( isset($match->penalty) && $match->penalty['home'] != '' && $match->penalty['away'] != '' ) ? true : false;
					$match->hadOvertime = $match->hadOvertime = ( isset($match->overtime) && $match->overtime['home'] != '' && $match->overtime['away'] != '' ) ? true : false;

					if ( $match->home_points != NULL && $match->away_points != NULL ) {
						if ( $match->hadPenalty )
							$match->score = sprintf("%s:%s", $match->penalty['home'], $match->penalty['away'])." "._c( 'o.P.|on penalty', 'leaguemanager' );
						elseif ( $match->hadOvertime )
							$match->score = sprintf("%s:%s", $match->overtime['home'], $match->overtime['away'])." "._c( 'AET|after extra time', 'leaguemanager' );
						else
							$match->score = sprintf("%s:%s", $match->home_points, $match->away_points);
						if ( $final['key'] == 'final' ) {
							$data['isFinal'] = true;
							$data['field_id'] = ( $match->winner_id == $match->home_team ) ? "final_home" : "final_away";
						} else {
							$data['isFinal'] = false;
							}
					} else {
						$match->score = "-:-";
					}

					$match->date = ( substr($match->date, 0, 10) == '0000-00-00' ) ? 'N/A' : mysql2date(get_option('date_format'), $match->date);
					$match->time = ( '00:00' == $match->hour.":".$match->minutes ) ? 'N/A' : mysql2date(get_option('time_format'), $match->date);
					if ( empty($match->location) ) $match->location = 'N/A';

					$matches[$i] = $match;
				}
			}

			$data['matches'] = $matches;
			$finals[] = (object)$data;
		}

		if ( empty($template) && $this->checkTemplate('championship-'.$league->sport) )
			$filename = 'championship-'.$league->sport;
		else
			$filename = ( !empty($template) ) ? 'championship-'.$template : 'championship';

		$out = $this->loadTemplate( $filename, array('league' => $league, 'championship' => $championship, 'finals' => $finals) );

		return $out;
	}


	/**
	 * Function to display Team list
	 *
	 *	[teams league_id=ID template=X season=x]
	 *
	 * @param array $atts
	 * @return the content
	 */
	function showTeams( $atts )
	{
		global $leaguemanager;
		extract(shortcode_atts(array(
			'league_id' => 0,
			'template' => '',
			'season' => false
		), $atts ));

		$league = $leaguemanager->getLeague($league_id);
		if (empty($season)) {
			$season = $leaguemanager->getSeason($league);
			$season = $season['name'];
		}

		$teams = $leaguemanager->getTeams( "`league_id` = {$league_id} AND `season` = '".$season."'" );

		if ( empty($template) && $this->checkTemplate('teams-'.$league->sport) )
			$filename = 'teams-'.$league->sport;
		else
			$filename = ( !empty($template) ) ? 'teams-'.$template : 'teams';

		$out = $this->loadTemplate( $filename, array('league' => $league, 'teams' => $teams) );

		return $out;
	}


	/**
	 * Function to display Team Info Page
	 *
	 *	[team id=ID template=X]
	 *
	 * @param array $atts
	 * @return the content
	 */
	function showTeam( $atts )
	{
		global $leaguemanager;
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => '',
			'echo' => 0,
		), $atts ));

		$team = $leaguemanager->getTeam( $id );
		$league = $leaguemanager->getLeague( $team->league_id );

		// Get next match
		$next_matches = $leaguemanager->getMatches("( `home_team` = {$team->id} OR `away_team` = {$team->id} ) AND DATEDIFF(NOW(), `date`) <= 0");
		$next_match = $next_matches[0];
		if ( $next_match ) {
			if ( $next_match->home_team == $team->id ) {
				$opponent = $leaguemanager->getTeam($next_match->away_team);
				$next_match->match = $team->title . " &#8211; " . $opponent->title;
			} else {
				$opponent = $leaguemanager->getTeam($next_match->home_team);
				$next_match->match = $opponent->title  . " &#8211; " . $team->title;
			}
		}

		// Get last match
		$prev_matches = $leaguemanager->getMatches("( `home_team` = {$team->id} OR `away_team` = {$team->id} ) AND DATEDIFF(NOW(), `date`) > 0", 1, "`date` DESC");
		$prev_match = $prev_matches[0];
		if ( $prev_match ) {
			if ( $prev_match->home_team == $team->id ) {
				$opponent = $leaguemanager->getTeam($prev_match->away_team);
				$prev_match->match = $team->title . " &#8211; " . $opponent->title;
			} else {
				$opponent = $leaguemanager->getTeam($prev_match->home_team);
				$prev_match->match = $opponent->title  . " &#8211; " . $team->title;
			}
		
			$prev_match->hadOvertime = ( isset($prev_match->overtime) && $prev_match->overtime['home'] != '' && $prev_match->overtime['away'] != '' ) ? true : false;
			$prev_match->hadPenalty = ( isset($prev_match->penalty) && $prev_match->penalty['home'] != '' && $prev_match->penalty['away'] != '' ) ? true : false;

			if ( $prev_match->hadPenalty )
				$prev_match->score = sprintf("%s - %s", $prev_match->penalty['home'], $prev_match->penalty['away'])." "._c( 'o.P.|on penalty', 'leaguemanager' );
			elseif ( $prev_match->hadOvertime )
				$prev_match->score = sprintf("%s - %s", $prev_match->overtime['home'], $prev_match->overtime['away'])." "._c( 'AET|after extra time', 'leaguemanager' );
			else
				$prev_match->score = sprintf("%s - %s", $prev_match->home_points, $prev_match->away_points);
		}


		if ( empty($template) && $this->checkTemplate('team-'.$league->sport) )
			$filename = 'team-'.$league->sport;
		else
			$filename = ( !empty($template) ) ? 'team-'.$template : 'team';

		$out = $this->loadTemplate( $filename, array('league' => $league, 'team' => $team, 'next_match' => $next_match, 'prev_match' => $prev_match) );

		if ( $echo )
			echo $out;
		else
			return $out;
	}


	/**
	 * Function to display Crosstable
	 *
	 * [crosstable league_id="1" mode="popup" template="name"]
	 *
	 * - league_id is the ID of league to display
	 * - league_name: get league by name and not ID (optional)
	 * - mode set to "popup" makes the crosstable be displayed in a thickbox popup window.
	 * - template is the template used for displaying. Replace name appropriately. Templates must be named "crosstable-template.php" (optional)
	 * - season: display crosstable of given season (optional)
	 *
	 *
	 * @param array $atts
	 * @return the content
	 */
	function showCrosstable( $atts )
	{
		global $leaguemanager;
		extract(shortcode_atts(array(
			'league_id' => 0,
			'league_name' => '',
			'template' => '',
			'mode' => '',
			'season' => false
		), $atts ));
		
		$search = !empty($league_name) ? $league_name : $league_id;
		$league = $leaguemanager->getLeague( $search );	
		if (empty($season)) {
			$season = $leaguemanager->getSeason($league);
			$season = $season['name'];
		}
		$teams = $leaguemanager->getTeams( "`league_id` = '".$league->id."' AND `season` = '".$season."'" );
		
		if ( empty($template) && $this->checkTemplate('crosstable-'.$league->sport) )
			$filename = 'crosstable-'.$league->sport;
		else
			$filename = ( !empty($template) ) ? 'crosstable-'.$template : 'crosstable';

		$out = $this->loadTemplate( $filename, array('league' => $league, 'teams' => $teams, 'mode' => $mode) );
		
		return $out;
	}
	
	
	/**
	 * show Archive
	 *
	 *	[leaguearchive league_id=ID season=x template=X]
	 *
	 * - league_id: ID of league
	 * - league_name: get league by name and not ID (optional)
	 * - template: template to use
	 *
	 * @param array $atts
	 * @return the content
	 */
	function showArchive( $atts )
	{
		global $leaguemanager, $championship;
		extract(shortcode_atts(array(
			'league_id' => false,
			'league_name' => '',
			'template' => ''
		), $atts ));
		
		// get all leagues, needed for dropdown
		$leagues = $leaguemanager->getLeagues();
		$league = false; // Initialize league variable

		// Get League by Name
		if (!empty($league_name)) {
			$league = $leaguemanager->getLeague( $league_name );
			$league_id = $league->id;
		}
		
		if ( isset($_GET['season']) && !empty($_GET['season']) )
			$season = $_GET['season'];
		else
			$season = false;

		// Get League ID from shortcode or $_GET
		$league_id = ( !$league_id && isset($_GET['league_id']) && !empty($_GET['league_id']) ) ? (int)$_GET['league_id'] : false;

		// select first league
		if ( !$league_id )
			$league_id = $leagues[0]->id;

		// Get League and first Season if not set
		if ( !$league ) $league = $leaguemanager->getLeague( $league_id );
		if ( !$season ) {
			$season = reset($league->seasons);
			$season = $season['name'];
		}

		$league->season = $season;

		if ( $league->mode == 'championship' ) $championship->initialize($league->id);

		$seasons = array();
		foreach ( $leagues AS $l ) {
			foreach( (array)$l->seasons AS $l_season ) {
				if ( !in_array($l_season['name'], $seasons) && !empty($l_season['name']) )
					$seasons[] = $l_season['name'];
			}
		}
		sort($seasons);

		if ( empty($template) && $this->checkTemplate('archive-'.$league->sport) )
			$filename = 'archive-'.$league->sport;
		else
			$filename = ( !empty($template) ) ? 'archive-'.$template : 'archive';

		$out = $this->loadTemplate( $filename, array('leagues' => $leagues, 'seasons' => $seasons, 'league_id' => $league_id) );
		return $out;
	}
	
	
	/**
	 * get specific field for crosstable
	 *
	 * @param int $curr_team_id
	 * @param int $opponent_id
	 * @return string
	 */
	function getCrosstableField($curr_team_id, $opponent_id)
	{
		global $wpdb, $leaguemanager;

		//$match = $leaguemanager->getMatches("(`home_team` = $curr_team_id AND `away_team` = $opponent_id) OR (`home_team` = $opponent_id AND `away_team` = $curr_team_id)");
		$match = $leaguemanager->getMatches("`home_team` = $curr_team_id AND `away_team` = $opponent_id");
		$match = $match[0];
		
 		if ( $match ) {
			return $this->getScore($curr_team_id, $opponent_id, $match);
		} else {
			$match = $leaguemanager->getMatches("`home_team` = $opponent_id AND `away_team` = $curr_team_id");
			$match = $match[0];
			return $this->getScore($curr_team_id, $opponent_id, $match);

		}
	}
	

	/**
	 * get score for specific field of crosstable
	 *
	 * @param int $curr_team_id
	 * @param int $opponent_id
	 * @return string
	 */
	function getScore($curr_team_id, $opponent_id, $match)
	{
		global $wpdb, $leaguemanager;
	
		if ( !empty($match->penalty['home']) && !empty($match->penalty['away']) ) {
			$match->penalty = maybe_unserialize($match->penalty);
			$points = array( 'home' => $match->penalty['home'], 'away' => $match->penalty['away']);
		} elseif ( !empty($match->overtime['home']) && !empty($match->overtime['away']) ) {
			$match->overtime = maybe_unserialize($match->overtime);
			$points = array( 'home' => $match->overtime['home'], 'away' => $match->overtime['away']);
		} else {
			$points = array( 'home' => $match->home_points, 'away' => $match->away_points );
		}
		
		// unplayed match
		if ( NULL == $match->home_points && NULL == $match->away_points )
			$out = "<td class='num'>-:-</td>";
		// match at home
		elseif ( $curr_team_id == $match->home_team )
			$out = "<td class='num'>".sprintf("%s:%s", $points['home'], $points['away'])."</td>";
		// match away
		elseif ( $opponent_id == $match->home_team )
			$out = "<td class='num'>".sprintf("%s:%s", $points['away'], $points['home'])."</td>";

		return $out;
	}


	/**
	 * Load template for user display. First the current theme directory is checked for a template
	 * before defaulting to the plugin
	 *
	 * @param string $template Name of the template file (without extension)
	 * @param array $vars Array of variables name=>value available to display code (optional)
	 * @return the content
	 */
	function loadTemplate( $template, $vars = array() )
	{
		global $leaguemanager, $lmStats, $championship;
		extract($vars);

		ob_start();
		if ( file_exists( TEMPLATEPATH . "/leaguemanager/$template.php")) {
			include(TEMPLATEPATH . "/leaguemanager/$template.php");
		} elseif ( file_exists(LEAGUEMANAGER_PATH . "/templates/".$template.".php") ) {
			include(LEAGUEMANAGER_PATH . "/templates/".$template.".php");
		} else {
			parent::setMessage( sprintf(__('Could not load template %s.php', 'leaguemanager'), $template), true );
			parent::printMessage();
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	
	/**
	 * check if template exists
	 *
	 * @param string $template
	 * @return boolean
	 */
	function checkTemplate( $template )
	{
		if ( file_exists( TEMPLATEPATH . "/leaguemanager/$template.php")) {
			return true;
		} elseif ( file_exists(LEAGUEMANAGER_PATH . "/templates/".$template.".php") ) {
			return true;
		}

		return false;
	}
}

?>
