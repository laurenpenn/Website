<?php
/**
 * AJAX class for the WordPress plugin LeagueManager
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerAJAX
{
	/**
	 * register ajax actions
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		add_action( 'wp_ajax_leaguemanager_add_team_from_db', array(&$this, 'addTeamFromDB') );
		add_action( 'wp_ajax_leaguemanager_set_team_roster_groups', array(&$this, 'setTeamRosterGroups') );
		add_action( 'wp_ajax_leaguemanager_get_match_box', array(&$this, 'getMatchBox') );
		add_action( 'wp_ajax_leaguemanager_save_team_standings', array(&$this, 'saveTeamStandings') );
		add_action( 'wp_ajax_leaguemanager_save_add_points', array(&$this, 'saveAddPoints') );
		add_action( 'wp_ajax_leaguemanager_insert_logo_from_library', array(&$this, 'insertLogoFromLibrary') );
		add_action( 'wp_ajax_leaguemanager_insert_home_stadium', array(&$this, 'insertHomeStadium') );
	}
	function LeagueManagerAJAX()
	{
		$this->__construct();
	}


	/**
	 * Ajax Response to set match index in widget
	 *
	 * @param none
	 * @return void
	 */
	function getMatchBox() {
		$widget = new LeagueManagerWidget(true);

		$current = $_POST['current'];
		$element = $_POST['element'];
		$operation = $_POST['operation'];
		$league_id = $_POST['league_id'];
		$match_limit = ( $_POST['match_limit'] == 'false' ) ? false : $_POST['match_limit'];
		$widget_number = $_POST['widget_number'];
		$season = $_POST['season'];
		$home_only = $_POST['home_only'];
		$date_format = $_POST['date_format'];

		if ( $operation == 'next' )
			$index = $current + 1;
		elseif ( $operation == 'prev' )
			$index = $current - 1;
	
		$widget->setMatchIndex( $index, $element );
		
		$instance = array( 'league' => $league_id, 'match_limit' => $match_limit, 'season' => $season, 'home_only' => $home_only, 'date_format' => $date_format );

		if ( $element == 'next' ) {
			$parent_id = 'next_matches_'.$widget_number;
			$match_box = $widget->showNextMatchBox($widget_number, $instance, false);
		} elseif ( $element == 'prev' ) {
			$parent_id = 'prev_matches_'.$widget_number;
			$match_box = $widget->showPrevMatchBox($widget_number, $instance, false);
		}

		die( "jQuery('div#".$parent_id."').fadeOut('fast', function() {
			jQuery('div#".$parent_id."').html('".addslashes_gpc($match_box)."').fadeIn('fast');
		});");
	}


	/**
	 * SACK response to manually set team ranking
	 *
	 * @since 2.8
	 */
	function saveTeamStandings() {
		global $wpdb, $lmLoader, $leaguemanager;
		$ranking = $_POST['ranking'];
		$ranking = $lmLoader->adminPanel->getRanking($ranking);
		foreach ( $ranking AS $rank => $team_id ) {
			$old = $leaguemanager->getTeam( $team_id );
			$oldRank = $old->rank;

			if ( $oldRank != 0 ) {
				if ( $rank == $oldRank )
					$status = '&#8226;';
				elseif ( $rank < $oldRank )
					$status = '&#8593';
				else
					$status = '&#8595';
			} else {
				$status = '&#8226;';
			}

			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `rank` = '%d', `status` = '%s' WHERE `id` = '%d'", $rank, $status, $team_id ) );
		}
	}


	/**
	* SACK response to manually set additional points
	*
	* @since 2.8
	*/
	function saveAddPoints() {
		global $wpdb, $leaguemanager;
		$team_id = intval($_POST['team_id']);
		$points = $_POST['points'];
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `add_points` = '%s' WHERE `id` = '%d'", $points, $team_id ) );
		$leaguemanager->rankTeams(1);

		die("Leaguemanager.doneLoading('loading_".$team_id."')");
	}


	/**
	 * SACK response to get team data from database and insert into team edit form
	 *
	 * @since 2.9
	 */
	function addTeamFromDB() {
		global $leaguemanager;
	
		$team_id = (int)$_POST['team_id'];
		$team = $leaguemanager->getTeam( $team_id );
	
		$roster = '';
		if ( $leaguemanager->hasBridge() ) {
			global $projectmanager;
			$html = '<select size="1" name="roster" id="roster" onChange="Leaguemanager.toggleTeamRosterGroups(this.value);return false;"><option value="">'.__('None','leaguemanager').'</option>';
			foreach ( $projectmanager->getProjects() AS $dataset ) {
				$selected = ( $dataset->id == $team->roster['id'] ) ? ' selected="selected"' : '';
				$html .= '<option value="'.$dataset->id.'"'.$selected.'>'.$dataset->title.'</option>';
			}
			$html .= '</select>';
			$roster = "jQuery('span#rosterbox').fadeOut('fast', function() {
					jQuery('span#rosterbox').html('".addslashes_gpc($html)."').fadeIn('fast')
				   });";

			if ( isset($team->roster['cat_id']) ) {
				$project = $projectmanager->getProject($team->roster['id']);
				$category = $project->category;

				if ( !empty($category) ) {
					$html = wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'roster_group', 'orderby' => 'name', 'echo' => 0, 'show_option_none' => __('Select Group (Optional)', 'leaguemanager'), 'child_of' => $category, 'selected' => $team->roster['cat_id'] ));
					$html = str_replace("\n", "", $html);
				} else {
					$html = "";
				}
				$roster .= "jQuery('span#team_roster_groups').fadeOut('fast', function () {
						jQuery('span#team_roster_groups').html('".addslashes_gpc($html)."').fadeIn('fast');
					   });";
			} else {
				$roster .= "jQuery('span#team_roster_groups').fadeOut('fast');";
			}
		}

		$home = ( $team->home == 1 ) ? "document.getElementById('home').checked = true;" : "document.getElementById('home').checked = false;";

		$logo = ( !empty($team->logo) ) ? "<img src='".$team->logo."' />" : "";	
		die("
			document.getElementById('team').value = '".$team->title."';
			document.getElementById('website').value = '".$team->website."';
			document.getElementById('coach').value = '".$team->coach."';
			document.getElementById('logo_db').value = '".$team->logo."';
			jQuery('div#logo_db_box').html('".addslashes_gpc($logo)."').fadeIn('fast');
			".$home."
			".$roster."
		");
	}


	/**
	 * SACK response to display respective ProjectManager Groups as Team Roster
	 *
	 * @since 3.0
	 */
	function setTeamRosterGroups() {
		global $projectmanager;
	
		$roster = (int)$_POST['roster'];
		$project = $projectmanager->getProject($roster);
		$category = $project->category;

		if ( !empty($category) ) {
			$html = wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'roster_group', 'orderby' => 'name', 'echo' => 0, 'show_option_none' => __('Select Group (Optional)', 'leaguemanager'), 'child_of' => $category ));
			$html = str_replace("\n", "", $html);
		} else {
			$html = "";
		}
	
		die("jQuery('span#team_roster_groups').fadeOut('fast', function () {
			jQuery('span#team_roster_groups').html('".addslashes_gpc($html)."').fadeIn('fast');
		});");
	}


	/**
	 * insert Logo from Library
	 *
	 * @param none
	 * @return void
	 */
	function insertLogoFromLibrary()
	{
		$logo = (string)$_POST['logo'];
		$logo = 'http://' . $logo;
		$html = "<img id='logo_image' src='".$logo."' />";

		if ( $_SERVER['HTTP_HOST'] != substr($logo, 7, strlen($_SERVER['HTTP_HOST'])) ) {
			die("alert('".__('The image cannot be on a remote server', 'leaguemanager')."')");
		} else {
			die("jQuery('div#logo_db_box').fadeOut('fast', function() {
				document.getElementById('logo_db').value = '".$logo."';
				jQuery('div#logo_db_box').html('".addslashes_gpc($html)."').fadeIn('fast');
			});");
		}
	}


	/**
	 * insert home team stadium if available
	 *
	 * @param none
	 * @rturn void
	 */
	function insertHomeStadium()
	{
		global $leaguemanager;
		$team_id = (int)$_POST['team_id'];
		$i = (int)$_POST['i'];

		$team = $leaguemanager->getTeam( $team_id );
		die("document.getElementById('location[".$i."]').value = '".$team->stadium."';");
	}
}
?>
