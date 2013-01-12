<?PHP
define('DONOTCACHEPAGE', true);
define('DONOTCACHEDB', true);
define('DONOTMINIFY', true);
define('DONOTCDN', true);
define('DONOTCACHCEOBJECT', true);
define('W3TC_IN_MINIFY',false); //W3TC will not loaded

$backwpupjobtemp=str_replace('\\','/',dirname(__FILE__).'/../tmp/');
$backwpupjobtemp=rtrim(realpath($backwpupjobtemp),'/');	
if (!empty($backwpupjobtemp) && is_dir($backwpupjobtemp) && is_file($backwpupjobtemp.'/.running')) 
	$runningfile=file_get_contents($backwpupjobtemp.'/.running');
$infile=array();
if (!empty($runningfile)) 
	$infile=unserialize(trim($runningfile));
if (is_file(trim($infile['ABSPATH']).'wp-load.php') and $_POST['nonce']==$infile['WORKING']['NONCE'] and $_POST['type']=='getxmlexport') {
	require_once(trim($infile['ABSPATH']).'wp-load.php'); /** Setup WordPress environment */
	require_once(trim($infile['ABSPATH']).'wp-admin/includes/export.php');
	export_wp();
}