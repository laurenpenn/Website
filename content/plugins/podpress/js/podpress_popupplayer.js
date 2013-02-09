function podPress_makeAjaxRequest(url, podPressHTML5sec, podPressBackendURL) {
	if ( typeof podPressBackendURL == 'string' && podPressBackendURL != '' && typeof podPressHTML5sec == 'string' && podPressHTML5sec != '' ) {
		podpress_http_request = false;
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
			podpress_http_request = new XMLHttpRequest();
			if (podpress_http_request.overrideMimeType) {
				podpress_http_request.overrideMimeType('text/html');
			}
		} else if (window.ActiveXObject) { // IE
			try {
				podpress_http_request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					podpress_http_request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
		}
		if (!podpress_http_request) {
			//~ alert('It is not possible to create an XMLHTTP instance.');
			return false;
		}
		podpress_http_request.open('POST', podPressBackendURL + 'podpress_backend.php', true);
		podpress_http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		podpress_http_request.send( 'action=getrealurl&url='+encodeURIComponent(url)+'&_ajax_nonce=' + podPressHTML5sec );
		//~ podpress_http_request.onreadystatechange = function() { podPress_AjaxResponse(); }
	}
}

function podPress_AjaxResponse() {
	switch (podpress_http_request.readyState) {
		case 0 : // UNINITIALIZED
		case 1 : // LOADING
		case 2 : // LOADED
		case 3 : // INTERACTIVE
			break;
		case 4 : // COMPLETED
			if (podpress_http_request.status == 200) {
				alert('podpress_http_request.status: \n' + podpress_http_request.status + '\n data:\n ' + podpress_http_request.responseText);
			} else {
				alert('There was a problem with the request (Probably no data saved). podpress_http_request.status: \n' + podpress_http_request.status + '\n Error message:\n ' + podpress_http_request.responseText);
			}
			break;
		default : ; // fehlerhafter Status
	}
}