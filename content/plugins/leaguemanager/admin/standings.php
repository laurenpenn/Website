<form id="teams-filter" action="" method="post" name="standings">
<?php wp_nonce_field( 'teams-bulk' ) ?>
			
	<div class="tablenav">
		<!-- Bulk Actions -->
		<select name="action" size="1">
			<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
			<option value="delete"><?php _e('Delete')?></option>
		</select>
		<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
	</div>
		
	<table id="standings" class="widefat" summary="" title="<?php _e( 'Table', 'leaguemanager' ) ?>">
	<thead>
	<tr>
		<th scope="col" class="check-column"><input type="checkbox" onclick="Leaguemanager.checkAll(document.getElementById('teams-filter'));" /></th>
		<th class="num"><?php _e( 'ID', 'leaguemanager' ) ?></th>
		<th class="num">#</th>
		<th class="num">&#160;</th>
		<th class="logo">&#160;</th>
		<th><?php _e( 'Club', 'leaguemanager' ) ?></th>
		<?php if ( !empty($league->groups) && $league->mode != 'championship' ) : ?><th class="num"><?php _e( 'Group', 'leaguemanager' ) ?></th><?php endif; ?>
		<th class="num"><?php if ( 1 == $league->standings['pld'] ) : ?><?php _e( 'Pld', 'leaguemanager' ) ?><?php endif; ?></th>
		<th class="num"><?php if ( 1 == $league->standings['won'] ) : ?><?php echo _c( 'W|Won','leaguemanager' ) ?><?php endif; ?></th>
		<th class="num"><?php if ( 1 == $league->standings['tie'] ) : ?><?php echo _c( 'T|Tie','leaguemanager' ) ?><?php endif; ?></th>
		<th class="num"><?php if ( 1 == $league->standings['lost'] ) : ?><?php echo _c( 'L|Lost','leaguemanager' ) ?><?php endif; ?></th>
		<?php do_action( 'leaguemanager_standings_header_'.$league->sport ) ?>
		<th class="num"><?php _e( 'Pts', 'leaguemanager' ) ?></th>
		<th class="num"><?php _e( '+/- Points', 'leaguemanager' ) ?></th>
	</tr>
	</thead>
	<tbody id="<?php echo ( $league->mode == 'championship' ) ? "the-list-standings-".$group : "the-list-standings" ?>" class="form-table">
	<?php if ( count($teams) > 0 ) : $class = ''; ?>
	<?php foreach( $teams AS $team ) : $class = ( 'alternate' == $class ) ? '' : 'alternate'; ?>
	<tr class="<?php echo $class ?>" id="team_<?php echo $team->id ?>">
		<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $team->id ?>" name="team[<?php echo $team->id ?>]" /></th>
		<td class="num"><?php echo $team->id ?></td>
		<td class="num"><?php echo $team->rank ?></td>
		<td class="num"><?php echo $team->status ?></td>
		<td class="logo">
		<?php if ( !empty($team->logo) ) : ?>
			<img src="<?php echo $leaguemanager->getThumbnailUrl($team->logo) ?>" alt="<?php _e( 'Logo', 'leaguemanager' ) ?>" title="<?php _e( 'Logo', 'leaguemanager' ) ?> <?php echo $team->title ?>" />
		<?php endif; ?>
		</td>
		<td><a href="admin.php?page=leaguemanager&amp;subpage=team&amp;edit=<?php echo $team->id; ?>"><?php echo $team->title ?></a></td>
		<?php if ( !empty($league->groups) && $league->mode != 'championship' ) : ?><td class="num"><?php echo $team->group ?></td><?php endif; ?>
		<?php if ( $league->point_rule != 'manual' ) : ?>
			<td class="num"><?php if ( 1 == $league->standings['pld'] ) : ?><?php echo $team->done_matches ?><?php endif; ?></td>
			<td class="num"><?php if ( 1 == $league->standings['won'] ) : ?><?php echo $team->won_matches ?><?php endif; ?></td>
			<td class="num"><?php if ( 1 == $league->standings['tie'] ) : ?><?php echo $team->draw_matches ?><?php endif; ?></td>
			<td class="num"><?php if ( 1 == $league->standings['lost'] ) : ?><?php echo $team->lost_matches ?><?php endif; ?></td>
		<?php else : ?>
			<td class="num">
				<?php if ( 1 == $league->standings['pld'] ) : ?>
				<input type="text" size="2" name="num_done_matches[<?php echo $team->id ?>]" value="<?php echo $team->done_matches  ?>" />
				<?php else : ?>
				<input type="hidden" name="num_done_matches[<?php echO $team->id ?>]" value="0" />
				<?php endif; ?>
			</td>
			<td class="num">
				<?php if ( 1 == $league->standings['won'] ) : ?>
				<input type="text" size="2" name="num_won_matches[<?php echo $team->id ?>]" value="<?php echo $team->won_matches  ?>" />
				<?php else : ?>
				<input type="hidden" name="num_won_matches[<?php echo $team->id ?>]" value="0" />
				<?php endif; ?>
			</td>
			<td class="num">
				<?php if ( 1 == $league->standings['tie'] ) : ?>
				<input type="text" size="2" name="num_draw_matches[<?php echo $team->id ?>]" value="<?php echo $team->draw_matches ?>" />
				<?php else : ?>
				<input type="hidden" name="num_draw_matches[<?php echo $team->id ?>]" value="0" />
				<?php endif; ?>
			</td>
			<td class="num">
				<?php if ( 1 == $league->standings['lost'] ) : ?>
				<input type="text" size="2" name="num_lost_matches[<?php echo $team->id ?>]" value="<?php echo $team->lost_matches ?>" />
				<?php else : ?>
				<input type="hidden" name="num_lost_matches[<?php echo $team->id ?>]" value="0" />
				<?php endif; ?>
			</td>

		<?php endif; ?>
		<?php do_action( 'leaguemanager_standings_columns_'.$league->sport, $team, $league->point_rule ) ?>
		<td class="num">
		<?php if ( $league->point_rule != 'manual' ) : ?>
			<?php printf($league->point_format, $team->points_plus+$team->add_points, $team->points_minus) ?>
		<?php else : ?>
			<input type="text" size="2" name="points_plus[<?php echo $team->id ?>]" value="<?php echo $team->points_plus + $team->add_points ?>" /> : <input type="text" size="2" name="points_minus[<?php echo $team->id ?>]" value="<?php echo $team->points_minus ?>" />
		<?php endif; ?>
		</td>
		<td class="num">
			<input type="text" size="2" name="add_points[<?php echo $team->id ?>]" value="<?php echo $team->add_points ?>" id="add_points_<?php echo $team->id ?>" onblur="Leaguemanager.saveAddPoints(<?php echo $team->id ?>)" /><span class="loading" id="loading_<?php echo $team->id ?>"></span>
		</td>
		<input type="hidden" name="team_id[<?php echo $team->id ?>]" value="<?php echo $team->id ?>" />
	</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
		
<?php if ( $league->team_ranking == 'manual' && $league->mode != 'championship' ) : ?>
<script type='text/javascript'>
// <![CDATA[
	Sortable.create("the-list-standings",
	{dropOnEmpty:true, tag: 'tr', ghosting:true, constraint:false, onUpdate: function() {Leaguemanager.saveStandings(Sortable.serialize('the-list-standings'))} });
//")
// ]]>
</script>
<?php endif; ?>
		
<?php if ( $league->point_rule == 'manual' ) : ?>
	<input type="hidden" name="updateLeague" value="teams_manual" />
	<p class="submit"><input type="submit" value="<?php _e( 'Save Standings', 'leaguemanager' ) ?> &raquo;" class="button" /></p>
<?php endif; ?>

</form>
