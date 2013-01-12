<?PHP
if (!defined('ABSPATH')) {
	die();
}

global $wpdb;	

ignore_user_abort(true);
$cfg=get_option('backwpup'); //Load Settings
@set_time_limit($cfg['jobscriptruntimelong']); //300 is most webserver time limit.
	
//Vars
$oldblogabspath="";
$oldblogurl="";
$oldtabelprefix="";
$numcommands="";
if (defined(WP_SITEURL))
	$blogurl=trailingslashit(WP_SITEURL);
else
	$blogurl=trailingslashit(get_option('siteurl'));
$blogabspath=trailingslashit(ABSPATH);

$file = fopen ($sqlfile, "r");
while (!feof($file)){
	$line = trim(fgets($file));
	
	if (substr($line,0,12)=="-- Blog URL:")
		$oldblogurl=trim(substr($line,13));
	if (substr($line,0,16)=="-- Blog ABSPATH:")
		$oldblogabspath=trim(substr($line,17));
	if (substr($line,0,16)=="-- Table Prefix:") {
		$oldtabelprefix=trim(substr($line,17));
		if ($oldtabelprefix!=$wpdb->prefix and !empty($oldtabelprefix)) {
			echo __('ERROR:','backwpup').' '.sprintf(__('Pleace set <i>$table_prefix  = \'%1$s\';</i> in wp-config.php','backwpup'), $oldtabelprefix)."<br />\n";
			break;
		}
	}	
	if (substr($line,0,2)=="--" or empty($line))
		continue;
	
	$line=str_replace("/*!40000","", $line);
	$line=str_replace("/*!40101","", $line);
	$line=str_replace("/*!40103","", $line);
	$line=str_replace("/*!40014","", $line);
	$line=str_replace("/*!40111","", $line);
	$line=str_replace("*/;",";", trim($line));
	
	if (substr($line,0,9)=="SET NAMES") {
		$chrset=trim(str_replace("'","",substr($line,10,-1)));
		if (function_exists("mysql_set_charset"))
			mysql_set_charset($chrset);
		if ((defined('DB_CHARSET') and $chrset!=DB_CHARSET) or ($chrset!=mysql_client_encoding())) {
			echo __('ERROR:','backwpup').' '.sprintf(__('Pleace set <i>define(\'DB_CHARSET\', \'%1$s\');</i> in wp-config.php','backwpup'), $chrset)."<br />\n";
			break;
		}
	}
	
	$command="";
	if (";"==substr($line,-1)) {
		$command=$rest.$line;
		$rest="";
	} else {
		$rest.=$line;
	}
	if (!empty($command)) {
		$result=mysql_query($command);
		if ($sqlerr=mysql_error($wpdb->dbh)) {
			echo __('ERROR:','backwpup').' '.sprintf(__('BackWPup database error %1$s for query %2$s','backwpup'), $sqlerr, $command)."<br />\n";
		}
		$numcommands++;
	}
}
fclose($file);
echo sprintf(__('%1$s Database Querys done.','backwpup'),$numcommands).'<br />';
echo __('Make changes for Blogurl and ABSPATH if needed.','backwpup')."<br />";
if (!empty($oldblogurl) and $oldblogurl!=$blogurl) {
	mysql_query("UPDATE ".$wpdb->prefix."options SET option_value = replace(option_value, '".untrailingslashit($oldblogurl)."', '".untrailingslashit($blogurl)."');");
	if ($sqlerr=mysql_error()) 
		echo __('ERROR:','backwpup').' '.sprintf(__('BackWPup database error %1$s for query %2$s','backwpup'), $sqlerr, "UPDATE ".$wpdb->prefix."options SET option_value = replace(option_value, '".untrailingslashit($oldblogurl)."', '".untrailingslashit($blogurl)."');")."<br />\n";
	mysql_query("UPDATE ".$wpdb->prefix."posts SET guid = replace(guid, '".untrailingslashit($oldblogurl)."','".untrailingslashit($blogurl)."');");
	if ($sqlerr=mysql_error())
		echo __('ERROR:','backwpup').' '.sprintf(__('BackWPup database error %1$s for query %2$s','backwpup'), $sqlerr, "UPDATE ".$wpdb->prefix."posts SET guid = replace(guid, '".untrailingslashit($oldblogurl)."','".untrailingslashit($blogurl)."');")."<br />\n";
	mysql_query("UPDATE ".$wpdb->prefix."posts SET post_content = replace(post_content, '".untrailingslashit($oldblogurl)."', '".untrailingslashit($blogurl)."');");
	if ($sqlerr=mysql_error())
		echo __('ERROR:','backwpup').' '.sprintf(__('BackWPup database error %1$s for query %2$s','backwpup'), $sqlerr, "UPDATE ".$wpdb->prefix."posts SET post_content = replace(post_content, '".untrailingslashit($oldblogurl)."', '".untrailingslashit($blogurl)."');")."<br />\n";
}
if (!empty($oldblogabspath) and $oldblogabspath!=$blogabspath) {
	mysql_query("UPDATE ".$wpdb->prefix."options SET option_value = replace(option_value, '".untrailingslashit($oldblogabspath)."', '".untrailingslashit($blogabspath)."');");
	if ($sqlerr=mysql_error())
		echo __('ERROR:','backwpup').' '.sprintf(__('BackWPup database error %1$s for query %2$s','backwpup'), $sqlerr, "UPDATE ".$wpdb->prefix."options SET option_value = replace(option_value, '".untrailingslashit($oldblogabspath)."', '".untrailingslashit($blogabspath)."');")."<br />\n";
}
echo __('Restore Done. Please delete the SQL file after restoring.','backwpup')."<br />";