<?php

function get_coauthors( $post_id = 0, $args = array() ) {
	global $post, $post_ID, $coauthors_plus, $wpdb;
	
	$coauthors = array();
	$post_id = (int) $post_id;
	if( !$post_id && $post_ID ) $post_id = $post_ID;
	if( !$post_id && $post ) $post_id = $post->ID;

	$defaults = array( 'orderby'=>'term_order', 'order'=>'ASC' );
	$args = wp_parse_args( $args, $defaults );
	
	if( $post_id ) {
		
		// 3.0: Get as custom_post_type
		$coauthors = _coauthors_get_as_posts( $post_id, $args, true );
		
		/*
		// 2.1: Get as term
		if( !$coauthors || empty( $coauthors ) ) {
			$coauthors = _coauthors_get_as_terms( $post_id, $args );
		}
		*/
		
		// Fallback to core
		if( !$coauthors || empty( $coauthors ) ) {
			$coauthors = _coauthors_get_as_core( $post_id );
		}
		
	}
	return $coauthors;
}

function _coauthors_get_as_posts( $post_id, $args = array(), $convert_to_user = false ) {
	global $coauthors_plus;
	
	$args['post_parent'] = $post_id;
	$args['post_type'] = $coauthors_plus->coauthor_post_type;

	$coauthors = get_children( $args );
	
	if( !$coauthors || is_wp_error( $coauthors ) )
		return array();
	
	if( $convert_to_user ) {
		$coauthors_as_users = array();
		foreach( $coauthors as $coauthor_as_post ) {
			$coauthors_as_users[] = get_userdata( $coauthor_as_post->post_author );
		}
		$coauthors = $coauthors_as_users;
	}
	
	return $coauthors;
}

function _coauthors_get_as_terms( $post_id, $args = array() ) {
	global $coauthors_plus;
	
	$coauthor_terms = wp_get_post_terms( $post_id, $coauthors_plus->coauthor_taxonomy, $args );
		
	if( is_array( $coauthor_terms) && !empty( $coauthor_terms ) ) {
		foreach( $coauthor_terms as $coauthor ) {
			$post_author =  get_userdatabylogin( $coauthor->name );
			// In case the user has been deleted while plugin was deactivated
			if( !empty( $post_author ) ) $coauthors[] = $post_author;
		}
	}
	return $coauthors;
}

function _coauthors_get_as_core( $post_id ) {
	global $post, $wpdb;
	
	$coauthors = array();
	
	if( $post ) {
		$author_id = $post->post_author;
	} else {
		$author_id = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d", $post_id));
	}
	
	$author = get_userdata( $author_id );
	
	if( $author )
		$coauthors[] = $author;
	
	return $coauthors;
}

/**
 * Checks to see if the the specified user is author of the current global post or post (if specified)
 * @param object|int $user 
 * @param int $post_id
 */
function is_coauthor_for_post( $user, $post_id = 0 ) {
	global $post;
	
	if( !$post_id ) $post_id = $post->ID;
	if( !$post_id ) return false;
	
	$coauthors = get_coauthors( $post_id );
	if( is_numeric( $user ) ) {
		$user = get_userdata( $user );
		$user = $user->user_login;
	}
	
	foreach( $coauthors as $coauthor ) {
		if( $user == $coauthor->user_login ) return true;
	}
	return false;
}

class CoAuthorsIterator {
	var $position = -1;
	var $original_authordata;
	var $authordata_array;
	var $count;
	
	function CoAuthorsIterator($postID = 0){
		global $post, $authordata, $wpdb;
		$postID = (int)$postID;
		if(!$postID && $post)
			$postID = (int)$post->ID;
		if(!$postID)
			trigger_error(__('No post ID provided for CoAuthorsIterator constructor. Are you not in a loop or is $post not set?', 'co-authors-plus')); //return null;

		$this->original_authordata = $authordata;
		$this->authordata_array = get_coauthors($postID);
		
		$this->count = count($this->authordata_array);
	}
	
	function iterate(){
		global $authordata;
		$this->position++;
		
		//At the end of the loop
		if($this->position > $this->count-1){
			$authordata = $this->original_authordata;
			$this->position = -1;
			return false;
		}
		
		//At the beginning of the loop
		if($this->position == 0 && !empty($authordata))
			$this->original_authordata = $authordata;
		
		$authordata = $this->authordata_array[$this->position];
		
		return true;
	}
	
	function get_position(){
		if($this->position === -1)
			return false;
		return $this->position;
	}
	function is_last(){
		return  $this->position === $this->count-1;
	}
	function is_first(){
		return $this->position === 0;
	}
	function count(){
		return $this->count;
	}
	function get_all(){
		return $this->authordata_array;
	}
}

//Helper function for the following new template tags
function coauthors__echo($tag, $between, $betweenLast, $before, $after){
	$i = new CoAuthorsIterator();
	echo $before;
	if($i->iterate())
		$tag();
	while($i->iterate()){
		echo $i->is_last() ? $betweenLast : $between;
		$tag();
	}
	echo $after;
}
function coauthors__return($tag){
	$return = array();
	$i = new CoAuthorsIterator();
	if($i->iterate())
		$return[] = $tag();
	while($i->iterate()){
		$return[] = $tag();
	}
	echo $after;
}

//Provide co-author equivalents to the existing author template tags
function coauthors($between = null, $betweenLast = null, $before = null, $after = null){
	if($between === NULL)
		$between = __(COAUTHORS_DEFAULT_BETWEEN, 'co-authors-plus');
	if($betweenLast === NULL)
		$betweenLast = __(COAUTHORS_DEFAULT_BETWEEN_LAST, 'co-authors-plus');
	if($before === NULL)
		$before = COAUTHORS_DEFAULT_BEFORE; //__(COAUTHORS_DEFAULT_BEFORE, 'co-authors-plus');
	if($after === NULL)
		$after = COAUTHORS_DEFAULT_AFTER; //__(COAUTHORS_DEFAULT_AFTER, 'co-authors-plus');
	coauthors__echo('the_author', $between, $betweenLast, $before, $after);
}
function coauthors_posts_links($between = null, $betweenLast = null, $before = null, $after = null){
	if($between === NULL)
		$between = __(COAUTHORS_DEFAULT_BETWEEN, 'co-authors-plus');
	if($betweenLast === NULL)
		$betweenLast = __(COAUTHORS_DEFAULT_BETWEEN_LAST, 'co-authors-plus');
	if($before === NULL)
		$before = COAUTHORS_DEFAULT_BEFORE; //__(COAUTHORS_DEFAULT_BEFORE, 'co-authors');
	if($after === NULL)
		$after = COAUTHORS_DEFAULT_AFTER; //__(COAUTHORS_DEFAULT_AFTER, 'co-authors');
	coauthors__echo('the_author_posts_link', $between, $betweenLast, $before, $after);
}
function coauthors_firstnames($between = null, $betweenLast = null, $before = null, $after = null){
	if($between === NULL)
		$between = __(COAUTHORS_DEFAULT_BETWEEN, 'co-authors-plus');
	if($betweenLast === NULL)
		$betweenLast = __(COAUTHORS_DEFAULT_BETWEEN_LAST, 'co-authors-plus');
	if($before === NULL)
		$before = COAUTHORS_DEFAULT_BEFORE; //__(COAUTHORS_DEFAULT_BEFORE, 'co-authors');
	if($after === NULL)
		$after = COAUTHORS_DEFAULT_AFTER; //__(COAUTHORS_DEFAULT_AFTER, 'co-authors');
	coauthors__echo('the_author_firstname', $between, $betweenLast, $before, $after);
}
function coauthors_lastnames($between = null, $betweenLast = null, $before = null, $after = null){
	if($between === NULL)
		$between = __(COAUTHORS_DEFAULT_BETWEEN, 'co-authors-plus');
	if($betweenLast === NULL)
		$betweenLast = __(COAUTHORS_DEFAULT_BETWEEN_LAST, 'co-authors-plus');
	if($before === NULL)
		$before = COAUTHORS_DEFAULT_BEFORE; 
	if($after === NULL)
		$after = COAUTHORS_DEFAULT_AFTER;
	coauthors__echo('the_author_lastname', $between, $betweenLast, $before, $after);
}
function coauthors_nicknames($between = null, $betweenLast = null, $before = null, $after = null){
	if($between === NULL)
		$between = __(COAUTHORS_DEFAULT_BETWEEN, 'co-authors-plus');
	if($betweenLast === NULL)
		$betweenLast = __(COAUTHORS_DEFAULT_BETWEEN_LAST, 'co-authors-plus');
	if($before === NULL)
		$before = COAUTHORS_DEFAULT_BEFORE;
	if($after === NULL)
		$after = COAUTHORS_DEFAULT_AFTER; 
	coauthors__echo('the_author_nickname', $between, $betweenLast, $before, $after);
}
function coauthors_links($between = null, $betweenLast = null, $before = null, $after = null){
	if($between === NULL)
		$between = __(COAUTHORS_DEFAULT_BETWEEN, 'co-authors-plus');
	if($betweenLast === NULL)
		$betweenLast = __(COAUTHORS_DEFAULT_BETWEEN_LAST, 'co-authors-plus');
	if($before === NULL)
		$before = COAUTHORS_DEFAULT_BEFORE; //__(COAUTHORS_DEFAULT_BEFORE, 'co-authors');
	if($after === NULL)
		$after = COAUTHORS_DEFAULT_AFTER; //__(COAUTHORS_DEFAULT_AFTER, 'co-authors');
	coauthors__echo('the_author_link', $between, $betweenLast, $before, $after);
}
function coauthors_IDs($between = null, $betweenLast = null, $before = null, $after = null){
	if($between === NULL)
		$between = __(COAUTHORS_DEFAULT_BETWEEN, 'co-authors-plus');
	if($betweenLast === NULL)
		$betweenLast = __(COAUTHORS_DEFAULT_BETWEEN_LAST, 'co-authors-plus');
	if($before === NULL)
		$before = COAUTHORS_DEFAULT_BEFORE;
	if($after === NULL)
		$after = COAUTHORS_DEFAULT_AFTER; 
		coauthors__echo('the_author_ID', $between, $betweenLast, $before, $after);
}

// @TODO: fix this function
function get_the_coauthor_meta( $field, $user_id = 0 ) {
	global $wp_query, $post;
	
	$coauthors = get_coauthors();
	$meta = array();
	
	foreach( $coauthors as $coauthor ) {
		// TODO: build this
	}
}

function the_coauthor_meta( $field, $user_id = 0 ) {
	// need before after options
	echo get_the_coauthor_meta($field, $user_id);
}

//customized wp_list_authors() from WP core
/**
 * List all the *co-authors* of the blog, with several options available.
 * optioncount (boolean) (false): Show the count in parenthesis next to the author's name.
 * exclude_admin (boolean) (true): Exclude the 'admin' user that is installed by default.
 * show_fullname (boolean) (false): Show their full names.
 * hide_empty (boolean) (true): Don't show authors without any posts.
 * feed (string) (''): If isn't empty, show links to author's feeds.
 * feed_image (string) (''): If isn't empty, use this image to link to feeds.
 * echo (boolean) (true): Set to false to return the output, instead of echoing.
 * @param array $args The argument array.
 * @return null|string The output, if echo is set to false.
 */
 /*
function coauthors_wp_list_authors($args = '') {
	global $wpdb, $coauthors_plus;

	$defaults = array(
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
		'style' => 'list', 'html' => true
	);

	$r = wp_parse_args( $args, $defaults );
	extract($r, EXTR_SKIP);
	$return = '';

	// @todo Move select to get_authors()
	$authors = $wpdb->get_results("SELECT ID, user_nicename from $wpdb->users " . ($exclude_admin ? "WHERE user_login <> 'admin' " : '') . "ORDER BY display_name");

	$author_count = array();
		
	$query  = "SELECT DISTINCT $wpdb->users.ID AS post_author, $wpdb->terms.name AS user_name, $wpdb->term_taxonomy.count AS count";
	$query .= " FROM $wpdb->posts";
	$query .= " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)";
	$query .= " INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)";
	$query .= " INNER JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)";
	$query .= " INNER JOIN $wpdb->users ON ($wpdb->terms.name = $wpdb->users.user_login)";
	$query .= " WHERE post_type = 'post' AND " . get_private_posts_cap_sql( 'post' );
	$query .= " AND $wpdb->term_taxonomy.taxonomy = '$coauthors_plus->coauthor_taxonomy'";
	$query .= " GROUP BY post_author"; 

	foreach ((array) $wpdb->get_results($query) as $row) {
		$author_count[$row->post_author] = $row->count;
	}

	foreach ( (array) $authors as $author ) {

		$link = '';

		$author = get_userdata( $author->ID );
		$posts = (isset($author_count[$author->ID])) ? $author_count[$author->ID] : 0;
		$name = $author->display_name;

		if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )
			$name = "$author->first_name $author->last_name";

		if( !$html ) {
			if ( $posts == 0 ) {
				if ( ! $hide_empty )
					$return .= $name . ', ';
			} else
				$return .= $name . ', ';

			// No need to go further to process HTML.
			continue;
		}

		if ( !($posts == 0 && $hide_empty) && 'list' == $style )
			$return .= '<li>';
		if ( $posts == 0 ) {
			if ( ! $hide_empty )
				$link = $name;
		} else {
			$link = '<a href="' . get_author_posts_url($author->ID, $author->user_nicename) . '" title="' . esc_attr( sprintf(__("Posts by %s", 'co-authors-plus'), $author->display_name) ) . '">' . $name . '</a>';

			if ( (! empty($feed_image)) || (! empty($feed)) ) {
				$link .= ' ';
				if (empty($feed_image))
					$link .= '(';
				$link .= '<a href="' . get_author_feed_link($author->ID) . '"';

				if ( !empty($feed) ) {
					$title = ' title="' . esc_attr($feed) . '"';
					$alt = ' alt="' . esc_attr($feed) . '"';
					$name = $feed;
					$link .= $title;
				}

				$link .= '>';

				if ( !empty($feed_image) )
					$link .= "<img src=\"" . esc_url($feed_image) . "\" style=\"border: none;\"$alt$title" . ' />';
				else
					$link .= $name;

				$link .= '</a>';

				if ( empty($feed_image) )
					$link .= ')';
			}

			if ( $optioncount )
				$link .= ' ('. $posts . ')';

		}

		if ( !($posts == 0 && $hide_empty) && 'list' == $style )
			$return .= $link . '</li>';
		else if ( ! $hide_empty )
			$return .= $link . ', ';
	}

	$return = trim($return, ', ');

	if ( ! $echo )
		return $return;
	echo $return;
}
*/

function coauthors_wp_list_authors($args = '') {
	global $wpdb;

	$defaults = array(
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
		'style' => 'list', 'html' => true
	);

	$r = wp_parse_args( $args, $defaults );
	extract($r, EXTR_SKIP);
	$return = '';

	/** @todo Move select to get_authors(). */
	$users = get_users_of_blog();
	$author_ids = array();
	foreach ( (array) $users as $user )
		$author_ids[] = $user->user_id;
	if ( count($author_ids) > 0  ) {
		$author_ids = implode(',', $author_ids );
		$authors = $wpdb->get_results( "SELECT ID, user_login, user_nicename from $wpdb->users WHERE ID IN($author_ids) " . ($exclude_admin ? "AND user_login <> 'admin' " : '') . "ORDER BY display_name" );
	} else {
		$authors = array();
	}

	$author_count = array();
	
	$post_counts_query = "SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author";
	
	$post_counts = $wpdb->get_results($post_counts_query);
	
	foreach ( (array) $post_counts as $row )
		$author_count[$row->post_author] = $row->count;

	foreach ( (array) $authors as $author ) {

		$link = '';

		$author = get_userdata( $author->ID );
		$posts = (isset($author_count[$author->ID])) ? $author_count[$author->ID] : 0;
		$name = $author->display_name;

		if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )
			$name = "$author->first_name $author->last_name";

		if( !$html ) {
			if ( $posts == 0 ) {
				if ( ! $hide_empty )
					$return .= $name . ', ';
			} else
				$return .= $name . ', ';

			// No need to go further to process HTML.
			continue;
		}

		if ( !($posts == 0 && $hide_empty) && 'list' == $style )
			$return .= '<li>';
		if ( $posts == 0 ) {
			if ( ! $hide_empty )
				$link = $name;
		} else {
			$link = '<a href="' . get_author_posts_url($author->ID, $author->user_nicename) . '" title="' . esc_attr( sprintf(__("Posts by %s"), $author->display_name) ) . '">' . $name . '</a>';

			if ( (! empty($feed_image)) || (! empty($feed)) ) {
				$link .= ' ';
				if (empty($feed_image))
					$link .= '(';
				$link .= '<a href="' . get_author_feed_link($author->ID) . '"';

				if ( !empty($feed) ) {
					$title = ' title="' . esc_attr($feed) . '"';
					$alt = ' alt="' . esc_attr($feed) . '"';
					$name = $feed;
					$link .= $title;
				}

				$link .= '>';

				if ( !empty($feed_image) )
					$link .= "<img src=\"" . esc_url($feed_image) . "\" style=\"border: none;\"$alt$title" . ' />';
				else
					$link .= $name;

				$link .= '</a>';

				if ( empty($feed_image) )
					$link .= ')';
			}

			if ( $optioncount )
				$link .= ' ('. $posts . ')';

		}

		if ( $posts || ! $hide_empty )
			$return .= $link . ( ( 'list' == $style ) ? '</li>' : ', ' );
	}

	$return = trim($return, ', ');

	if ( ! $echo )
		return $return;
	echo $return;
}

?>