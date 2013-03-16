<?php

if( !defined( 'PRODUCTION_DOMAIN' ) )
	die( 'You must define PRODUCTION_DOMAIN' );

if (isset($_SERVER['HTTP_HOST'])) {
	$host = $_SERVER['HTTP_HOST'];
} elseif (isset($_SERVER['SERVER_NAME'])) {
	$host = $_SERVER['SERVER_NAME'];
}

/**
 * This parses a domain and returns its first subdomain,
 * returning false if the given domain matches the environment
 * domain. This is because we don't want the subdomain of the 
 * environment domain if it already includes a subdomain.
 */
function getSubDomain ($domain) {
    if( $domain == ENV_DOMAIN )
    	return;
    	
    $eDom = explode('.', $domain);
    return $eDom[0] .'.';
}

if ( getSubDomain( $host ) . ENV_DOMAIN == $host) {
	// switch the host.
	$host = getSubDomain( $host ) . PRODUCTION_DOMAIN;
}

if (is_subdomain_install()) {
	$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->blogs} WHERE domain = %s LIMIT 1", $host);
} else {
	// this probably needs some work.
	$path = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
	if (count($path))
		$path = '/' . $path[0];
	else
		$path = '/';

	$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->blogs} WHERE domain = %s AND path = %s LIMIT 1", $host, $path);
}

if ($_blog = $wpdb -> get_row($sql)) {
	
	$current_blog = $_blog;

	$blog_id = $_blog -> blog_id;
	$site_id = $current_blog -> site_id;

	// set current site
	$current_site = $wpdb -> get_row($wpdb -> prepare("SELECT * from {$wpdb->site} WHERE id = %d", $site_id));
	$current_site -> blog_id = $blog_id;

	$current_site = get_current_site_name($current_site);
}