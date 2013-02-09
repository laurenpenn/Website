<?php
if ( !current_user_can( 'manage_leagues' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
	
else :
	if ( isset($_POST['saveSeason']) ) {
		if ( !empty($_POST['season']) ) {
			if ( empty($_POST['season_id']) ) {
				$add_teams = isset($_POST['no_add_teams']) ? false : true;
				$this->saveSeason( $_POST['season'], $_POST['num_match_days'], $add_teams );
			} else {
				$this->saveSeason( $_POST['season'], $_POST['num_match_days'], false, $_POST['season_id'] );
			}
		} else {
			$leaguemanager->setMessage( __( 'Season was empty', 'leaguemanager' ), true );
			$leaguemanager->printMessage();
		}
	} elseif ( isset($_POST['doaction']) ) {
		check_admin_referer('seasons-bulk');
		$league = $leaguemanager->getCurrentLeague();
		if ( 'delete' == $_POST['action'] ) {
			$this->delSeasons( $_POST['del_season'], $league->id );
		}
	}

	$league = $leaguemanager->getCurrentLeague();
	
	$season_id = false;
	$season_data = array('name' => '', 'num_match_days' => '');
	if ( isset($_GET['edit']) ) {
		$season_id = $_GET['edit'];
		$season_data = $league->seasons[$season_id];
	}
?>
<div class="wrap">
	<p class="leaguemanager_breadcrumb"><a href="admin.php?page=leaguemanager"><?php _e( 'Leaguemanager', 'leaguemanager' ) ?></a> &raquo; <a href="admin.php?page=leaguemanager&amp;subpage=show-league&amp;league_id=<?php echo $league->id ?>"><?php echo $league->title ?></a> &raquo; <?php _e( 'Seasons', 'leaguemanager' ) ?></p>

	<div class="narrow">

	<h2><?php _e( 'Seasons', 'leaguemanager' ) ?></h2>
	<form id="seaons-filter" action="" method="post">
		<?php wp_nonce_field( 'seasons-bulk' ) ?>
		
		<div class="tablenav" style="margin-bottom: 0.1em;">
			<!-- Bulk Actions -->
			<select name="action" size="1">
				<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
				<option value="delete"><?php _e('Delete')?></option>
			</select>
			<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
		</div>
		<table class="widefat">
		<thead>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" onclick="Leaguemanager.checkAll(document.getElementById('seaons-filter'));" /></th>
			<th scope="col"><?php _e( 'Season', 'leaguemanager' ) ?></th>
			<th scope="col"><?php _e( 'Match Days', 'leaguemanager' ) ?></th>
			<th scope="col"><?php _e( 'Actions', 'leaguemanager' ) ?></th>
		</tr>
		</thead>
		<tbody id="the-list">
			<?php if ( !empty($league->seasons) ) : ?>
			<?php foreach( (array)$league->seasons AS $key => $season ) : $class = ( 'alternate' == $class ) ? '' : 'alternate' ?>
			<tr class="<?php echo $class ?>">
				<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $key ?>" name="del_season[<?php echo $key ?>]" /></th>
				<td><?php echo $season['name'] ?></td>
				<td><?php echo $season['num_match_days'] ?></td>
				<td><a href="admin.php?page=leaguemanager&amp;subpage=seasons&amp;league_id=<?php echo $league->id ?>&amp;edit=<?php echo $key ?>"><?php _e( 'Edit', 'leaguemanager' ) ?></a></td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
		</table>
	</form>
	

	<h3>
		<?php if ( $season_id ) _e('Edit Season', 'leaguemanager'); else _e( 'Add new Season', 'leaguemanager' ) ?>
		<?php if ( $season_id ) : ?>
		(<a href="admin.php?page=leaguemanager&amp;subpage=seasons&amp;league_id=<?php echo $league->id ?>"><?php _e( 'Add New', 'leaguemanager') ?></a>)
		<?php endif; ?>
	</h3>
	<form action="" method="post">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="season"><?php _e( 'Season', 'leaguemanager' ) ?></th>
				<td>
					<input type="text" name="season" id="season" value="<?php echo $season_data['name'] ?>" size="8" />&#160;<span class="setting-description"><?php _e('Usually 4-digit year, e.g. 2008. Can also be any kind of string, e.g. 0809', 'leaguemanager') ?></span><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="num_match_days"><?php _e( 'Number of Match Days', 'leaguemanager' ) ?></label></th>
				<td>
					<input type="text" name="num_match_days" id="num_match_days" value="<?php echo $season_data['num_match_days'] ?>" size="2" />
				</td>
			</tr>
			<?php if ( !$season_id ) : ?>
			<tr valign="top">
				<th scope="row"><label for="no_add_teams"><?php _e( 'No Teams', 'leaguemanager' ) ?></th>
				<td>
					<input type="checkbox" name="no_add_teams" id="no_add_teams" value="1" />&#160;<span class="setting-description"><?php _e( 'Check this to not automatically get teams from database and add them to the season', 'leaguemanager' ) ?></span>
				</td>
			</tr>
			<?php endif; ?>
		</table>

		<input type="hidden" name="season_id" value="<?php echo $season_id ?>" />
		<p class="submit"><input type="submit" name="saveSeason" class="button" value="<?php if ( !$season_id ) _e( 'Add Season', 'leaguemanager' ); else _e( 'Edit Season', 'leaguemanager' ); ?>" /></p>
	</form>

	</div>
</div>

<?php endif; ?>
