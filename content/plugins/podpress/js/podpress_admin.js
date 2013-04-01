/* podpress_admin.js | podPress - JS scripts for the Admin Site */
/* some functions rely on function in podpres.js that makes it necessary to enqueue in Admin Site always both files */
if (!self.getHTTPObject) {
	function getHTTPObject() {
		var xmlhttp;
		var container;
		if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
			try {
				xmlhttp = new XMLHttpRequest();
			} catch (e) {
				xmlhttp = false;
			}
		} else {
			try {
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (E) {
					xmlhttp = false;
				}
			}
		}		
		return xmlhttp;
	}
}
var podPressHttp_TestStats = getHTTPObject();

var podPressLoadedMP3Info = '';

var podPressMediaFiles = new Array();
var podPressMediaContentNum = 0;

var podPressMediaFileForRSS = 'NOTSET';

var podPress_colorLocked = false;
var podPress_colorInput = '';

function podPressShowDirectoryPreview(directoryName) {
	if(directoryName != 'PodcastAlley') { 
		document.getElementById('PodcastAlleyPreviewDisplay').style.display='none'; 
	}
	if(directoryName != 'Yahoo') { 
		document.getElementById('YahooPreviewDisplay').style.display='none'; 
	}
	if(directoryName != 'iTunes') { 
		document.getElementById('iTunesPreviewDisplay').style.display='none'; 
	}
	if(directoryName != 'PodcastReady') { 
		document.getElementById('PodcastReadyPreviewDisplay').style.display='none'; 
	}
	if(directoryName != 'Blubrry') { 
		document.getElementById('BlubrryPreviewDisplay').style.display='none'; 
	}
	if(directoryName == 'PodcastAlley') { 
		document.getElementById('PodcastAlleyPreviewDisplay').style.display='block'; 
	}
	if(directoryName == 'Yahoo') { 
		document.getElementById('YahooPreviewDisplay').style.display='block'; 
	}
	if(directoryName == 'iTunes') { 
		document.getElementById('iTunesPreviewDisplay').style.display='block'; 
	}
	if(directoryName == 'PodcastReady') { 
		document.getElementById('PodcastReadyPreviewDisplay').style.display='block'; 
	}
	if(directoryName == 'Blubrry') { 
		document.getElementById('BlubrryPreviewDisplay').style.display='block'; 
	}
}

function podPressCheckForNotSafeFilenameChr( VarNum, str ) {
	if ( '' != str && 0 < String(VarNum).length ) {
		if ( (-1 == str.search(/youtube.com/gi) && null != str.match(/[^\u002D-\u003A && \u0041-\u005A && \u005C-\u005F && \u0061-\u007A]/g)) || null != str.match(/\s/g) ) {
			document.getElementById('podPressMedia_' + VarNum + '_URI_chrWarning').style.display = 'block';
		} else {
			document.getElementById('podPressMedia_' + VarNum + '_URI_chrWarning').style.display = 'none';
		}
	} 
}

/**
* podPress_show_HTML5_player_always - deactivate this option when "Use HTML5 tags" is deactivated (used in podpress_admin_player_class.php)
* @package podPress
* @since 8.8.10
* @param String id_o - the id of the main checkbox
* @param String id_t - the id of the target element
*/
function podPress_show_HTML5_player_always(id_o, id_t) {
	if ( document.getElementById(id_o).checked == false ) {
		document.getElementById(id_t).checked = false;
		document.getElementById(id_t).disabled = true;
	} else {
		document.getElementById(id_t).disabled = false;
	}
}


/**
* podPress_getfileinfo - retrieve information about a media file with AJAX
* @package podPress
* @since 8.8.9.2
* @param String rqtype - the identifier of the action
* @param Numeric VarNum - the index of the 
*/
function podPress_getfileinfo(rqtype, VarNum) {
	var mediaUrlField = document.getElementById('podPressMedia_'+VarNum+'_URI');
	var params = podPress_sanitizeURL(mediaUrlField.value);
	if (params.length > 0) {
		podpress_http_request = false;
		mediaUrlField.value = params;
		var url = podPressBackendURL + 'podpress_backend.php';
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
			podpress_http_request = new XMLHttpRequest();
			if (podpress_http_request.overrideMimeType) {
				podpress_http_request.overrideMimeType('application/x-httpd-php'); 
			}
		} else if (window.ActiveXObject) { // IE
			try {
				http_request = new ActiveXObject('Msxml2.XMLHTTP');
			} catch (e) {
				try {
					http_request = new ActiveXObject('Microsoft.XMLHTTP');
				} catch (e) {}
			}
		}
		if (!podpress_http_request) {
			alert(podpressL10.notxmlhttpinstance);
			return false;
		}
		var id_base = 'podPressMedia_'+VarNum+'_' + rqtype;
		document.getElementById( id_base + '_loadimg').style.display = 'inline';
		document.getElementById( id_base + '_detectbutton').style.display = 'none';
		switch (rqtype) {
			case 'id3tags' :
				document.getElementById( id_base ).innerHTML = podpressL10.loadingID3msg;
			break;
			case 'duration' :
			case 'size' :
			default :
				document.getElementById( id_base ).value = podpressL10.detecting;
			break;
		}
		var old_cell_content = '';
		podpress_http_request.open('POST', url, true);
		podpress_http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		podpress_http_request.send( 'action=' + rqtype + '&filename=' + encodeURIComponent(params) + '&_ajax_nonce=' + document.getElementById( 'podPress_AJAX_sec' ).value );
		podpress_http_request.onreadystatechange = function() { podPress_getfileinfo_result( rqtype, old_cell_content, id_base, VarNum ); }
	}
}
function podPress_getfileinfo_result( rqtype, old_cell_content, resultcell_id, VarNum ) {
	switch (podpress_http_request.readyState) {
		case 0 : // UNINITIALIZED
		case 1 : // LOADING
		case 2 : // LOADED
		case 3 : // INTERACTIVE
			break;
		case 4 : // COMPLETED
			if (podpress_http_request.status == 200) { // alles ok
				switch (rqtype) {
					case 'id3tags' :
						document.getElementById( resultcell_id + '_loadimg').style.display = 'none';
						document.getElementById( resultcell_id + '_detectbutton').style.display = 'inline';	
						document.getElementById( resultcell_id ).innerHTML = podpress_http_request.responseText;
					break;
					default :					
						if(podpress_http_request.responseText == '' || podpress_http_request.responseText == 'M' || podpress_http_request.responseText == '4096' ) {
							if(podPressMediaFiles[VarNum][rqtype] == '') {
								podPressMediaFiles[VarNum][rqtype] = podpressL10.unknown;
							}
						} else {
							podPressMediaFiles[VarNum][rqtype] = podpress_http_request.responseText;
						}
						document.getElementById( resultcell_id + '_loadimg').style.display = 'none';
						document.getElementById( resultcell_id + '_detectbutton').style.display = 'inline';	
						document.getElementById( resultcell_id ).value = podPress_trim(podPressMediaFiles[VarNum][rqtype]);
					break;
				}
			} else {
				switch (rqtype) {
					case 'id3tags' :
						document.getElementById( resultcell_id ).innerHTML = '<p class="message error">' + podpressL10.ajaxerror + podpress_http_request.status + '</p>';
					break;
					default :
						document.getElementById( resultcell_id ).value = podpressL10.ajaxerror + podpress_http_request.status;
					break;
				} 
				if ( confirm(podpressL10.viewerrormsg) ) {
					alert(podpress_http_request.responseText);
				} 
			}
			break;
		default : ; // fehlerhafter Status
	}
}

/* getID3 Tag info functions */
function podPressShowHideID3Tags(VarNum) {
	if (document.getElementById('podPressMedia_'+VarNum+'_id3tags').style.display=='block') {
		document.getElementById('podPressMedia_'+VarNum+'_id3tags_detectbutton').value= podpressL10.Show;
		document.getElementById('podPressMedia_'+VarNum+'_id3tags').style.display='none';
		return;
	} else {
		document.getElementById('podPressMedia_'+VarNum+'_id3tags_detectbutton').value=podpressL10.Hide;
		document.getElementById('podPressMedia_'+VarNum+'_id3tags').style.display='block';
		podPress_getfileinfo('id3tags', VarNum);
	}
}
function podPressID3ToPost(VarNum) {
	document.getElementById('title').value = document.getElementById('podPressMedia_'+VarNum+'_tagTitle').innerHTML;
	document.getElementById('content').value = document.getElementById('podPressMedia_'+VarNum+'_tagDescription').innerHTML;
}

function podPress_sanitizeURL(rawurl) {
	var clUrl = rawurl.replace(/<\S[^><]*>/g, '');
	clUrl = podPress_trim(clUrl);
	return clUrl;
}
function podPress_trim(unsanitized_str) {
	if ( typeof unsanitized_str != 'string' ) {
		return unsanitized_str;
	} else {
		var ltrim = /\s+$/; // one blank or blanks at the begin of the string
		var rtrim = /^\s+/; // one blank or blanks at the end of the string
		sanitized_str = unsanitized_str.replace(ltrim, '');
		sanitized_str = sanitized_str.replace(rtrim, '');
		return sanitized_str;
	}
}

/**
* podPressTestStats - function to test whether it is possible to resolve the custom statistics URL scheme
*
* @package podPress
* @since unknown
*
* @param String url - an URL in the custom scheme (see podpress_admin_general_class.php and podpress_functions.php - podPress_processDownloadRedirect() for the counter parts)
*/
function podPressTestStats(url) {
	podPressHttp_TestStats.open("GET", url, true);
	podPressHttp_TestStats.onreadystatechange = podPressTestStatsHandler;
	podPressHttp_TestStats.send(null);
}
function podPressTestStatsHandler() {
 	if (podPressHttp_TestStats.readyState == 4) {
		var x = podPressHttp_TestStats.responseText;
		x = x.replace(/^\s*|\s*$/g,"");
		if(x == 'Worked') {
			document.getElementById('statTestResult').value = podpressL10.test_successful;
		} else {
			document.getElementById('statTestResult').value = podpressL10.test_failed;
		}
	}
}

function podPressShowHideDiv(divname) {
	if(document.getElementById(divname).style.display == 'none') {
		document.getElementById(divname).style.display='block';
		if(document.getElementById(divname+'_text') != undefined) {
			document.getElementById(divname+'_text').innerHTML = '(' + podpressL10.Hide + ')';
		}
		if(document.getElementById(divname+'_button') != undefined) {
			document.getElementById(divname+'_button').value = podpressL10.Hide;
		}
	} else {
		document.getElementById(divname).style.display='none';
		if(document.getElementById(divname+'_text') != undefined) {
			document.getElementById(divname+'_text').innerHTML = '(' + podpressL10.Show + ')';
		}
		if(document.getElementById(divname+'_button') != undefined) {
			document.getElementById(divname+'_button').value = podpressL10.Show;
		}
	}
}
function podpress_check_method_requirements(selectedvalue) {
	var selectedvalue = String(selectedvalue);
	if ( '' != selectedvalue) {
		switch (selectedvalue) {
			case 'permalinks' :
				document.getElementById('podpress_trac_dirWarning').style.display = 'none';
				var showWarning = String(document.getElementById('podpress_usingpermalinks').value);
			break;
			case 'podpress_trac_dir' :
				document.getElementById('permalinksWarning').style.display = 'none';
				var showWarning = String(document.getElementById('podpress_trac_folder_in_place').value);
			break;
		}
		switch (showWarning) {
			case 'false' :
				document.getElementById(selectedvalue + 'Warning').style.display = 'block';
			break;
			case 'true' :
				document.getElementById(selectedvalue + 'Warning').style.display = 'none';
			break;
			default:
				document.getElementById('permalinksWarning').style.display = 'none';
				document.getElementById('podpress_trac_dirWarning').style.display = 'none';				
			break;
		}
	}
}
function podpress_showhide_adv(id, switchtext, text_id) {
	if ( typeof switchtext != 'boolean' ) {
		var switchtext = false;
	}
	if (true == switchtext) {
		if (document.getElementById( id ).style.display == 'none') {
			document.getElementById( id ).style.display = '';
			document.getElementById( text_id ).innerHTML = '[ ' + podpressL10.Hide + ' ]';
		} else {
			document.getElementById( id ).style.display = 'none';
			document.getElementById( text_id ).innerHTML = '[ ' + podpressL10.Show + ' ]';
		}
	} else {
		if (document.getElementById( id ).style.display == 'none') {
			document.getElementById( id ).style.display = '';
		} else {
			document.getElementById( id ).style.display = 'none';
		}
	}
}
function podPressShowHideRow(rowname) {
	if (document.getElementById(rowname).style.display == 'none') {
		document.getElementById(rowname).style.display='';
	} else {
		document.getElementById(rowname).style.display='none';
	}
}

function podPressShowHideWrapper(id) {
	// ntm: since 8.8.6 it is only called if the Podango player is in use
	if ( true == podPressMP3PlayerWrapper ) {
		podPressMP3PlayerWrapper = false;
	} else {
		podPressMP3PlayerWrapper = true;
	}
	document.getElementById(id).innerHTML = podPressGeneratePlayer(1, 'sample.mp3', '', '');
}

function podPressUpdateDimensions(VarNum, val) {
	// this function updates the player preview on the editor pages
	var valArray = String(val).split(':');
	if (2 != valArray.length) {
		valArray[0] = 320; //newMediaDefaults['dimensionW'];
		valArray[1] = 240; //newMediaDefaults['dimensionH'];
	} else {
		var dim_x = Number(valArray[0].replace(/\D+/g, ''));
		var dim_y = Number(valArray[1].replace(/\D+/g, ''));
		if ('NaN' == dim_x || 120 > dim_x) {
			valArray[0]=120;
		}
		if (1650 < dim_x) {
			valArray[0]=1650;
		}
		if ('NaN' == dim_y || 25 >= dim_y) {
			valArray[1]=0;
		}
		if (1050 < dim_y) {
			valArray[1]=1050;
		}
	}
	document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value = valArray[0];
	document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value = valArray[1];
	podPressMediaFiles[VarNum]['dimensionW'] = valArray[0];
	podPressMediaFiles[VarNum]['dimensionH'] = valArray[1];
	document.getElementById('podPressPlayerSpace_'+String(VarNum)).innerHTML = podPressGenerateVideoPreview(VarNum, '', valArray[0], valArray[1], document.getElementById('podPressMedia_'+String(VarNum)+'_previewImage').value, true);
	podPressShowPreviewImage(VarNum);
}

function podPressUpdateDimensionList(VarNum) {
	// this function updates the player preview on the editor pages and tries to select the value W:H value in the selectbox with the predefined formats
	var dimensionsStr = String(document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value)+':'+String(document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value);
	podPressUpdateDimensions(VarNum, dimensionsStr);
	var is_in = podPress_isset_in_the_list(dimensionsStr, 'podPressMedia_'+VarNum+'_dimensionList');
	if ( true == is_in[0] ) {
		document.getElementById('podPressMedia_'+VarNum+'_dimensionList').selectedIndex = is_in[1];
	}
}

function podPress_set_blognamePreview(val) {
	document.getElementById('iTunesBlognamePreview').innerHTML = val;
	document.getElementById('PCABlognamePreview').innerHTML = val;
	document.getElementById('YahooBlognamePreview').innerHTML = val;
	document.getElementById('PodcastReadyBlognamePreview').innerHTML = val;
	document.getElementById('BlubrryBlognamePreview').innerHTML = val;
}

function podPress_set_descriptionPreview(val) {
	document.getElementById('itunesDescriptionPreview').innerHTML = val;
	document.getElementById('PCADescriptionPreview').innerHTML = val;
	document.getElementById('YahooDescriptionPreview').innerHTML = val;
	document.getElementById('PodcastReadyDescriptionPreview').innerHTML = val;
	document.getElementById('BlubrryDescriptionPreview').innerHTML = val;
}

function podPress_set_FeedURLPreview(val) {
	document.getElementById('YahooFeedURLPreview').innerHTML = val;
	document.getElementById('PodcastReadyFeedURLPreview').href = 'http://www.podcastready.com/channel.php?action=subscribe&feedUrl='+val;
}

function podPress_set_AuthorPreview(val) {
	document.getElementById('itunesAuthorPreview').innerHTML = val;
	document.getElementById('YahooAuthorPreview').innerHTML = val;
	document.getElementById('BlubrryAuthorPreview').innerHTML = val;
}

function podPress_set_PreviewImage(val) {
	document.getElementById('iTunesPreviewImage').src = val;
	document.getElementById('YahooPreviewImage').src = val;
	document.getElementById('PodcastReadyPreviewImage').src = val;
	document.getElementById('BlubrryPreviewImage').src = val;
}

function podPress_set_RssLanguagePreview(val) {
	document.getElementById('iTunesRssLanguagePreview').innerHTML = val;
	document.getElementById('YahooRssLanguagePreview').innerHTML = val;
}

function podPress_set_CategoryPreview(val) {
	var nothingStr = '[ ' + podpressL10.nothing + ' ]';
	if ( nothingStr == String(val)) {
		val = '';
	}
	document.getElementById('itunesCategoryPreview').innerHTML = val;
	document.getElementById('PCACategoryPreview').innerHTML = val;
	document.getElementById('PodcastReadyCategoryPreview').innerHTML = val;
}

function podPress_updateFeedSettings() {
	podPress_set_blognamePreview(document.getElementById('blogname').innerHTML);

	if(document.getElementById('iTunesSummary').value == '') {
		if(document.getElementById('blogdescription').innerHTML == '') {
			podPress_set_descriptionPreview(podpressL10.noiTunesSummary);
		} else {
			podPress_set_descriptionPreview(document.getElementById('blogdescription').innerHTML);
		}
	} else {
		podPress_set_descriptionPreview(document.getElementById('iTunesSummary').value);
	}

	podPress_set_FeedURLPreview(document.getElementById('podcastFeedURL').value);

	podPress_set_AuthorPreview(document.getElementById('iTunesAuthor').value);

	var iTunesImageURL = 'http://plugins.svn.wordpress.org/podpress/tags/8.8.10/images/powered_by_podpress_large.png';
	if(document.getElementById('iTunesImage').value == '') {
		if (document.getElementById('rss_image').value != '') {
			iTunesImageURL = document.getElementById('rss_image').value;
		}
	} else {
		iTunesImageURL = document.getElementById('iTunesImage').value;
	}

	podPress_set_PreviewImage(podPress_https_check(iTunesImageURL));

	var rsslanguageIDX = document.getElementById('rss_language').selectedIndex;
	var rssLanguageParts = document.getElementById('rss_language').options[rsslanguageIDX].text.split("[");
	podPress_set_RssLanguagePreview(rssLanguageParts[0]);

	var catArray = document.getElementById('iTunesCategory_0').value.split(':');
	if (catArray[0] != undefined) {
		var cat = catArray[0];
	} else {
		/* var cat = catArray[1]; */
		var cat = '';
	}

	podPress_set_CategoryPreview(cat);

	if(document.getElementById('rss_image').value == '') {
		document.getElementById('rss_imagePreview').src = 'http://plugins.svn.wordpress.org/podpress/tags/8.8.10/images/powered_by_podpress.png';
	} else {
		document.getElementById('rss_imagePreview').src = podPress_https_check(document.getElementById('rss_image').value);
	}

	if(document.getElementById('iTunesImage').value == '') {
		document.getElementById('iTunesImagePreview').src = 'http://plugins.svn.wordpress.org/podpress/tags/8.8.10/images/powered_by_podpress_large.png';
	} else {
		document.getElementById('iTunesImagePreview').src = podPress_https_check(document.getElementById('iTunesImage').value);
	}
}

function podPress_updateCategoryCasting(main_wp_version, taxonomy) {
	var main_wp_version = Number(main_wp_version);
	if (typeof taxonomy == 'undefined') { taxonomy = 'category'; }
	
	if(document.getElementById('categoryCasting').checked == false) { 
		document.getElementById('iTunesSpecificSettings').style.display='none'; 
	} else {
		document.getElementById('iTunesSpecificSettings').style.display='block';
	}
	// the cat_name field is called only name and the form name is different since WP 3.0 
	if ( 2 < main_wp_version ) {
		var cat_name = document.forms['edittag'].name;
		var cat_descr = document.forms['edittag'].description;
	} else {
		if ( 'category' == taxonomy) {
			var cat_name = document.forms['editcat'].cat_name;
			var cat_descr = document.forms['editcat'].category_description;
		} else {
			var cat_name = document.forms['edittag'].name;
			var cat_descr = document.forms['edittag'].description;
		}
	}
	if(document.getElementById('blognameChoice').value == 'Global') { 
		podPress_set_blognamePreview(document.getElementById('global_blogname').value);
	} else if(document.getElementById('blognameChoice').value == 'Append') { 
		podPress_set_blognamePreview(document.getElementById('global_blogname').value+' &raquo; '+cat_name.value);
	} else {
		podPress_set_blognamePreview(cat_name.value);
	}

	if(document.getElementById('blogdescriptionChoice').value == 'Global') { 
		podPress_set_descriptionPreview(document.getElementById('global_blogdescription').value);
	} else {
		podPress_set_descriptionPreview(cat_descr.value);
	}

	if(document.getElementById('iTunesSummaryChoice').value != 'Global') {
		podPress_set_descriptionPreview(document.getElementById('iTunesSummary').value);
		document.getElementById('iTunesSummaryWrapper').style.display=''; 
	} else {
		document.getElementById('iTunesSummaryWrapper').style.display='none'; 
		if(document.getElementById('global_iTunesSummary').value == '') {
			podPress_set_descriptionPreview(document.getElementById('global_iTunesSummary').value);
		}
	}

	if(document.getElementById('iTunesSubtitleChoice').value == 'Global') { 
		document.getElementById('iTunesSubtitleWrapper').style.display='none'; 
	} else {
		document.getElementById('iTunesSubtitleWrapper').style.display='';
	}

	if(document.getElementById('iTunesKeywordsChoice').value == 'Global') { 
		document.getElementById('iTunesKeywordsWrapper').style.display='none'; 
	} else {
		document.getElementById('iTunesKeywordsWrapper').style.display='';
	}

	if(document.getElementById('iTunesAuthorChoice').value == 'Custom') { 
		document.getElementById('iTunesAuthorWrapper').style.display='';
		podPress_set_AuthorPreview(document.getElementById('iTunesAuthor').value);
	} else {
		document.getElementById('iTunesAuthorWrapper').style.display='none'; 
		podPress_set_AuthorPreview(document.getElementById('global_iTunesAuthor').value);
	}

	if(document.getElementById('iTunesAuthorEmailChoice').value == 'Custom') { 
		document.getElementById('iTunesAuthorEmailWrapper').style.display='';
	} else {
		document.getElementById('iTunesAuthorEmailWrapper').style.display='none'; 
	}

	var iTunesImageURL = 'http://plugins.svn.wordpress.org/podpress/tags/8.8.10/images/powered_by_podpress_large.png';
	if(document.getElementById('global_iTunesImage').value == '') {
		if (document.getElementById('global_rss_image').value != '') {
			iTunesImageURL = document.getElementById('global_rss_image').value;
		}
	} else {
		iTunesImageURL = document.getElementById('global_iTunesImage').value;
	}

	if(document.getElementById('iTunesImageChoice').value != 'Global') {
		if(document.getElementById('iTunesImage').value == '') {
			if (document.getElementById('rss_image').value != '') {
				iTunesImageURL = document.getElementById('rss_image').value;
			}
		} else {
			iTunesImageURL = document.getElementById('iTunesImage').value;
		}		
	}
	document.getElementById('iTunesPreviewImage').src = podPress_https_check(iTunesImageURL);
	document.getElementById('YahooPreviewImage').src = podPress_https_check(iTunesImageURL);

	if(document.getElementById('iTunesImageChoice').value == 'Custom') {
		document.getElementById('iTunesImageWrapper').style.display='';
		document.getElementById('itunes_image_display').src = podPress_https_check(document.getElementById('iTunesImage').value);
	} else {
		document.getElementById('iTunesImageWrapper').style.display='none';
		document.getElementById('itunes_image_display').src = podPress_https_check(document.getElementById('global_iTunesImage').value);
	}

	if(document.getElementById('rss_imageChoice').value == 'Custom') {
		document.getElementById('rss_imageWrapper').style.display='';
		document.getElementById('rss_image_Display').src = podPress_https_check(document.getElementById('rss_image').value);
	} else {
		document.getElementById('rss_imageWrapper').style.display='none';
		document.getElementById('rss_image_Display').src = podPress_https_check(document.getElementById('global_rss_image').value);
	}
	if(document.getElementById('rss_copyrightChoice').value == 'Custom') { 
		document.getElementById('rss_copyrightWrapper').style.display='';
	} else {
		document.getElementById('rss_copyrightWrapper').style.display='none'; 
	}
	if(document.getElementById('rss_license_urlChoice').value == 'Custom') { 
		document.getElementById('rss_license_urlWrapper').style.display='';
	} else {
		document.getElementById('rss_license_urlWrapper').style.display='none'; 
	}
	if(document.getElementById('rss_language').value == '##Global##') {
		podPress_set_RssLanguagePreview(document.getElementById('global_rss_language').value);
	} else {
		var rsslanguageIDX = document.getElementById('rss_language').selectedIndex;
		var rssLanguageParts = document.getElementById('rss_language').options[rsslanguageIDX].text.split("[");
		podPress_set_RssLanguagePreview(rssLanguageParts[0]);
	}

	var catArray = document.getElementById('iTunesCategory_0').value.split(':');
	if (catArray[0] != undefined) {
		var cat = catArray[0];
	} else {
		/* var cat = catArray[1]; */
		var cat = '';
	}
	if(cat == '##Global##') {
		podPress_set_CategoryPreview(document.getElementById('global_iTunesCategory').value);
	} else {
		podPress_set_CategoryPreview(cat);
	}

}

function podPressDisplayMediaFiles() {
	for (var i=0; i < podPressMaxMediaFiles; i++) {
		if (podPressMediaFiles[i] != undefined && podPressMediaFiles[i]['showme'] == true) {
			document.getElementById('podPressMediaFileContainer_'+i).style.display = 'block';
		} else {
			document.getElementById('podPressMediaFileContainer_'+i).style.display = 'none';
		}
		podPressSetMediaFileValues(i);
	}
}

function podPressAddMediaFile(showme, txtURI, txtURI_torrent, txttitle, txttype, txtsize, duration, dimensionW, dimensionH, previewImage, rss, atom, feedonly, disablePlayer, content_level) {
	if(rss == 'new') {
		if(podPressMediaFiles.length == 0) {
			rss = true;
		} else if(podPressMediaFileForRSS =='NOTSET') {
			rss = true;
		} else {
			rss = false;
		}
	} 
	var newMediaFileData = new Array();
	newMediaFileData['showme'] = showme;
	newMediaFileData['URI'] = txtURI;
	newMediaFileData['URI_torrent'] = txtURI_torrent;
	newMediaFileData['title'] = txttitle;
	newMediaFileData['type'] = txttype;
	newMediaFileData['size'] = txtsize;
	newMediaFileData['duration'] = duration;
	if( 0 >= Number(dimensionW) ) {
		//dimensionW = newMediaDefaults['dimensionW'];
		dimensionW = document.getElementById('podpress_mediadefault_dimensionW').value;
	}
	if( 0 > Number(dimensionH) ) {
		//dimensionH = newMediaDefaults['dimensionH'];
		dimensionH = document.getElementById('podpress_mediadefault_dimensionH').value;
	}
	newMediaFileData['dimensionW'] = dimensionW;
	newMediaFileData['dimensionH'] = dimensionH;
	if(previewImage == '') {
		previewImage = podPressDefaultPreviewImage;
	}
	newMediaFileData['previewImage'] = previewImage;
	newMediaFileData['rss'] = rss;
	newMediaFileData['atom'] = atom;
	newMediaFileData['feedonly'] = feedonly;
	newMediaFileData['disablePlayer'] = disablePlayer;
	newMediaFileData['content_level'] = content_level;
	podPressMediaFiles[podPressMediaContentNum] = newMediaFileData;
	podPressMediaContentNum++;
}

function podPressRemoveFile(VarNum) {
	var orig_podPressMediaFiles = podPressMediaFiles;
	if(podPressMediaFileForRSS == VarNum) {
		podPressMediaFileForRSS = 'NOTSET';
	}
  podPressMediaFiles = new Array();
	podPressMediaContentNum = 0;
	max = orig_podPressMediaFiles.length;
	for (var i=0; i<=max; i++) {
		if(i != VarNum) {
			podPressMediaFiles[podPressMediaContentNum] = orig_podPressMediaFiles[i];
			podPressMediaContentNum++;
		}
	}
}

function podPressMoveFile(VarNum, Direction) {
	if(Direction == 'up') {
		var newVarNum	= VarNum - 1;
	}

	if(Direction == 'down') {
		var newVarNum	= VarNum + 1;
	}

	if(podPressMediaFiles[newVarNum] != undefined) {
		var holdme = podPressMediaFiles[newVarNum];
		podPressMediaFiles[newVarNum] = podPressMediaFiles[VarNum];
		podPressMediaFiles[VarNum] = holdme;
	}
}

function podPressSetMediaFileValues (VarNum) {
	var data = new Array();
	if(podPressMediaFiles[VarNum] == undefined) {
		data = newMediaDefaults;
	} else {
		data = podPressMediaFiles[VarNum];
	}
	document.getElementById('podPressMedia_'+VarNum+'_URI').value = data['URI'];

	if(document.getElementById('podPressMedia_'+VarNum+'_URI').value != data['URI']) {
		var option = document.createElement('option');
		option.setAttribute('value', data['URI']);
		option.innerHTML = data['URI'];
		option.selected = true;
		document.getElementById('podPressMedia_'+VarNum+'_URI').appendChild(option);
	}

	if(document.getElementById('podPressMedia_'+VarNum+'_URItorrent') != undefined) {
		document.getElementById('podPressMedia_'+VarNum+'_URItorrent').value = data['URI_torrent']; 
	}
	document.getElementById('podPressMedia_'+VarNum+'_title').value = data['title']; 	
	document.getElementById('podPressMedia_'+VarNum+'_type').value = data['type']; 	
	document.getElementById('podPressMedia_'+VarNum+'_size').value = data['size']; 	
	document.getElementById('podPressMedia_'+VarNum+'_duration').value = data['duration']; 	
	document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value = data['dimensionW']; 	
	document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value = data['dimensionH'];
	document.getElementById('podPressMedia_'+VarNum+'_previewImage').value = data['previewImage'];
	document.getElementById('podPressMedia_'+VarNum+'_disablePlayer').checked = data['disablePlayer'];

	podPressMediaSetNonTextInputs(VarNum);
	podPressShowPreviewImage(VarNum);
}

function podPressShowPreviewImage(VarNum) {
	var data = new Array();
	if(podPressMediaFiles[VarNum] == undefined) {
		data = newMediaDefaults;
	} else {
		data = podPressMediaFiles[VarNum];
	}

	var typeOfMedia = data['type'].substring(0,6);
	if (typeOfMedia != 'video_' && data['type'] != 'audio_m4a' && data['type'] != 'audio_mp4' && data['type'] != 'audio_wma' && data['type'] != 'audio_ogg') {
		document.getElementById('podPressMediaPreviewImageWrapper_'+VarNum).style.display = 'none';
		document.getElementById('podPressMediaPreviewImageDisplayWrapper_'+VarNum).style.display = 'none';
	} else {
		if(data['previewImage'] == '') {
			document.getElementById('podPressMediaPreviewImageWrapper_'+VarNum).style.display = '';
			data['previewImage'] = document.getElementById('podPressMedia_defaultpreviewImage').value;
		} else {
			document.getElementById('podPressMediaPreviewImageWrapper_'+VarNum).style.display = '';
			document.getElementById('podPressMediaPreviewImageDisplayWrapper_'+VarNum).style.display = '';
		}
	}
}

function podPressPreviewImageOnChange(VarNum, imageUrl) {
	imageUrl = imageUrl.replace(/<\S[^><]*>/g, '');
	imageUrl = podPress_trim(imageUrl);
	var data = new Array();
	if (podPressMediaFiles[VarNum] == undefined) {
		data = newMediaDefaults;
	} else {
		data = podPressMediaFiles[VarNum];
	}
	data['previewImage'] = imageUrl;
	try {
		document.getElementById('podPressMedia_'+VarNum+'_previewImage').value = imageUrl;
		document.getElementById('podPress_previewImageIMG_'+VarNum).src = imageUrl;
	} catch (e) {
		//alert('preview player without preview image');
	}
}

function podPressMediaSetNonTextInputs(VarNum) {
	var data = new Array();
	if(podPressMediaFiles[VarNum] == undefined) {
		data = newMediaDefaults;
	} else {
		data = podPressMediaFiles[VarNum];
	}
	document.getElementById('podPressMedia_'+VarNum+'_type').value = data['type']; 	
	podPressAdjustMediaFieldsBasedOnType(VarNum);

	podPressSetSingleRSS(VarNum);
	document.getElementById('podPressMedia_'+VarNum+'_atom').checked = data['atom']; 
	document.getElementById('podPressMedia_'+VarNum+'_feedonly').checked = data['feedonly']; 
	if(document.getElementById('podPressMedia_'+VarNum+'_content_level') != undefined) {
		document.getElementById('podPressMedia_'+VarNum+'_content_level').value = data['content_level'];
	}
}

function podPressSetSingleRSS(VarNum) {
	var data = new Array();
	if(podPressMediaFiles[VarNum] == undefined) {
		data = newMediaDefaults;
	} else {
		data = podPressMediaFiles[VarNum];
	}
	if(podPressMediaFileForRSS == VarNum && data['rss'] == false) {
		podPressMediaFileForRSS = 'NOTSET';
		document.getElementById('podPressMedia_'+VarNum+'_rss').checked = data['rss']; 	
	}
	if(podPressMediaFileForRSS == 'NOTSET' && data['rss'] == true) {
		podPressMediaFileForRSS = VarNum;
	}
	if(podPressMediaFileForRSS != VarNum && podPressMediaFileForRSS != 'NOTSET' && data['rss'] == true) {
		if(podPressMediaFiles[podPressMediaFileForRSS] != undefined) {
			podPressMediaFiles[podPressMediaFileForRSS]['rss'] = false;
		}
		if(document.getElementById('podPressMedia_'+podPressMediaFileForRSS+'_rss') != undefined) {
			document.getElementById('podPressMedia_'+podPressMediaFileForRSS+'_rss').checked = podPressMediaFiles[podPressMediaFileForRSS]['rss']; 	
		}
		podPressMediaFileForRSS = VarNum;
	}
	if(podPressMediaFileForRSS == VarNum && data['rss'] == true) {
		document.getElementById('podPressMedia_'+VarNum+'_rss').checked = true; 
	}
}

function podPress_isset_in_the_list(value, list_id) {	
	var list = document.getElementById( list_id );
	var listlen = list.length;
	var is_in = false;
	var index = 0;
	for (var i = 0; i < listlen; i++ ) {
		if ( value == list.options[i].value ) {
			is_in = true;
			index=i;
		}
	}
	return Array(is_in, index);
}

function podPressAdjustMediaFieldsBasedOnType(VarNum) {
	var data = new Array();
	if(podPressMediaFiles[VarNum] == undefined) {
		data = newMediaDefaults;
	} else {
		data = podPressMediaFiles[VarNum];
	}
	var typeOfMedia = data['type'].substring(0,6);
	if(typeOfMedia == 'audio_' || typeOfMedia == 'video_') {
		document.getElementById('podPressMediaDurationWrapper_'+VarNum).style.display=''; 	
	} else {
		document.getElementById('podPressMediaDurationWrapper_'+VarNum).style.display='none'; 	
	}
	if (typeOfMedia == 'video_' || data['type'] == 'audio_m4a' || data['type'] == 'audio_mp4' || data['type'] == 'audio_wma' || data['type'] == 'audio_ogg') {
		document.getElementById('podPressMediaPreviewImageWrapper_'+VarNum).style.display='';
		document.getElementById('podPressMediaDimensionWrapper_'+VarNum).style.display='';
		if ('video_' == typeOfMedia) {
			if ( 0 >= Number(data['dimensionW']) || 0 >= Number(data['dimensionH'])) {// this means: if nothing is saved then set the default values 
				data['dimensionW'] = document.getElementById('podpress_mediadefault_dimensionW').value;
				data['dimensionH'] = document.getElementById('podpress_mediadefault_dimensionH').value;
			}
		} else {
			//~ alert(data['type']+': '+String(data['dimensionW']) + ' : ' + String(data['dimensionH']) + ' - '+String(newMediaDefaults['dimensionW']) + ' : ' + String(newMediaDefaults['dimensionH']));
			if ( 0 >= Number(data['dimensionW']) || 0 > Number(data['dimensionH'])) {
				data['dimensionW'] = document.getElementById('podpress_mediadefault_dimensionW').value;
				data['dimensionH'] = 0;
			}
			//~ alert(data['type']+': '+String(data['dimensionW']) + ' : ' + String(data['dimensionH']));
		}
		document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value = data['dimensionW'];
		document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value = data['dimensionH'];
		var dimensionsStr = String(document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value)+':'+String(document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value);
		var is_in = podPress_isset_in_the_list(dimensionsStr, 'podPressMedia_'+VarNum+'_dimensionList');
		if ( true == is_in[0] ) {
			document.getElementById('podPressMedia_'+VarNum+'_dimensionList').selectedIndex = is_in[1];
		}
		//document.getElementById('podPressMedia_'+VarNum+'_id3tags_details_row').style.display = 'block';
		document.getElementById('podPressMedia_'+VarNum+'_id3tags_details_row').style.visibility = 'visible';
		podPressPreviewImageOnChange(VarNum, document.getElementById('podPressMedia_'+VarNum+'_previewImage').value);
		podPressUpdateDimensions(VarNum, dimensionsStr);
	} else if (typeOfMedia == 'embed_') {
		document.getElementById('podPressMediaPreviewImageWrapper_'+VarNum).style.display='';
		document.getElementById('podPressMediaPreviewImageDisplayWrapper_'+VarNum).style.display='';
		document.getElementById('podPressMediaDimensionWrapper_'+VarNum).style.display=''; 
		document.getElementById('podPressMediaDurationWrapper_'+VarNum).style.display='none'; 
		document.getElementById('podPressMediaSizeWrapper_'+VarNum).style.display='none'; 
 		if ( 0 >= Number(data['dimensionW']) || 0 >= Number(data['dimensionH'])) { // this means: if nothing is saved then set the default values 
			data['dimensionW'] = document.getElementById('podpress_mediadefault_dimensionW').value;
			data['dimensionH'] = document.getElementById('podpress_mediadefault_dimensionH').value;
			document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value = data['dimensionW'];
			document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value = data['dimensionH'];
		}
		document.getElementById('podPressMedia_'+VarNum+'_id3tags_details_row').style.visibility = 'hidden';
		podPressPreviewImageOnChange(VarNum, document.getElementById('podPressMedia_'+VarNum+'_previewImage').value);
		podPressUpdateDimensions(VarNum, String(document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value)+':'+String(document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value));
	} else {
		document.getElementById('podPressMediaPreviewImageWrapper_'+VarNum).style.display='none';
		document.getElementById('podPressMediaPreviewImageDisplayWrapper_'+VarNum).style.display='none';
		document.getElementById('podPressMediaDimensionWrapper_'+VarNum).style.display='none';
		data['dimensionW'] = 0;
		data['dimensionH'] = 0;
		document.getElementById('podPressMedia_'+VarNum+'_dimensionW').value = data['dimensionW'];
		document.getElementById('podPressMedia_'+VarNum+'_dimensionH').value = data['dimensionH'];
		//document.getElementById('podPressMedia_'+VarNum+'_id3tags_details_row').style.display = 'block';
		document.getElementById('podPressMedia_'+VarNum+'_id3tags_details_row').style.visibility = 'visible';
	}
}

function podPressDetectType(VarNum) {
	var data = new Array();
	if(podPressMediaFiles[VarNum] == undefined) {
		return;
	}
	var lenOfMedia = podPressMediaFiles[VarNum]['URI'].length;

	if(podPressMediaFiles[VarNum]['URI'].substring(0, 24) == 'http://youtube.com/watch') {
		var extOfMedia = 'youtube';
	} else if(podPressMediaFiles[VarNum]['URI'].substring(0, 28) == 'http://www.youtube.com/watch') {
		var extOfMedia = 'youtube';
	} else if(podPressMediaFiles[VarNum]['URI'].substring(lenOfMedia-8, lenOfMedia) == '.torrent') {
		var extOfMedia = 'torrent';
	} else if(podPressMediaFiles[VarNum]['URI'].substring(lenOfMedia-3, lenOfMedia-2) == '.') {
		var extOfMedia = podPressMediaFiles[VarNum]['URI'].substring(lenOfMedia-2, lenOfMedia);
	} else if(podPressMediaFiles[VarNum]['URI'].substring(lenOfMedia-4, lenOfMedia-3) == '.') {
		var extOfMedia = podPressMediaFiles[VarNum]['URI'].substring(lenOfMedia-3, lenOfMedia);
	} else if(podPressMediaFiles[VarNum]['URI'].substring(lenOfMedia-5, lenOfMedia-4) == '.') {
		var extOfMedia = podPressMediaFiles[VarNum]['URI'].substring(lenOfMedia-4, lenOfMedia);
	} else {
		var extOfMedia = '';
	}
	extOfMedia = extOfMedia.toLowerCase();
	var result = '';
	switch(extOfMedia) {
		case 'mp3': result = 'audio_mp3'; break
		case 'ogg': result = 'audio_ogg'; break
		case 'ogv': result = 'video_ogv'; break
		case 'opus': result = 'audio_opus'; break
		case 'm4a': result = 'audio_m4a'; break
		case 'aa':  result = 'audio_aa'; break
		case 'm3u': result = 'audio_m3u'; break
		case 'mp4': result = 'video_mp4'; break
		case 'm4v': result = 'video_m4v'; break
		case 'mov': result = 'video_mov'; break
		case 'qt':  result = 'video_qt'; break
		case 'avi': result = 'video_avi'; break
		case 'mpg': result = 'video_mpg'; break
		case 'peg': result = 'video_mpg'; break
		case 'asf': result = 'video_asf'; break
		case 'wma': result = 'audio_wma'; break
		case 'wmv': result = 'video_wmv'; break
		case 'flv': result = 'video_flv'; break
		case 'swf': result = 'video_swf'; break
		case 'pdf': result = 'ebook_pdf'; break
		case 'epub': result = 'ebook_epub'; break
		case 'torrent': result = 'misc_torrent'; break
		case 'youtube': result = 'embed_youtube'; break
		default: result = 'misc_other';
	}
	document.getElementById('podPressMedia_'+VarNum+'_type').value=result;
	podPressMediaFiles[VarNum]['type'] = result;
	podPressAdjustMediaFieldsBasedOnType(VarNum);
}

function podPress_colorSet(hval) {
	if (!podPress_colorLocked) {
		if(document.getElementById(podPress_colorInput) != undefined) {
			document.getElementById(podPress_colorInput).value = hval;
			document.getElementById(podPress_colorInput).style.background = hval;
		}
	}
}
function podPress_switchColorInputs(idname) {
	if(idname != podPress_colorInput) {
		podPress_colorInput = idname;
		podPress_colorLocked = false;
	}
}
function podPress_colorLock() {
	if (podPress_colorLocked)
	{
		podPress_colorLocked = false;
	} else {
		podPress_colorLocked = true;
	}
}
function podPress_colorReset() {
	document.getElementById('playerSettings_bg_').value = '#E5E5E5';
	document.getElementById('playerSettings_bg_').style.background = '#E5E5E5';
	document.getElementById('playerSettings_text_').value = '#333333';
	document.getElementById('playerSettings_text_').style.background = '#333333';
	document.getElementById('playerSettings_leftbg_').value = '#CCCCCC';
	document.getElementById('playerSettings_leftbg_').style.background = '#CCCCCC';
	document.getElementById('playerSettings_lefticon_').value = '#333333';
	document.getElementById('playerSettings_lefticon_').style.background = '#333333';
	
	document.getElementById('playerSettings_volslider_').value = '#666666';
	document.getElementById('playerSettings_volslider_').style.background = '#666666';
	document.getElementById('playerSettings_voltrack_').value = '#FFFFFF';
	document.getElementById('playerSettings_voltrack_').style.background = '#FFFFFF';
	
	document.getElementById('playerSettings_rightbg_').value = '#B4B4B4';
	document.getElementById('playerSettings_rightbg_').style.background = '#B4B4B4';
	document.getElementById('playerSettings_rightbghover_').value = '#999999';
	document.getElementById('playerSettings_rightbghover_').style.background = '#999999';
	document.getElementById('playerSettings_righticon_').value = '#333333';
	document.getElementById('playerSettings_righticon_').style.background = '#333333';
	document.getElementById('playerSettings_righticonhover_').value = '#FFFFFF';
	document.getElementById('playerSettings_righticonhover_').style.background = '#FFFFFF';
	
	document.getElementById('playerSettings_loader_').value = '#009900';
	document.getElementById('playerSettings_loader_').style.background = '#009900';
	document.getElementById('playerSettings_track_').value = '#FFFFFF';
	document.getElementById('playerSettings_track_').style.background = '#FFFFFF';
	document.getElementById('playerSettings_border_').value = '#CCCCCC';
	document.getElementById('playerSettings_border_').style.background = '#CCCCCC';
	
	document.getElementById('playerSettings_tracker_').value = '#DDDDDD';
	document.getElementById('playerSettings_tracker_').style.background = '#DDDDDD';
	document.getElementById('playerSettings_skip_').value = '#666666';
	document.getElementById('playerSettings_skip_').style.background = '#666666';
	
	document.getElementById('playerSettings_slider_').value = '#666666';
	document.getElementById('playerSettings_slider_').style.background = '#666666';
}
function podpress_checkonlyone( otherID, thisID ) {
	if ( document.getElementById(thisID).checked == true && document.getElementById(otherID).checked == true ) {
		document.getElementById(otherID).checked = false;
	}
}
function podPress_customSelectVal(select_elm, prompt_text){
	var val = prompt(prompt_text, '');
	var option = document.createElement('option');
	option.setAttribute('value', val);
	option.innerHTML = val;
	option.selected = true;
	select_elm.appendChild(option);
}