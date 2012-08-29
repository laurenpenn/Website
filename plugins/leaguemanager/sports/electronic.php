<?php
/**
 * Electronic Class 
 * 
 * @author 	Kolja Schleich
 * @package	LeagueManager
 * @copyright 	Copyright 2008-2009
*/
class LeagueManagerElectronic extends LeagueManager
{
	/**
	 * sports keys
	 *
	 * @var string
	 */
	var $keys = array();


	/**
	 * load specific settings
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		$this->keys = array( 'shooter' => __( 'PC &#8211; Shooter', 'leaguemanager' ), 'strategy' => __( 'PC &#8211; Strategy', 'leaguemanager'), 'role-playing-game' => __( 'PC &#8211; Role-Playing Game', 'leaguemanager') );

		add_filter( 'leaguemanager_sports', array(&$this, 'sports') );
	}
	function LeagueManagerElectronic()
	{
		$this->__construct();
	}


	/**
	 * add sports to list
	 *
	 * @param array $sports
	 * @return array
	 */
	function sports( $sports )
	{
		foreach ( $this->keys AS $key => $name )
			$sports[$key] = $name;
		return $sports;
	}
}

$electronic = new LeagueManagerElectronic();
?>
