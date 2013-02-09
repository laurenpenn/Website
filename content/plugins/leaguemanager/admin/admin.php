<?php
/**
* Admin class holding all adminstrative functions for the WordPress plugin LeagueManager
* 
* @author 	Kolja Schleich
* @package	LeagueManager
* @copyright 	Copyright 2009
*/

class LeagueManagerAdminPanel extends LeagueManager
{
	/**
	 * load admin area
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		require_once( ABSPATH . 'wp-admin/includes/template.php' );
		
		add_action('admin_print_scripts', array(&$this, 'loadScripts') );
		add_action('admin_print_styles', array(&$this, 'loadStyles') );
		
		add_action( 'admin_menu', array(&$this, 'menu') );
		
		// Add meta box to post screen
		add_meta_box( 'leaguemanager', __('Match-Report','leaguemanager'), array(&$this, 'addMetaBox'), 'post' );
		add_action( 'publish_post', array(&$this, 'editMatchReport') );
		add_action( 'edit_post', array(&$this, 'editMatchReport') );
	
		add_action('wp_ajax_leaguemanager_get_season_dropdown', array(&$this, 'getSeasonDropdown'));
		add_action('wp_ajax_leaguemanager_get_match_dropdown', array(&$this, 'getMatchDropdown'));
	}
	function LeagueManagerAdminPanel()
	{
		$this->__construct();
	}
	
	
	/**
	 * adds menu to the admin interface
	 *
	 * @param none
	 */
	function menu()
	{
		$plugin = 'leaguemanager/leaguemanager.php';

		if ( function_exists('add_object_page') )
			$page = add_object_page( __('League','leaguemanager'), __('League','leaguemanager'), 'leagues', LEAGUEMANAGER_PATH, array(&$this, 'display'), LEAGUEMANAGER_URL.'/admin/icons/cup.png' );
		else
			$page = add_menu_page( __('League','leaguemanager'), __('League','leaguemanager'), 'leagues', LEAGUEMANAGER_PATH, array(&$this, 'display'), LEAGUEMANAGER_URL.'/admin/icons/cup.png' );

		add_submenu_page(LEAGUEMANAGER_PATH, __('Leaguemanager', 'leaguemanager'), __('Overview','leaguemanager'),'leagues', LEAGUEMANAGER_PATH, array(&$this, 'display'));
		add_submenu_page(LEAGUEMANAGER_PATH, __('Settings', 'leaguemanager'), __('Settings','leaguemanager'),'manage_leagues', 'leaguemanager-settings', array( $this, 'display' ));
		add_submenu_page(LEAGUEMANAGER_PATH, __('Import'), __('Import'),'manage_leagues', 'leaguemanager-import', array( $this, 'display' ));
		add_submenu_page(LEAGUEMANAGER_PATH, __('Export'), __('Export'),'manage_leagues', 'leaguemanager-export', array( $this, 'display' ));
		add_submenu_page(LEAGUEMANAGER_PATH, __('Documentation', 'leaguemanager'), __('Documentation','leaguemanager'),'leagues', 'leaguemanager-doc', array( $this, 'display' ));
		
		add_action("admin_print_scripts-$page", array(&$this, 'loadScriptsPage') );
		add_filter( 'plugin_action_links_' . $plugin, array( &$this, 'pluginActions' ) );
	}
	
	
	/**
	 * buid league menu
	 *
	 * @param none
	 * @return array
	 */
	function getMenu()
	{
		global $leaguemanager;

		$league = $leaguemanager->getCurrentLeague();

		$menu = array();
		$menu['settings'] = array( 'title' => __('Preferences', 'leaguemanager'), 'file' => dirname(__FILE__) . '/settings.php', 'show' => true );
		$menu['seasons'] = array( 'title' => __('Seasons', 'leaguemanager'), 'file' => dirname(__FILE__) . '/seasons.php', 'show' => true );
		$menu['team'] = array( 'title' => __('Add Team', 'leaguemanager'), 'file' => dirname(__FILE__) . '/team.php', 'show' => true );
		$menu['match'] = array( 'title' => __('Add Matches', 'leaguemanager'), 'file' => dirname(__FILE__) . '/match.php', 'show' => true );

		$menu = apply_filters('league_menu_'.$league->sport, $menu, $leaguemanager->getLeagueID(), $leaguemanager->getCurrentSeason('name'));
		$menu = apply_filters('league_menu_'.$league->mode, $menu, $leaguemanager->getLeagueID(), $leaguemanager->getCurrentSeason('name'));

		return $menu;
	}


	/**
	 * showMenu() - show admin menu
	 *
	 * @param none
	 */
	function display()
	{
		global $leaguemanager;
		
		$options = get_option('leaguemanager');

		// Update Plugin Version
		if ( $options['version'] != LEAGUEMANAGER_VERSION ) {
			$options['version'] = LEAGUEMANAGER_VERSION;
			update_option('leaguemanager', $options);
		}

		// Update database
		if( $options['dbversion'] != LEAGUEMANAGER_DBVERSION ) {
			include_once ( dirname (__FILE__) . '/upgrade.php' );
			leaguemanager_upgrade_page();
			return;
		}
		// Do some upgrade
		/*if ( isset($_GET['update']) ) {
			include_once ( dirname (__FILE__) . '/upgrade.php' );
			leaguemanager_upgrade();
			return;
		}*/

		if ( $leaguemanager->hasBridge() ) global $lmBridge;

		switch ($_GET['page']) {
			case 'leaguemanager-doc':
				include_once( dirname(__FILE__) . '/documentation.php' );
				break;
			case 'leaguemanager-settings':
				$this->displayOptionsPage();
				break;
			case 'leaguemanager-import':
				include_once( dirname(__FILE__) . '/import.php' );
				break;
			case 'leaguemanager-export':
				include_once( dirname(__FILE__) . '/export.php' );
				break;
			case 'leaguemanager':
			default:
				if ( isset($_GET['subpage']) ) {
					$menu = $this->getMenu();
					$page = $_GET['subpage'];
					if ( array_key_exists( $page, $menu ) ) {
						if ( isset($menu[$page]['callback']) && is_callable($menu[$page]['callback']) )
							call_user_func($menu[$page]['callback']);
						else
							include_once( $menu[$page]['file'] );
					} else {
						include_once( dirname(__FILE__) . '/show-league.php' );
					}
				} else {
					include_once( dirname(__FILE__) . '/index.php' );
				}

				break;
		}
	}
	
	
	/**
	 * display link to settings page in plugin table
	 *
	 * @param array $links array of action links
	 * @return void
	 */
	function pluginActions( $links )
	{
		$settings_link = '<a href="admin.php?page=leaguemanager-settings">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
	
		return $links;
	}
	
	
	/**
	 * load scripts
	 *
	 * @param none
	 * @return void
	 */
	function loadScriptsPage()
	{
		wp_register_script( 'leaguemanager', LEAGUEMANAGER_URL.'/admin/js/functions.js', array('colorpicker', 'thickbox', 'jquery' ), LEAGUEMANAGER_VERSION );
		wp_enqueue_script('leaguemanager');
	}
	function loadScripts()
	{
		wp_register_script( 'leaguemanager_ajax', LEAGUEMANAGER_URL.'/admin/js/ajax.js', array('sack'), LEAGUEMANAGER_VERSION );
		wp_enqueue_script('leaguemanager_ajax');
		
		?>
		<script type='text/javascript'>
		//<![CDATA[
		LeagueManagerAjaxL10n = {
			requestUrl: "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", manualPointRuleDescription: "<?php _e( 'Order: win, win overtime, tie, loss, loss overtime', 'leaguemanager' ) ?>", pluginPath: "<?php echo LEAGUEMANAGER_PATH; ?>", pluginUrl: "<?php echo LEAGUEMANAGER_URL; ?>", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", pleaseWait: "<?php _e("Please wait..."); ?>", Delete: "<?php _e('Delete', 'leaguemanager') ?>", Yellow: "<?php _e( 'Yellow', 'leaguemanager') ?>", Red: "<?php _e( 'Red', 'leaguemanager') ?>", Yellow_Red: "<?php _e('Yellow/Red', 'leaguemanager') ?>", Insert: "<?php _e( 'Insert', 'leaguemanager' ) ?>", InsertPlayer: "<?php _e( 'Insert Player', 'leaguemanager' ) ?>", AddPlayerFromRoster: "<?php _e( 'Add Player from Team Roster', 'leaguemanager' ) ?>"
		}
		//]]>
		</script>
		<?php
	}
	
	
	/**
	 * load styles
	 *
	 * @param none
	 * @return void
	 */
	function loadStyles()
	{
		wp_enqueue_style('thickbox');
		wp_enqueue_style('leaguemanager', LEAGUEMANAGER_URL . "/style.css", false, '1.0', 'screen');
	}
	

	/**
	 * set message by calling parent function
	 *
	 * @param string $message
	 * @param boolean $error (optional)
	 * @return void
	 */
	function setMessage( $message, $error = false )
	{
		parent::setMessage( $message, $error );
	}
	
	
	/**
	 * print message calls parent
	 *
	 * @param none
	 * @return string
	 */
	function printMessage()
	{
		parent::printMessage();
	}
	
	
	/**
	 * get available league modes
	 *
	 * @param none
	 * @return array
	 */
	function getModes()
	{
		$modes = array( 'default' => __('Default', 'leaguemanager') );
		$modes = apply_filters( 'leaguemanager_modes', $modes);
		return $modes;
	}
	
	
	/**
	 * savePointsManually() - update points manually
	 *
	 * @param array $teams
	 * @param array $points_plus
	 * @param array $points_minus
	 * @param array $num_done_matches
	 * @param array $num_won_matches
	 * @param array $num_draw_matches
	 * @param array $num_lost_matches
	 * @param array $add_points
	 * @return none
	 */
	function saveStandingsManually( $teams, $points_plus, $points_minus,  $num_done_matches, $num_won_matches, $num_draw_matches, $num_lost_matches, $add_points, $custom )
	{
		global $wpdb, $leaguemanager;
		$league = $leaguemanager->getCurrentLeague();

		while ( list($id) = each($teams) ) {
			$points2_plus = isset($custom[$id]['points2']) ? $custom[$id]['points2']['plus'] : 0;
			$points2_minus = isset($custom[$id]['points2']) ? $custom[$id]['points2']['minus'] : 0;
			$diff = $points2_plus - $points2_minus;

			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `points_plus` = '%d', `points_minus` = '%d', `points2_plus` = '%d', `points2_minus` = '%d', `done_matches` = '%d', `won_matches` = '%d', `draw_matches` = '%d', `lost_matches` = '%d', `diff` = '%d', `add_points` = '%d' WHERE `id` = '%d'", $points_plus[$id], $points_minus[$id], $points2_plus, $points2_minus, $num_done_matches[$id], $num_won_matches[$id], $num_draw_matches[$id], $num_lost_matches[$id], $diff[$id], $add_points[$id], $id ) );
		}
		
		// Update Teams Rank and Status
		$leaguemanager->rankTeams( $league->id );
	}
	
	
	/**
	 * get array of supported point rules
	 *
	 * @param none
	 * @return array
	 */
	function getPointRules()
	{
		$rules = array( 'manual' => __( 'Update Standings Manually', 'leaguemanager' ), 'one' => __( 'One-Point-Rule', 'leaguemanager' ), 'two' => __('Two-Point-Rule','leaguemanager'), 'three' => __('Three-Point-Rule', 'leaguemanager'), 'score' => __( 'Score', 'leaguemanager'), 'user' => __('User defined', 'leaguemanager') );

		$rules = apply_filters( 'leaguemanager_point_rules_list', $rules );
		asort($rules);

		return $rules;
	}
	
	
	/**
	 * get point rule depending on selection.
	 * For details on point rules see http://de.wikipedia.org/wiki/Drei-Punkte-Regel (German)
	 *
	 * @param int $rule
	 * @return array of points
	 */
	function getPointRule( $rule )
	{
		$rule = maybe_unserialize($rule);
		
		// Manual point rule
		if ( is_array($rule) ) {
			return $rule;
		} else {
			$point_rules = array();
			// One point rule
			$point_rules['one'] = array( 'forwin' => 1, 'fordraw' => 0, 'forloss' => 0 );
			// Two point rule
			$point_rules['two'] = array( 'forwin' => 2, 'fordraw' => 1, 'forloss' => 0 );
			// Three-point rule
			$point_rules['three'] = array( 'forwin' => 3, 'fordraw' => 1, 'forloss' => 0 );
			// Score. One point for each scored goal
			$point_rules['score'] = 'score';

			$point_rules = apply_filters( 'leaguemanager_point_rules', $point_rules );

			return $point_rules[$rule];
		}
	}
	
	
	/**
	 * get available point formats
	 *
	 * @param none
	 * @return array
	 */
	function getPointFormats()
	{
		$point_formats = array( '%d:%d' => '%d:%d', '%d - %d' => '%d - %d', '%d' => '%d', '%.1f:%.1f' => '%f:%f', '%.1f - %.1f' => '%f - %f', '%.1f' => '%f' );
		$point_formats = apply_filters( 'leaguemanager_point_formats', $point_formats );
		return $point_formats;
	}
	
	
	/**
	 * get number of matches for team
	 *
	 * @param int $team_id
	 * @return int
	 */
	function getNumDoneMatches( $team_id )
	{
		global $wpdb, $leaguemanager;
		$league = $leaguemanager->getCurrentLeague();

		$num_matches = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->leaguemanager_matches} WHERE (`home_team` = '".$team_id."' OR `away_team` = '".$team_id."') AND `home_points` IS NOT NULL AND `away_points` IS NOT NULL" );
		$num_matches = apply_filters( 'leaguemanager_done_matches_'.$league->sport, $num_matches, $team_id );
		return $num_matches;
	}
	
	
	/**
	 * get number of won matches 
	 *
	 * @param int $team_id
	 * @return int
	 */
	function getNumWonMatches( $team_id )
	{
		global $wpdb, $leaguemanager;
		$league = $leaguemanager->getCurrentLeague();

		$num_win = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->leaguemanager_matches} WHERE `winner_id` = '".$team_id."'" );
		$num_win = apply_filters( 'leaguemanager_won_matches_'.$league->sport, $num_win, $team_id );
		return $num_win;
	}
	
	
	/**
	 * get number of draw matches
	 *
	 * @param int $team_id
	 * @return int
	 */
	function getNumDrawMatches( $team_id )
	{
		global $wpdb, $leaguemanager;
		$league = $leaguemanager->getCurrentLeague();

		$num_draw = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->leaguemanager_matches} WHERE `winner_id` = -1 AND `loser_id` = -1 AND (`home_team` = '".$team_id."' OR `away_team` = '".$team_id."')" );
		$num_draw = apply_filters( 'leaguemanager_tie_matches_'.$league->sport, $num_draw, $team_id );
		return $num_draw;
	}
	
	
	/**
	 * get number of lost matches
	 *
	 * @param int $team_id
	 * @return int
	 */
	function getNumLostMatches( $team_id )
	{
		global $wpdb, $leaguemanager;
		$league = $leaguemanager->getCurrentLeague();

		$num_lost = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->leaguemanager_matches} WHERE `loser_id` = '".$team_id."'" );
		$num_lost = apply_filters( 'leaguemanager_lost_matches_'.$league->sport, $num_lost, $team_id );
		return $num_lost;
	}
	
	
	/**
	 * update points for given team
	 *
	 * @param int $team_id
	 * @return none
	 */
	function saveStandings( $team_id )
	{
		global $wpdb, $leaguemanager;
		
		$this->league = $league = parent::getLeague($this->league_id);
		if ( $league->point_rule != 'manual' ) {
			$this->num_done = $this->getNumDoneMatches($team_id);
			$this->num_won = $this->getNumWonMatches($team_id);
			$this->num_draw = $this->getNumDrawMatches($team_id);
			$this->num_lost = $this->getNumLostMatches($team_id);

			$points['plus'] = $this->calculatePoints( $team_id, 'plus' );
			$points['minus'] = $this->calculatePoints( $team_id, 'minus' );
				
			$points2 = array( 'plus' => 0, 'minus' => 0 );
			$points2 = apply_filters( 'team_points2_'.$league->sport, $team_id );

			$diff = $points2['plus'] - $points2['minus'];
			
			$wpdb->query ( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `points_plus` = '%s', `points_minus` = '%s', `points2_plus` = '%d', `points2_minus` = '%d', `done_matches` = '%d', `won_matches` = '%d', `draw_matches` = '%d', `lost_matches` = '%d', `diff` = '%d' WHERE `id` = '%d'", $points['plus'], $points['minus'], $points2['plus'], $points2['minus'], $this->num_done, $this->num_won, $this->num_draw, $this->num_lost, $diff, $team_id ) );

			do_action( 'leaguemanager_save_standings_'.$league->sport, $team_id );
		}
	}
	
	
	/**
	 * calculate points for given team depending on point rule
	 *
	 * @param int $team_id
	 * @param string $option
	 * @return int
	 */
	function calculatePoints( $team_id, $option )
	{
		global $wpdb;
		
		$league = $this->league;
			
		$rule = $this->getPointRule( $league->point_rule );
		$points = array( 'plus' => 0, 'minus' => 0 );
		
		if ( 'score' == $rule ) {
			$home = $this->getMatches( "`home_team` = '".$team_id."'" );
			foreach ( $home AS $match ) {
				$points['plus'] += $match->home_points;
				$points['minus'] += $match->away_points;
			}

			$away = $this->getMatches("`away_team` = '".$team_id."'" );
			foreach ( $away AS $match ) {
				$points['plus'] += $match->away_points;
				$points['minus'] += $match->home_points;
			}
		} else {
			extract( $rule );

			$points['plus'] = $this->num_won * $forwin + $this->num_draw * $fordraw + $this->num_lost * $forloss;
			$points['minus'] = $this->num_draw * $fordraw + $this->num_lost * $forwin + $this->num_won * $forloss;
		}
		
		$points = apply_filters( 'team_points_'.$league->sport, $points, $team_id, $rule );
		return $points[$option];
	}
	
	
	/**
	 * add new League
	 *
	 * @param string $title
	 * @return void
	 */
	function addLeague( $title )
	{
		global $wpdb;
		
		$settings = array( 'upload_dir' => 'wp-content/uploads/leaguemanager', 'standings' => array('pld' => 1, 'won' => 1, 'tie' => 1, 'lost' => 1) );
		$wpdb->query( $wpdb->prepare ( "INSERT INTO {$wpdb->leaguemanager} (title, settings, seasons) VALUES ('%s', '%s', '%s')", $title, maybe_serialize($settings), '') );
		parent::setMessage( __('League added', 'leaguemanager') );
	}


	/**
	 * edit League
	 *
	 * @param string $title
	 * @param array $settings
	 * @param int $league_id
	 * @return void
	 */
	function editLeague( $title, $settings, $league_id )
	{
		global $wpdb;

		$wpdb->query( $wpdb->prepare ( "UPDATE {$wpdb->leaguemanager} SET `title` = '%s', `settings` = '%s' WHERE `id` = '%d'", $title, maybe_serialize($settings), $league_id ) );
		parent::setMessage( __('Settings saved', 'leaguemanager') );
	}


	/**
	 * delete League
	 *
	 * @param int $league_id
	 * @return void
	 */
	function delLeague( $league_id )
	{
		global $wpdb;
		
		// Delete Teams and with it Matches
		foreach ( parent::getTeams( "league_id = '".$league_id."'" ) AS $team ) {
			$this->delTeam( $team->id );
		}

		// remove statistics
		$wpdb->query( "DELETE FROM {$wpdb->leaguemanager_stats} WHERE `league_id` = {$league_id}" );

		$wpdb->query( "DELETE FROM {$wpdb->leaguemanager} WHERE `id` = {$league_id}" );
	}

	
	/**
	 * add new season to league
	 *
	 * @param string $season
	 * @param int $num_match_days
	 * @param int $league_id
	 * @param boolean $add_teams
	 * @return void
	 */
	function saveSeason( $season, $num_match_days, $add_teams = false, $key = false )
	{
		global $leaguemanager, $wpdb;

		$league = $leaguemanager->getCurrentLeague();
		//$league = $leaguemanager->getLeague($league_id);
		if ( $add_teams && !empty($league->seasons) && !$key ) {
			$last_season = end($league->seasons);
			if ( !empty($last_season) ) {
				if ( $teams = $leaguemanager->getTeams("`league_id` = ".$league->id." AND `season` = '".$last_season['name']."'") ) {
					foreach ( $teams AS $team ) {
						$this->addTeamFromDB( $league->id, $season, $team->id, false );
					}
				}
			}
		}
		
		if ( $key ) {
			if ( $teams = $leaguemanager->getTeams( "`season` = '".$key."' AND `league_id` = ".$league->id ) ) {
				foreach ( $teams AS $team ) {
					$wpdb->query( "UPDATE {$wpdb->leaguemanager_teams} SET `season` = '".$season."' WHERE `id` = {$team->id}" );
				}
			}
			if ( $matches = $leaguemanager->getMatches( "`season` = '".$key."' AND `league_id` = ".$league->id ) ) {
				foreach ( $matches AS $match ) {
					$wpdb->query( "UPDATE {$wpdb->leaguemanager_matches} SET `season` = '".$season."' WHERE `id` = {$match->id}" );
				}
			}
		}
		
		// unset broken season, due to delete bug
		if ( $key && $key != $season )
			unset($league->seasons[$key]);

		//array_push($league->seasons, array( 'name' => $season, 'num_match_days' => $num_match_days ));
		$league->seasons[$season] = array( 'name' => $season, 'num_match_days' => $num_match_days );
		ksort($league->seasons);
		$this->saveSeasons($league->seasons, $league->id);

		parent::setMessage( sprintf(__('Season <strong>%s</strong> added','leaguemanager'), $season ) );
		parent::printMessage();
	}

	
	/**
	 * delete season of league
	 *
	 * @param array $seasons
	 * @param int $league_id
	 * @return array of new options
	 */
	function delSeasons( $seasons, $league_id )
	{
		global $leaguemanager;
		$league = $leaguemanager->getCurrentLeague();

		if ( !empty($seasons) ) {
			foreach ( $seasons AS $key ) {
				$season = $league->seasons[$key];

				// Delete teams and matches if there are any
				if ( $teams = $leaguemanager->getTeams("`league_id` = ".$league->id." AND `season` = ".$season['name']) ) {
					foreach ( $teams AS $team )
						$this->delTeam($team->id);
				}
		
				unset($league->seasons[$key]);
			}
			$this->saveSeasons($league->seasons, $league->id);
		}
	}
	
	
	/**
	 * save seasons array to database
	 *
	 * @param array $seasons
	 * @param int $league_id
	 */
	function saveSeasons($seasons, $league_id)
	{
		global $wpdb;
		$wpdb->query( "UPDATE {$wpdb->leaguemanager} SET `seasons` = '".maybe_serialize($seasons)."' WHERE `id` = {$league_id}" );
	}


	/**
	 * add new team
	 *
	 * @param int $league_id
	 * @param mixed $season
	 * @param string $title
	 * @param string $website
	 * @param string $coach
	 * @param string $stadium
	 * @param int $home 1 | 0
	 * @param mixed $group
	 * @param int|array $roster
	 * @param array $custom
	 * @param string $logo (optional)
	 * @param boolean $message (optional)
	 * @return void
	 */
	function addTeam( $league_id, $season, $title, $website, $coach, $stadium, $home, $group, $roster, $custom, $logo = '', $message = true )
	{
		global $wpdb, $leaguemanager;

		$sql = "INSERT INTO {$wpdb->leaguemanager_teams} (`title`, `website`, `coach`, `stadium`, `home`, `group`, `roster`, `season`, `custom`, `logo`, `league_id`) VALUES ('%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d')";
		$wpdb->query( $wpdb->prepare ( $sql, $title, $website, $coach, $stadium, $home, $group, maybe_serialize($roster), $season, maybe_serialize($custom), $logo, $league_id ) );
		$team_id = $wpdb->insert_id;

		if ( !empty($logo) ) {
			$logo_file = new LeagueManagerImage($logo);
			$logo_file->createThumbnail();
		}

		if ( isset($_FILES['logo']) && $_FILES['logo']['name'] != '' )
			$this->uploadLogo($team_id, $_FILES['logo']);
		
		if ( $message )
			$leaguemanager->setMessage( __('Team added','leaguemanager') );
			
		return $team_id;
	}


	/**
	 * add new team with data from existing team
	 *
	 * @param int $league_id
	 * @param string $season
	 * @param int $team_id
	 * @param boolean $message (optional)
	 * @return void
	 */
	function addTeamFromDB( $league_id, $season, $team_id, $message = false )
	{
		global $wpdb;
		$team = $wpdb->get_results( "SELECT `league_id`, `title`, `website`, `coach`, `stadium`, `home`, `group`, `roster`, `logo`, `custom` FROM {$wpdb->leaguemanager_teams} WHERE `id` = {$team_id}" );
		$team = $team[0];

		$new_team_id = $this->addTeam($league_id, $season, $team->title, $team->website, $team->coach, $team->stadium, $team->home, $team->group, maybe_unserialize($team->roster), maybe_unserialize($team->custom), $team->logo, $message);
	}
	
	
	/**
	 * edit team
	 *
	 * @param int $team_id
	 * @param string $title
	 * @param string $website
	 * @param string $coach
	 * @param string $stadium
	 * @param int $home 1 | 0
	 * @param mixed $group
	 * @param int|array $roster
	 * @param array $custom
	 * @param boolean $del_logo
	 * @param string $image_file
	 * @param boolean $overwrite_image
	 * @return void
	 */
	function editTeam( $team_id, $title, $website, $coach, $stadium, $home, $group, $roster, $custom, $logo, $del_logo = false, $overwrite_image = false )
	{
		global $wpdb, $leaguemanager;
		
		$wpdb->query( $wpdb->prepare ( "UPDATE {$wpdb->leaguemanager_teams} SET `title` = '%s', `website` = '%s', `coach` = '%s', `stadium` = '%s', `logo` = '%s', `home` = '%d', `group` = '%s', `roster`= '%s', `custom` = '%s' WHERE `id` = %d", $title, $website, $coach, $stadium, $logo, $home, $group, maybe_serialize($roster), maybe_serialize($custom), $team_id ) );
			
		// Delete Image if options is checked
		if ($del_logo || $overwrite_image) {
			$wpdb->query("UPDATE {$wpdb->leaguemanager_teams} SET `logo` = '' WHERE `id` = {$team_id}");
			$this->delLogo( $logo );
		}

		if ( !empty($logo) && !$del_logo ) {
			$logo_image = new LeagueManagerImage($logo);
			$logo_image->createThumbnail();
		}
		
		if ( isset($_FILES['logo']) && $_FILES['logo']['name'] != '' )
			$this->uploadLogo($team_id, $_FILES['logo'], $overwrite_image);
		
		$leaguemanager->setMessage( __('Team updated','leaguemanager') );
	}


	/**
	 * delete Team
	 *
	 * @param int $team_id
	 * @return void
	 */
	function delTeam( $team_id )
	{
		global $wpdb;
		
		$team = parent::getTeam( $team_id );
		// check if other team uses the same logo
		$keep_logo = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->leaguemanager_teams} WHERE `logo` = '".$team->logo."'" );
		if ( $keep_logo == 0 )
			$this->delLogo( $team->logo );
			
		$wpdb->query( "DELETE FROM {$wpdb->leaguemanager_matches} WHERE `home_team` = '".$team_id."' OR `away_team` = '".$team_id."'" );
		$wpdb->query( "DELETE FROM {$wpdb->leaguemanager_teams} WHERE `id` = '".$team_id."'" );
	}


	/**
	 * display dropdon menu of teams (cleaned from double entries)
	 *
	 * @param none
	 * @return void
	 */
	function teamsDropdownCleaned()
	{
		global $wpdb;
		$all_teams = $wpdb->get_results( "SELECT `title`, `id` FROM {$wpdb->leaguemanager_teams} ORDER BY `title` ASC" );
		$teams = array();
		foreach ( $all_teams AS $team ) {
			if ( !in_array($team->title, $teams) )
				$teams[$team->id] = htmlspecialchars(stripslashes($team->title), ENT_QUOTES);
		}
		foreach ( $teams AS $team_id => $name )
			echo "<option value='".$team_id."'>".$name."</option>";
	}
	
	
	/**
	 * gets ranking of teams
	 *
	 * @param string $input serialized string with order
	 * @param string $listname ID of list to sort
	 * @return sorted array of parameters
	 */
	function getRanking( $input, $listname = 'the-list-standings' )
	{
		parse_str( $input, $input_array );
		$input_array = $input_array[$listname];
		$order_array = array();
		for ( $i = 0; $i < count($input_array); $i++ ) {
			if ( $input_array[$i] != '' )
				$order_array[$i+1] = $input_array[$i];
		}
		return $order_array;	
	}
	
	
	/**
	 * set image path in database and upload image to server
	 *
	 * @param int  $team_id
	 * @param string $file
	 * @param string $uploaddir
	 * @param boolean $overwrite_image
	 * @return void | string
	 */
	function uploadLogo( $team_id, $file, $overwrite = false )
	{
		global $wpdb, $leaguemanager;
		
		$new_file = $leaguemanager->getImagePath().'/'. basename($file['name']);
		$logo = new LeagueManagerImage($leaguemanager->getImageUrl() .'/'. basename($file['name']));
		if ( $logo->supported() ) {
			if ( $file['size'] > 0 ) {
				if ( file_exists($new_file) && !$overwrite ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `logo` = '%s' WHERE id = '%d'", $leaguemanager->getImageUrl() .'/'. basename($file['name']), $team_id ) );
					parent::setMessage( __('Logo exists and is not uploaded. Set the overwrite option if you want to replace it.','leaguemanager'), true );
				} else {
					if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
						if ( $team = $this->getTeam( $team_id ) )
							if ( $team->logo != '' ) $this->delLogo($team->logo);
							
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `logo` = '%s' WHERE id = '%d'", $leaguemanager->getImageUrl() .'/'. basename($file['name']), $team_id ) );
			
						$logo->createThumbnail();
					} else {
						parent::setMessage( sprintf( __('The uploaded file could not be moved to %s.' ), $leaguemanager->getImagePath() ), true );
					}
				}
			}
		} else {
			parent::setMessage( __('The file type is not supported.','leaguemanager'), true );
		}
	}
	
	
	/**
	 * delete logo from server
	 *
	 * @param string $image
	 * @return void
	 *
	 */
	function delLogo( $image )
	{
		global $leaguemanager;
		@unlink( $leaguemanager->getImagePath($image) );
		@unlink( $leaguemanager->getThumbnailPath($image) );
	}
	
	
	/**
	 * add Match
	 *
	 * @param string $date
	 * @param int $home_team
	 * @param int $away_team
	 * @param int $match_day
	 * @param string $location
	 * @param int $league_id
	 * @param mixed $season
	 * @param mixed $group
	 * @param string $final
	 * @param array $custom
	 * @return string
	 */
	function addMatch( $date, $home_team, $away_team, $match_day, $location, $league_id, $season, $group, $final, $custom )
	{
	 	global $wpdb;
		$sql = "INSERT INTO {$wpdb->leaguemanager_matches} (date, home_team, away_team, match_day, location, league_id, season, final, custom, `group`) VALUES ('%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s')";
		$wpdb->query ( $wpdb->prepare ( $sql, $date, $home_team, $away_team, $match_day, $location, $league_id, $season, $final, maybe_serialize($custom), $group ) );
		return $wpdb->insert_id;
	}


	/**
	 * edit Match
	 *
	 * @param string $date
	 * @param int $home_team
	 * @param int $away_team
	 * @param int $match_day
	 * @param string $location
	 * @param int $league_id
	 * @param int $match_id
	 * @param mixed $group
	 * @param string $final
	 * @param array $custom
	 * @return string
	 */
	function editMatch( $date, $home_team, $away_team, $match_day, $location, $league_id, $match_id, $group, $final, $custom )
	{
	 	global $wpdb;
		$this->league_id = $league_id;

		$home_points = ($home_points == '') ? 'NULL' : $home_points;
		$away_points = ($away_points == '') ? 'NULL' : $away_points;

		$match = $wpdb->get_results( "SELECT `custom` FROM {$wpdb->leaguemanager_matches} WHERE `id` = {$match_id}" );
		$custom = array_merge( (array)maybe_unserialize($match[0]->custom), $custom );
		$wpdb->query( $wpdb->prepare ( "UPDATE {$wpdb->leaguemanager_matches} SET `date` = '%s', `home_team` = '%s', `away_team` = '%s', `match_day` = '%d', `location` = '%s', `league_id` = '%d', `group` = '%s', `final` = '%s', `custom` = '%s' WHERE `id` = %d", $date, $home_team, $away_team, $match_day, $location, $league_id, $group, $final, maybe_serialize($custom), $match_id ) );
	}


	/**
	 * delete Match
	 *
	 * @param int $cid
	 * @return void
	 */
	function delMatch( $match_id )
	{
	  	global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->leaguemanager_matches} WHERE `id` = '".$match_id."'" );
		return;
	}


	/**
	 * update match results
	 *
	 * @param int $league_id
	 * @param array $matches
	 * @param array $home_points2
	 * @param array $away_points2
	 * @param array $home_points
	 * @param array $away_points
	 * @return string
	 */
	function updateResults( $league_id, $matches, $home_points, $away_points, $home_team, $away_team, $custom, $final = false, $message = true )
	{
		global $wpdb, $leaguemanager;

		$this->league_id = $league_id;
		$league = $leaguemanager->getLeague($this->league_id);
		$season = $leaguemanager->getSeason($league);

		if ( !empty($matches) ) {
			while ( list($match_id) = each($matches) ) {
				$home_points[$match_id] = ( '' == $home_points[$match_id] ) ? 'NULL' : $home_points[$match_id];
				$away_points[$match_id] = ( '' == $away_points[$match_id] ) ? 'NULL' : $away_points[$match_id];
				
				// Support for penalty and overtime hardcoded to determine winner of match
				if ( isset($custom[$match_id]['penalty']) && !empty($custom[$match_id]['penalty']['home']) && !empty($custom[$match_id]['penalty']['home']) )
					$points = array( 'home' => $custom[$match_id]['penalty']['home'], 'away' => $custom[$match_id]['penalty']['away'] );
				elseif ( isset($custom[$match_id]['overtime']) && !empty($custom[$match_id]['overtime']['home']) && !empty($custom[$match_id]['overtime']['away']) )
					$points = array( 'home' => $custom[$match_id]['overtime']['home'], 'away' => $custom[$match_id]['overtime']['away'] );
				else
					$points = array( 'home' => $home_points[$match_id], 'away' => $away_points[$match_id] );

				$winner = $this->getMatchResult( $points['home'], $points['away'], $home_team[$match_id], $away_team[$match_id], 'winner' );
				$loser = $this->getMatchResult($points['home'], $points['away'], $home_team[$match_id], $away_team[$match_id], 'loser' );
				
				$m = $leaguemanager->getMatch( $match_id );
				$c = array_merge( (array)$m->custom, (array)$custom[$match_id] );
				$wpdb->query( "UPDATE {$wpdb->leaguemanager_matches} SET `home_points` = ".$home_points[$match_id].", `away_points` = ".$away_points[$match_id].", `winner_id` = ".intval($winner).", `loser_id` = ".intval($loser).", `custom` = '".maybe_serialize($c)."' WHERE `id` = {$match_id}" );
				
				do_action('leaguemanager_update_results_'.$league->sport, $match_id);
			}
		}

		if ( !$final ) {
			// update Standings for each team
			$teams = $leaguemanager->getTeams( "`league_id` = {$league->id} AND `season` = '".$season['name']."'" );
			foreach ( $teams AS $team ) {
				$this->saveStandings($team->id);
			}

			// Update Teams Rank and Status
			$leaguemanager->rankTeams( $league->id );

			/*
			 * Initialize finals if championship mode is activated and all matches have results
			 */
			$matches = $leaguemanager->getMatches("`league_id` = '".$league_id."' AND `season` = '".$season['name']."' AND `final` = '' AND `home_points` IS NULL AND `away_points` IS NULL");
			if ( !$matches && $league->mode == 'champioship' ) {
				global $championship;
				$championship->proceed( false, $championship->getFinalKeys(1) );
			}
		}

		if ( $message )
			$leaguemanager->setMessage( __('Updated Results','leaguemanager') );
	}
	

	/**
	 * determine match result
	 *
	 * @param int $home_points
	 * @param int $away_points
	 * @param int $home_team
	 * @param int $away_team
	 * @param string $option
	 * @return int
	 */
	function getMatchResult( $home_points, $away_points, $home_team, $away_team, $option )
	{
		if ( $home_points > $away_points ) {
			$match['winner'] = $home_team;
			$match['loser'] = $away_team;
		} elseif ( $home_points < $away_points ) {
			$match['winner'] = $away_team;
			$match['loser'] = $home_team;
		} elseif ( 'NULL' === $home_points && 'NULL' === $away_points ) {
			$match['winner'] = 0;
			$match['loser'] = 0;
		} else {
			$match['winner'] = -1;
			$match['loser'] = -1;
		}
		
		return $match[$option];
	}
	
	
	/**
	 * get date selection.
	 *
	 * @param int $day
	 * @param int $month
	 * @param int $year
	 * @param int $index default 0
	 * @return string
	 */
	function getDateSelection( $day, $month, $year, $index = 0 )
	{
		$out = '<select size="1" name="day['.$index.']" class="date">';
		$out .= "<option value='00'>".__('Day','leaguemanager')."</option>";
		for ( $d = 1; $d <= 31; $d++ ) {
			$selected = ( $d == $day ) ? ' selected="selected"' : '';
			$out .= '<option value="'.str_pad($d, 2, 0, STR_PAD_LEFT).'"'.$selected.'>'.$d.'</option>';
		}
		$out .= '</select>';
		$out .= '<select size="1" name="month['.$index.']" class="date">';
		$out .= "<option value='00'>".__('Month','leaguemanager')."</option>";
		foreach ( parent::getMonths() AS $key => $m ) {
			$selected = ( $key == $month ) ? ' selected="selected"' : '';
			$out .= '<option value="'.str_pad($key, 2, 0, STR_PAD_LEFT).'"'.$selected.'>'.$m.'</option>';
		}
		$out .= '</select>';
		$out .= '<select size="1" name="year['.$index.']" class="date">';
		$out .= "<option value='0000'>".__('Year','leaguemanager')."</option>";
		for ( $y = date("Y")-20; $y <= date("Y")+10; $y++ ) {
			$selected =  ( $y == $year ) ? ' selected="selected"' : '';
			$out .= '<option value="'.$y.'"'.$selected.'>'.$y.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	
	/**
	 * display global settings page (e.g. color scheme options)
	 *
	 * @param none
	 * @return void
	 */
	function displayOptionsPage()
	{
		$options = get_option('leaguemanager');
		
		if ( isset($_POST['updateLeagueManager']) ) {
			check_admin_referer('leaguemanager_manage-global-league-options');
			$options['colors']['headers'] = $_POST['color_headers'];
			$options['colors']['rows'] = array( 'alternate' => $_POST['color_rows_alt'], 'main' => $_POST['color_rows'], 'ascend' => $_POST['color_rows_ascend'], 'descend' => $_POST['color_rows_descend'], 'relegation' => $_POST['color_rows_relegation'] );
			
			update_option( 'leaguemanager', $options );
			parent::setMessage(__( 'Settings saved', 'leaguemanager' ));
			parent::printMessage();
		}
		
		require_once (dirname (__FILE__) . '/settings-global.php');
	}
	
	
	/**
	 * add meta box to post screen
	 *
	 * @param object $post
	 * @return none
	 */
	function addMetaBox( $post )
	{
		global $wpdb, $post_ID, $leaguemanager;
		
		if ( $leagues = $wpdb->get_results( "SELECT `title`, `id` FROM {$wpdb->leaguemanager} ORDER BY id ASC" ) ) {
			$league_id = $season = 0;
			$curr_league = $match = false;
			if ( $post_ID != 0 ) {
				$match = $wpdb->get_results( "SELECT `id`, `league_id`, `season` FROM {$wpdb->leaguemanager_matches} WHERE `post_id` = {$post_ID}" );
				$match = $match[0];
				if ( $match ) {
					$match_id = ( $match ) ? $match->id : 0;
					$league_id = $match->league_id;	
					$season = $match->season;
					$curr_league = $leaguemanager->getLeague($league_id);
				}
			} else {
				$match_id = 0;
			}
		
			echo "<input type='hidden' name='curr_match_id' value='".$match_id."' />";
			echo "<select name='league_id' class='alignleft' id='league_id' onChange='Leaguemanager.getSeasonDropdown(this.value, ".$season.")'>";
			echo "<option value='0'>".__('Choose League','leaguemanager')."</option>";
			foreach ( $leagues AS $league ) {
				$selected = ( $league_id == $league->id ) ? ' selected="selected"' : '';
				echo "<option value='".$league->id."'".$selected.">".$league->title."</option>";
			}
			echo "</select>";

			echo "<div id='seasons'>";
			if ( $match )
				echo $this->getSeasonDropdown($curr_league, $season);
			echo '</div>';
			echo "<div id='matches'>";
			if ( $match )
				echo $this->getMatchDropdown($match);
			echo '</div>';

			echo '<br style="clear: both;" />';
		}
	}
	

	/**
	 * display Seaason dropdown
	 *
	 * @param mixed $league
	 * @param mixed $season
	 * @return void|string
	 */
	function getSeasonDropdown( $league = false, $season = false )
	{
		global $leaguemanager;

		if ( !$league ) {
			$league_id = (int)$_POST['league_id'];
			$league = $leaguemanager->getLeague($league_id);
			$ajax = true;
		} else {
			$league_id = $league->id;
			$ajax = false;
		}

		$league->seasons = maybe_unserialize($league->seasons);

		$out = '<select size="1" class="alignleft" id="season" name="season" onChange="Leaguemanager.getMatchDropdown('.$league_id.', this.value);">';
		$out .= '<option value="">'.__('Choose Season', 'leaguemanager').'</option>';
		foreach ( $league->seasons AS $s ) {
			$selected = ( $season == $s['name'] ) ? ' selected="selected"' : '';
			$out .= '<option value="'.$s['name'].'"'.$selected.'>'.$s['name'].'</option>';
		}
		$out .= '</select>';

		if ( !$ajax ) {
			return $out;
		} else {
			die( "jQuery('div#matches').fadeOut('fast', function() {
				jQuery('div#seasons').fadeOut('fast');
				jQuery('div#seasons').html('".addslashes_gpc($out)."').fadeIn('fast');
			});");
		}
	}


	/**
	 * display match dropdown
	 *
	 * @param mixed $match
	 * @return void|string
	 */
	function getMatchDropdown( $match = false )
	{
		global $leaguemanager;

		if ( !$match ) {
			$league_id = (int)$_POST['league_id'];
			$season = $_POST['season'];
			$match_id = false;
			$ajax = true;
		} else {
			$league_id = $match->league_id;
			$season = $match->season;
			$match_id = $match->id;
			$ajax = false;
		}

		$matches = $leaguemanager->getMatches("`league_id` = {$league_id} AND `season` = '".$season."'");
		$teams = $leaguemanager->getTeams("`league_id` = {$league_id} AND `season` = '".$season."'", "`id` ASC", 'ARRAY');

		$out = '<select size="1" name="match_id" id="match_id" class="alignleft">';
		$out .= '<option value="0">'.__('Choose Match', 'leaguemanager').'</option>';
		foreach ( $matches AS $match ) {
			$selected = ( $match_id == $match->id ) ? ' selected="selected"' : '';
			$out .= '<option value="'.$match->id.'"'.$selected.'>'.$teams[$match->home_team]['title'] . ' &#8211; ' . $teams[$match->away_team]['title'].'</option>';
		}
		$out .= '</select>';

		if ( !$ajax ) {
			return $out;
		} else {
			die( "jQuery('div#matches').fadeOut('fast', function() {
				jQuery('div#matches').html('".addslashes_gpc($out)."').fadeIn('fast');
			});");
		}
	}


	/**
	 * update post id for match report
	 *
	 * @param none
	 * @return none
	 */
	function editMatchReport()
	{
		global $wpdb;
		
		$post_ID = (int) $_POST['post_ID'];
		$match_ID = (int) $_POST['match_id'];
		$curr_match_ID = (int) $_POST['curr_match_id'];
		if ( $curr_match_ID != $match_ID ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_matches} SET `post_id` = '%d' wHERE `id` = '%d'", $post_ID, $match_ID ) );
			if ( $curr_match_ID != 0 )
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_matches} SET `post_id` = 0 wHERE `id` = '%d'", $curr_match_ID ) );
		}
	}
	
	
	/**
	 * get supported image types
	 *
	 * @param none
	 * @return array
	 */
	function getSupportedImageTypes()
	{
		return LeagueManagerImage::getSupportedImageTypes();
	}
	
	
	/**
	 * import data from CSV file
	 *
	 * @param int $league_id
	 * @param array $file CSV file
	 * @param string $delimiter
	 * @param array $mode 'teams' | 'matches'
	 * @return string
	 */
	function import( $league_id, $file, $delimiter, $mode )
	{
		global $leaguemanager;

		if ( $file['size'] > 0 ) {
			/*
			* Upload CSV file to image directory, temporarily
			*/
			$new_file =  ABSPATH.'wp-content/uploads/'.basename($file['name']);
			if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
				$this->league_id = $league_id;
				if ( 'teams' == $mode )
					$this->importTeams($new_file, $delimiter);
				elseif ( 'matches' == $mode )
					$this->importMatches($new_file, $delimiter);
			} else {
				parent::setMessage(sprintf( __('The uploaded file could not be moved to %s.' ), ABSPATH.'wp-content/uploads') );
			}
			@unlink($new_file); // remove file from server after import is done
		} else {
			parent::setMessage( __('The uploaded file seems to be empty', 'leaguemanager'), true );
		}
	}
	
	
	/**
	 * import teams from CSV file
	 *
	 * @param string $file
	 * @param string $delimiter
	 */
	function importTeams( $file, $delimiter )
	{
		global $leaguemanager;
		
		$handle = @fopen($file, "r");
		if ($handle) {
			$league = $leaguemanager->getLeague( $this->league_id );
			if ( "TAB" == $delimiter ) $delimiter = "\t"; // correct tabular delimiter
			
			$teams = $points_plus = $points_minus = $points2_plus = $points2_minus = $pld = $won = $draw = $lost = $custom = array();

			$i = $x = 0;
			while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
				$line = explode($delimiter, $buffer);
				
				// ignore header and empty lines
				if ( $i > 0 && $line ) {
					$season = $line[0]; $team = $line[1]; $website = $line[2]; $coach = $line[3]; $home = $line[4]; $logo = '';
					$custom = apply_filters( 'leaguemanager_import_teams_'.$league->sport, $custom, $line );
					$team_id = $this->addTeam( $this->league_id, $season, $team, $website, $coach, $home, $custom, $logo, false );
	
					$points2 = explode("-", $line[9]);
					$points = explode("-", $line[11]);

					$teams[$team_id] = $team_id;
					$pld[$team_id] = $line[5];
					$won[$team_id] = $line[6];
					$draw[$team_id] = $line[7];
					$lost[$team_id] = $line[8];
					$points_plus[$team_id] = $points[0];
					$points_minus[$team_id] = $points[1];
					$custom[$team_id]['points2'] = array( 'plus' => $points2[0], 'minus' => $points2[1] );

					$x++;
				}
				$i++;
			}

			$this->saveStandingsManually($teams, $points_plus, $points_minus, $pld, $won, $draw, $lost, 0, $custom);

			fclose($handle);
			
			parent::setMessage(sprintf(__( '%d Teams imported', 'leaguemanager' ), $x));
		}
	}
	
	
	/**
	 * import matches from CSV file
	 *
	 * @param string $file
	 * @param string $delimiter
	 */
	function importMatches( $file, $delimiter )
	{
		global $leaguemanager;
		
		$handle = @fopen($file, "r");
		if ($handle) {
			if ( "TAB" == $delimiter ) $delimiter = "\t"; // correct tabular delimiter
			
			$league = $leaguemanager->getLeague( $this->league_id );
	
			$matches = $home_points = $away_points = $home_teams = $away_teams = $custom = array();

			$i = $x = 0;
			while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
				$line = explode($delimiter, $buffer);

				// ignore header and empty lines
				if ( $i > 0 && $line ) {
					$date = ( !empty($line[6]) ) ? $line[0]." ".$line[6] : $line[0]. " 00:00";
					$season = $this->season = $line[1];
					$match_day = $line[2];
					$date = trim($date);
					$location = $line[5];
					$home_team = $this->getTeamID($line[3]);
					$away_team = $this->getTeamID($line[4]);

					$match_id = $this->addMatch($date, $home_team, $away_team, $match_day, $location, $this->league_id, $season, '','', array());

					$matches[$match_id] = $match_id;
					$home_teams[$match_id] = $this->getTeamID($line[3]);
					$away_teams[$match_id] = $this->getTeamID($line[4]);
					$score = explode("-", $line[7]);
					$home_points[$match_id] = $score[0];
					$away_points[$match_id] = $score[1];
					$custom = apply_filters( 'leaguemanager_import_matches_'.$league->sport, $custom, $line, $match_id );

					$x++;
				}
				
				$i++;
			}
			$this->updateResults( $league->id, $matches, $home_points, $away_points, $home_teams, $away_teams, $custom, false );

			fclose($handle);
			
			parent::setMessage(sprintf(__( '%d Matches imported', 'leaguemanager' ), $x));
		}
	}
	
	
	/**
	 * get Team ID for given string
	 *
	 * @param string $title
	 * @return int
	 */
	function getTeamID( $title )
	{
		global $wpdb;
		
		$team = $wpdb->get_results( "SELECT `id` FROM {$wpdb->leaguemanager_teams} WHERE `title` = '".$title."' AND `league_id` = {$this->league_id} AND `season` = '".$this->season."'" );
		return $team[0]->id;
	}
	
	
	/**
	 * export league data
	 *
	 * @param int $league_id
	 * @param string $mode
	 * @return file
	 */
	function export( $league_id, $mode )
	{
		global $leaguemanager;
		
		$this->league_id = $league_id;
		$this->league = $leaguemanager->getLeague($league_id);
		$filename = sanitize_title($this->league->title)."-".$mode."_".date("Y-m-d").".csv";
		
		if ( 'teams' == $mode )
			$contents = $this->exportTeams();
		elseif ( 'matches' ==  $mode )
			$contents = $this->exportMatches();
		
		
		header('Content-Type: text/csv');
    		header('Content-Disposition: inline; filename="'.$filename.'"');
		echo $contents;
		exit();
	}
	
	
	/**
	 * export teams
	 *
	 * @param none
	 * @return string
	 */
	function exportTeams()
	{
		global $leaguemanager;
		
		$league = $this->league;

		$teams = parent::getTeams( "league_id =".$this->league_id );
		
		if ( $teams ) {
			$contents = __('Season','leaguemanager')."\t". __('Team','leaguemanager')."\t".__('Website','leaguemanager')."\t".__('Coach','leaguemanager')."\t".__('Home Team','leaguemanager')."\t".__('Pld','leaguemanager')."\t"._c('W|Won','leaguemanager')."\t"._c('T|Tie','leaguemanager')."\t"._c('L|Lost','leaguemanager')."\t".__('Points2', 'leaguemanager')."\t".__('Diff','leaguemanager')."\t".__('Pts','leaguemanager');
			$contents = apply_filters( 'leaguemanager_export_teams_header_'.$league->sport, $contents );
			
			foreach ( $teams AS $team ) {
				$home = ( $team->home == 1 ) ? 1 : 0;
				$contents .= "\n".$team->season."\t".$team->title."\t".$team->website."\t".$team->coach."\t".$home."\t".$team->done_matches."\t".$team->won_matches."\t".$team->draw_matches."\t".$team->lost_matches."\t".sprintf("%d-%d",$team->points2_plus, $team->points2_minus)."\t".$team->diff."\t".sprintf("%d-%d", $team->points_plus, $team->points_minus);
				$contents = apply_filters( 'leaguemanager_export_teams_data_'.$league->sport, $contents, $team );
			}
			return $contents;
		}
		return false;
	}
	
	
	/**
	 * export matches
	 *
	 * @param none
	 * @return string
	 */
	function exportMatches()
	{
		global $leaguemanager;
		
		$matches = parent::getMatches( "league_id=".$this->league_id );
		if ( $matches ) {
	  	$league = $this->league;
			$teams = parent::getTeams( "league_id=".$this->league_id, "`id` ASC", 'ARRAY' );
		
			// Build header
			$contents = __('Date','leaguemanager')."\t".__('Season','leaguemanager')."\t".__('Match Day','leaguemanager')."\t".__('Home','leaguemanager')."\t".__('Guest','leaguemanager')."\t".__('Location','leaguemanager')."\t".__('Begin','leaguemanager')."\t".__('Score', 'leaguemanager');
			$contents = apply_filters( 'leaguemanager_export_matches_header_'.$league->sport, $contents );

			foreach ( $matches AS $match ) {
				$contents .= "\n".mysql2date('Y-m-d', $match->date)."\t".$match->season."\t".$match->match_day."\t".$teams[$match->home_team]['title']."\t".$teams[$match->away_team]['title']."\t".$match->location."\t".mysql2date("H:i", $match->date)."\t";
				$contents .= !empty($match->home_points) ? sprintf("%d-%d",$match->home_points, $match->away_points) : '';
				$contents = apply_filters( 'leaguemanager_export_matches_data_'.$league->sport, $contents, $match );
			}

			return $contents;
		}
		
		return false;
	}
}
?>
