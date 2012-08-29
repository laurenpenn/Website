<?php
/**
Template page for the Archive

The following variables are usable:
	
	$leagues: array of all leagues
	$seasons: available seasons of all leagues
	$league_id: ID of league
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<div id="leaguemanager_archive_selections">
	<form method="get" action="<?php get_permalink(get_the_ID()) ?>">
		<input type="hidden" name="page_id" value="<?php the_ID() ?>" />
		<select size="1" name="league_id">
			<option value=""><?php _e( 'League', 'leaguemanager' ) ?></option>
			<?php foreach ( $leagues AS $league ) : ?>
			<option value="<?php echo $league->id ?>"<?php if ( $league->id == $league_id ) echo ' selected="selected"' ?>><?php echo $league->title ?></option>
			<?php endforeach ?>
		</select>
		<select size="1" name="season">
			<option value=""><?php _e( 'Season', 'leaguemanager' ) ?></option>
			<?php foreach ( $seasons AS $s ) : ?>
			<option value="<?php echo $s ?>"<?php if ( $s == $league->season ) echo ' selected="selected"' ?>><?php echo $s ?></option>
			<?php endforeach ?>
		</select>
		<input type="submit" class="submit" value="<?php _e( 'Show' ) ?>" />
	</form>
</div>

<!-- Standings Table -->
<h4><?php _e('Standings', 'leaguemanager') ?></h4>
<?php leaguemanager_standings( $league_id, array( 'season' => $league->season ) ) ?>

<?php if ( !isset($_GET['team']) ) : ?>
<!-- Match Overview -->
<h4><?php _e('Matches', 'leaguemanager') ?></h4>
<?php leaguemanager_matches( $league_id, array('season' => $league->season, 'archive' => true) ) ?>
<?php endif; ?>
