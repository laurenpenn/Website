<?PHP
if (!defined('ABSPATH')) 
	die();


?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( __('BackWPup Working', 'backwpup') ); ?></h2>

<?php if (isset($backwpup_message) and !empty($backwpup_message)) : ?>
	<div id="message" class="updated"><p><?php echo $backwpup_message; ?></p></div>
<?php endif; 	
	if ($infile=backwpup_get_working_file()) {
		wp_nonce_field('backwpupworking_ajax_nonce', 'backwpupworkingajaxnonce', false );
		$logfilarray=backwpup_read_logfile(trim($_GET['logfile']));
		if (isset($_GET['action']) and defined('ALTERNATE_WP_CRON') and ALTERNATE_WP_CRON and $_GET['action']=='runnow') {
			echo "<input type=\"hidden\" name=\"backwpupworkingajaxurl\" id=\"backwpuprunurl\" value=\"".BACKWPUP_PLUGIN_BASEURL."/job/job_run.php\">";
			echo "<input type=\"hidden\" name=\"alternate_wp_cron\" id=\"alternate_wp_cron\" value=\"1\">";
			echo "<input type=\"hidden\" name=\"alternate_wp_cron_nonce\" id=\"alternate_wp_cron_nonce\" value=\"".$infile['WORKING']['NONCE']."\">";
		}
		echo "<input type=\"hidden\" name=\"logfile\" id=\"logfile\" value=\"".trim($_GET['logfile'])."\">";
		echo "<input type=\"hidden\" name=\"logpos\" id=\"logpos\" value=\"".count($logfilarray)."\">";
		echo "<input type=\"hidden\" name=\"backwpupworkingajaxurl\" id=\"backwpupworkingajaxurl\" value=\"".BACKWPUP_PLUGIN_BASEURL."/job/show_working.php\">";			
		echo "<div id=\"showworking\">";
		for ($i=0;$i<count($logfilarray);$i++)
			echo $logfilarray[$i]."\n";
		echo "</div>";
		echo "<div id=\"runniginfos\">";
		$stylewarning=" style=\"display:none;\"";
		if ($infile['WORKING']['WARNING']>0)
			$stylewarning="";
		echo "<span id=\"warningsid\"".$stylewarning.">".__('Warnings:','backwpup')." <span id=\"warnings\">".$infile['WORKING']['WARNING']."</span></span><br/>";
		$styleerror=" style=\"display:none;\"";
		if ($infile['WORKING']['ERROR']>0)
			$styleerror="";		
		echo "<span id=\"errorid\"".$styleerror.">".__('Errors:','backwpup')." <span id=\"errors\">".$infile['WORKING']['ERROR']."</span></span>";
		echo "<div>";
		echo "<div class=\"clear\"></div>";
		echo "<div class=\"progressbar\"><div id=\"progressstep\" style=\"width:".$infile['STEPSPERSENT']."%;\">".$infile['STEPSPERSENT']."%</div></div>";
		echo "<div class=\"progressbar\"><div id=\"progresssteps\" style=\"width:".$infile['STEPPERSENT']."%;\">".$infile['STEPPERSENT']."%</div></div>";
	} elseif (is_file(trim($_GET['logfile']))) {
		echo '<div id="showlogfile">';
		foreach (backwpup_read_logfile(trim($_GET['logfile'])) as $line)
			echo $line."\n";
		echo "</div>";
		echo "<div class=\"clear\"></div>";
	}
	?>
</div>