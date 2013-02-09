<?php
/**
 * Statistics class for the WordPress plugin LeagueManager
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerStats extends LeagueManager
{
	/**
	 * sports that have match statistics
	 *
	 * @var array
	 */
	var $sports = array();


	/**
	 * page key
	 *
	 * @var string
	 */
	var $page = 'matchstats';


	/**
	 * statistic object
	 *
	 * @var object
	 */
	var $stat;


	/**
	 * team roster
	 *
	 * @var array
	 */
	var $roster = array();


	/**
	 * league object
	 *
	 * @var object
	 */
	var $league;


	/**
	 * ID of current league
	 *
	 * @var int
	 */
	var $league_id;


	/**
	 * initialize class
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		global $leaguemanager;

		if ( is_admin() ) {
			add_action( 'league_settings', array(&$this, 'settings') );
		
			$league = $leaguemanager->getCurrentLeague();

			if ( isset($league->use_stats) && 1 == $league->use_stats )
				$this->addSport($league->sport);

			if ( !empty($this->sports) ) {
				foreach ( $this->sports AS $sport ) {
					add_filter( 'matchtable_header_'.$sport, array(&$this, 'displayMatchesHeader'), 10, 0);
					add_filter( 'matchtable_columns_'.$sport, array(&$this, 'displayMatchesColumns') );
					add_filter( 'league_menu_'.$sport, array(&$this, 'leagueMenu') );
				}
			}
		
			add_action( 'wp_ajax_leaguemanager_add_stats_field', array(&$this, 'addStatsField') );
			add_action( 'wp_ajax_leaguemanager_add_stat', array(&$this, 'addMatchStatsField') );
		}
		add_filter( 'projectmanager_formfields', array(&$this, 'addToProfile') );
	}
	function LeagueManagerStats()
	{
		$this->__construct();
	}


	/**
	 * add sports type
	 *
	 * @param string $sport
	 * @return void
	 */
	function addSport( $sport )
	{
		array_push($this->sports, $sport);
	}


	/**
	 * add settings
	 *
	 * @param object $league
	 * @return void
	 */
	function settings( $league )
	{
		echo '<tr>';
		echo '<th scope="row"><label for="use_stats">'.__('Activate Match Statistics', 'leaguemanager').'</label></th>';
		$checked = ( isset($league->use_stats) && 1 == $league->use_stats ) ? ' checked="checked"' : '';
		echo '<td><input type="checkbox" id="use_stats" name="settings[use_stats]" value="1"'.$checked.' /></td>';
		echo '</tr>';
	}


	/**
	 * display Table Header for Match Administration
	 *
	 * @param none
	 * @return void
	 */
	function displayMatchesHeader()
	{
		echo '<th>'.__('Stats', 'leaguemanager').'</th>';
	}


	/**
	 * display Table columns for Match Administration
	 *
	 * @param object $match
	 * @return void
	 */
	function displayMatchesColumns( $match )
	{
		echo '<td><a href="admin.php?page=leaguemanager&subpage='.$this->page.'&league_id='.$match->league_id.'&match_id='.$match->id.'">'.__('Stats', 'leaguemanager').'</td>';
	}


	/**
	 * extend league menu
	 *
	 * @param array $menu
	 * @return array
	 */
	function leagueMenu( $menu )
	{
		$menu[$this->page] = array( 'title' => __('Match Statistics', 'leaguemanager'), 'file' => LEAGUEMANAGER_PATH . '/admin/stats.php' );
		return $menu;
	}


	/**
	 * insert stat fiedls to ProjectManager Formfields
	 *
	 * @param array $formfields
	 * @return void
	 */
	function addToProfile($formfields)
	{
		global $leaguemanager;

		foreach ( $this->get() AS $stat ) {
			$formfields[sanitize_title($stat->name)] = array( 'name' => $stat->name, 'callback' => array(&$this, 'getStatistics'), 'args' => array('statistics' => $stat), 'msg' => __( 'Stastistics from LeagueManager', 'leaguemanager') );
		}

		return $formfields;
	}


	/**
	 * get player statistics
	 *
	 * @param array $user
	 * @param object $statistics
	 * @param mixed $season
	 * @return mixed
	 */
	function getStatistics( $user, $statistics, $season = false)
	{
		global $leaguemanager;

		$num = 0;

		$search = ( $season ) ? "`season` = '".$season."'" : '';
		if ( $matches = $leaguemanager->getMatches($search) ) {
			foreach ( $matches AS $match ) {
				if ( isset($match->{sanitize_title($statistics->name)}) ) {
					foreach ( $match->{sanitize_title($statistics->name)} AS $stat ) {
						foreach ( (array)maybe_unserialize($statistics->fields) AS $field ) {
							if  ( 'roster' == $field['type'] && $user['name'] == $stat[sanitize_title($field['name'])] )
								$num++;
						}
					}
				}
			}
		}

		if ( empty( $num ) )
			return __( 'None', 'leaguemanager' );

		return $num;
	}


	/**
	 * save stat object
	 *
	 * @param object $stat
	 * @return void
	 */
	function set( $stat )
	{
		$this->stat = $stat;
	}


	/**
	 * get stats
	 *
	 * @param int $league_id
	 * @param int $id (optional)
	 * @return array
	 */
	function get( $league_id = false, $id = false )
	{
		global $wpdb;

		$sql = "SELECT `name`, `fields`, `id`, `league_id` FROM {$wpdb->leaguemanager_stats}";
		if ( $id )
			$sql .= " WHERE `id` = {$id}";
		elseif ( $league_id )
			$sql .= " WHERE `league_id` = {$league_id} ORDER BY `id` ASC";

		$stats = $wpdb->get_results( $sql );

		if ( $id )
			return $stats[0];

		return $stats;
	}


	/**
	 * add new stats
	 *
	 * @param string $name
	 * @param array $fields
	 * @param int $league_id
	 * @return int new stat ID
	 */
	function add( $name, $fields, $league_id )
	{
		global $wpdb, $leaguemanager;

		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->leaguemanager_stats} (name, fields, league_id) VALUES ('%s', '%s', '%d')", $name, maybe_serialize(array_values($fields)), $league_id ) );
		$id = $wpdb->insert_id;

		$leaguemanager->setMessage( __('Statistics Field added', 'leaguemanager') );
		return $id;
	}


	/**
	 * edit stats field
	 *
	 * @param string $name
	 * @param array $fields
	 * @param init $id
	 * @return void
	 */
	function edit( $name, $fields, $id )
	{
		global $wpdb, $leaguemanager;

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_stats} SET `name` = '%s', `fields` = '%s' WHERE`id` = '%d'", $name, maybe_serialize(array_values($fields)), $id ) );
		$leaguemanager->setMessage( __('Statistics Fields updated', 'leaguemanager') );
	}


	/**
	 * delete stats field
	 *
	 * @param int $id
	 * @return void
	 */
	function del( $id )
	{
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->leaguemanager_stats} WHERE `id` = {$id}" );
	}
	

	/**
	 * save match statistics
	 *
	 * The first parameter is simply the match ID. The second is a multidimensional array holding all statistics.
	 *
	 * @param int $match_id
	 * @param array $stats
	 * @return string
	 */
	function save( $match_id, $stats )
	{
		global $wpdb, $leaguemanager;
		$match = $leaguemanager->getMatch($match_id);
	
		$custom = $match->custom;
		foreach ( (array) $stats AS $stat => $data ) {
			$custom[$stat] = array_values($data);
		}
		$custom['hasStats'] = true;
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_matches} SET `custom` = '%s' WHERE `id` = '%s'", maybe_serialize($custom), $match_id ) );
		
		parent::setMessage(__('Saved Statstics', 'leaguemanager'));
		parent::printMessage();
	}
	
	
	/**
	 * SACK response to add stats field
	 *
	 * @param int $number
	 * @param string $name
	 * @param string $type
	 */
	function addStatsField( $number = false, $name = false, $type = false, $ajax = true )
	{
		$num = !$ajax ? $number : $_POST['number'];

		$types = array('text' => __( 'Text', 'leaguemanager' ), 'roster' => __( 'Team Roster', 'leaguemanager' ) );
		$html = '';
		if ( !$ajax ) $html .= "<tr id='stats_field_".$num."'>";
		$html .= "<th scope='row'>".__( 'Stats Field', 'leaguemannager' )."</th>";	
		$html .= "<td><input type='text' name='fields[".$num."][name]' value='".$name."' />&#160;";
		$html .= "<select size='1' name='fields[".$num."][type]'>";
		foreach ( $types AS $key => $value ) {
			$selected = ( $key == $type ) ? ' selected="selected"' : '';
			$html .= "<option value='".$key."'".$selected.">".$value."</option>";
		}
		$html .= "</select>&#160;";
		$html .= "<a href='#' onClick='Leaguemanager.removeStatsField(\"stats_field_".$num."\");' title='".__( 'Delete', 'leaguemanager' )."'><img src='".LEAGUEMANAGER_URL . "/admin/icons/trash.gif' alt='".__('Delete','leaguemanager')."' /></a>";
		$html .= "</td>";
		if (!$ajax ) $html .= "</tr>";

		if ( !$ajax ) {
			return $html;
		} else {	
			die("
				el_id = 'stats_field_".$num."';
				new_element = document.createElement('tr');
				new_element.id = el_id;
				document.getElementById('stats_fields').appendChild(new_element);
				document.getElementById(el_id).innerHTML = '".addslashes_gpc($html)."';
			");
		}
	}


	/**
	 * SACK response to add match stats field
	 *
	 */
	function addMatchStatsField()
	{
		global $leaguemanager, $lmBridge;

		$i = $_POST['number'];
		$parent_id = $_POST['parent_id'];
		$match_id = (int)$_POST['match_id'];
		$stat_id = (int)$_POST['stat_id'];

		$match = $leaguemanager->getMatch( $match_id );
		$league = $leaguemanager->getLeague($match->league_id);

		$roster = array();
		if ( $league->hasBridge ) {
			$lmBridge->setProjectID( $league->project_id );

			$home = $leaguemanager->getTeam($match->home_team);
			$away = $leaguemanager->getTeam($match->away_team);

			$home->teamRoster = $lmBridge->getTeamRoster( $home->roster );
			$away->teamRoster = $lmBridge->getTeamRoster( $away->roster );

			if ( $home->teamRoster )
				$roster[$home->title] = $home->teamRoster;
			if ( $away->teamRoster )
				$roster[$away->title] = $away->teamRoster;
		}

		$stat = $this->get(false, $stat_id);
		$stat->fields = maybe_unserialize($stat->fields);
		$stat->key = sanitize_title($stat->name);

		$html = '';
		foreach ( (array)$stat->fields AS $x => $field ) {
			$html .= '<td>';
			$html .= '<input type="text" size="10" name="stats['.$stat->key.']['.$i.']['.sanitize_title($field['name']).']" id="'.$stat->key.'_'.sanitize_title($field['name']).'_'.$i.'" value="" />';
			if ( 'roster' == $field['type'] && !empty($roster) ) {
				$html .= '<div id="'.$stat->key.'_roster_box_'.$i.'_'.$x.'" style="display: none; overflow: auto;" class="leaguemanager_thickbox">';
				$html .= $lmBridge->getTeamRosterSelection($roster, '', $stat->key."_".sanitize_title($field['name'])."_roster_".$i);
				$html .= '<div style="text-align: center; margin-top: 1em;"><input type="button" value="'.__('Insert', 'leaguemanager').'" class="button-secondary" onClick="Leaguemanager.insertPlayer(\''.$stat->key.'_'.sanitize_title($field['name']).'_roster_'.$i.'\', \''.$stat->key.'_'.sanitize_title($field['name']).'_'.$i.'\'); return false;" />&#160;<input type="button" value="'.__('Cancel', 'leaguemanager').'" class="button-secondary" onClick="tb_remove();" /></div></div>';
				$html .= '<span class="team_roster"><a class="thickbox" href="#TB_inline&height=100&width=300&inlineId='.$stat->key.'_roster_box_'.$i.'_'.$x.'" title="'.__( 'Add Player from Team Roster', 'leaguemanager' ).'"><img src="'.LEAGUEMANAGER_URL.'/admin/icons/player.png" border="0" alt="'.__('Insert Player', 'leaguemanager').'" /></a></span>';
			}
			$html .= '</td>';
		}
		$html .= '<td style="text-align: center; width: 12px; vertical-align: middle;"><a class="image_link" href="#" onclick="return Leaguemanager.removeField(\''.$stat->key.'_'.$i.'\', \'stat_'.$stat->key.'\');"><img src="'.LEAGUEMANAGER_URL.'/admin/icons/trash.gif" alt="'.__( 'Delete', 'leaguemanager' ).'" title="'.__( 'Delete', 'leaguemanager' ).'" /></a></td>';	

		die("	
			el_id = '".$stat->key."_".$i."';
			new_element = document.createElement('tr');
			new_element.id = el_id;
			document.getElementById('".$parent_id."').appendChild(new_element);
			document.getElementById(el_id).innerHTML = '".addslashes_gpc($html)."';

  			Leaguemanager.reInit();
		");
	}
}
?>
