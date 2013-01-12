<?php
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  src VARCHAR(255) NOT NULL,
		  url VARCHAR(255) NOT NULL,
		  target VARCHAR(55) NOT NULL,
		  orderby int(11) NOT NULL,
		  UNIQUE KEY id (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
?>
