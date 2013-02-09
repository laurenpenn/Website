<?php
/**
 * leaguemanager_upgrade() - update routine for older version
 * 
 * @return Success Message
 */
function leaguemanager_upgrade() {
	global $wpdb, $leaguemanager, $lmLoader;
	
	$options = get_option( 'leaguemanager' );
	$installed = $options['dbversion'];
	
	echo __('Upgrade database structure...', 'leaguemanager');
	$wpdb->show_errors();

	$lmLoader->install();

	if (version_compare($options['version'], '2.0', '<')) {
		/*
		* Drop deprecated tables
		*/
		$wpdb->query( "DROP TABLE `wp_leaguemanager_leaguemeta`" );
		$wpdb->query( "DROP TABLE `wp_leaguemanager_teammeta`" );
		
		/*
		* Update leagues table
		*/
		$lm_cols = $wpdb->get_col( "SHOW COLUMNS FROM {$wpdb->leaguemanager}" );
		if ( !in_array('forwin', $lm_cols) )	
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `forwin` tinyint( 4 ) NOT NULL default '2';" );
		if ( !in_array('fordraw', $lm_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `fordraw` tinyint( 4 ) NOT NULL default '1';" );
		if ( !in_array('forloss', $lm_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `forloss` tinyint( 4 ) NOT NULL default '0';" );
		if ( !in_array('match_calendar', $lm_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `match_calendar` tinyint( 1 ) NOT NULL default '1';" );
		if ( !in_array('type', $lm_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `type` tinyint( 1 ) NOT NULL default '2';" );
		if ( !in_array('active', $lm_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `active` tinyint( 1 ) NOT NULL default '1';" );
	
		
		/*
		* Update Match table
		*/
		$wpdb->query( "RENAME TABLE `wp_leaguemanager_competitions` TO `wp_leaguemanager_matches`" ); 
		
		$wpdb->query( "ALTER TABLE  {$wpdb->leaguemanager_matches} DROP `competitor`" );
		$wpdb->query( "ALTER TABLE  {$wpdb->leaguemanager_matches} DROP `home`" );
		
		$lm_matches_cols = $wpdb->get_col( "SHOW COLUMNS FROM {$wpdb->leaguemanager_matches}" );
		if ( !in_array('home_team', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `home_team` int( 11 ) NOT NULL;" );
		if ( !in_array('away_team', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `away_team` int( 11 ) NOT NULL;" );
		if ( !in_array('home_apparatus_points', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `home_apparatus_points` tinyint( 4 ) NULL default NULL;" );
		if ( !in_array('away_apparatus_points', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `away_apparatus_points` tinyint( 4 ) NULL default NULL;" );
		if ( !in_array('home_points', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `home_points` tinyint( 4 ) NULL default NULL;" );
		if ( !in_array('away_points', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `away_points` tinyint( 4 ) NULL default NULL;" );
		if ( !in_array('winner_id', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `winner_id` int( 11 ) NOT NULL;" );
		if ( !in_array('loser_id', $lm_matches_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `loser_id` int( 11 ) NOT NULL;" );
	}

	/*
	* Upgrade from 2.0 to 2.1
	*/
	if (version_compare($options['version'], '2.0', '<')) {
		$lm_cols = $wpdb->get_col( "SHOW COLUMNS FROM {$wpdb->leaguemanager}" );
		if ( in_array('date_format', $lm_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `date_format`" );
		
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} CHANGE `home_teams_only` `match_calendar` TINYINT( 1 ) NOT NULL DEFAULT '1'" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} CHANGE `gymnastics` `type` TINYINT( 1 ) NOT NULL DEFAULT '2'" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `home_apparatus_points` `home_apparatus_points` TINYINT( 4 ) NULL DEFAULT NULL , 
		CHANGE `away_apparatus_points` `away_apparatus_points` TINYINT( 4 ) NULL DEFAULT NULL ,
		CHANGE `home_points` `home_points` TINYINT( 4 ) NULL DEFAULT NULL ,
		CHANGE `away_points` `away_points` TINYINT( 4 ) NULL DEFAULT NULL" );
	}

	/*
	* Upgrade to Version 2.3.1
	*/
	if (version_compare($options['version'], '2.3.1', '<')) {
		$charset_collate = '';
		if ( $wpdb->supports_collation() ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "CONVERT TO CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}
		
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} $charset_collate" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} $charset_collate" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} $charset_collate" );
	}

	/*
	* Upgrade to 2.4.1
	*/
	if (version_compare($options['version'], '2.4.1', '<')) {
		$lm_cols = $wpdb->get_col( "SHOW COLUMNS FROM {$wpdb->leaguemanager}" );
		if ( !in_array('show_logo', $lm_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `show_logo` TINYINT( 1 ) NOT NULL" );
		
		$lm_teams_cols = $wpdb->get_col( "SHOW COLUMNS FROM {$wpdb->leaguemanager_teams}" );
		if ( !in_array('logo', $lm_teams_cols) )
			$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `logo` VARCHAR( 50 ) NOT NULL AFTER `short_title`" );
	}

	/*
	* Upgrade to 2.5
	*/
	if (version_compare($options['version'], '2.5', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `match_day` TINYINT( 4 ) NOT NULL AFTER `away_team`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `num_match_days` TINYINT( 4 ) NOT NULL AFTER `type`" );
			
		/**
		* Copy Logos to new image directory and delete old one
		*/
		$dir_src = WP_CONTENT_DIR.'/leaguemanager';
		$dir_handle = opendir($dir_src);
		if ( wp_mkdir_p( $leaguemanager->getImagePath() ) ) {
			while( $file = readdir($dir_handle) ) {
				if( $file!="." && $file!=".." ) {
					if ( copy ($dir_src."/".$file, $leaguemanager->getImagePath()."/".$file) )
						@unlink($dir_src."/".$file);
				}
			}
			
			@rmdir($dir_src);
		}
	}

	/*
	* Upgrade to 2.5.1
	*/
	if (version_compare($options['version'], '2.5.1', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `match_calendar`" );
	}
	
	
	/*
	* Upgrade to 2.6.6
	*/
	if (version_compare($installed, '2.6.6', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `post_id` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `points_plus` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `points_minus` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `points2_plus` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `points2_minus` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `done_matches` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `won_matches` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `draw_matches` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams}, ADD `lost_matches` int( 11 ) NOT NULL" );
	}
	
	/*
	* Upgrade to 2.7
	*/
	if (version_compare($installed, '2.7', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `forwin`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `fordraw`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `forloss`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `match_calendar`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `show_logo`" );
			
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD point_rule LONGTEXT NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `point_format` varchar( 255 ) NOT NULL" );
			
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `overtime` LONGTEXT NOT NULL AFTER `loser_id`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `penalty` LONGTEXT NOT NULL AFTER `overtime`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `points2` LONGTEXT  NOT NULL" );
		
		if ( $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->leaguemanager_matches}" ) ) {
			$points2 = array();
			foreach ( $matches AS $match ) {
				$points2[] = array( 'plus' => $match->home_apparatus_points, 'minus' => $match->away_appratus_points );
					
				$wpdb->query( "UPDATE {$wpdb->leaguemanager_matches} SET `points2` = '".maybe_serialize($points2)."' WHERE id = '".$match->id."'" );
			}
		}
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `diff` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `website` varchar( 255 ) NOT NULL AFTER `logo`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `coach` varchar( 100 ) NOT NULL AFTER `website`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `title` `title` varchar( 100 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `short_title` `short_title` varchar( 50 ) NOT NULL" );
		//$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} DROP `home_apparatus_points`, DROP `away_apparatus_points`" );
	}
	
	/*
	* Upgrade to 2.8
	*/
	if (version_compare($installed, '2.8', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD team_ranking varchar( 20 ) NOT NULL default 'auto'" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `rank` int( 11 ) NOT NULL default '0'" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `add_points` int( 11 ) NOT NULL" );
	}
	
	
	/*
	* Upgrade to 2.9-RC1
	*/
	if (version_compare($installed, '2.9-RC1', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `project_id` int( 11 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `mode` varchar( 255 ) NOT NULL default 'season'" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `goals` LONGTEXT  NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `cards` LONGTEXT  NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `exchanges` LONGTEXT  NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `season` varchar( 255 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `final` varchar( 150 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `season` varchar( 255 ) NOT NULL" );
			
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} CHANGE `type` `sport` varchar( 255 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `home_team` `home_team` varchar( 255 ) NOT NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `away_team` `away_team` varchar( 255 ) NOT NULL" );
		
	}
	
	
	/**
	 * Upgrade to 2.9-RC2
	 */
	if (version_compare($installed, '2.9-RC2', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `home_points` `home_points` varchar( 30 ) NULL default NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `away_points` `away_points` varchar( 30 ) NULL default NULL" );

		// Add default values
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} CHANGE `title` `title` varchar( 100 ) NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} CHANGE `num_match_days` `num_match_days` tinyint( 4 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} CHANGE `point_rule` `point_rule` longtext NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} CHANGE `point_format` `point_format` varchar( 255 ) NOT NULL default ''");

		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `title` `title` varchar( 100 ) NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} DROP `short_title`");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `logo` `logo` varchar( 150 ) NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `website` `website` varchar( 255 ) NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `coach` `coach` varchar( 100 ) NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `home` `home` tinyint( 1 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `points_plus` `points_plus` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `points_minus` `points_minus` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `points2_plus` `points2_plus` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `points2_minus` `points2_minus` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `add_points` `add_points` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `done_matches` `done_matches` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `won_matches` `won_matches` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `draw_matches` `draw_matches` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `lost_matches` `lost_matches` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `diff` `diff` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `rank` `rank` int( 11 ) NOT NULL default '0'");

		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `date` `date` datetime NOT NULL default '0000-00-00'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `match_day` `match_day` tinyint( 4 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `location` `location` varchar( 100 ) NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `points2` `points2` longtext NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `winner_id` `winner_id` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `loser_id` `loser_id` int( 11 ) NOT NULL default '0'");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `overtime` `overtime` longtext NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `penalty` `penalty` longtext NOT NULL default ''");
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `post_id` `post_id` int( 11 ) NOT NULL default '0'");
	}
	
	
	/**
	 * Upgrade to 2.9
	 */
	if (version_compare($installed, '2.9', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `seasons` longtext NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `custom` longtext NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `custom` longtext NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `status` varchar( 50 ) NOT NULL default '&#8226;'" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `custom` longtext NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `final` varchar( 150 ) NOT NULL" );

		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} DROP `goals`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} DROP `cards`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} DROP `exchanges`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `num_match_days`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} DROP `points2`" );
	}


	/**
	 * Upgrade to 2.9.1
	 */
	if (version_compare($installed, '2.9.1', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} DROP `status`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `status` varchar( 50 ) NOT NULL default '&#8226;'" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `final` varchar( 150 ) NOT NULL" );
	}

	
	/**
	 * Upgrade to 3.0
	 */
	if (version_compare($installed, '3.0', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `roster` longtext NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} ADD `settings` longtext NOT NULL default ''" );

		$leagues = $wpdb->get_results( "SELECT * FROM {$wpdb->leaguemanager}" );
		foreach ( $leagues AS $league ) {
			$settings = array();
			$settings['sport'] = $league->sport;
			$settings['point_rule'] = maybe_unserialize($league->point_rule);
			$settings['point_format'] = $league->point_format;
			$settings['save_standings'] = $league->save_standings;
			$settings['team_ranking'] = $league->team_ranking;
			$settings['mode'] = $league->mode;

			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager} SET `settings` = '%s' WHERE `id` = '%d'", maybe_serialize($settings), $league->id ) );
		}

		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} DROP `overtime`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} DROP `overtime`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `project_id`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `sport`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `point_rule`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `point_format`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `save_standings`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `team_ranking`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `mode`" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager} DROP `active`" );
	}


	if (version_compare($installed, '3.0.1', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `roster` longtext NOT NULL default ''" );
	}


	if (version_compare($installed, '3.1', '<')) {
		$lmLoader->install(); // call install function to make sure new database table for stats is created
	}
	
	if (version_compare($installed, '3.1.1', '<')) {
		$teams = $wpdb->get_results( "SELECT `logo` FROM {$wpdb->leaguemanager_teams}" );
		foreach ( $teams AS $team ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `logo` = '%s' WHERE `id` = '%%d'", $leaguemanager->getImageUrl($team->logo), $team->id ) );
		}
	}

	if (version_compare($installed, '3.1.3', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} CHANGE `match_day` `match_day` int( 11 ) default '0'" );
		$teams = $wpdb->get_results( "SELECT `logo` FROM {$wpdb->leaguemanager_teams}" );
		foreach ( $teams AS $team ) {
			if ( !empty($team->logo) ) {
				$logo = new LeagueManagerImage($leaguemanager->getImageUrl().'/'.$team->logo);
				$logo->createThumbnail();
			}
		}
	}

	if (version_compare($installed, '3.1.5', '<')) {
		chmod($leaguemanager->getImagePath(), 0755);
	}


	if ( version_compare($installed, '3.1.6', '<') || version_compare($installed, '3.1.7', '<') ) {
		$teams = $wpdb->get_results( "SELECT `logo` FROM {$wpdb->leaguemanager_teams}" );
		foreach ( $teams AS $team ) {
			if ( !empty($team->logo) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager_teams} SET `logo` = '%s' WHERE `id` = '%%d'", $leaguemanager->getImageUrl() .'/'. basename($team->logo), $team->id ) );
			}
		}
	}


	if ( version_compare($installed, '3.2-RC1', '<') ) {
		$leagues = $wpdb->get_results( "SELECT `id`, `settings` FROM {$wpdb->leaguemanager}" );
		foreach ( $leagues AS $league ) {
			$settings = maybe_unserialize($league->settings);
			$settings['upload_dir'] = 'wp-content/uploads/leaguemanager';
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->leaguemanager} SET `settings` = '%s' WHERE `id` = '%d'", maybe_serialize($settings), $league->id ) );
		}
	}


	if (version_compare($installed, '3.4-RC1', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `group` varchar( 30 ) NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_matches} ADD `group` varchar( 30 ) NOT NULL default ''" );
	}


	if (version_compare($installed, '3.5', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} ADD `stadium` varchar( 150 ) NOT NULL default ''");
	}

	if (version_compare($installed, '3.6.3', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `points_plus` `points_plus` float NULL default NULL" );
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `points_minus` `points_minus` float NULL default NULL" );
	}
	
	if (version_compare($installed, '3.7', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->leaguemanager_teams} CHANGE `add_points` `add_points` float NULL default NULL" );
	}
	
	
	/*
	* Update version and dbversion
	*/
	$options['dbversion'] = LEAGUEMANAGER_DBVERSION;
	$options['version'] = LEAGUEMANAGER_VERSION;
	
	update_option('leaguemanager', $options);
	echo __('finished', 'leaguemanager') . "<br />\n";
	$wpdb->hide_errors();
	return;
}


/**
* leaguemanager_upgrade_page() - This page showsup , when the database version doesn't fit to the script LEAGUEMANAGER_DBVERSION constant.
* 
* @return Upgrade Message
*/
function leaguemanager_upgrade_page()  {	
	$filepath    = admin_url() . 'admin.php?page=' . $_GET['page'];

	if ($_GET['upgrade'] == 'now') {
		leaguemanager_do_upgrade($filepath);
		return;
	}
?>
	<div class="wrap">
		<h2><?php _e('Upgrade LeagueManager', 'leaguemanager') ;?></h2>
		<p><?php _e('Your database for LeagueManager is out-of-date, and must be upgraded before you can continue.', 'leaguemanager'); ?>
		<p><?php _e('The upgrade process may take a while, so please be patient.', 'leaguemanager'); ?></p>
		<h3><a class="button" href="<?php echo $filepath;?>&amp;upgrade=now"><?php _e('Start upgrade now', 'leaguemanager'); ?>...</a></h3>
	</div>
	<?php
}


/**
 * leaguemanager_do_upgrade() - Proceed the upgrade routine
 * 
 * @param mixed $filepath
 * @return void
 */
function leaguemanager_do_upgrade($filepath) {
	global $wpdb;
?>
<div class="wrap">
	<h2><?php _e('Upgrade LeagueManager', 'leaguemanager') ;?></h2>
	<p><?php leaguemanager_upgrade();?></p>
	<p><?php _e('Upgrade successful', 'leaguemanager') ;?></p>
	<h3><a class="button" href="<?php echo $filepath;?>"><?php _e('Continue', 'leaguemanager'); ?>...</a></h3>
</div>
<?php
}


/**
 * display upgrade page for 2.9.2
 */
function leaguemanager_upgrade_292() {
	global $leaguemanager;

	if ( isset($_POST['set_season']) ) {
		$new_league = empty($_POST['new_league']) ? false : $_POST['new_league'];
		$old_season = empty($_POST['old_season']) ? false : $_POST['old_season'];

		if ( !empty($_POST['season']) ) {
			move_league_to_season( $_POST['league'], $_POST['season'], $new_league, $old_season );
			$leaguemanager->setMessage( __( 'Successfully set Season for Matches and Teams', 'leaguemanager') );
		} else {
			$leaguemanager->setMessage( __( 'Season was empty', 'leaguemanager' ), true );
		}
		$leaguemanager->printMessage();
	}

	$leagues = $leaguemanager->getLeagues();
?>
<div class="wrap">
<h2><?php _e( 'Upgrade to Version 2.9.2', 'leaguemanager' ) ?></h2>

<form action="" method="post">
<table class="form-table">
<tr>
	<th scope="row"><label for="league"><?php _e( 'League', 'leaguemanager' ) ?></label></th>
	<td>
		<select id="league" name="league" size="1">
			<?php foreach ( $leagues AS $league ) : ?>
			<option value="<?php echo $league->id ?>"><?php echo $league->title ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>
<tr>
	<th scope="row"><label for="season"><?php _e( 'Season', 'leaguemanager' ) ?></label></th>
	<td><input type="text" name="season" id="season" size="10" /></td>
</tr>
<tr>
	<th scope="row"><label for="new_league"><?php _e( 'New League', 'leaguemanager' ) ?></label></th>
	<td>
		<select id="new_league" name="new_league" size="1">
			<option value=""><?php _e( 'Keep League', 'leaguemanager' ) ?></option>
			<?php foreach ( $leagues AS $league ) : ?>
			<option value="<?php echo $league->id ?>"><?php echo $league->title ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr>
<tr>
	<th scope="row"><label for="old_season"><?php _e( 'Old Season', 'leaguemanager' ) ?></label></th>
	<td><input type="text" name="old_season" id="old_season" size="10" /></td>
</tr>
</table>
<p class="submit"><input type="submit" name="set_season" value="<?php _e( 'Submit' ) ?>" /></p>
</form>
</div>
<?php
}

?>
