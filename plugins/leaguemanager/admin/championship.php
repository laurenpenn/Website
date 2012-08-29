<?php
global $championship;

$finalkey = isset($_GET['final']) ? $_GET['final'] : $championship->getFinalKeys(1);

$league = $championship->getLeague();
$season = $leaguemanager->getSeason( $league );
$num_first_round = $championship->getNumTeamsFirstRound();
$groups = $championship->getGroups();
if ( empty($group) ) $group = $groups[0];

if ( isset($_POST['updateFinalResults']) ) {
	if ( !is_numeric(end($_POST['home_team'])) ) {
		$leaguemanager->setMessage(__( "It seems the previous round is not over yet.", 'leaguemanager'), true);
		$leaguemanager->printMessage();
	} else {
		$custom = isset($_POST['custom']) ? $_POST['custom'] : false;
		$championship->updateResults($_POST['league_id'], $_POST['matches'], $_POST['home_points'], $_POST['away_points'], $_POST['home_team'], $_POST['away_team'], $custom, $_POST['round']);

	}
}
?>

<div class="wrap">
	<!--<p class="leaguemanager_breadcrumb"><a href="admin.php?page=leaguemanager"><?php _e( 'Leaguemanager', 'leaguemanager' ) ?></a> &raquo; <a href="admin.php?page=leaguemanager&amp;subpage=show-league&amp;league_id=<?php echo $league->id ?>"><?php echo $league->title ?></a> &raquo; <?php _e( 'Championship Finals', 'leaguemanager') ?></p>-->

	<div class="alignright" style="margin-right: 1em;">
		<form action="admin.php" method="get" style="display: inline;">
			<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']) ?>" />
			<input type="hidden" name="subpage" value="<?php echo htmlspecialchars($_GET['subpage']) ?>" />
			<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />

			<select name="group" size="1">
			<?php foreach ( $championship->getGroups() AS $key => $g ) : ?>
			<option value="<?php echo $g ?>"<?php selected($g, $group) ?>><?php printf(__('Group %s','leaguemanager'), $g) ?></option>
			<?php endforeach; ?>
			</select>
			<input type="submit" class="button-secondary" value="<?php _e( 'Show', 'leaguemanager' ) ?>" />
		</form>
	</div>
	
	<h3 style="clear: both;"><?php _e( 'Final Results', 'leaguemanager' ) ?></h3>
	
	<table class="widefat">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Round', 'leaguemanger' ) ?></th>
		<th scope="col" colspan="<?php echo ($num_first_round > 4) ? 4 : $num_first_round; ?>" style="text-align: center;"><?php _e( 'Matches', 'leaguemanager' ) ?></td>
	</tr>
	<tbody id="the-list-finals" class="form-table">
	<?php foreach ( $championship->getFinals() AS $final ) : $class = ( 'alternate' == $class ) ? '' : 'alternate'; ?>
	<?php
		if ( $matches = $leaguemanager->getMatches("`league_id` = '".$league->id."' AND `season` = '".$season['name']."' AND `final` = '".$final['key']."'", false, "`id` ASC") ) {
			$teams = $leaguemanager->getTeams( "league_id = '".$league->id."' AND `season` = '".$season['name']."'", false, 'ARRAY' );
			$teams2 = $championship->getFinalTeams( $final, 'ARRAY' );
		}
	?>
		<tr class="<?php echo $class ?>">
			<th scope="row"><strong><?php echo $final['name'] ?></strong></th>
			<?php for ( $i = 1; $i <= $final['num_matches']; $i++ ) : $match = $matches[$i-1]; ?>
			<?php $colspan = ( $num_first_round/2 >= 4 ) ? ceil(4/$final['num_matches']) : ceil(($num_first_round/2)/$final['num_matches']); ?>
			<td colspan="<?php echo $colspan ?>" style="text-align: center;">
				<?php if ( $match ) : ?>

				<?php 
				$match->hadPenalty = $match->hadPenalty = ( isset($match->penalty) && $match->penalty['home'] != '' && $match->penalty['away'] != '' ) ? true : false;
				$match->hadOvertime = $match->hadOvertime = ( isset($match->overtime) && $match->overtime['home'] != '' && $match->overtime['away'] != '' ) ? true : false;
				?>
				<?php if ( isset($teams[$match->home_team]) && isset($teams[$match->away_team]) ) : ?>
					<?php if ( $final['key'] == 'final' ) : ?>
					<p><span id="final_home" style="margin-right: 0.5em;"></span><?php printf('%s &#8211; %s', $teams[$match->home_team]['title'], $teams[$match->away_team]['title']) ?><span id="final_away" style="margin-left: 0.5em;"></span></p>
					<?php else : ?>
					<p><?php printf('%s &#8211; %s', $teams[$match->home_team]['title'], $teams[$match->away_team]['title']) ?></p>
					<?php endif; ?>

					<?php if ( $match->home_points != NULL && $match->away_points != NULL ) : ?>
						<?php if ( $final['key'] == 'final' ) : ?>
						<?php $field_id = ( $match->winner_id == $match->home_team ) ? "final_home" : "final_away"; ?>
						<script type="text/javascript">
							<?php $img = '<img style="vertical-align: middle;" src="'.LEAGUEMANAGER_URL . '/admin/icons/cup.png" />'; ?>
							jQuery('span#<?php echo $field_id ?>').html('<?php echo addslashes_gpc($img) ?>').fadeIn('fast');
						</script>
						<?php endif; ?>

						<?php
						if ( $match->hadPenalty )
							$match->score = sprintf("%d:%d", $match->penalty['home'], $match->penalty['away'])." "._c( 'o.P.|on penalty', 'leaguemanager' );
						elseif ( $match->hadOvertime )
							$match->score = sprintf("%d:%d", $match->overtime['home'], $match->overtime['away'])." "._c( 'AET|after extra time', 'leaguemanager' );
						else
							$match->score = sprintf("%d:%d", $match->home_points, $match->away_points);
						?>
						<p><strong><?php echo $match->score ?></strong></p>
					<?php else : ?>
						<p>-:-</p>
					<?php endif; ?>
				<?php else : ?>
					&#8211;
				<?php endif; ?>

				<?php endif; ?>
			</td>
			<?php if ( $i%4 == 0 && $i < $final['num_matches'] ) : ?>
			</tr><tr class="<?php echo $class ?>"><th>&#160;</th>
			<?php endif; ?>

			<?php endfor; ?>
		</tr>
	<?php endforeach ?>
	</tbody>
	</table>
	
	
	<h2><?php printf(__( 'Finals &#8211; %s', 'leaguemanager' ), $championship->getFinalName($finalkey)) ?></h2>

	<div class="tablenav">
	<form action="admin.php" method="get" style="display: inline;">
		<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']) ?>" />
		<input type="hidden" name="subpage" value="<?php echo htmlspecialchars($_GET['subpage']) ?>" />
		<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />

		<select size="1" name="final" id="final">
			<?php foreach ( $championship->getFinals() AS $final ) : ?>
			<option value="<?php echo $final['key'] ?>"<?php selected($finalkey, $final['key']) ?>><?php echo $final['name'] ?></option>	
			<?php endforeach; ?>
		</select>
		<input type="submit" class="button-secondary" value="<?php _e( 'Show', 'leaguemanager' ) ?>" />
	</form>
	<form action="admin.php" method="get" style="display: inline;">
		<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']) ?>" />
		<input type="hidden" name="subpage" value="match" />
		<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />

		<!-- Bulk Actions -->
		<select name="mode" size="1">
			<option value="-1" selected="selected"><?php _e('Actions', 'leaguemanager') ?></option>
			<option value="add"><?php _e('Add Matches', 'leaguemanager')?></option>
			<option value="edit"><?php _e( 'Edit Matches', 'leaguemanager' ) ?></option>
		</select>

		<select size="1" name="final" id="final1">
		<?php foreach ( $championship->getFinals() AS $final ) : ?>
			<option value="<?php echo $final['key'] ?>"><?php echo $final['name'] ?></option>
		<?php endforeach; ?>
		</select>
		<input type="submit" class="button-secondary" value="<?php _e( 'Go', 'leaguemanager' ) ?>" />
	</form>
	</div>

	<?php $final = $championship->getFinals($finalkey); ?>
	<!--<h3><?php echo $final['name'] ?></h3>-->
	<?php $teams = $leaguemanager->getTeams( "league_id = '".$league->id."' AND `season` = '".$season['name']."'", false, 'ARRAY' ); ?>
	<?php $teams2 = $championship->getFinalTeams( $final, 'ARRAY' ); ?>
	<?php $matches = $leaguemanager->getMatches("`league_id` = '".$league->id."' AND `final` = '".$final['key']."'", false, "`id` ASC"); ?>

	<form method="post" action="">
	<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />
	<input type="hidden" name="round" value="<?php echo $final['round'] ?>" />
	<table class="widefat">
	<thead>
	<tr>
		<th><?php _e( '#', 'leaguemanager' ) ?></th>
		<th><?php _e( 'Date','leaguemanager' ) ?></th>
		<th><?php _e( 'Match','leaguemanager' ) ?></th>
		<th><?php _e( 'Location','leaguemanager' ) ?></th>
		<th><?php _e( 'Begin','leaguemanager' ) ?></th>
		<th><?php _e( 'Score', 'leaguemanager' ) ?></th>
		<?php do_action( 'matchtable_header_'.$league->sport ); ?>
	</tr>
	</thead>
	<tbody id="the-list-<?php echo $final['key'] ?>" class="form-table">
	<?php for ( $i = 1; $i <= $final['num_matches']; $i++ ) : $match = $matches[$i-1]; ?>
		<?php if ( is_numeric($match->home_team) && is_numeric($match->away_team) )
			$title = sprintf("%s &#8211; %s", $teams[$match->home_team]['title'], $teams[$match->away_team]['title']);
		      else
			$title = sprintf("%s &#8211; %s", $teams2[$match->home_team], $teams2[$match->away_team]);
		?>
		<tr class="<?php echo $class ?>">
			<td><?php echo $i ?><input type="hidden" name="matches[<?php echo $match->id ?>]" value="<?php echo $match->id ?>" /><input type="hidden" name="home_team[<?php echo $match->id ?>]" value="<?php echo $match->home_team ?>" /><input type="hidden" name="away_team[<?php echo $match->id ?>]" value="<?php echo $match->away_team ?>" /></td>
			<td><?php echo ( substr($match->date, 0, 10) == '0000-00-00' ) ? 'N/A' : mysql2date(get_option('date_format'), $match->date) ?></td>
			<td><?php echo $title ?></td>
			<td><?php echo ( '' == $match->location ) ? 'N/A' : $match->location ?></td>
			<td><?php echo ( '00:00' == $match->hour.":".$match->minutes ) ? 'N/A' : mysql2date(get_option('time_format'), $match->date) ?></td>
			<td>
				<input class="points" type="text" size="2" id="home_points_<?php echo $match->id ?>_regular" name="home_points[<?php echo $match->id ?>]" value="<?php echo $match->home_points ?>" /> : <input class="points" type="text" size="2" id="away_points[<?php echo $match->id ?>]" name="away_points[<?php echo $match->id ?>]" value="<?php echo $match->away_points ?>" />
			</td>
			<?php do_action( 'matchtable_columns_'.$league->sport, $match ) ?>
		</tr>
	<?php endfor; ?>
	</tbody>
	</table>

	<p class="submit"><input type="submit" name="updateFinalResults" value="<?php _e( 'Save Results','leaguemanager' ) ?> &raquo;" class="button" /></p>
	</form>

	<h2><?php printf(__( 'Preliminary Rounds &#8211; Group %s', 'leaguemanager' ), $group) ?></h2>
	<div class="alignright">
		<form action="admin.php" method="get" style="display: inline;">
			<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']) ?>" />
			<input type="hidden" name="subpage" value="<?php echo htmlspecialchars($_GET['subpage']) ?>" />
			<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />

			<select name="group" size="1">
			<?php foreach ( $championship->getGroups() AS $key => $g ) : ?>
			<option value="<?php echo $g ?>"<?php selected($g, $group) ?>><?php printf(__('Group %s','leaguemanager'), $g) ?></option>
			<?php endforeach; ?>
			</select>
			<input type="submit" class="button-secondary" value="<?php _e( 'Show', 'leaguemanager' ) ?>" />
		</form>
	</div>

	<?php $teams = $leaguemanager->getTeams( "`league_id` = '".$league->id."' AND `season` = '".$season['name']."' AND `group` = '".$group."'" ); ?>
	<h3><?php _e( 'Table', 'leaguemanager' ) ?></h3>
	<?php include('standings.php'); ?>
	
	<?php $matches = $leaguemanager->getMatches( "`league_id`= '".$league->id."' AND `season` = '".$season['name']."' AND `final` = '' AND `group` = '".$group."'" ); ?>
	<h3><?php _e( 'Match Plan','leaguemanager' ) ?></h3>
	<?php include('matches.php'); ?>

	<?php $matches = $leaguemanager->getMatches( "`league_id`= '".$league->id."' AND `season` = '".$season['name']."' AND `final` = '' AND `group` = ''" ); ?>
	<?php if ( $matches ) : ?>
	<h3><?php _e( 'Inter Group Matches', 'leaguemanager' ) ?></h3>
	<?php include('matches.php'); ?>
	<?php endif; ?>
</div>
