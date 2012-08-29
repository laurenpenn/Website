<?php if ( !empty($season['num_match_days']) ) : ?>
<!-- Bulk Editing of Matches -->
<form action="admin.php" method="get" style="float: right;">
	<input type="hidden" name="page" value="leaguemanager" />
	<input type="hidden" name="subpage" value="match" />
	<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />
	<input type="hidden" name="season" value="<?php echo $season['name'] ?>" />
		
	<select size="1" name="match_day">
	<?php for ($i = 1; $i <= $season['num_match_days']; $i++) : ?>
		<option value="<?php echo $i ?>"><?php printf(__( '%d. Match Day', 'leaguemanager'), $i) ?></option>
	<?php endfor; ?>
	</select>
	<input type="submit" value="<?php _e('Edit Matches', 'leaguemanager'); ?>" class="button-secondary action" />
</form>
<?php endif; ?>

<form id="competitions-filter" action="" method="post">
<?php wp_nonce_field( 'matches-bulk' ) ?>
		
	<div class="tablenav" style="margin-bottom: 0.1em; clear: none;">
		<!-- Bulk Actions -->
		<select name="action2" size="1">
			<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
			<option value="delete"><?php _e('Delete')?></option>
		</select>
		<input type="submit" value="<?php _e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
			
		<?php if ( !empty($season['num_match_days']) ) : ?>
		<select size='1' name='match_day'>
			<?php $selected = ( !isset($_POST['doaction3']) || (isset($_POST['doaction3']) && $_POST['match_day'] == -1) ) ? ' selected="selected"' : ''; ?>
			<option value="-1"<?php echo $selected ?>><?php _e( 'Show all Matches', 'leaguemanager' ) ?></option>
			<?php for ($i = 1; $i <= $season['num_match_days']; $i++) : ?>
			<option value='<?php echo $i ?>'<?php if ($leaguemanager->getMatchDay() == $i && isset($_POST['doaction3']) && $_POST['doaction'] != -1 ) echo ' selected="selected"' ?>><?php printf(__( '%d. Match Day', 'leaguemanager'), $i) ?></option>
			<?php endfor; ?>
		</select>
		<input type='submit' name="doaction3" id="doaction3" class="button-secondary action" value='<?php _e( 'Filter' ) ?>' />
		<?php endif; ?>
	</div>
		
	<table class="widefat" summary="" title="<?php _e( 'Match Plan','leaguemanager' ) ?>" style="margin-bottom: 2em;">
	<thead>
	<tr>
		<th scope="col" class="check-column"><input type="checkbox" onclick="Leaguemanager.checkAll(document.getElementById('competitions-filter'));" /></th>
		<th><?php _e( 'ID', 'leaguemanager' ) ?></th>
		<th><?php _e( 'Date','leaguemanager' ) ?></th>
		<?php if ( !empty($league->groups) && $league->mode != 'championchp' ) : ?><th class="num"><?php _e( 'Group', 'leaguemanager' ) ?></th><?php endif; ?>
		<th><?php _e( 'Match','leaguemanager' ) ?></th>
		<th><?php _e( 'Location','leaguemanager' ) ?></th>
		<th><?php _e( 'Begin','leaguemanager' ) ?></th>
		<th><?php _e( 'Score', 'leaguemanager' ) ?></th>
		<?php do_action( 'matchtable_header_'.$league->sport ); ?>
	</tr>
	</thead>
	<tbody id="the-list-matches-<?php echo $group ?>" class="form-table">
	<?php if ( $matches ) : $class = ''; ?>
	<?php foreach ( $matches AS $match ) : $class = ( 'alternate' == $class ) ? '' : 'alternate'; ?>
	<?php $title = ( isset($match->title) && !empty($match->title) ) ? $match->title : $team_list[$match->home_team]['title'] . " - " . $team_list[$match->away_team]['title']; ?>
	<?php $title = apply_filters( 'leaguemanager_matchtitle_'.$league->sport, $title, $match, $team_list ); ?>

		<tr class="<?php echo $class ?>">
			<th scope="row" class="check-column"><input type="hidden" name="matches[<?php echo $match->id ?>]" value="<?php echo $match->id ?>" /><input type="hidden" name="home_team[<?php echo $match->id ?>]" value="<?php echo $match->home_team ?>" /><input type="hidden" name="away_team[<?php echo $match->id ?>]" value="<?php echo $match->away_team ?>" /><input type="checkbox" value="<?php echo $match->id ?>" name="match[<?php echo $match->id ?>]" /></th>
			<td><?php echo $match->id ?></td>
			<td><?php echo ( substr($match->date, 0, 10) == '0000-00-00' ) ? 'N/A' : mysql2date(get_option('date_format'), $match->date) ?></td>
			<?php if ( !empty($league->groups) ) : ?><td class="num"><?php echo $match->group ?></td><?php endif; ?>
			<td><a href="admin.php?page=leaguemanager&amp;subpage=match&amp;edit=<?php echo $match->id ?>&amp;season=<?php echo $season['name'] ?>"><?php echo $title ?></a></td>
			<td><?php echo ( empty($match->location) ) ? 'N/A' : $match->location ?></td>
			<td><?php echo ( '00:00' == $match->hour.":".$match->minutes ) ? 'N/A' : mysql2date(get_option('time_format'), $match->date) ?></td>
			<td>
				<input class="points" type="text" size="2" id="home_points_<?php echo $match->id ?>_regular" name="home_points[<?php echo $match->id ?>]" value="<?php echo $match->home_points ?>" /> : <input class="points" type="text" size="2" id="away_points[<?php echo $match->id ?>]" name="away_points[<?php echo $match->id ?>]" value="<?php echo $match->away_points ?>" />
			</td>
			<?php do_action( 'matchtable_columns_'.$league->sport, $match ) ?>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
	</table>

	<?php do_action ( 'leaguemanager_match_administration_descriptions' ) ?>	

	<?php if ( $matches ) : ?>
		<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />
		<input type="hidden" name="updateLeague" value="results" />
		<p style="margin: 0;" class="submit"><input type="submit" name="updateResults" value="<?php _e( 'Update Results','leaguemanager' ) ?> &raquo;" class="button" /></p>
	<?php endif; ?>
</form>
