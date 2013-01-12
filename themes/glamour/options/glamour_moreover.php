<style>
	.widefat td{
		vertical-align:middle;
		padding-top:20px;
		padding-bottom:20px;
	}
</style>

<div class="wrap nosubsub">
	<div id="icon-link-manager" class="icon32"><br /></div><h2>The Deluxe Some Settings</h2><br  />
	
	<div class="clear"></div>

	<form action="options.php" method="post">
	<?php wp_nonce_field('update-options'); ?>

	<table class="widefat" cellspacing="0">
	
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name" colspan="3">Morever Table Of Content</th>
			</tr>
			</thead>
			
			<tfoot>
			<tr>
				<th scope="col"  class="manage-column column-name" colspan="3"></th>
			</tr>
		</tfoot>
		<tbody>
	
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Home Page First Menu Enable ? <input name="disable_home_page" type="radio" value="0" <?php if(get_option('disable_home_page') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
				<input name="disable_home_page" type="radio" value="1" <?php if(get_option('disable_home_page', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
				<br/><br/>
				Home Page First Menu Name <input type="text" name="home_page_name" tabindex="1" size="20" value="<?php echo get_option('home_page_name', 'Home'); ?>" />
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>
		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				SubMenu Fade Effect Enable ? <input name="disable_submenu_effect" type="radio" value="0" <?php if(get_option('disable_submenu_effect') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
				<input name="disable_submenu_effect" type="radio" value="1" <?php if(get_option('disable_submenu_effect', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
			</td>
		</tr>
		
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>
		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Shortcut Enable ? <input name="disable_shortcut" type="radio" value="0" <?php if(get_option('disable_shortcut') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
				<input name="disable_shortcut" type="radio" value="1" <?php if(get_option('disable_shortcut', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>
		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Navigation Title ? <input type="text" name="navigation_title" tabindex="1" size="20" value="<?php echo get_option('navigation_title', 'Navigation'); ?>" />
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Leave a Reply <input type="text" name="leave_comment" tabindex="1" size="20" value="<?php echo get_option('leave_comment', 'Leave a Reply'); ?>" />
				<br/>
				<br/>
				Post Comment <input type="text" name="post_comment" tabindex="1" size="20" value="<?php echo get_option('post_comment', 'Post Comment'); ?>" />
				<br/>
				<br/>
				Comment Form Name <input type="text" name="cf_name" tabindex="1" size="20" value="<?php echo get_option('cf_name', 'Name'); ?>" />
				<br/>
				<br/>
				Comment Form Email <input type="text" name="cf_email" tabindex="1" size="20" value="<?php echo get_option('cf_email', 'Email Address'); ?>" />
				<br/>
				<br/>
				Comment Form  Message <input type="text" name="cf_comment" tabindex="1" size="20" value="<?php echo get_option('cf_comment', 'Comment'); ?>" />
				<br/>
				<br/>
				Comment Form WebSite <input type="text" name="cf_web" tabindex="1" size="20" value="<?php echo get_option('cf_web', 'Web Site'); ?>" />
				<br/>
				<br/>
				Comment Form (* required) <input type="text" name="cf_required" tabindex="1" size="20" value="<?php echo get_option('cf_required', '(* required)'); ?>" />
				<br/>
				<br/>
				Comment Form (*will not be published) <input type="text" name="cf_will_not" tabindex="1" size="40" value="<?php echo get_option('cf_will_not', '( * required - will not be published)'); ?>" />
				<br/>
				<br/>
				Comment form (Your comment is awaiting moderation.) <input type="text" name="cf_awaiting" tabindex="1" size="50" value="<?php echo get_option('cf_awaiting', 'Your comment is awaiting moderation.'); ?>" />
				<br/>
				<br/>
				Comment form (One Comment) <input type="text" name="cf_one_comment" tabindex="1" size="20" value="<?php echo get_option('cf_one_comment', 'One Comment'); ?>" />
				<br/>
				<br/>
				Comment form (Comments) <input type="text" name="cf_one_comments" tabindex="1" size="20" value="<?php echo get_option('cf_one_comments', 'Comments'); ?>" />
				<br/>
				<br/>
				Comment form (No Comments) <input type="text" name="cf_no_comments" tabindex="1" size="20" value="<?php echo get_option('cf_no_comments', 'No Comments'); ?>" />
				<br/>
				<br/>
				Comment form (No Comments) <input type="text" name="cf_off_comments" tabindex="1" size="20" value="<?php echo get_option('cf_off_comments', 'Comments Off'); ?>" />
				<br/>
				<br/>
				
				
				
				
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>
		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Site Keywords : <input type="text" name="sitekeywords" tabindex="1" size="60" value="<?php echo get_option('sitekeywords', 'the, deluxe, web sites, clean, dark, useful, etc...'); ?>" />
			</td>
		</tr>
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>
		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Serach Results : <input type="text" name="searchword" tabindex="1" size="30" value="<?php echo get_option('searchword', 'Search Results'); ?>" />
			</td>
		</tr>
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Not Post Found : <input type="text" name="notfound" tabindex="1" size="50" value="<?php echo get_option('notfound', 'No posts found. Try a different search?'); ?>" />
			</td>
		</tr>
		
		
				<tr>
			<td class="column-name" colspan="3"></td>
		</tr>
		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				Google Analytics Code : <textarea rows='3' style="width:100%;" name='google_analytics'><?php echo get_option('google_analytics'); ?></textarea>
			</td>
		</tr>

		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		
		<tr class="alternate">
			<td class="column-name" colspan="3">
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="google_analytics,notfound, searchword, sitekeywords, disable_submenu_effect, home_page_name, disable_home_page, cf_no_comments, cf_off_comments, disable_shortcut, navigation_title, leave_comment, post_comment, cf_name, cf_email, cf_comment, cf_web, cf_required, cf_will_not, cf_awaiting, cf_one_comment, cf_one_comments" />
				<input type="submit" name="publish" id="publish" accesskey="p" tabindex="5" class="button-primary" value="Save" />
				</form>
			</td>
		</tr>

	
		</tbody>
	</table>

			
</div>
