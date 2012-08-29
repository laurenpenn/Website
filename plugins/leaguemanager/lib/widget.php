<?php
/** Widget class for the WordPress plugin LeagueManager
* 
* @author 	Kolja Schleich
* @package	LeagueManager
* @copyright 	Copyright 2008-2009
*/

class LeagueManagerWidget extends WP_Widget
{
	/**
	 * index for matches in widget
	 *
	 * @var array
	 */
	var $match_index = array( 'next' => 0, 'prev' => 0 );


	/**
	 * initialize
	 *
	 * @param none
	 * @return void
	 */
	function __construct( $template = false )
	{
		add_action( 'leaguemanager_widget_next_match', array(&$this, 'showNextMatchBox'), 10, 3 );
		add_action( 'leaguemanager_widget_prev_match', array(&$this, 'showPrevMatchBox'), 10, 3 );

		if ( !$template ) {
			$widget_ops = array('classname' => 'leaguemanager_widget', 'description' => __('League results and upcoming matches at a glance', 'leaguemanager') );
			parent::__construct('leaguemanager-widget', __( 'League', 'leaguemanager' ), $widget_ops);
		}
		return;
	}
	function LeagueManagerWidget( $template = false )
	{
		$this->__construct($template);
	}
	
	
	/**
	 * get index for current match
	 *
	 * @param string $type next|prev
	 * @return the index
	 */
	function getMatchIndex( $type )
	{
		return $this->match_index[$type];
	}
	
	
	/**
	 * set index for current match
	 *
	 * @param int $index
	 * @param string $type
	 * @return void
	 */
	function setMatchIndex( $index, $type )
	{
		$this->match_index[$type] = $index;
	}
	
		
	/**
	 * displays widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance )
	{
		global $lmBridge, $lmShortcodes, $leaguemanager;

		$defaults = array(
			'before_widget' => '<li id="'.sanitize_title(get_class($this)).'" class="widget '.get_class($this).'_'.__FUNCTION__.'">',
			'after_widget' => '</li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
			'number' => $this->number,
		);
		$args = array_merge( $defaults, $args );
		extract( $args , EXTR_SKIP );
	
		$league = $leaguemanager->getLeague( $instance['league'] );
		if (empty($instance['season']))  $season = $leaguemanager->getSeason($league, false, 'name');

		echo $before_widget . $before_title . $league->title . " " . $season . $after_title;
		
		echo "<div class='leaguemanager_widget'>";
		if ( $instance['match_display'] != 'none' ) {
			$show_prev_matches = $show_next_matches = false;
			if ( $instance['match_display'] == 'prev' )
				$show_prev_matches = true;
			elseif ( $instance['match_display'] == 'next' )
				$show_next_matches = true;
			elseif ( $instance['match_display'] == 'all' )
				$show_prev_matches = $show_next_matches = true;
			
			if ( $show_next_matches ) {
				echo "<div id='next_matches_".$number."'>";
				do_action( 'leaguemanager_widget_next_match', $number, $instance );
				echo "</div>";
			}

			if ( $show_prev_matches ) {
				echo "<div id='prev_matches_".$number."'>";
				do_action( 'leaguemanager_widget_prev_match', $number, $instance );
				echo "</div>";
			}
	
		}
		
		if ( $instance['table'] != 'none' && !empty($instance['table']) ) {
			$show_logos = ( $instance['show_logos'] ) ? true : false;
			echo "<h4 class='standings'>". __( 'Table', 'leaguemanager' ). "</h4>";
			echo $lmShortcodes->showStandings( array('template' => $instance['table'], 'league_id' => $instance['league'], 'season' => $instance['season'], 'logo' => $show_logos, 'home' => $instance['home']), true );
		}

		echo "</div>";
		echo $after_widget;
	}


	/**
	 * show next match box
	 *
	 * @param int $number
	 * @param array $instance
	 * @param boolean $echo (optional)
	 * @return void
	 */
	function showNextMatchBox($number, $instance, $echo = true)
	{
		global $leaguemanager;

		$match_limit = ( intval($instance['match_limit']) > 0 ) ? $instance['match_limit'] : false;			
		$search = "`league_id` = '".$instance['league']."' AND `final` = '' AND `season` = '".$instance['season']."' AND TIMEDIFF(NOW(), `date`) <= 0";

		if ( isset($instance['home_only']) && $instance['home_only'] == 1 )
			$search .= $leaguemanager->buildHomeOnlyQuery($instance['league']);
			
		$matches = $leaguemanager->getMatches( $search, $match_limit );
		if ( $matches ) {
			$teams = $leaguemanager->getTeams( 'league_id = '.$instance['league'], "`id` ASC", 'ARRAY' );

			$curr = $this->getMatchIndex('next');
			$match = $matches[$curr];
			$match_limit_js = ( $match_limit ) ? $match_limit : 'false';
			
			$next_link = $prev_link = '';
			if ( $curr < count($matches) - 1 ) {
				$next_link = "<a class='next' href='#null' onclick='Leaguemanager.setMatchBox(".$curr.", \"next\", \"next\", ".$instance['league'].", \"".$match_limit_js."\", ".$number.", \"".$instance['season']."\", ".intval($instance['home_only']).", \"".$instance['date_format']."\"); return false'><img src='".LEAGUEMANAGER_URL."/images/arrow_right.png' alt='&raquo;' /></a>";
			}
			if ( $curr > 0 ) {
				$prev_link = "<a class='prev' href='#null' onclick='Leaguemanager.setMatchBox(".$curr.", \"prev\", \"next\", ".$instance['league'].", \"".$match_limit_js."\", ".$number.", \"".$instance['season']."\", ".intval($instance['home_only']).", \"".$instance['date_format']."\"); return false'><img src='".LEAGUEMANAGER_URL."/images/arrow_left.png' alt='&laquo;' /></a>";
			}
	
			$out = "<div id='next_match_box_".$number."' class='match_box'>";
			$out .= "<h4>$prev_link".__( 'Next Match', 'leaguemanager' )."$next_link</h4>";
						
			$out .= "<div class='match' id='match-".$match->id."'>";
							
			$home_team = $teams[$match->home_team]['title'];
			$away_team = $teams[$match->away_team]['title'];

			if ( !empty($teams[$match->home_team]['website']) )
				$home_team = "<a href='http://".$teams[$match->home_team]['website']."' target='_blank'>".$home_team."</a>";
			if ( $teams[$match->away_team]['website'] != '' )
				$away_team = "<a href='http://".$teams[$match->away_team]['website']."' target='_blank'>".$away_team."</a>";
								
			if ( !isset($match->title) ) $match->title = sprintf("%s &#8211; %s", $home_team, $away_team);

			$out .= "<p class='match_title'><strong>". $match->title."</strong></p>";
			$out .= "<p class='logos'><img class='home_logo' src='".$teams[$match->home_team]['logo']."' alt='' /><img class='away_logo' src='".$teams[$match->away_team]['logo']."' alt='' /></p>";

			if ( !empty($match->match_day) )
			$out .= "<p class='match_day'>".sprintf(__("<strong>%d.</strong> Match Day", 'leaguemanager'), $match->match_day)."</p>";
			
			$time = ( '00:00' == $match->hour.":".$match->minutes ) ? '' : mysql2date(get_option('time_format'), $match->date);
			$out .= "<p class='date'>".mysql2date($instance['date_format'], $match->date).", <span class='time'>".$time."</span></p>";
			$out .= "<p class='location'>".$match->location."</p>";
			
			$out .= "</div></div>";
		
	
			if ( $echo )
				echo $out;
				
			return $out;
		}
	}
	
	
	/**
	 * show previous match box
	 *
	 * @param int $number
	 * @param array $instance
	 * @param boolean $echo (optional)
	 * @return void
	 */
	function showPrevMatchBox($number, $instance, $echo = true)
	{
		global $leaguemanager;

		$match_limit = ( intval($instance['match_limit']) > 0 ) ? $instance['match_limit'] : false;			
		$search = "`league_id` = '".$instance['league']."' AND `final` = '' AND `season` = '".$instance['season']."' AND TIMEDIFF(NOW(), `date`) > 0";

		if ( isset($instance['home_only']) && $instance['home_only'] == 1 )
			$search .= $leaguemanager->buildHomeOnlyQuery($instance['league']);

		$matches = $leaguemanager->getMatches( $search, $match_limit, '`date` DESC, `id` DESC' );
		if ( $matches ) {
			$teams = $leaguemanager->getTeams( 'league_id = '.$instance['league'], "`id` ASC", 'ARRAY' );

			$curr = $this->getMatchIndex('prev');
			$match = $matches[$curr];
			$match_limit_js = ( $match_limit ) ? $match_limit : 'false';
			
			$next_link = $prev_link = '';
			if ( $curr < count($matches) - 1 ) {
				$next_link = "<a class='next' href='#null' onclick='Leaguemanager.setMatchBox(".$curr.", \"next\", \"prev\", ".$instance['league'].", \"".$match_limit_js."\", ".$number.", ".$instance['season'].", ".intval($instance['home_only']).", \"".$instance['date_format']."\"); return false'><img src='".LEAGUEMANAGER_URL."/images/arrow_right.png' alt='&raquo;' /></a>";
			}
			if ( $curr > 0 ) {
				$prev_link = "<a class='prev' href='#null' onclick='Leaguemanager.setMatchBox(".$curr.", \"prev\", \"prev\", ".$instance['league'].", \"".$match_limit_js."\", ".$number.", ".$instance['season'].", ".intval($instance['home_only']).", \"".$instance['date_format']."\"); return false'><img src='".LEAGUEMANAGER_URL."/images/arrow_left.png' alt='&laquo;' /></a>";
			}
					
			$out = "<div id='prev_match_box_".$number."' class='match_box'>";
			$out .= "<h4>$prev_link".__( 'Last Match', 'leaguemanager' )."$next_link</h4>";
										
			
			$out .= "<div class='match' id='match-".$match->id."'>";
			
			$match->hadOvertime = ( isset($match->overtime) && $match->overtime['home'] != '' && $match->overtime['away'] != '' ) ? true : false;
			$match->hadPenalty = ( isset($match->penalty) && $match->penalty['home'] != '' && $match->penalty['away'] != '' ) ? true : false;

			$home_team = $teams[$match->home_team]['title'];
			$away_team = $teams[$match->away_team]['title'];

			if ( !empty($teams[$match->home_team]['website']) )
				$home_team = "<a href='http://".$teams[$match->home_team]['website']."' target='_blank'>".$home_team."</a>";
			if ( $teams[$match->away_team]['website'] != '' )
				$away_team = "<a href='http://".$teams[$match->away_team]['website']."' target='_blank'>".$away_team."</a>";
								
			if ( !isset($match->title) ) $match->title = sprintf("%s &#8211; %s", $home_team, $away_team);

			if ( $match->hadPenalty )
				$score = sprintf("%d - %d", $match->penalty['home'], $match->penalty['away'])." "._c( 'o.P.|on penalty', 'leaguemanager' );
			elseif ( $match->hadOvertime )
				$score = sprintf("%d - %d", $match->overtime['home'], $match->overtime['away'])." "._c( 'AET|after extra time', 'leaguemanager' );
			else
				$score = sprintf("%d - %d", $match->home_points, $match->away_points);

			$out .= "<p class='match_title'><strong>". $match->title."</strong></p>";
			$out .= "<p class='logos'><img class='home_logo' src='".$teams[$match->home_team]['logo']."' alt='' /><span class='result'>".$score."</span><img class='away_logo' src='".$teams[$match->away_team]['logo']."' alt='' /></p>";

			if ( !empty($match->match_day) )
			$out .= "<p class='match_day'>".sprintf(__("<strong>%d.</strong> Match Day", 'leaguemanager'), $match->match_day)."</p>";
			
			$time = ( '00:00' == $match->hour.":".$match->minutes ) ? '' : mysql2date(get_option('time_format'), $match->date);

			if ( $match->post_id != 0 && $instance['report'] == 1 )
				$out .=  "<p class='report'><a href='".get_permalink($match->post_id)."'>".__( 'Report', 'leaguemanager' )."&raquo;</a></p>";
					
			$out .= "</div></div>";
		
			if ( $echo )
				echo $out;
			
			return $out;
		}
	}
	
	
	/**
	 * save settings
	 *
	 * @param array $new_instance
	 * @param $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		return $new_instance;
	}


	/**
	 * widget control panel
	 *
	 * @param int|array $widget_args
	 */
	function form( $instance )
	{
		global $leaguemanager;
		echo '<div class="leaguemanager_widget_control" id="leaguemanager_widget_control_'.$this->number.'">';
		echo '<p><label for="'.$this->get_field_id('league').'">'.__('League','leaguemanager').'</label>';
		echo '<select size="1" name="'.$this->get_field_name('league').'" id="'.$this->get_field_id('league').'">';
		foreach ( $leaguemanager->getLeagues() AS $league ) {
			$selected = ( $instance['league'] == $league->id ) ? ' selected="seleccted"' : '';
			echo '<option value="'.$league->id.'"'.$selected.'>'.$league->title.'</option>';
		}
		echo '</select>';
		echo '<p><label for="'.$this->get_field_id('season').'">'.__('Season','leaguemanager').'</label><input type="text" name="'.$this->get_field_name('season').'" id="'.$this->get_field_id('season').'" size="8" value="'.$instance['season'].'" /></p>';

		echo '<p><label for="'.$this->get_field_id('match_display').'">'.__('Matches','leaguemanager').'</label>';
		$match_display = array( 'none' => __('Do not show','leaguemanager'), 'prev' => __('Last Matches','leaguemanager'), 'next' => __('Next Matches','leaguemanager'), 'all' => __('Next & Last Matches','leaguemanager') );
		echo '<select size="1" name="'.$this->get_field_name('match_display').'" id="'.$this->get_field_id('match_display').'">';
		foreach ( $match_display AS $key => $text ) {
			$selected = ( $key == $instance['match_display'] ) ? ' selected="selected"' : '';
			echo '<option value="'.$key.'"'.$selected.'>'.$text.'</option>';
		}
		echo '</select></p>';
		$checked = ( isset($instance['home_only']) && $instance['home_only'] == 1 ) ? ' checked="checked"' : '';
		echo '<p><input type="checkbox" name="'.$this->get_field_name('home_only').'" id="'.$this->get_field_id('home_only').'" value="1"'.$checked.' /><label for="'.$this->get_field_id('home_only').'" class="right">'.__('Only own matches','leaguemanager').'</label></p>';
		echo '<p><label for="'.$this->get_field_id('match_limit').'">'.__('Limit','leaguemanager').'</label><input type="text" name="'.$this->get_field_name('match_limit').'" id="'.$this->get_field_id('match_limit').'" value="'.$instance['match_limit'].'" size="5" /></p>';

		$table_display = array( 'none' => __('Do not show','leaguemanager'), 'compact' => __('Compact Version','leaguemanager'), 'extend' => __('Extend Version','leaguemanager') );
		echo '<p><label for="'.$this->get_field_id('table').'">'.__('Table','leaguemanager').'</label>';
		echo '<select size="1" name="'.$this->get_field_name('table').'" id="'.$this->get_field_id('table').'">';
		foreach ( $table_display AS $key => $text ) {
			$selected = ( $key == $instance['table'] ) ? ' selected="selected"' : '';
			echo '<option value="'.$key.'"'.$selected.'>'.$text.'</option>';
		}
		echo '</select><input type="text" name="'.$this->get_field_name('home').'" id="'.$this->get_field_id('home').'" value="'.$instance['home'].'" size="1" /></p>';
		$checked = ( $instance['report'] ) ? ' checked="checked"' : '';
		echo '<p><input type="checkbox" name="'.$this->get_field_name('report').'" id="'.$this->get_field_id('report').'" value="1"'.$checked.' /><label for="'.$this->get_field_id('report').'" class="right">'.__('Link to report','leaguemanager').'</label></p>';
		echo '<p><label for="'.$this->get_field_id('date_format').'">'.__('Date Format').'</label><input type="text" id="'.$this->get_field_id('date_format').'" name="'.$this->get_field_name('date_format').'" value="'.$instance['date_format'].'" size="10" /></p>';
		echo '</div>';
		
		return;
	}
}

?>
