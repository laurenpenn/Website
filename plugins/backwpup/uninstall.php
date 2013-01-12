<?PHP
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
	die();
}

delete_option('backwpup');
delete_option('backwpup_jobs');