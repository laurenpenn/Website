<?php
     if ( !current_user_can( 'manage_leagues' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

if ( isset($_POST['import']) ) {
	check_admin_referer('leaguemanager_import-datasets');
	$this->import( $_POST['league_id'], $_FILES['leaguemanager_import'], $_POST['delimiter'], $_POST['mode'] );
     	$this->printMessage();
}
?>

<div class="wrap narrow">
	<h2><?php _e('LeagueManager Import') ?></h2>
	
	<p><?php _e( 'Choose a CSV file to upload and import data from', 'leaguemanager') ?></p>
	
	<form action="" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field( 'leaguemanager_import-datasets' ) ?>
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="leaguemanager_import"><?php _e('File','leaguemanager') ?></label></th><td><input type="file" name="leaguemanager_import" id="leaguemanager_import" size="40"/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="delimiter"><?php _e('Delimiter','leaguemanager') ?></label></th><td><input type="text" name="delimiter" id="delimiter" value="TAB" size="2" /><p><?php _e('For tab delimited files use TAB as delimiter', 'leaguemanager') ?></td>
	</tr>
	<tr>
		<th scope="row"><label for="league_id"><?php _e( 'League', 'leaguemanager' ) ?></label></th>
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

	<p class="submit"><input type="submit" name="import" value="<?php _e( 'Upload file and import' ); ?>" class="button" /></p>
	</form>
</div>
<?php endif; ?>
