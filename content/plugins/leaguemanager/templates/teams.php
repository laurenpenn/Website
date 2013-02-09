<?php
/**
Template page for Team List

The following variables are usable:
	
	$league league object
	$teams: all teams of league
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>

<?php if (isset($_GET['team_id'])) : ?>
	<?php leaguemanager_team($_GET['team_id']); ?>
<?php else : ?>

<?php if ( $teams ) : ?>

<table class="leaguemanager teamslist" summary="" title="<?php _e( 'Teams', 'leaguemanager' ) ?>">
<thead>
<tr>
	<th><?php _e( 'Team', 'leaguemanager' ) ?></th>
	<th><?php _e( 'Coach', 'leaguemanager' ) ?></th>
	<th><?php echo _c( 'W|Won', 'leaguemanager' ) ?></th>
	<th><?php echo _c( 'T|Tie', 'leaguemanager' ) ?></th>
	<th><?php echo _c( 'L|Lost', 'leaguemanager' ) ?></th>
	<th><?php _e( 'Website', 'leaguemanager' ) ?></th>
</tr>
</thead>
<tbody id="the-list">
<?php foreach ( $teams AS $team ) : $class = ('alternate' == $class) ? '' : 'alternate'; ?>
<?php $url = add_query_arg('team_id', $team->id, get_permalink()); ?>
<tr class="<?php echo $class ?>">
	<td><a href="<?php echo $url; ?>"><?php echo $team->title ?></a></td>
	<td><?php echo $team->coach ?></td>
	<td><?php echo $team->won_matches ?></td>
	<td><?php echo $team->draw_matches ?></td>
	<td><?php echo $team->lost_matches ?></td>
	<td><a href="http://<?php echo $team->website ?>" target="_blank"><?php echo $team->website ?></a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php endif; ?>

<?php endif; ?>
