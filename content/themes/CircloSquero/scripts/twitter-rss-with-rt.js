/// <summary>
///    Uses the parameters provided to retrieve the latest tweets, including retweets that were
///    created either with the original or new twitter process and outputs a number of <li> items
///    to the <ul> control with the id provided
/// </summary>
/// <param name="username">(string) Your twitter username</param>
/// <param name="maxTweets">(int) The maximum number of tweets to return</param>
/// <param name="outputElementId">(string) The id given to the <ul> element that will contain the tweets. 
///
function GetTwitterFeedIncRT(username, maxTweets, outputElementId, WidgetSidebar)
{
	if (maxTweets == null) maxTweets = 10;

	var url = 'http://twitter.com/statuses/user_timeline/'+username+'.atom?count='+maxTweets+'&include_rts=true&callback=?'
	var gurl = "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&callback=?&num="+maxTweets+"&q="+url;

	var fnk = function(feeds) 
				 {
					var statusHTML = [];
					for(var i=0; i<feeds.entries.length; i++){
					var entry = feeds.entries[i];

					var status = entry.title.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, 
														function(url) { return '<a href="'+url+'">'+url+'</a>';})
												   .replace(/\B@([_a-z0-9]+)/ig, 
														function(reply) { return reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';})
													.replace(username + ': ', 
														function(clean) { return clean.substring(username.length + 3); });

					var timeValues = entry.publishedDate.split(" ");
  					var time_value = timeValues[1] + " " + timeValues[2] + " " + timeValues[3] + ", " + timeValues[4];

  					var parsed_date = Date.parse(time_value);
					var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
  					var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
  					delta = delta + (relative_to.getTimezoneOffset() * 60);

					var relativeTime = '';

		  			if (delta < 60) {
			 				relativeTime = 'less than a minute ago';
					} else if(delta < 120) {
						relativeTime = 'about a minute ago';
					} else if(delta < (60*60)) {
						relativeTime = (parseInt(delta / 60)).toString() + ' minutes ago';
					} else if(delta < (120*60)) {
						relativeTime = 'about an hour ago';
					} else if(delta < (24*60*60)) {
						relativeTime = 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
					} else if(delta < (48*60*60)) {
						relativeTime = '1 day ago';
					} else {
						relativeTime = (parseInt(delta / 86400)).toString() + ' days ago';
					}
					if (WidgetSidebar == 1) 
					statusHTML.push('<li><span>'+status+'</span> </li><div class="twitterwidgetbg"></div><span class="twitterwidgetusername"><a href="http://twitter.com/'+username+'/">'+username+'</a>, '+relativeTime+'</span>');
					else {
						statusHTML.push('<li>' +
										 '<span>' + status + '</span>' + 
										 '&nbsp;<a style="font-size:85%" href="' + entry.link +'">' + 
										 relativeTime +
										 '</a>' +
										'</li>');

					}
				}

				

				document.getElementById(outputElementId).innerHTML = statusHTML.join('');
			};

	$.getJSON(gurl, 
		  function(data)
		  {
			  if(typeof fnk == 'function')
			fnk.call(this, data.responseData.feed);
			  else
			return false;
		  });
}	

