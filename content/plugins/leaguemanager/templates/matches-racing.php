<?php
/**
Template page for full racing results

The following variables are usable:
	
	$league: contains data of current league
	$matches: contains all matches for current league
	$teams: contains teams of current league in an assosiative array
	$season: current season
	$roster: ID of individual team member or false
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php if (isset($_GET['match']) ) : ?>
	<?php leaguemanager_match($_GET['match']); ?>
<?php else : ?>

<?php if ( $league->match_days && $league->mode != 'championchip' ) : ?>
<div style='float: left; margin-top: 1em;'>
<form method='get' action='<?php the_permalink(get_the_ID()) ?>'>
<div>
	<input type='hidden' name='page_id' value='<?php the_ID() ?>' />
	<input type="hidden" name="season" value="<?php echo $season ?>" />
	<input type="hidden" name="league_id" value="<?php echo $league->id ?>" />

	<select size='1' name='match_day'>
	<?php for ($i = 1; $i <= $league->num_match_days; $i++) : ?>
		<option value='<?php echo $i ?>'<?php if ($leaguemanager->getMatchDay($league->isCurrMatchDay) == $i) echo ' selected="selected"'?>><?php printf(__( '%d. Match Day', 'leaguemanager'), $i) ?></option>
	<?php endfor; ?>
	</select>
	<select size="1" name="team_id">
	<option value=""><?php _e( 'Choose Team', 'leaguemanager' ) ?></option>
	<?php foreach ( $teams AS $team_id => $team ) : ?>
		<?php $selected = (isset($_GET['team_id']) && $_GET['team_id'] == $team_id) ? ' selected="selected"' : ''; ?>
		<option value="<?php echo $team_id ?>"<?php echo $selected ?>><?php echo $team['title'] ?></option>
	<?php endforeach; ?>
	</select>
	<input type='submit' value='<?php _e('Show') ?>' />
</div>
</form>
</div>
<br style='clear: both;' />
<?php endif; ?>


<?php if ( $matches ) : ?>

<table class='leaguemanager matchtable' summary='' title='<?php echo __( 'Races', 'leaguemanager' )." ".$league->title ?>'>
<tr>
	<th><?php _e( 'Date', 'leaguemanager' ) ?></th>
	<?php if (!$roster) : ?>
	<th><?php _e( 'Name', 'leaguemanager' ) ?></th>
	<?php endif; ?>
	<th><?php _e( 'Event', 'leaguemanager' ) ?></th>
	<th><?php _e( 'Category', 'leaguemanager' ) ?></th>
	<th><?php _e( 'Race Type', 'leaguemanager' ) ?></th>
	<th><?php _e( 'Result', 'leaguemanager' ) ?></th>
	<th><?php _e( 'Other Info', 'leaguemanager' ) ?></th>
</tr>
<?php foreach ( $matches AS $match ) : ?>

<?php if ( !empty($match->raceresult) ) : ?>
<?php foreach ( $match->raceresult AS $id => $racer ) : ?>

<?php if ( !$roster || ( $roster && ($roster == $id || $roster == $racer['name']) ) ) : ?>
<?php $class = ( 'alternate' == $class ) ? '' : 'alternate'; ?>
<tr class='<?php echo $class ?>'>
	<td><?php echo $match->date ?></td>
	<?php if (!$roster) : ?>
	<td><?php echo $racer['name'] ?></td>
	<?php endif; ?>
	<td><a href="<?php echo $match->pageURL ?>"><?php echo $match->title ?></a></td>
	<td><?php echo $racer['category'] ?></td>
	<td><?php echo $match->racetype ?></td>
	<td><?php echo $racer['result'] ?></td>
	<td><?php echo $racer['info'] ?></td>
</tr>
<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>

<?php endforeach; ?>
</table>

<?php endif; ?>

<?php endif; ?>
