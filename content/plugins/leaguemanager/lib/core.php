<?php
/**
 * Core class for the WordPress plugin LeagueManager
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManager
{
	/**
	 * array of leagues
	 *
	 * @var array
	 */
	var $leagues = array();
	

	/**
	 * data of certain league
	 *
	 * @var array
	 */
	var $league = array();


	/**
	 * ID of current league
	 *
	 * @var int
	 */
	var $league_id;

	
	/**
	 * current season
	 *
	 * @var mixed
	 */
	var $season;


	/**
	 * error handling
	 *
	 * @var boolean
	 */
	var $error = false;
	
	
	/**
	 * message
	 *
	 * @var string
	 */
	var $message;
	
	
	/**
	 * control variable if bridge is active
	 *
	 * @var boolean
	 */
	var $bridge = false;


	/**
	 * Initializes plugin
	 *
	 * @param boolean $bridge
	 * @return void
	 */
	function __construct( $bridge = false )
	{
		$this->bridge = $bridge;
		if (isset($_GET['league_id'])) {
			$this->setLeagueID( $_GET['league_id'] );
			$this->league = $this->getLeague($this->getLeagueID());
		}

		$this->loadOptions();
	}
	function LeagueManager( $bridge = false )
	{
		$this->__construct( $bridge );
	}
	
	
	/**
	 * load options
	 *
	 * @param none
	 * @return void
	 */
	function loadOptions()
	{
		$this->options = get_option('leaguemanager');
	}
	
	
	/**
	 * get options
	 *
	 * @param none
	 * @return void
	 */
	function getOptions()
	{
		return $this->options;
	}
	
	
	/**
	 * check if bridge is active
	 *
	 * @param none
	 * @return boolean
	 */
	function hasBridge()
	{
		return $this->bridge;
	}
	
	
	/**
	 * set league id
	 *
	 * @param int $league_id
	 * @return void
	 */
	function setLeagueID( $league_id )
	{
		$this->league_id = $league_id;
	}
	
	
	/**
	 * retrieve league ID
	 *
	 * @param none
	 * @return int ID of current league
	 */
	function getLeagueID()
	{
		return $this->league_id;
	}
	

	/**
	 * get current league object
	 *
	 * @param none
	 * @return object
	 */
	function getCurrentLeague()
	{
		return $this->league;
	}
	

	/**
	 * set season
	 *
	 * @param mixed $season
	 * @return void
	 */
	function setSeason( $season )
	{
		$this->season = $season;
	}
	
	
	/**
	 * get current season
	 *
	 * @param mixed $index
	 * @return array
	 */
	function getCurrentSeason( $index = false )
	{
		if ( $index )
			return $this->season[$index];

		return $this->season;
	}


	/**
	 * get league types
	 *
	 * @param none
	 * @return array
	 */
	function getLeagueTypes()
	{
		$types = array( 'other' => __('Other', 'leaguemanager') );
		$types = apply_filters('leaguemanager_sports', $types);
		asort($types);

		return $types;
	}
	
	
	/**
	 * get supported image types from Image class
	 *
	 * @param none
	 * @return array
	 */
	function getSupportedImageTypes()
	{
		return LeagueManagerImage::getSupportedImageTypes();
	}
	
	
	/**
	 * build home only query
	 *
	 * @param int $league_id
	 * @return string MySQL search query
	 */
	function buildHomeOnlyQuery($league_id)
	{
		global $wpdb;
		
		$queries = array();
		$teams = $wpdb->get_results( "SELECT `id` FROM {$wpdb->leaguemanager_teams} WHERE `league_id` = {$league_id} AND `home` = 1" );
		if ( $teams ) {
			foreach ( $teams AS $team )
				$queries[] = "`home_team` = {$team->id} OR `away_team` = {$team->id}";
		
			$query = " AND (".implode(" OR ", $queries).")";
			
			return $query;
		}
		
		return false;
	}
	
	
	/**
	 * get months
	 *
	 * @param none
	 * @return void
	 */
	function getMonths()
	{
		$locale = get_locale();
		setlocale(LC_ALL, $locale);
		for ( $month = 1; $month <= 12; $month++ ) 
			$months[$month] = htmlentities( strftime( "%B", mktime( 0,0,0, $month, date("m"), date("Y") ) ) );
			
		return $months;
	}
	
	
	/**
	 * returns image directory
	 *
	 * @param string|false $file
	 * @return string
	 */
	function getImagePath( $file = false )
	{
		$league = $this->getCurrentLeague();
		if ( $file ) 
			return trailingslashit($_SERVER['DOCUMENT_ROOT']) . substr($file,strlen($_SERVER['HTTP_HOST'])+8, strlen($file));
		 else 
			return ABSPATH . $league->upload_dir;
	}
	
	
	/**
	 * returns url of image directory
	 *
	 * @param string|false $file image file
	 * @return string
	 */
	function getImageUrl( $file = false )
	{
		$league = $this->getCurrentLeague();
		if ( $file )
			return trailingslashit(get_option('siteurl')) . $league->upload_dir . $file;
		else
			return trailingslashit(get_option('siteurl')) . $league->upload_dir;
	}

	
	/**
	 * get Thumbnail image
	 *
	 * @param string $file
	 * @return string
	 */
	function getThumbnailUrl( $file )
	{
		if ( file_exists($this->getThumbnailPath($file)) )
			return trailingslashit(dirname($file)) . 'thumb_' . basename($file);
		else
			return trailingslashit(dirname($file)) . 'thumb.' . basename($file);
	}

	
	/**
	 * get Thumbnail path
	 *
	 * @param string $file
	 * @return string
	 */
	function getThumbnailPath( $file )
	{
		return trailingslashit($_SERVER['DOCUMENT_ROOT']) . dirname(substr($file,strlen($_SERVER['HTTP_HOST'])+8, strlen($file))) . '/thumb_' . basename($file);
	}
	
	
	/**
	 * set message
	 *
	 * @param string $message
	 * @param boolean $error triggers error message if true
	 * @return none
	 */
	function setMessage( $message, $error = false )
	{
		$type = 'success';
		if ( $error ) {
			$this->error = true;
			$type = 'error';
		}
		$this->message[$type] = $message;
	}
	
	
	/**
	 * return message
	 *
	 * @param none
	 * @return string
	 */
	function getMessage()
	{
		if ( $this->error )
			return $this->message['error'];
		else
			return $this->message['success'];
	}
	
	
	/**
	 * print formatted message
	 *
	 * @param none
	 * @return string
	 */
	function printMessage()
	{
		if ( $this->error )
			echo "<div class='error'><p>".$this->getMessage()."</p></div>";
		else
			echo "<div id='message' class='updated fade'><p><strong>".$this->getMessage()."</strong></p></div";
	}

	
	/**
	 * Set match day
	 *
	 * @param int 
	 * @return void
	 */
	function setMatchDay( $match_day )
	{
		$this->match_day = $match_day;
	}
	
	
	/**
	* retrieve match day
	 *
	 * @param none
	 * @return int
	 */
	function getMatchDay( $current = false )
	{
		global $wpdb;
		
		if ( isset($_GET['match_day']) )
			$match_day = (int)$_GET['match_day'];
		elseif ( $this->match_day )
			$match_day = $this->match_day;
		elseif ( $current && $match = $this->getMatches( "league_id = '".$this->league_id."' AND `season` = '".$this->season."' AND DATEDIFF(NOW(), `date`) <= 0", 1 ) )
			$match_day = $match[0]->match_day;
		else
			$match_day = 1;

		return $match_day;
	}
	
	
	/**
	 * get current season
	 *
	 * @param object $league
	 * @param mixed $season
	 * @return array
	 */
	function getSeason( $league, $season = false, $index = false )
	{
		if ( isset($_GET['season']) && !empty($_GET['season']) )
			$data = $league->seasons[$_GET['season']];
		elseif ( $season )
			$data = $league->seasons[$season];
		elseif ( !empty($league->seasons) )
			$data = end($league->seasons);
		else
			return false;

		if ( $index )
			return $data[$index];
		else
			return $data;
	}


	/**
	 * get leagues from database
	 *
	 * @param int $league_id (default: false)
	 * @param string $search
	 * @return array
	 */
	function getLeagues( $search = '' )
	{
		global $wpdb;
		
		$leagues = $wpdb->get_results( "SELECT `title`, `id`, `settings`, `seasons` FROM {$wpdb->leaguemanager} ORDER BY id ASC" );

		$i = 0;
		foreach ( $leagues AS $league ) {
			$leagues[$i]->seasons = $league->seasons = maybe_unserialize($league->seasons);
			$league->settings = maybe_unserialize($league->settings);

			$leagues[$i] = (object)array_merge((array)$league,(array)$league->settings);
			unset($leagues[$i]->settings, $league->settings);

			$this->leagues[$league->id] = $league;
			$i++;
		}
		return $leagues;
	}
	
	
	/**
	 * get league
	 *
	 * @param mixed $league_id either ID of League or title
	 * @return league object
	 */
	function getLeague( $league_id )
	{
		global $wpdb;
		
		$league = $wpdb->get_results( "SELECT `title`, `id`, `seasons`, `settings` FROM {$wpdb->leaguemanager} WHERE `id` = '".$league_id."' OR `title` = '".$league_id."'" );
		$league = $league[0];
		$league->seasons = maybe_unserialize($league->seasons);
		$league->settings = (array)maybe_unserialize($league->settings);

		$this->league_id = $league->id;
		$league->hasBridge = $this->hasBridge();

		$league = (object)array_merge((array)$league,(array)$league->settings);
		unset($league->settings);

		$this->league = $league;
		return $league;
	}
	
	
	/**
	 * get teams from database
	 *
	 * @param string $search search string for WHERE clause.
	 * @param string $output OBJECT | ARRAY
	 * @return array database results
	 */
	function getTeams( $search, $orderby = false, $output = 'OBJECT' )
	{
		global $wpdb;
		
		if ( !empty($search) ) $search = " WHERE $search";
		if ( !$orderby ) $orderby = "`rank` ASC, `id` ASC";

		$teamlist = $wpdb->get_results( "SELECT `title`, `website`, `coach`, `stadium`, `logo`, `home`, `group`, `roster`, `points_plus`, `points_minus`, `points2_plus`, `points2_minus`, `add_points`, `done_matches`, `won_matches`, `draw_matches`, `lost_matches`, `diff`, `league_id`, `id`, `season`, `rank`, `status`, `custom` FROM {$wpdb->leaguemanager_teams} $search ORDER BY $orderby" );
		$teams = array(); $i = 0;
		foreach ( $teamlist AS $team ) {
			$team->custom = maybe_unserialize($team->custom);
			if ( 'ARRAY' == $output ) {
				$teams[$team->id]['title'] = htmlspecialchars(stripslashes($team->title), ENT_QUOTES);
				$teams[$team->id]['rank'] = $team->rank;
				$teams[$team->id]['status'] = $team->status;
				$teams[$team->id]['season'] = $team->season;
				$teams[$team->id]['website'] = $team->website;
				$teams[$team->id]['coach'] = $team->coach;
				$teams[$team->id]['stadium'] = $team->stadium;
				$teams[$team->id]['logo'] = $team->logo;
				$teams[$team->id]['home'] = $team->home;
				$teams[$team->id]['group'] = $team->group;
				$teams[$team->id]['roster'] = maybe_unserialize($team->roster);
				if ( $this->hasBridge() ) {
					global $lmBridge;
					$teams[$team->id]['teamRoster'] = $lmBridge->getTeamRoster(maybe_unserialize($team->roster));
				}
				$teams[$team->id]['points'] = array( 'plus' => $team->points_plus, 'minus' => $team->points_minus );
				$teams[$team->id]['points2'] = array( 'plus' => $team->points2_plus, 'minus' => $team->points2_minus );
				$teams[$team->id]['add_points'] = $team->add_points;
				foreach ( (array)$team->custom AS $key => $value )
					$teams[$team->id][$key] = $value;
			} else {
				$teamlist[$i]->roster = maybe_unserialize($team->roster);
				if ( $this->hasBridge() ) {
					global $lmBridge;
					$teamlist[$i]->teamRoster = $lmBridge->getTeamRoster(maybe_unserialize($team->roster));
				}
				$teamlist[$i]->title = htmlspecialchars(stripslashes($team->title), ENT_QUOTES);
				$teamlist[$i] = (object)array_merge((array)$team, (array)$team->custom);
			}

			unset($teamlist[$i]->custom, $team->custom);
			$i++;
		}

		if ( 'ARRAY' == $output )
			return $teams;

		return $teamlist;
	}
	
	
	/**
	 * get single team
	 *
	 * @param int $team_id
	 * @return object
	 */
	function getTeam( $team_id )
	{
		global $wpdb;

		$team = $wpdb->get_results( "SELECT `title`, `website`, `coach`, `stadium`, `logo`, `home`, `group`, `roster`, `points_plus`, `points_minus`, `points2_plus`, `points2_minus`, `add_points`, `done_matches`, `won_matches`, `draw_matches`, `lost_matches`, `diff`, `league_id`, `id`, `season`, `rank`, `status`, `custom` FROM {$wpdb->leaguemanager_teams} WHERE `id` = '".$team_id."' ORDER BY `rank` ASC, `id` ASC" );
		$team = $team[0];

		$team->title = htmlspecialchars(stripslashes($team->title), ENT_QUOTES);
		$team->custom = maybe_unserialize($team->custom);
		$team->roster = maybe_unserialize($team->roster);
		if ( $this->hasBridge() ) {
			global $lmBridge;
			$team->teamRoster = $lmBridge->getTeamRoster($team->roster);
		}

		$team = (object)array_merge((array)$team,(array)$team->custom);
		unset($team->custom);
		
		return $team;
	}
	
	
	/**
	 * get number of seasons
	 *
	 * @param array $seasons
	 * @return int
	 */
	function getNumSeasons( $seasons )
	{
		if (empty($seasons))
			return 0;
		else
			return count($seasons);
	}


	/**
	 * gets number of teams for specific league
	 *
	 * @param int $league_id
	 * @return int
	 */
	function getNumTeams( $league_id )
	{
		global $wpdb;
	
		$num_teams = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->leaguemanager_teams} WHERE `league_id` = '".$league_id."'" );
		return $num_teams;
	}
	
	
	/**
	 * gets number of matches
	 *
	 * @param string $search
	 * @return int
	 */
	function getNumMatches( $league_id )
	{
		global $wpdb;
	
		$num_matches = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->leaguemanager_matches} WHERE `league_id` = '".$league_id."'" );
		return $num_matches;
	}
	
		
	/**
	 * rank teams
	 *
	 * The Team Ranking can be altered by sport specific rules via the hook <em>rank_teams_`sport_type`</em>
	 * `sport_type` needs to be the key of current sport type. Below is an example how it could be used
	 *
	 * add_filter('rank_teams_soccer', 'soccer_ranking');
	 *
	 * function soccer_ranking( $teams ) {
	 *	// do some stuff
	 *	return $teams
	 * }
	 *
	 *
	 * @param int $league_id
	 * @param mixed $season
	 * @param boolean $update
	 * @return array $teams ordered
	 */
	function rankTeams( $league_id, $season = false, $update = true )
	{
		global $wpdb;
		$league = $this->getLeague( $league_id );
                                    
		if ( !$season )
			$season = $this->getSeason($league);

		$season = is_array($season) ? $season['name'] : $season;

		// rank Teams in groups
		$groups = !empty($league->groups) ? explode(";", $league->groups) : array( '' );

		foreach ( $groups AS $group ) {
			$search = "`league_id` = '".$league_id."' AND `season` = '".$season."'";
			if ( !empty($group) ) $search .= " AND `group` = '".$group."'";

			$teams = array();
			foreach ( $this->getTeams( $search ) AS $team ) {
				$team->diff = ( $team->diff > 0 ) ? '+'.$team->diff : $team->diff;
				$team->points = array( 'plus' => $team->points_plus, 'minus' => $team->points_minus );
				$team->points2 = array( 'plus' => $team->points2_plus, 'minus' => $team->points2_minus );
				$team->winPercent = ($team->done_matches > 0) ? ($team->won_matches/$team->done_matches) * 100 : 0;

				$teams[] = $team;
			}
		
			if ( !empty($teams) && $league->team_ranking == 'auto' ) {
				if ( has_filter( 'rank_teams_'.$league->sport ) ) {
					$teams = apply_filters( 'rank_teams_'.$league->sport, $teams );
				} else {
					foreach ( $teams AS $key => $row ) {
						$points[$key] = $row->points['plus'] + $row->add_points;
						$done[$key] = $row->done_matches;
					}
			
					array_multisort($points, SORT_DESC, $done, SORT_ASC, $teams);
				}
		
				/*
				* Update Team Rank and status
				*/
				if ( $update ) {
					$rank = $incr = 1;
					$was_tie = false;
					foreach ( $teams AS $key => $team ) {
						$old = $this->getTeam( $team->id );
						$oldRank = $old->rank;

						if ( $oldRank != 0 ) {
							if ( $rank == $oldRank )
								$status = '&#8226;';
							elseif ( $rank < $oldRank )
								$status = '&#8593;';
							else
								$status = '&#8595;';
						} else {
							$status = '&#8226;';
						}
	
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `rank` = '%d', `status` = '%s' WHERE `id` = '%d'", $rank, $status, $team->id ) );
	

						if ( isset($teams[$key+1]) ) {
							if ( $this->isTie($team, $teams[$key+1]) ) {
								$incr++;
								$was_tie = true;
							} else {
								$rank += $incr;

								if ( $was_tie ) {
									$incr = 1;
									$was_tie = false;
								}
							}
						}
					}
				}
			}
		}

		return true;
	}
	

	/**
	 * determine if two teams are tied
	 *
	 * @param object $team
	 * @param object $team2
	 * @return boolean
	 */
	function isTie( $team, $team2 )
	{
		if ( $team->points['plus'] == $team2->points['plus'] && $team->diff == $team2->diff && $team->points2['plus'] == $team2->points2['plus'] )
			return true;

		return false;
	}

	
	/**
	 * gets matches from database
	 * 
	 * @param string $search (optional)
	 * @param int $limit (optional)
	 * @param string $order (optional)
	 * @param string $output (optional)
	 * @return array
	 */
	function getMatches( $search = false, $limit = false, $order = false, $output = 'OBJECT' )
	{
	 	global $wpdb;
	
		if ( !$order ) $order = "`date` ASC";

		$sql = "SELECT `group`, `home_team`, `away_team`, DATE_FORMAT(`date`, '%Y-%m-%d %H:%i') AS date, DATE_FORMAT(`date`, '%e') AS day, DATE_FORMAT(`date`, '%c') AS month, DATE_FORMAT(`date`, '%Y') AS year, DATE_FORMAT(`date`, '%H') AS `hour`, DATE_FORMAT(`date`, '%i') AS `minutes`, `match_day`, `location`, `league_id`, `home_points`, `away_points`, `winner_id`, `loser_id`, `post_id`, `season`, `id`, `custom` FROM {$wpdb->leaguemanager_matches}";
		if ( $search ) $sql .= " WHERE $search";
		$sql .= " ORDER BY $order";
		if ( $limit ) $sql .= " LIMIT 0,".$limit."";
		
		$matches = $wpdb->get_results( $sql, $output );

		$i = 0;
		foreach ( $matches AS $match ) {
			$matches[$i]->custom = $match->custom = maybe_unserialize($match->custom);
			$matches[$i]->custom = $match->custom = stripslashes_deep($match->custom);
			$matches[$i] = (object)array_merge((array)$match, (array)$match->custom);
		//	unset($matches[$i]->custom);

			$i++;
		}
		return $matches;
	}
	
	
	/**
	 * get single match
	 *
	 * @param int $match_id
	 * @return object
	 */
	function getMatch( $match_id )
	{
		global $wpdb;

		$match = $wpdb->get_results( "SELECT `group`, `home_team`, `away_team`, DATE_FORMAT(`date`, '%Y-%m-%d %H:%i') AS date, DATE_FORMAT(`date`, '%e') AS day, DATE_FORMAT(`date`, '%c') AS month, DATE_FORMAT(`date`, '%Y') AS year, DATE_FORMAT(`date`, '%H') AS `hour`, DATE_FORMAT(`date`, '%i') AS `minutes`, `match_day`, `location`, `league_id`, `home_points`, `away_points`, `winner_id`, `loser_id`, `post_id`, `season`, `id`, `custom` FROM {$wpdb->leaguemanager_matches} WHERE `id` = {$match_id}" );
		$match = $match[0];

		$match->custom = maybe_unserialize($match->custom);
		$match->custom = stripslashes_deep($match->custom);
		$match = (object)array_merge((array)$match, (array)$match->custom);
		//unset($match->custom);

		return $match;
	}
	
	
	/**
	 * test if it's a match of home team
	 *
	 * @param int $home_team
	 * @param int $away_team
	 * @param array $teams
	 * @return boolean
	 */
	function isHomeTeamMatch( $home_team, $away_team, $teams )
	{
		if ( 1 == $teams[$home_team]['home'] )
			return true;
		elseif ( 1 == $teams[$away_team]['home'] )
			return true;
		else
			return false;
	}


	/**
	 * get card name
	 *
	 * @param string $type
	 * @return nice card name
	 */
	function getCards( $type = false )
	{
		$cards = array( 'red' => __( 'Red', 'leaguemanager' ), 'yellow' => __( 'Yellow', 'leaguemanager' ), 'yellow-red' => __( 'Yellow/Red', 'leaguemanager' ) );
		$cards = apply_filters( 'leaguemanager_cards', $cards );

		if ( $type )
			return $cards[$type];
		else
			return $cards;
	}
}
?>
