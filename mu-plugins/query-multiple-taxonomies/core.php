<?php

class QMT_Terms {

	private static $filtered_ids;

	// Get a list of all the terms attached to all the posts in the current query
	public function get( $tax ) {
		global $wp_query, $wpdb;

		self::set_filtered_ids();

		if ( empty( self::$filtered_ids ) )
			return array();

		$raw_terms = wp_get_object_terms( self::$filtered_ids, $tax );

		// distinct terms
		$terms = array();
		foreach ( $raw_terms as $term )
			$terms[ $term->term_id ] = $term;

		return $terms;
	}

	private function set_filtered_ids() {
		global $wp_query;

		if ( isset( self::$filtered_ids ) )
			return;

		$wp_query->query = wp_parse_args( $wp_query->query );

		$args = array_merge( $wp_query->query, array(
			'nopaging' => true,
			'no_found_rows' => true,
			'ignore_sticky_post' => true,
			'cache_results' => false,
		) );

		add_filter( 'posts_fields', array( __CLASS__, 'posts_fields' ) );

		$query = new WP_Query();
		$posts = $query->query( $args );

		remove_filter( 'posts_fields', array( __CLASS__, 'posts_fields' ) );

		foreach ( $posts as &$post )
			$post = $post->ID;

		self::$filtered_ids = $posts;
	}

	function posts_fields( $fields ) {
		return 'ID';
	}
}


class QMT_URL {

	public function for_tax( $taxonomy, $value ) {
		$query = qmt_get_query();

		if ( empty( $value ) )
			unset( $query[ $taxonomy ] );
		else
			$query[ $taxonomy ] = trim( implode( '+', $value ), '+' );

		return self::get( $query );
	}

	public function get( $query = array() ) {
		$url = self::get_base();

		if ( empty($query) )
			return apply_filters( 'qmt_reset_url', $url );

		ksort( $query );

		foreach ( $query as $taxonomy => $value )
			$url = add_query_arg( get_taxonomy( $taxonomy )->query_var, $value, $url );

		return apply_filters( 'qmt_url', $url, $query );
	}

	public function get_base() {
		static $base_url;

		if ( empty( $base_url ) )
			$base_url = apply_filters( 'qmt_base_url', get_bloginfo( 'url' ) );

		return $base_url;
	}
}

class QMT_Template {

	public function get_title() {
		$title = array();
		foreach ( qmt_get_query() as $tax => $value ) {
			$key = get_taxonomy( $tax )->label;

			if ( is_array( $value ) ) {
				extract( $value );

				if ( isset( $or ) )
					$value = implode( ',', $or );
				elseif ( isset( $and ) )
					$value = implode( '+', $and );
			}

			$title[] .= "$key: $value";
		}

		return implode( '; ', $title );
	}
}

/**
 * Wether multiple taxonomies are queried
 * @param array $taxonomies A list of taxonomies to check for (AND).
 *
 * @return bool
 */
function is_multitax( $taxonomies = array() ) {
	$queried = array_keys( qmt_get_query() );
	$count = count( $taxonomies );

	if ( !$count )
		return count( $queried ) > 1;

	return count( array_intersect( $queried, $taxonomies) ) == $count;
}

/**
 * Get the list of selected terms
 *
 * @param string $taxname a certain taxonomy name
 *
 * @return array( taxonomy => query )
 */
function qmt_get_query( $taxname = '' ) {
	global $wp_query;

	$qmt_query = array();

	if ( is_null( $wp_query->tax_query ) )
		return $qmt_query;

	foreach ( $wp_query->tax_query->queries as $tax_query ) {
		if ( 'IN' != $tax_query['operator'] )
			continue;

		if ( 'slug' != $tax_query['field'] )
			continue;

		$qmt_query[ $tax_query['taxonomy'] ][] = implode( ',', $tax_query['terms'] );
	}

	foreach ( $qmt_query as &$value )
		$value = implode( '+', $value );

	if ( $taxname ) {
		if ( isset( $qmt_query[ $taxname ] ) )
			return $qmt_query[ $taxname ];
		
		return false;
	}

	return $qmt_query;
}

// Deprecated
function qmt_get_terms( $tax ) {
	_deprecated_function( __FUNCTION__, '1.4' );

	if ( is_archive() )
		return QMT_Terms::get( $tax );
	else
		return get_terms( $tax );
}

