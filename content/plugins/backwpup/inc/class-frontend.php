<?php
/**
 *
 */
class BackWPup_FrontEnd {

	private static $instance = NULL;

	/**
	 *
	 * @return \BackWPup_FrontEnd
	 */
	private function __construct() {

		//add for job start/run with url
		add_filter( 'query_vars', array( $this, 'add_start_job_query_vars' ) );
		add_action( 'request', array( $this, 'request' ), 0 );
	}

	/**
	 * @static
	 * @return \BackWPup_FrontEnd
	 */
	public static function getInstance() {

		if (NULL === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Add Query var for job start
	 */
	public function add_start_job_query_vars($vars) {

		$vars[] = 'backwpup_run';

		return $vars;
	}

	/**
	 * Start job if run query's.
	 */
	public function request( $query_vars ) {

		//only work if backwpup_run as query var ist set and nothing else and the value ist right
		if ( empty( $query_vars[ 'backwpup_run' ] ) || count( $query_vars ) != 1 || ! in_array( $query_vars[ 'backwpup_run' ], array( 'test','restart', 'runnow', 'runnowalt', 'runext', 'cronrun' ) ) )
			return $query_vars;

		// generate normal nonce
		$nonce = substr( wp_hash( wp_nonce_tick() . 'backwup_job_run-' . $query_vars[ 'backwpup_run' ], 'nonce' ), - 12, 10 );
		//special nonce on external start
		if ( $query_vars[ 'backwpup_run' ] == 'runext' )
			$nonce = BackWPup_Option::get( 'cfg', 'jobrunauthkey' );
		// check nonce
		if ( empty( $_GET['_nonce'] ) || $nonce != $_GET['_nonce'] )
			return $query_vars;

		//response on test
		if ( $query_vars[ 'backwpup_run' ] == 'test') {
			@header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
			@header( 'X-Robots-Tag: noindex, nofollow' );
			send_nosniff_header();
			nocache_headers();
			die( 'Response Test O.K.' );
		}

		//check runext is allowed for job
		if ( $query_vars[ 'backwpup_run' ] == 'runext' ) {
			$jobids_external = BackWPup_Option::get_job_ids( 'activetype', 'link' );
			if ( !isset( $_GET[ 'jobid' ] ) || ! in_array( $_GET[ 'jobid' ], $jobids_external ) )
				return $query_vars;
		}

		//run BackWPup job
		BackWPup_Job::start_http( $query_vars[ 'backwpup_run' ] );
		die();
	}

}
