var Leaguemanager = new Object();

Leaguemanager.setMatchBox = function( curr_index, operation, element, league_id, match_limit, widget_number, season, home_only, date_format ) {
	var ajax = new sack(LeagueManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "leaguemanager_get_match_box" );
	ajax.setVar( "widget_number", widget_number );
	ajax.setVar( "current", curr_index );
	ajax.setVar( "season", season );
	ajax.setVar( "operation", operation );
	ajax.setVar( "element", element );
	ajax.setVar( "league_id", league_id );
	ajax.setVar( "match_limit", match_limit );
	ajax.setVar( "home_only", home_only );
	ajax.setVar( "date_format", date_format );
	ajax.onError = function() { alert('Ajax error'); };
	ajax.onCompletion = function() { return true; };
	ajax.runAJAX();
}
 
