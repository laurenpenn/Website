<?php
/**
 * Template Name: Share
 *
 * Used to promote DBC to visitors and share with Facebook friends.
 * Full width. No primary or secondary sidebars.
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<style type="text/css">
		#facebook-invite {
			background: #3B5998;
			border: 1px solid #133783;
			box-shadow: 0 0 2px rgba(0, 0, 0, 0.52);
			color: #fff;
			font: bold 0.875em/1em "lucida grande",tahoma,verdana,arial,sans-serif;
			line-height: 1em;
			padding: 10px;
			text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.6);
		}
		#facebook-invite-intro .highlight {
			display: inline-block;
			font: normal 2.25em/1em "LeagueGothicRegular";
			margin: 5px 15px 0 0;
			text-transform: uppercase;
			vertical-align: middle;
		}
		#facebook-invite a {
			text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.6);
			border: 1px solid #133783;
			background: none;
			color: #fff;
			float: right;
			padding: 8px 15px;
			-moz-box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			-webkit-box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			-o-box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			box-shadow: 0 1px 0px rgba(255, 255, 255, 0.2), inset 0 1px 4px rgba(0, 0, 0, 0.2);
			cursor: pointer;
			border-image: initial;
			border-radius: 4px;
		}
		#facebook-invite a:hover {
			background: #2B4477;
		}
		#facebook-invite .fb-send {
			margin: 0 20px 0 50px;
			vertical-align: middle;
		}
		.layout-default #content {
			padding: 0;
			width: 720px;
		}
		#content section {
			font: 1.15em/1.5em "Georgia", "Times New Roman", Times, Serif;
			-moz-box-shadow: inset 0 5px 5px rgba(0, 0, 0, 0.2);
			-webkit-box-shadow: inset 0 5px 5px rgba(0, 0, 0, 0.2);
			-o-box-shadow: inset 0 5px 5px rgba(0, 0, 0, 0.2);
			box-shadow: inset 0 5px 5px rgba(0, 0, 0, 0.2);
			padding: 60px 50px 40px;
		}
		#content section h2 {
			color: #15100E;
			font: 3em/1em "LeagueGothicRegular";
			text-transform: uppercase;
		}
		#content section h3 {
			color: #666;
			font: 2em/1em "LeagueGothicRegular";
			text-transform: none;
		}
		#content section p.intro:first-letter {
			float: left;
			font-size: 3.6em;
			line-height: .8em;
			margin-right: 3px;
			padding: 2px 2px 0;
		}
		#content section .right {
			margin: 0 40px 40px 50px;
		}
		#content section .left {
			margin: 0 50px 0 40px;
		}
		#content section .caption p {
			margin: 0 0 5px;
		}
		#content section .column {
			display: inline;
			width: 360px;
		}
		#content section .column.left {
			float: left;
		}
		#content section .gadget .title,
		#content section .gadget .powered {
			display: none;
		}
		#content section .gadget tr:first-child,
		#content section .gadget tr:nth-child(2),
		#content section .gadget tr:nth-child(4) {
			display: none;
		}
		#content section .gadget div {
			border: none !important;
		}
	</style>
	
	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>
	
		<div id="facebook-invite">
			
			<div id="facebook-invite-intro">
				
				<div class="fb-send" data-href="http://dentonbible.org/about-us/visitor-information/"></div> Click "Send" and choose which Facebook friends to invite
					
			</div>
			
		</div>
		
		<section>
			<h2>Thinking of Visiting Denton Bible Church?</h2>

			<div class="caption right">
			
				<p><img src="http://sheasumlin.files.wordpress.com/2009/07/tom_nelson_01.jpg" width="200" /></p>
				
				<p>Tom Nelson<br />
				Senior Pastor</p>
			
			</div>

			<p class="intro">Home is important. It is where we get our identity, our sure foundation, our truth for life. It is a place for valued guests. As our valued guest, we’d like to welcome you to our home.</p>

			<p>The purpose of this web site is to help you feel at home when you visit Denton Bible. It is our desire that you feel comfortable enough that you drop off your bags and stay for a while. In order to overcome the fear, uncertainty, and confusion that goes along with visiting a new place, this web site is here to inform you of the different places in this home that you can connect with.</p>

			<h3>Get Directions</h3>
			
			<script src="//www.gmodules.com/ig/ifr?url=http://hosting.gmodules.com/ig/gadgets/file/114281111391296844949/driving-directions.xml&amp;up_fromLocation=&amp;up_myLocations=2300%20East%20University%20Drive%2C%20Denton%2C%20TX%2076209-7806%20(Denton%20Bible%20Church)&amp;up_defaultDirectionsType=&amp;up_autoExpand=&amp;synd=open&amp;w=620&amp;h=55&amp;title=Directions+by+Google+Maps&amp;brand=light&amp;lang=en&amp;country=US&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js"></script>			
			
		</section>

		<section>
			<h2>When are the services?</h2>
						
			<img src="http://aux.iconpedia.net/uploads/1516167603212363444.png" class="left" width="75" /></p>

			<p>Denton Bible offers traditional worship with a choir and orchestra at both our <strong>9:00 A.M.</strong> and <strong>11:00 A.M.</strong> services, and a contemporary worship service at <strong>6:00 P.M.</strong></p>

			<p>Sunday School classes for children are offered as follows:</p>
			
			<ul>
				<li>9:00 A.M. – 4 months through 4th grade</li>
				<li>11:00 A.M. – 4 months through high school</li>
				<li>6:00 P.M. – Childcare: 4 months through 42 months &amp; AWANA: 3 years through 12th Grade</li>
			</ul>
			<p><small>The 11:00 A.M. service offers sign interpretation for the hearing impaired.</small></p>

			<h3>When should I arrive?</h3>

			<p>We suggest arriving 10 minutes before the service begins if you are not bringing children with you, or 20 minutes early if you are bringing children. Be sure to look for our Guest Parking, located in the front of the building close to the Chapel and Children’s Learning Center. When you arrive please visit us Starting Point to get information about the church and a cup of coffee and refreshments.</p>
		
		</section>

		<section>
			<h2>What should I wear?</h2>
						
			<p class="intro">Simply, whatever you like. You will not see very many ties, since most of our people prefer a casual dress. We know God is much more concerned with a person’s heart than their clothes. However, a person’s outside reflects their inside. So, all should dress modestly and have a reverence for where they are and who they are among.</p>

		</section>

		<section>
			<h2>Where do I go when I get there?</h2>
						
			<p class="intro">As you enter the building please follow the signs to STARTING POINT. It is located across from the chapel and exists to welcome you to Denton Bible and help you understand how to get where you want to go. Look for our greeters (they will be wearing nametags and are located at each door). They are available every Sunday morning to assist you and to find answers to your questions.</p>
			
			<p>If you don’t have time before the service, please join us at Starting Point afterwards. We would love to have the opportunity to meet you, share a cup of coffee and refreshments with you and answer any questions you have about DBC.</p>

		</section>

		<section>
			<h2>How will I fit in?</h2>
						
			<p class="intro">DBC is a big church, and it is easy to feel lost in the crowd. One of the best ways to fit in is to get involved in one of DBC’s many ministries. Learn more about our ministries (Sunday school, small groups, volunteer teams, etc.) at the STARTING POINT Information Center. The relationships that are built in these ministries will help you feel connected and make DBC easier to call “home.”</p>
			
			<p>For more information. Please contact Mike Spencer, ChurchLife Pastor, at (940) 297-6838 or mspencer@dentonbible.org.</p>

		</section>

		<section>
			<h2>How do I check in my children?</h2>
						
			<p class="intro">All parents and children check into the Children’s Learning Center at computer stations on either floor. Our greeters will assist you. Each family receives an alpha-numeric code for the day which is printed on the name tag for each family member. Children receive two name tags – one to place on their person and one to be given to the classroom greeter. Classroom staff will verify the family number when releasing the children to parents.</p>
			
			<p>Parents are requested to wear their name tags. You will not be allowed to reenter the learning center without it.</p>
			
			<p>You will be paged on the large screen in the main auditorium using your family “code” for the day if your child should need you. If you will be attending a class in another location, please tell the classroom greeter and leave a cell phone number. Please set your phone on vibrate.</p>

		</section>

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>