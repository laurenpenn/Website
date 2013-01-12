<?PHP
if (!defined('ABSPATH')) 
	die();

	
echo "<div class=\"wrap\">";
screen_icon();
echo "<h2>".esc_html( __('BackWPup Logs', 'backwpup'))."</h2>";
if (isset($backwpup_message) and !empty($backwpup_message)) 
	echo "<div id=\"message\" class=\"updated\"><p>".$backwpup_message."</p></div>";
echo "<form id=\"posts-filter\" action=\"\" method=\"get\">";
echo "<input type=\"hidden\" name=\"page\" value=\"backwpuplogs\" />";
$backwpup_listtable->display();
echo "<div id=\"ajax-response\"></div>";
echo "</form>"; 
echo "</div>";