<?php
/*
 
 $Id: sitemap-builder.php 296055 2010-10-03 00:23:08Z arnee $

*/
/**
 * Default sitemap builder
 *
 * @author Arne Brachhold
 * @package sitemap
 * @since 4.0
 */
class GoogleSitemapGeneratorStandardBuilder {
	
	function __construct() {
		add_action("sm_build_index",array($this,"Index"),10,1);
		add_action("sm_build_content",array($this,"Content"),10,3);
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 * @param $type String
	 * @param $params array
	 */
	function Content($gsg, $type, $params) {
		
		switch($type) {
			case "posts":
			case "pages":
				$this->BuildPosts($gsg, $type, $params);
				break;
			case "archives":
				$this->BuildArchives($gsg);
				break;
			case "categories":
				$this->BuildCategories($gsg);
				break;
			case "authors":
				$this->BuildAuthors($gsg);
				break;
			case "tags":
				$this->BuildTags($gsg);
				break;
			case "taxonomies":
				$this->BuildTaxonomies($gsg);
				break;
			case "externals":
				$this->BuildExternals($gsg);
				break;
			case "misc":
				$this->BuildMisc($gsg);
				break;
		}
	}
	
	/**
	 * Adds a condition to the query to filter out password protected posts
	 * @param $where The where statement
	 * @return String Changed where statement
	 */
	function FilterPassword($where) {
		global $wpdb;
		$where.="AND ($wpdb->posts.post_password = '') ";
		return $where;
	}
	
	/**
	 * Adds the list of required fields to the query so no big fields like post_content will be selected
	 * @param $fields The current fields
	 * @return String Changed fields statement
	 */
	function FilterFields($fields) {
		global $wpdb;
		
		$newFields = array(
			$wpdb->posts . ".ID",
			$wpdb->posts . ".post_author",
			$wpdb->posts . ".post_date",
			$wpdb->posts . ".post_date_gmt",
			$wpdb->posts . ".post_content",
			$wpdb->posts . ".post_title",
			$wpdb->posts . ".post_excerpt",
			$wpdb->posts . ".post_status",
			$wpdb->posts . ".post_name",
			$wpdb->posts . ".post_modified",
			$wpdb->posts . ".post_modified_gmt",
			$wpdb->posts . ".post_content_filtered",
			$wpdb->posts . ".post_parent",
			$wpdb->posts . ".guid",
			$wpdb->posts . ".post_type","post_mime_type",
			$wpdb->posts . ".comment_count"
		);
		
		$fields = implode(", ",$newFields);
		return $fields;
	}

	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 * @param $params String
	 */
	function BuildPosts($gsg, $type, $params) {
		
		//Remove the "s" at the end
		$type = substr($type,0,-1);
		
		global $wp_version;
		
		if(preg_match('/^([0-9]{4})\-([0-9]{2})$/',$params,$matches)) {
			$year = $matches[1];
			$month = $matches[2];
			
			//Pre 2.1 compatibility. 2.1 introduced 'future' as post_status so we don't need to check post_date
			$wpCompat = (floatval($wp_version) < 2.1);
			
			//All comments as an asso. Array (postID=>commentCount)
			$comments=($gsg->GetOption("b_prio_provider")!=""?$gsg->GetComments():array());
			
			//Full number of comments
			$commentCount=(count($comments)>0?$gsg->GetCommentCount($comments):0);
						
			$qp = $this->BuildPostQuery($gsg,$type);

			$qp['year'] = $year;
			$qp['monthnum'] = $month;

			//Add filter to remove password protected posts
			add_filter('posts_search',array($this,'FilterPassword'),10,1);
			
			//Add filter to filter the fields
			add_filter('posts_fields',array($this,'FilterFields'),10,1);
			
			$posts = get_posts($qp);
			
			//Remove the filter again
			remove_filter("posts_where",array($this,'FilterPassword'),10,1);
			remove_filter("posts_fields",array($this,'FilterFields'),10,1);
			
			if($postCount = count($posts) > 0) {
			
				$prioProvider=NULL;
				
				if($gsg->GetOption("b_prio_provider") != '') {
					$providerClass = $gsg->GetOption('b_prio_provider');
					$prioProvider = new $providerClass($commentCount,$postCount);
				}
				
				//Default priorities
				$default_prio_posts = $gsg->GetOption('pr_posts');
				$default_prio_pages = $gsg->GetOption('pr_pages');
				
				//Change frequencies
				$cf_pages = $gsg->GetOption('cf_pages');
				$cf_posts = $gsg->GetOption('cf_posts');
				
				//Minimum priority
				$minPrio = $gsg->GetOption('pr_posts_min');
				
				//Page as home handling
				$homePid = 0;
				$home = get_bloginfo('url');
				if('page' == get_option('show_on_front') && get_option('page_on_front')) {
					$pageOnFront = get_option('page_on_front');
					$p = get_page($pageOnFront);
					if($p) $homePid = $p->ID;
				}
				
				foreach($posts AS $post) {
					
					$permalink = get_permalink($post->ID);
					
					if($permalink != $home && $post->ID != $homePid) {
						
						//Is a page or post
						$isPage = false;
						if($wpCompat) $isPage = ($post->post_status == 'static');
						else $isPage = ($post->post_type == 'page');
					
						//Default Priority if auto calc is disabled
						$prio = ($isPage?$default_prio_pages:$default_prio_posts);
						
						//If priority calc. is enabled, calculate (but only for posts, not pages)!
						if($prioProvider !== null && !$isPage) {
							//Comment count for this post
							$cmtcnt = (isset($comments[$post->ID])?$comments[$post->ID]:0);
							$prio = $prioProvider->GetPostPriority($post->ID, $cmtcnt, $post);
						}
						
						if(!$isPage && $minPrio>0 && $prio<$minPrio) $prio = $minPrio;
						
						$gsg->AddUrl($permalink,$gsg->GetTimestampFromMySql(($post->post_modified_gmt && $post->post_modified_gmt!='0000-00-00 00:00:00'?$post->post_modified_gmt:$post->post_date_gmt)),($isPage?$cf_pages:$cf_posts),$prio);
				
					}
				}
			}
		}
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function BuildArchives($gsg) {
		global $wpdb, $wp_version;
		$now = current_time('mysql');

		//WP2.1 introduced post_status='future', for earlier WP versions we need to check the post_date_gmt
		$arcresults = $wpdb->get_results("
			SELECT DISTINCT
				YEAR(post_date_gmt) AS `year`,
				MONTH(post_date_gmt) AS `month`,
				MAX(post_date_gmt) as last_mod,
				count(ID) as posts
			FROM
				$wpdb->posts
			WHERE
				post_date < '$now'
				AND post_status = 'publish'
				AND post_type = 'post'
				" . (floatval($wp_version) < 2.1?"AND {$wpdb->posts}.post_date_gmt <= '" . gmdate('Y-m-d H:i:59') . "'":"") . "
			GROUP BY
				YEAR(post_date_gmt),
				MONTH(post_date_gmt)
			ORDER BY
				post_date_gmt DESC
		");
				
		if ($arcresults) {
			foreach ($arcresults as $arcresult) {
				
				$url  = get_month_link($arcresult->year, $arcresult->month);
				$changeFreq="";
				
				//Archive is the current one
				if($arcresult->month == date("n") && $arcresult->year==date("Y")) {
					$changeFreq = $gsg->GetOption("cf_arch_curr");
				} else { // Archive is older
					$changeFreq = $gsg->GetOption("cf_arch_old");
				}
				
				$gsg->AddUrl($url,$gsg->GetTimestampFromMySql($arcresult->last_mod),$changeFreq,$gsg->GetOption("pr_arch"));
			}
		}
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function BuildMisc($gsg) {
		
		if($gsg->GetOption("in_home")) {
			$home = get_bloginfo('url'); $homePid = 0;
			//Add the home page (WITH a slash!)
			if($gsg->GetOption("in_home")) {
				if('page' == get_option('show_on_front') && get_option('page_on_front')) {
					$pageOnFront = get_option('page_on_front');
					$p = get_page($pageOnFront);
					if($p) {
						$homePid = $p->ID;
						$gsg->AddUrl(trailingslashit($home),$gsg->GetTimestampFromMySql(($p->post_modified_gmt && $p->post_modified_gmt!='0000-00-00 00:00:00'?$p->post_modified_gmt:$p->post_date_gmt)),$gsg->GetOption("cf_home"),$gsg->GetOption("pr_home"));
					}
				} else {
					$gsg->AddUrl(trailingslashit($home),$gsg->GetTimestampFromMySql(get_lastpostmodified('GMT')),$gsg->GetOption("cf_home"),$gsg->GetOption("pr_home"));
				}
			}
		}
		
		do_action('sm_buildmap');
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function BuildCategories($gsg) {
		global $wpdb;

		$exclCats = $gsg->GetOption("b_exclude_cats"); // Excluded cats
		if($exclCats == null) $exclCats=array();
					
		if(!$gsg->IsTaxonomySupported()) {
		
			$catsRes=$wpdb->get_results("
						SELECT
							c.cat_ID AS ID,
							MAX(p.post_modified_gmt) AS last_mod
						FROM
							`" . $wpdb->categories . "` c,
							`" . $wpdb->post2cat . "` pc,
							`" . $wpdb->posts . "` p
						WHERE
							pc.category_id = c.cat_ID
							AND p.ID = pc.post_id
							AND p.post_status = 'publish'
							AND p.post_type='post'
						GROUP
							BY c.cat_id
						");
			if($catsRes) {
				foreach($catsRes as $cat) {
					if($cat && $cat->ID && $cat->ID>0 && !in_array($cat->ID, $exclCats)) {
						$gsg->AddUrl(get_category_link($cat->ID),$gsg->GetTimestampFromMySql($cat->last_mod),$gsg->GetOption("cf_cats"),$gsg->GetOption("pr_cats"));
					}
				}
			}
		} else {
			$cats = get_terms("category",array("hide_empty"=>true,"hierarchical"=>false));
			if($cats && is_array($cats) && count($cats)>0) {
				foreach($cats AS $cat) {
					if(!in_array($cat->term_id, $exclCats)) $gsg->AddUrl(get_category_link($cat->term_id),0,$gsg->GetOption("cf_cats"),$gsg->GetOption("pr_cats"));
				}
			}
		}
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function BuildAuthors($gsg) {
		global $wpdb, $wp_version;
		
		$linkFunc = null;
		
		//get_author_link is deprecated in WP 2.1, try to use get_author_posts_url first.
		if(function_exists('get_author_posts_url')) {
			$linkFunc = 'get_author_posts_url';
		} else if(function_exists('get_author_link')) {
			$linkFunc = 'get_author_link';
		}
		
		//Who knows what happens in later WP versions, so check again if it worked
		if($linkFunc !== null) {
		    //Unfortunately there is no API function to get all authors, so we have to do it the dirty way...
			//We retrieve only users with published and not password protected posts (and not pages)
			//WP2.1 introduced post_status='future', for earlier WP versions we need to check the post_date_gmt
			$sql = "SELECT DISTINCT
						u.ID,
						u.user_nicename,
						MAX(p.post_modified_gmt) AS last_post
					FROM
						{$wpdb->users} u,
						{$wpdb->posts} p
					WHERE
						p.post_author = u.ID
						AND p.post_status = 'publish'
						AND p.post_type = 'post'
						AND p.post_password = ''
						" . (floatval($wp_version) < 2.1?"AND p.post_date_gmt <= '" . gmdate('Y-m-d H:i:59') . "'":"") . "
					GROUP BY
						u.ID,
						u.user_nicename";
						
			$authors = $wpdb->get_results($sql);
			
			if($authors && is_array($authors)) {
				foreach($authors as $author) {
					$url = ($linkFunc=='get_author_posts_url'?get_author_posts_url($author->ID,$author->user_nicename):get_author_link(false,$author->ID,$author->user_nicename));
					$gsg->AddUrl($url,$gsg->GetTimestampFromMySql($author->last_post),$gsg->GetOption("cf_auth"),$gsg->GetOption("pr_auth"));
				}
			}
		}
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function BuildTags($gsg) {
		$tags = get_terms("post_tag",array("hide_empty"=>true,"hierarchical"=>false));
		if($tags && is_array($tags) && count($tags)>0) {
			foreach($tags AS $tag) {
				$gsg->AddUrl(get_tag_link($tag->term_id),0,$gsg->GetOption("cf_tags"),$gsg->GetOption("pr_tags"));
			}
		}
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function BuildTaxonomies($gsg) {
		global $wpdb;
		
		if($gsg->IsTaxonomySupported()) {

			$enabledTaxonomies = $gsg->GetOption("in_tax");
			
			$taxList = array();
			
			foreach ($enabledTaxonomies as $taxName) {
				$taxonomy = get_taxonomy($taxName);
				if($taxonomy) $taxList[] = $wpdb->escape($taxonomy->name);
			}
			
			if(count($taxList)>0) {
				//We're selecting all term information (t.*) plus some additional fields
				//like the last mod date and the taxonomy name, so WP doesnt need to make
				//additional queries to build the permalink structure.
				//This does NOT work for categories and tags yet, because WP uses get_category_link
				//and get_tag_link internally and that would cause one additional query per term!
				$sql="
					SELECT
						t.*,
						tt.taxonomy AS _taxonomy,
						UNIX_TIMESTAMP(MAX(post_date_gmt)) as _mod_date
					FROM
						{$wpdb->posts} p ,
						{$wpdb->term_relationships} r,
						{$wpdb->terms} t,
						{$wpdb->term_taxonomy} tt
					WHERE
						p.ID = r.object_id
						AND p.post_status = 'publish'
						AND p.post_type = 'post'
						AND p.post_password = ''
						AND r.term_taxonomy_id = t.term_id
						AND t.term_id = tt.term_id
						AND tt.count > 0
						AND tt.taxonomy IN ('" . implode("','",$taxList) . "')
					GROUP BY
						t.term_id";
							
				$termInfo = $wpdb->get_results($sql);
				
				foreach($termInfo AS $term) {
					$gsg->AddUrl(get_term_link($term,$term->_taxonomy),$term->_mod_date ,$gsg->GetOption("cf_tags"),$gsg->GetOption("pr_tags"));
				}
			}
		}
	}

	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function BuildExternals($gsg) {
		$pages = $gsg->GetPages();
		if($pages && is_array($pages) && count($pages)>0) {
			//#type $page GoogleSitemapGeneratorPage
			foreach($pages AS $page) {
				$gsg->AddUrl($page->GetUrl(),$page->getLastMod(),$page->getChangeFreq(),$page->getPriority());
			}
		}
	}
	
	function FilterIndexFields($fields) {
		return "YEAR(post_date_gmt) AS `year`, MONTH(post_date_gmt) AS `month`, COUNT(ID) AS `numposts`, MAX(post_date_gmt) as last_mod";
	}
	
	function FilterIndexGroup($group) {
		return "YEAR(post_date_gmt), MONTH(post_date_gmt)";
	}
	
	function BuildPostQuery($gsg, $postType) {
		//Default Query Parameters
		$qp = array(
			'post_type' => $postType,
			'numberposts'=>0,
			'nopaging'=>true,
			'suppress_filters'=>false
		);
		
		//Excluded posts and page IDs
		$excludes = (array) $gsg->GetOption('b_exclude');
		if(count($excludes)>0) {
			$qp["post__not_in"] = $excludes;
		}
		
		// Excluded categorie IDs
		$exclCats = (array) $gsg->GetOption("b_exclude_cats");

		if(count($exclCats)>0) {
			$qp["category__not_in"] = $exclCats;
		}
		
		return $qp;
	}
	
	/**
	 * @param $gsg GoogleSitemapGenerator
	 */
	function Index($gsg) {
		global $wpdb, $wp_version;

		
		$gsg->AddSitemap("misc");
		
		if($gsg->GetOption("in_arch"))	$gsg->AddSitemap("archives");
		if($gsg->GetOption("in_cats"))	$gsg->AddSitemap("categories");
		if($gsg->GetOption("in_auth"))	$gsg->AddSitemap("authors");
		
		if($gsg->IsTaxonomySupported()) {
			if($gsg->GetOption("in_tags"))	$gsg->AddSitemap("tags");
			if($gsg->GetOption("in_tax"))	$gsg->AddSitemap("taxonomies");
		}
		$pages = $gsg->GetPages();
		if($pages && is_array($pages) && count($pages)>0) $gsg->AddSitemap("externals");
		
		if($gsg->GetOption("in_posts") || $gsg->GetOption('in_pages')) {

			$qp = $this->BuildPostQuery($gsg,"post");
			
			$qp['cache_results']=false;
			
			//Add filter to remove password protected posts
			add_filter('posts_search',array($this,'FilterPassword'),10,2);
			
			//Add filter to remove fields
			add_filter('posts_fields',array($this,'FilterIndexFields'),10,2);
			
			//Add filter to group
			add_filter('posts_groupby',array($this,'FilterIndexGroup'),10,2);
			
			//First get posts, later get pages since WP < 3.0 can not handle multiple post types
			$posts = @get_posts($qp);

			if ($posts) {
				foreach ($posts as $arcresult) {
					$gsg->AddSitemap("posts",sprintf("%04d-%02d",$arcresult->year,$arcresult->month), $gsg->GetTimestampFromMySql($arcresult->last_mod));
				}
			}
			
			//Now get the pages
			$qp['post_type']='page';
			
			$posts = @get_posts($qp);
			if ($posts) {
				foreach ($posts as $arcresult) {
					$gsg->AddSitemap("pages",sprintf("%04d-%02d",$arcresult->year,$arcresult->month), $gsg->GetTimestampFromMySql($arcresult->last_mod));
				}
			}
			
			//Remove the filters again
			remove_filter('posts_where',array($this,'FilterPassword'),10,2);
			remove_filter('posts_fields',array($this,'FilterIndexFields'),10,2);
			remove_filter('posts_groupby',array($this,'FilterIndexGroup'),10,2);
	

		}
	}
}

if(defined("WPINC")) new GoogleSitemapGeneratorStandardBuilder();