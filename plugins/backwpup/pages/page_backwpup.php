<?PHP
if (!defined('ABSPATH')) 
	die();

	
echo "<div class=\"wrap\">";
screen_icon();
echo "<h2>".esc_html( __('BackWPup Jobs', 'backwpup'))."&nbsp;<a href=\"".wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpupeditjob', 'edit-job')."\" class=\"add-new-h2\">".esc_html__('Add New','backwpup')."</a></h2>";
if (isset($backwpup_message) and !empty($backwpup_message)) 
	echo "<div id=\"message\" class=\"updated\"><p>".$backwpup_message."</p></div>";
echo "<form id=\"posts-filter\" action=\"\" method=\"get\">";
echo "<input type=\"hidden\" name=\"page\" value=\"backwpup\" />";
wp_nonce_field('backwpup_ajax_nonce', 'backwpupajaxnonce', false ); 
$backwpup_listtable->display();
echo "<div id=\"ajax-response\"></div>";
echo "</form>"; 
echo "</div>";
