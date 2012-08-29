<?php
if ( !current_user_can( 'manage_leagues' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

global $lmStats, $lmAJAX;

if ( isset($_POST['add_stats']) ) {
	if ( empty($_POST['stats_id']) ) {
		$lmStats->add($_POST['stat_name'], $_POST['fields'], $_POST['league_id']);
	} else {
		$lmStats->edit($_POST['stat_name'], $_POST['fields'], $_POST['stats_id']);
	}
	$leaguemanager->printMessage();
} elseif ( isset($_POST['doaction']) ) {
	check_admin_referer('stats-bulk');
	if ( 'delete' == $_POST['action'] ) {
		foreach ( $_POST['stat_id'] AS $stat_id ) {
			$lmStats->del( $stat_id );
		}
	}
} elseif ( isset($_POST['updateMatchStats']) ) {
	$lmStats->save($_POST['match_id'], $_POST['stats']);
}

$match = false;
if ( isset($_GET['match_id']) ) {
	$match_id = (int)$_GET['match_id'];
	$match = $leaguemanager->getMatch( $match_id );
	$league = $leaguemanager->getLeague($match->league_id);

	$home = $leaguemanager->getTeam($match->home_team);
	$away = $leaguemanager->getTeam($match->away_team);

	// Load ProjectManager Bridge
	$roster = array();
	if ( $league->hasBridge ) {
		$lmBridge->setProjectID( $league->project_id );

		$home->teamRoster = $lmBridge->getTeamRoster( $home->roster );
		$away->teamRoster = $lmBridge->getTeamRoster( $away->roster );

		if ( $home->teamRoster )
			$roster[$home->title] = $home->teamRoster;
		if ( $away->teamRoster )
			$roster[$away->title] = $away->teamRoster;
	} else {
		$home->teamRoster = $away->teamRoster = false;
	}
} else {
	$league_id = (int)$_GET['league_id'];
	$stats_id = isset($_GET['edit']) ? (int)$_GET['edit'] : false;
	$statistic = $lmStats->get( false, $stats_id );
	$league = $leaguemanager->getLeague($league_id);
}
?>

<div class="wrap">
	<p class="leaguemanager_breadcrumb"><a href="admin.php?page=leaguemanager"><?php _e( 'Leaguemanager', 'leaguemanager' ) ?></a> &raquo; <a href="admin.php?page=leaguemanager&amp;subpage=show-league&amp;league_id=<?php echo $league->id ?>"><?php echo $league->title ?></a> &raquo; <?php _e( 'Match Statistics', 'leaguemanager' ) ?></p>

<?php if ( $match ) : ?>

	<h2><?php printf(__( 'Match Statistics &#8211; %s v.s. %s', 'leaguemanager'), $home->title, $away->title) ?></h2>

	<form action="" method="post">
	<?php foreach ( $lmStats->get($league->id) AS $stat ) : ?>
		<h3><?php echo $stat->name ?></h3>
		
		<table class="widefat">
		<thead>
			<tr>
			<?php foreach ( (array)maybe_unserialize($stat->fields) AS $field ) : ?>
				<th scope="col"><?php echo $field['name'] ?></th>
			<?php endforeach; ?>
			<th scope="col">&#160;</th>
			</tr>
		</thead>
		<tbody id="stat_<?php echo sanitize_title($stat->name) ?>" class="form-table">
		<?php $class = ''; ?>
		<?php $stat->key = sanitize_title($stat->name); $data = $match->{$stat->key}; ?>
		<?php foreach ( (array)$data AS $i => $values ) : ?>
		<?php $class = ( 'alternate' == $class ) ? '' : 'alternate'; ?>
		<tr id="<?php echo $stat->key ?>_<?php echo $i ?>" class="<?php echo $class ?>">
		<?php $stat->fields = maybe_unserialize($stat->fields) ?>
		<?php foreach ( (array)$stat->fields AS $x => $field ) : ?>
		<td>
			<input type="text" size="10" name="stats[<?php echo $stat->key ?>][<?php echo $i ?>][<?php echo sanitize_title($field['name']) ?>]" id="<?php echo $stat->key ?>_<?php echo sanitize_title($field['name']) ?>_<?php echo $i ?>" value="<?php echo $values[sanitize_title($field['name'])] ?>" />
			<?php if ( 'roster' == $field['type'] && !empty($roster) ) : ?>
			<div id="<?php echo $stat->key ?>_roster_box_<?php echo $i ?>_<?php echo $x ?>" style="display: none; overflow: auto;" class="leaguemanager_thickbox">
				<?php echo $lmBridge->getTeamRosterSelection($roster, $values[sanitize_title($field['name'])], $stat->key."_".sanitize_title($field['name'])."_roster_".$i); ?>
				<div style='text-align: center; margin-top: 1em;'><input type="button" value="<?php _e('Insert', 'leaguemanager') ?>" class="button-secondary" onClick="Leaguemanager.insertPlayer('<?php echo $stat->key ?>_<?php echo sanitize_title($field['name']) ?>_roster_<?php echo $i ?>', '<?php echo $stat->key ?>_<?php echo sanitize_title($field['name']) ?>_<?php echo $i ?>'); return false;" />&#160;<input type="button" value="<?php _e('Cancel', 'leaguemanager') ?>" class="button-secondary" onClick="tb_remove();" /></div>
				</div>

				<span class="team_roster"><a class="thickbox" href="#TB_inline&height=100&width=300&inlineId=<?php echo $stat->key ?>_roster_box_<?php echo $i ?>_<?php echo $x ?>" title="<?php _e( 'Add Player from Team Roster', 'leaguemanager' ) ?>"><img src="<?php echo LEAGUEMANAGER_URL ?>/admin/icons/player.png" border="0" alt="<?php _e('Insert Player', 'leaguemanager') ?>" /></a></span>
			<?php endif; ?>
		</td>
		<?php endforeach; ?>
		<td style="text-align: center; width: 12px; vertical-align: middle;"><a class="image_link" href="#" onclick='return Leaguemanager.removeField("<?php echo $stat->key ?>_<?php echo $i ?>", "stat_<?php echo $stat->key ?>");'><img src="<?php echo LEAGUEMANAGER_URL ?>/admin/icons/trash.gif" alt="<?php _e( 'Delete', 'leaguemanager' ) ?>" title="<?php _e( 'Delete', 'leaguemanager' ) ?>" /></a></td>
		</tr>

<?php //echo $lmAJAX->addMatchStatField( sanitize_title($stat->name), $match->{$stat_key}[$field_id], sanitize_title($field['name']), $class, false ) ?>
		<?php endforeach; ?>
		</tbody>
		</table>
		
		<p><a href='#' onclick='return Leaguemanager.addStat("stat_<?php echo $stat->key ?>", <?php echo $stat->id ?>, <?php echo $match->id ?>);'><?php _e( 'Insert more', 'leaguemanager' ) ?></a></p>
	<?php endforeach; ?>


		<input type="hidden" name="match_id" value="<?php echo $match->id ?>" />
		<p class="submit"><input type="submit" name="updateMatchStats" value="<?php _e( 'Save Statistics', 'leaguemanager' ) ?> &raquo;" class="button" /></p>

		</form>
<?php else : ?>
<div class="narrow">

<h2><?php _e( 'Statistics Settings', 'leaguemanager' ) ?></h2>

<form id="stats-filter" action="" method="post" name="stats">
<?php wp_nonce_field( 'stats-bulk' ) ?>
			
	<div class="tablenav" style="margin-bottom: 0.1em;">
	<!-- Bulk Actions -->
		<select name="action" size="1">
			<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
			<option value="delete"><?php _e('Delete')?></option>
		</select>
		<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
	</div>
		
	<table id="stats" class="widefat" summary="" title="<?php _e( 'Statistics', 'leaguemanager' ) ?>">
	<thead>
	<tr>
		<th scope="col" class="check-column"><input type="checkbox" onclick="Leaguemanager.checkAll(document.getElementById('stats-filter'));" /></th>
		<th scope="col"><?php _e( 'Name', 'leaguemanager' ) ?></th>
<!--		<th class="num" scope="col"><?php _e ( 'Number of Fields', 'leaguemanager' ) ?></th>-->
		<th scope="col"><?php _e( 'Actions', 'leaguemanager' ) ?></th>
	</tr>
	</thead>
	<tbody id="stats-list">
	<?php foreach ( (array)$lmStats->get($league->id) AS $stat ) : ?>
	<tr>
		<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $stat->id ?>" name="stat_id[<?php echo $stat->id ?>]" /></th>
		<td><?php echo $stat->name ?></td>
		<td><a href="admin.php?page=leaguemanager&amp;subpage=matchstats&amp;league_id=<?php echo $league->id ?>&amp;edit=<?php echo $stat->id ?>"><?php _e( 'Edit', 'leaguemanager' ) ?></a></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
</form>


<h3>
	<?php if ( $stats_id ) _e( 'Edit Statistics Field', 'leaguemanager' ); else _e( 'Add Statistics Field', 'leaguemanager' ) ?>&#160;
	<?php if ( $stats_id ) : ?>
	(<a href="admin.php?page=leaguemanager&amp;subpage=matchstats&amp;league_id=<?php echo $league->id ?>"><?php _e( 'Add New', 'leaguemanager') ?></a>)
	<?php endif; ?>
</h3>

<form action="" method="post">
<table class="form-table">
<thead>
	<tr>
		<th scope="row"><label for="stat_name"><?php _e( 'Name', 'leaguemanager' ) ?></label></th>
		<td><input type="text" name="stat_name" id="stat_name" value="<?php echo $statistic->name ?>" /></td>
		<td>&#160;</td>
	</tr>
	</thead>
	<tbody id="stats_fields">
	<?php if ( $statistic ) :?>
	<?php $fields = maybe_unserialize($statistic->fields); ?>
	<?php foreach ( (array)$fields AS $key => $field ) : ?>
		<?php echo $lmStats->addStatsFIeld($key, $field['name'], $field['type'], false) ?>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
	</table>

	<p><a href="#" onClick="Leaguemanager.addStatsField()"><?php _e( 'Add Field', 'leaguemanager' ) ?></a></p>

	<input type="hidden" name="add_stats" value="<?php if ( $stats_id ) echo 'add'; else echo 'edit'; ?>" />
	<input type="hidden" name="stats_id" value="<?php echo $stats_id ?>" />
	<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />
	<p class="submit"><input type="submit" value="<?php if ( $stats_id ) _e( 'Edit', 'leaguemanager' ); else _e( 'Add New', 'leaguemanager' ) ?>" /></p>
</form>

</div>

<?php endif; ?>

<?php endif; ?>
