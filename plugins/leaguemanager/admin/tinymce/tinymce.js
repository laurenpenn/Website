function init() {
	tinyMCEPopup.resizeToInnerSize();
}


function LeagueManagerGetCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function insertLeagueManagerLink() {

	var tagtext;

	var table = document.getElementById('table_panel');
	var matches = document.getElementById('matches_panel');
	var match = document.getElementById('match_panel');
	var crosstable = document.getElementById('crosstable_panel');
	var teams = document.getElementById('teams_panel');
	var team = document.getElementById('team_panel');
	var archive = document.getElementById('archive_panel');

	// who is active?
	if (table.className.indexOf('current') != -1) {
		var leagueId = document.getElementById('table_tag').value;
		var standings_display = document.getElementById('standings_display').value;
		if ( document.getElementById('show_logo').checked )
			var logo = 'true';
		else
			var logo = 'false';
		
		if (leagueId != 0)
			tagtext = "[standings league_id=" + leagueId + " template=" + standings_display + " logo=" + logo + "]";
		else
			tinyMCEPopup.close();
	}

	if (matches.className.indexOf('current') != -1) {
		var leagueId = document.getElementById('matches_tag').value;
		var match_display = document.getElementById('matches_display').value;
		
		if (leagueId != 0)
			tagtext = "[matches league_id=" + leagueId + " mode=" + match_display + "]";
		else
			tinyMCEPopup.close();
	}

	if (match.className.indexOf('current') != -1) {
		var matchId = document.getElementById('match_tag').value;
		
		if (matchId != 0)
			tagtext = "[match id=" + matchId + "]";
		else
			tinyMCEPopup.close();
	}
	
	if (teams.className.indexOf('current') != -1) {
		var leagueId = document.getElementById('teams_tag').value;
		
		if (leagueId != 0)
			tagtext = "[teams league_id=" + leagueId + "]";
		else
			tinyMCEPopup.close();
	}

	if (team.className.indexOf('current') != -1) {
		var teamId = document.getElementById('team_tag').value;
		
		if (teamId != 0)
			tagtext = "[team id=" + teamId + "]";
		else
			tinyMCEPopup.close();
	}

	if (crosstable.className.indexOf('current') != -1) {
		var leagueId = document.getElementById('crosstable_tag').value;
		var showtype = LeagueManagerGetCheckedValue(document.getElementsByName('crosstable_showtype'));

		if (leagueId != 0)
			tagtext = "[crosstable league_id=" + leagueId + " mode=" + showtype + "]";
		else
			tinyMCEPopup.close();
	}
	
	if (archive.className.indexOf('current') != -1) {
		var leagueId = document.getElementById('archive_tag').value;
		
		if (leagueId != 0)
			tagtext = "[leaguearchive league_id=" + leagueId + "]";
		else
			tinyMCEPopup.close();
	}
	
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
