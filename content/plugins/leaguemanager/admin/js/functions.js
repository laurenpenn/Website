if(typeof Leaguemanager == "undefined") {
	var Leaguemanager = new Object();
}

tb_init('a.thickbox, area.thickbox, input.thickbox');

Leaguemanager.checkAll = function(form) {
   for (i = 0, n = form.elements.length; i < n; i++) {
      if(form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick',2))) {
         if(form.elements[i].checked == true)
            form.elements[i].checked = false;
         else
            form.elements[i].checked = true;
      }
   }
}


Leaguemanager.checkPointRule = function( forwin, forwin_overtime, fordraw, forloss, forloss_overtime ) {
	var rule = document.getElementById('point_rule').value;
	
	// manual rule selected
	if ( rule == 'user' ) {
		new_element_contents = "";
		new_element_contents += "<input type='text' name='forwin' id='forwin' value=" + forwin + " size='2' />";
		new_element_contents += "<input type='text' name='forwin_overtime' id='forwin_overtime' value=" + forwin_overtime + " size='2' />";
		new_element_contents += "<input type='text' name='fordraw' id='fordraw' value=" + fordraw + " size='2' />";
		new_element_contents += "<input type='text' name='forloss' id='forloss' value=" + forloss + " size='2' />";
		new_element_contents += "<input type='text' name='forloss_overtime' id='forloss_overtime' value=" + forloss_overtime + " size='2' />";
		new_element_contents += "&#160;<span class='setting-description'>" + LeagueManagerAjaxL10n.manualPointRuleDescription + "</span>";
		new_element_id = "point_rule_manual_content";
		new_element = document.createElement('div');
		new_element.id = new_element_id;
		
		document.getElementById("point_rule_manual").appendChild(new_element);
		document.getElementById(new_element_id).innerHTML = new_element_contents;
	} else {
		element_count = document.getElementById("point_rule_manual").childNodes.length;
		if(element_count > 0) {
			target_element = document.getElementById("point_rule_manual_content");
			document.getElementById("point_rule_manual").removeChild(target_element);
		}
  		
	}
	
	return false;
}

Leaguemanager.insertPlayer = function(id, target) {
	tb_remove();
	var player = document.getElementById(id).value
	document.getElementById(target).value = player;
}

Leaguemanager.removeStatsField = function(id) {
  element_count = document.getElementById("stats_fields").childNodes.length;
  if(element_count > 1) {
    target_element = document.getElementById(id);
    document.getElementById("stats_fields").removeChild(target_element);
  }
  return false;
}


Leaguemanager.removeField = function(id, parent_id) {
  element_count = document.getElementById(parent_id).childNodes.length;
  if(element_count > 1) {
    target_element = document.getElementById(id);
    document.getElementById(parent_id).removeChild(target_element);
  }
  return false;
}


Leaguemanager.reInit = function() {
	tb_init('a.thickbox, area.thickbox, input.thickbox');
}


/*
*  Color Picker
*/

function PopupWindow_setSize(width,height) {
	this.width = 360;
	this.height = 210;
}

function syncColor(id, inputID, color) {
	var link = document.getElementById(id);
	if (color == '')
		color='white';
		
	link.style.background = color;
	link.style.color = color;
}

function pickColor(color) {
	if (ColorPicker_targetInput==null) {
		alert("Target Input is null, which means you either didn't use the 'select' function or you have no defined your own 'pickColor' function to handle the picked color!");
		return;
	}
	ColorPicker_targetInput.value = color;
	syncColor("pick_" + ColorPicker_targetInput.id, ColorPicker_targetInput.id, color);
}
var cp = new ColorPicker('window'); // Popup window
