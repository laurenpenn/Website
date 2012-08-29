<?php
/**
Template page to display single team

The following variables are usable:
	
	$league: league object
	$team: team object
	$next_match: next match object
	$prev_match: previous match object

	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>

<div class="teampage">

	<h3><?php echo $team->title ?></h3>

	<?php if ( isset($_GET['show']) ) : ?>
		<!-- Single Team Member -->
		<?php dataset($_GET['show']); ?>
	<?php else : ?>

	<p class="logo"><img src="<?php echo $team->logo ?>" alt="<?php _e( 'Logo', 'leaguemanager' ) ?>" /></p>
	
	<dl>
		<dt><?php _e( 'Rank', 'leaguemanager' ) ?></dt><dd><?php echo $team->rank ?></dd>
		<dt><?php _e( 'Matches', 'leaguemanager' ) ?></dt><dd><?php echo $team->done_matches ?></dd>
		<dt><?php _e( 'Won', 'leaguemanager' ) ?></dt><dd><?php echo $team->won_matches ?></dd>
		<dt><?php _e( 'Tied', 'leaguemanager' ) ?></dt><dd><?php echo $team->draw_matches ?></dd>
		<dt><?php _e( 'Lost', 'leaguemanager' ) ?></dt><dd><?php echo $team->lost_matches ?></dd>
		<dt><?php _e( 'Coach', 'leaguemanager' ) ?></dt><dd><?php echo $team->coach ?></dd>
		<dt><?php _e( 'Website', 'leaguemanager' ) ?></dt><dd><a href="http://<?php echo $team->website ?>" target="_blank"><?php echo $team->website ?></a></dd>
	</dl>

	<div class="matches">
	<?php if ( $next_match ) : ?>
	<div class="next_match">
		<h4><?php _e( 'Next Match','leaguemanager' ) ?></h4>
		<p class="match"><?php echo $next_match->match ?></p>
		<p class='match_day'><?php printf(__("<strong>%d.</strong> Match Day", 'leaguemanager'), $next_match->match_day); ?></p>

		<?php $time = ( '00:00' == $next_match->hour.":".$next_match->minutes ) ? '' : mysql2date(get_option('time_format'), $next_match->date); ?>
		<p class='match_date'><?php echo mysql2date("j. F Y", $next_match->date) ?>&#160;<span class='time'><?php echo $time ?></span> <span class='location'><?php echo $next_match->location ?></span></p>
	</div>
	<?php endif; ?>

	<?php if ( $prev_match ) : ?>
	<div class="prev_match">
		<h4><?php _e( 'Last Match','leaguemanager' ) ?></h4>
		<p class="match"><?php echo $prev_match->match ?></p>
		<p class='match_day'><?php printf(__("<strong>%d.</strong> Match Day", 'leaguemanager'), $prev_match->match_day); ?></p>
		<p class="score"><?php echo $prev_match->score ?></p>
	</div>
	<?php endif; ?>
	</div>

	<?php if ( !empty($team->roster['id']) && function_exists('project') ) : ?>
<!--		<h4 style="clear: both;"><?php _e( 'Team Roster', 'leaguemanager' ) ?></h4>-->
		<?php project($team->roster['id'], array('selections' => false) ); ?>
	<?php endif; ?>
	
	<?php endif; ?>
</div>
