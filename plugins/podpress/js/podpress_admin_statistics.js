//~ ######## PODPRESS | JS for the statistics pages in the Site Admin #########
function podpress_mark_same_all_bots( ip_or_agent , id_number, start ) {
	var id_number = Number(id_number);
	var start = Number(start);
	start = 0;
	//alert('ip or agent: ' + ip_or_agent + '\nstart: '+String(start) +'\nid_number: ' + String(id_number));
	if ( ip_or_agent == 'user_agent' ) {
		var ua_chbs = document.getElementsByName('podpress_user_agents[]');
		if (true == document.getElementById('podpress_user_agent_'+String(id_number)).checked) {
			var j = start;
			for (var i=0; i < ua_chbs.length; i++ ) {
				j++;
				if ( ua_chbs[i].value == document.getElementById('podpress_user_agent_'+String(id_number)).value ) {
					ua_chbs[i].checked = true;
					document.getElementById('podpress_ip_user_agent_row_'+String(j)).className = 'podpress_is_bot';
				}
			}
		} else {
			var j = start;
			for (var i=0; i < ua_chbs.length; i++ ) {
				j++;
				if ( ua_chbs[i].value == document.getElementById('podpress_user_agent_'+String(id_number)).value ) {
					ua_chbs[i].checked = false;
					if (false == document.getElementById('podpress_remote_ip_'+String(j)).checked) {
						if ( j & 1 ) {
							document.getElementById('podpress_ip_user_agent_row_'+String(j)).className ='';
						} else {
							document.getElementById('podpress_ip_user_agent_row_'+String(j)).className ='alternate';
						}
					}
				}
			}
		}
	} else {
		var ip_chbs = document.getElementsByName('podpress_remote_ips[]');
		if (true == document.getElementById('podpress_remote_ip_'+String(id_number)).checked) {
			var j = start;
			for (var i=0; i < ip_chbs.length; i++ ) {
				j++;
				if ( ip_chbs[i].value == document.getElementById('podpress_remote_ip_'+String(id_number)).value ) {
					ip_chbs[i].checked = true;
					document.getElementById('podpress_ip_user_agent_row_'+String(j)).className = 'podpress_is_bot';
				}
			}
		} else {
			var j = start;
			var nr_ips = ip_chbs.length;
			//~ if ( (nr_ips & 1) ) {
				//~ alert('ungerade '+String(nr_ips));
			//~ } else {
				//~ alert('gerade '+String(nr_ips));
			//~ }
			for (var i=0; i < nr_ips; i++ ) {
				j++;
				if ( ip_chbs[i].value == document.getElementById('podpress_remote_ip_'+String(id_number)).value ) {
					ip_chbs[i].checked = false;
					if (false == document.getElementById('podpress_user_agent_'+String(j)).checked) {
						if (j & 1) {
							document.getElementById('podpress_ip_user_agent_row_'+String(j)).className ='';
						} else {
							document.getElementById('podpress_ip_user_agent_row_'+String(j)).className = 'alternate';
						}
					}
				}
			}
		}
	}
}