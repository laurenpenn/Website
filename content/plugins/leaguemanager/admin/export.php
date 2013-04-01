<?php
if ( !current_user_can( 'manage_leagues' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :
?>
<div class="wrap narrow">
	<h2><?php _e('LeagueManager Export', 'leaguemanager') ?></h2>
	<p><?php _e( 'Here you can export teams and matches for a specific league.', 'leaguemanager' ) ?></p>
	<p><?php _e('Once you&#8217;ve saved the download file, you can use the Import function on another WordPress blog to import this blog.'); ?></p>
	<form action="" method="post">
	<?php wp_nonce_field( 'leaguemanager_export-datasets' ) ?>
		<h3><?php _e('Options'); ?></h3>
		<table class="form-table">
		<tr>
			<th><label for="league_id"><?php _e('League', 'leaguemanager'); ?></label></th>
			<td>
				<?php if ( $leagues = $leaguemanager->getLeagues() ) : ?>
				<select size="1" name="league_id" id="league_id">
				<?php foreach ( $leagues AS $league ) : ?>
					<option value="<?php echo $league->id ?>"><?php echo $league->title ?></option>
				<?php endforeach; ?>
				</select>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="mode"><?php _e('Data', 'leaguemanager'); ?></label></th>
			<td>
				<select size="1" name="mode" id="mode">
					<option value="teams"><?php _e( 'Teams', 'leaguemanager' ) ?></option>
					<option value="matches"><?php _e( 'Matches', 'leaguemanager' ) ?></option>
				</select>
			</td>
		</tr>
		</table>
		<p class="submit"><input type="submit" name="leaguemanager_export" value="<?php _e('Download File'); ?>" class="button" /></p>
	</form>
</div>

<?php endif; ?>