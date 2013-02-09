<?php
if ( isset($_POST['updateLeague']) && !isset($_POST['doaction']) && !isset($_POST['doaction2']) && !isset($_POST['doaction3']) )  {
	if ( 'team' == $_POST['updateLeague'] ) {
		check_admin_referer('leaguemanager_manage-teams');
		$home = isset( $_POST['home'] ) ? 1 : 0;
		$custom = !isset($_POST['custom']) ? array() : $_POST['custom'];
		$roster = ( isset($_POST['roster_group']) && !empty($_POST['roster_group']) ) ? array('id' => $_POST['roster'], 'cat_id' => $_POST['roster_group']) : array( 'id' => $_POST['roster'], 'cat_id' => false );
		$group = isset($_POST['group']) ? $_POST['group'] : '';
		if ( '' == $_POST['team_id'] ) {
			$this->addTeam( $_POST['league_id'], $_POST['season'], $_POST['team'], $_POST['website'], $_POST['coach'], $_POST['stadium'], $home, $group, $roster, $custom, $_POST['logo_db'] );
		} else {
			$del_logo = isset( $_POST['del_logo'] ) ? true : false;
			$overwrite_image = isset( $_POST['overwrite_image'] ) ? true: false;
			$this->editTeam( $_POST['team_id'], $_POST['team'], $_POST['website'], $_POST['coach'], $_POST['stadium'], $home, $group, $roster, $custom, $_POST['logo_db'], $del_logo, $overwrite_image );
		}
	} elseif ( 'match' == $_POST['updateLeague'] ) {
		check_admin_referer('leaguemanager_manage-matches');
		
		$group = isset($_POST['group']) ? $_POST['group'] : '';
		if ( 'add' == $_POST['mode'] ) {
			$num_matches = count($_POST['match']);
			foreach ( $_POST['match'] AS $i => $match_id ) {
				if ( isset($_POST['add_match'][$i]) || $_POST['away_team'][$i] != $_POST['home_team'][$i]  ) {
					$index = ( isset($_POST['year'][$i]) && isset($_POST['month'][$i]) && isset($_POST['day'][$i]) ) ? $i : 0;
					$date = $_POST['year'][$index].'-'.$_POST['month'][$index].'-'.$_POST['day'][$index].' '.$_POST['begin_hour'][$i].':'.$_POST['begin_minutes'][$i].':00';
					$match_day = is_array($_POST['match_day']) ? $_POST['match_day'][$i] : $_POST['match_day'];
					$custom = isset($_POST['custom']) ? $_POST['custom'][$i] : array();

					$this->addMatch( $date, $_POST['home_team'][$i], $_POST['away_team'][$i], $match_day, $_POST['location'][$i], $_POST['league_id'], $_POST['season'], $group, $_POST['final'], $custom );
				} else {
					$num_matches -= 1;
				}
			}
			$leaguemanager->setMessage(sprintf(__ngettext('%d Match added', '%d Matches added', $num_matches, 'leaguemanager'), $num_matches));
		} else {
			$num_matches = count($_POST['match']);
			foreach ( $_POST['match'] AS $i => $match_id ) {
				$index = ( isset($_POST['year'][$i]) && isset($_POST['month'][$i]) && isset($_POST['day'][$i]) ) ? $i : 0;
				$date = $_POST['year'][$index].'-'.$_POST['month'][$index].'-'.$_POST['day'][$index].' '.$_POST['begin_hour'][$i].':'.$_POST['begin_minutes'][$i].':00';
				$custom = isset($_POST['custom']) ? $_POST['custom'][$i] : array();
				$this->editMatch( $date, $_POST['home_team'][$i], $_POST['away_team'][$i], $_POST['match_day'], $_POST['location'][$i], $_POST['league_id'], $match_id, $group, $_POST['final'], $custom );
			}
			$leaguemanager->setMessage(sprintf(__ngettext('%d Match updated', '%d Matches updated', $num_matches, 'leaguemanager'), $num_matches));
		}
	} elseif ( 'results' == $_POST['updateLeague'] ) {
		check_admin_referer('matches-bulk');
		$this->updateResults( $_POST['league_id'], $_POST['matches'], $_POST['home_points'], $_POST['away_points'], $_POST['home_team'], $_POST['away_team'], $_POST['custom'] );
	} elseif ( 'teams_manual' == $_POST['updateLeague'] ) {
		check_admin_referer('teams-bulk');
		$this->saveStandingsManually( $_POST['team_id'], $_POST['points_plus'], $_POST['points_minus'], $_POST['num_done_matches'], $_POST['num_won_matches'], $_POST['num_draw_matches'], $_POST['num_lost_matches'], $_POST['add_points'], $_POST['custom'] );

		$leaguemanager->setMessage(__('Standings Table updated','leaguemanager'));
	}
	$leaguemanager->printMessage();
}  elseif ( isset($_POST['doaction']) || isset($_POST['doaction2']) ) {
	if ( isset($_POST['doaction']) && $_POST['action'] == "delete" ) {
		check_admin_referer('teams-bulk');
		foreach ( $_POST['team'] AS $team_id )
			$this->delTeam( $team_id, true );
	} elseif ( isset($_POST['doaction2']) && $_POST['action2'] == "delete" ) {
		check_admin_referer('matches-bulk');
		foreach ( $_POST['match'] AS $match_id )
			$this->delMatch( $match_id );
	}
}

$league = $leaguemanager->getCurrentLeague();
$season = $leaguemanager->getSeason($league);
$leaguemanager->setSeason($season);

// check if league is a cup championship
$cup = ( $league->mode == 'championship' ) ? true : false;

$group = isset($_GET['group']) ? htmlspecialchars($_GET['group']) : '';

$team_search = '`league_id` = "'.$league->id.'" AND `season` = "'.$season['name'].'"';
$team_list = $leaguemanager->getTeams( $team_search, "`id` ASC", 'ARRAY' );
$options = get_option('leaguemanager');

$match_search = '`league_id` = "'.$league->id.'" AND `final` = ""';

if ( $season )
	$match_search .= " AND `season` = '".$season['name']."'";
if ( isset($_POST['doaction3']) && $_POST['match_day'] != -1 ) {
	$leaguemanager->setMatchDay($_POST['match_day']);
	$match_search .= " AND `match_day` = '".$_POST['match_day']."'";
}

if ( empty($league->seasons)  ) {
	$leaguemanager->setMessage( __( 'You need to add at least one season', 'leaguemanager' ), true );
	$leaguemanager->printMessage();
}


if ( $league->mode != 'championship' ) {
	$teams = $leaguemanager->getTeams( $team_search );
	$matches = $leaguemanager->getMatches( $match_search );
}
?>
<div class="wrap">
	<p class="leaguemanager_breadcrumb"><a href="admin.php?page=leaguemanager"><?php _e( 'Leaguemanager', 'leaguemanager' ) ?></a> &raquo; <?php echo $league->title ?></p>
	
	<h2><?php echo $league->title ?></h2>
	
	<?php if ( !empty($league->seasons) ) : ?>
	<!-- Season Dropdown -->
	<div class="alignright" style="clear: both;">
	<form action="admin.php" method="get" style="display: inline;">
		<input type="hidden" name="page" value="leaguemanager" />
		<input type="hidden" name="subpage" value="show-league" />
		<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />
		<label for="season" style="vertical-align: middle;"><?php _e( 'Season', 'leaguemanager' ) ?></label>
		<select size="1" name="season" id="season">
		<?php foreach ( $league->seasons AS $s ) : ?>
			<option value="<?php echo $s['name'] ?>"<?php if ( $s['name'] == $season['name'] ) echo ' selected="selected"' ?>><?php echo $s['name'] ?></option>	
		<?php endforeach; ?>
		</select>
		<input type="submit" value="<?php _e( 'Show', 'leaguemanager' ) ?>" class="button" />
	</form>
	</div>
	<?php endif; ?>
	
	<!-- League Menu -->
	<ul class="subsubsub">
	<?php foreach ( $this->getMenu() AS $key => $menu ) : ?>
	<?php if ( !isset($menu['show']) || $menu['show'] ) : ?>
		<li><a href="admin.php?page=leaguemanager&amp;subpage=<?php echo $key ?>&amp;league_id=<?php echo $league->id ?>&amp;season=<?php echo $season['name'] ?>&amp;group=<?php echo $group ?>"><?php echo $menu['title'] ?></a></li>
	<?php endif; ?>
	<?php endforeach; ?>
	</ul>
	
	
	<?php if ( $league->mode == 'championship' ) : ?>
		<?php include('championship.php'); ?>
	<?php else : ?>
		<h3 style="clear: both;"><?php _e( 'Table', 'leaguemanager' ) ?></h3>
		<?php include_once('standings.php'); ?>

		<h3><?php _e( 'Match Plan','leaguemanager' ) ?></h3>
		<?php include('matches.php'); ?>
	<?php endif; ?>
</div>
