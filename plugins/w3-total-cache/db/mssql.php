<?php

	/*
	
	http://code.google.com/p/wordpress-mssql/
	
	Contributors: Aizu Ikmal Ahmad & Iwani Khalid
	Requires at least: 2.3.0
	Tested up to: 2.7.1
	Development Status: Alpha
	Database Abstraction Class: ezSQL by Justin Vincent (http://php.justinvincent.com)

	How to install MS-SQL support for Wordpress
	
	1. Download Wordpress ( http://wordpress.org/wordpress-2.7.1-IIS-RC1.zip )
	2. Then, upload it on your webhosting that supports MSSQL and IIS like how you would usually do it
	3. Download our Magic Plugin at ( http://code.google.com/p/wordpress-mssql/ )
	4. Then upload that db.php to your wp-contents folder
	5. Edit the MSSQL database setting in db.php file according to your MSSQL settings 6. You're done :D

	At this moment, this workaround works for these functions, to where we have debugged and tested

	    * Basic submit, edit, manage posts and pages
	    * Comments system
	    * Dynamic sidebar
	    * Changing themes
	    * Installing plugins
	    * Long text retrieval 

	What we didn't have time to debug

	    * Pagination
	    * Categories > Posts retrieval
	    * Dates / Time of posts
	    * Others which we look forward to debug later :D 
	
	*/

	// ==================================================================
	//  Author: Justin Vincent (justin@visunet.ie)
	//	Web: 	http://php.justinvincent.com
	//	Name: 	ezSQL
	// 	Desc: 	Class to make it very easy to deal with MS SQL database connections.
	//
	//	N.B. ezSQL was converted for use with MS SQL
	//	     by Tom De Bruyne (tom@challenge.be).
	//
	// !! IMPORTANT !!
	//
	//  Please send me a mail telling me what you think of ezSQL
	//  and what your using it for!! Cheers. [ justin@visunet.ie ]
	//
	// ==================================================================
	// User Settings -- CHANGE HERE

	ini_set ('mssql.textlimit','2147483647');			
	ini_set ('mssql.textsize','2147483647');
   
	define("EZSQL_DB_USER", "tretinoin_wpuser");		// <-- MS SQL Server db user
	define("EZSQL_DB_PASSWORD", "83h93hdi.");			// <-- MS SQL Server db password
	define("EZSQL_DB_NAME", "tretinoin_wordpress");		// <-- MS SQL Server db pname
	define("EZSQL_DB_HOST", "lamp2win");				// <-- MS SQL Server server host

	// ==================================================================
	//	ezSQL Constants
	define("EZSQL_VERSION","1.26");
	define("OBJECT","OBJECT",true);
	define("ARRAY_A","ARRAY_A",true);
	define("ARRAY_N","ARRAY_N",true);

	// ==================================================================
	//	The Main Class

	class W3_Db_Driver {

		var $debug_called;
		var $vardump_called;
		var $show_errors = false;
		var $num_queries = 0;
		var $debug_all = false;
		var $last_query;
		var $col_info;
		
		var $suppress_errors = false;
		var $last_error = '';
		var $queries;
		var $prefix = '';
		var $ready = false;
		var $posts;
		var $users;
		var $categories;
		var $post2cat;
		var $comments;
		var $links;
		var $options;
		var $postmeta;
		var $usermeta;
		var $terms;
		var $term_taxonomy;
		var $term_relationships;
		var $tables = array('users', 'usermeta', 'posts', 'categories', 'post2cat', 'comments', 'links', 'link2cat', 'options',
				'postmeta', 'terms', 'term_taxonomy', 'term_relationships');

		var $charset;
		var $collate;		
		
		
		
		
		// ==================================================================
		//	DB Constructor - connects to the server and selects a database


		
		function W3_Db_Driver($dbuser, $dbpassword, $dbname, $dbhost)
		{

			$this->dbh = mssql_connect($dbhost, $dbuser, $dbpassword);
						
			if ( ! $this->dbh )	{
				$this->print_error("<ol><b>Error establishing a database connection!</b><li>Are you sure you have the correct user/password?<li>Are you sure that you have typed the correct hostname?<li>Are you sure that the database server is running?</ol>");
			}
			
			$this->select($dbname);
			
		}

		// ==================================================================
		//	Select a DB (if another one needs to be selected)

		function select($db)
		{
			mssql_select_db ($db);
		}

		
		// ==================================================================
		//	Basic Query	- see docs for more detail

		function query($query)
		{
		
			$query = trim($query);
			$query = str_replace('`',"'",$query);
			
			$query = str_replace('\012',"\n",$query);
			$query = str_replace('\015',"\r",$query);
			
			//$query = str_replace("\n",'',$query);
			//$query = str_replace("\r",'',$query);			
	
			//$query = $this->escape($query);
			//$query = str_replace('"','\"',$query);
			//$query = str_replace("'","\'",$query);
			
			
			if(substr($query,0,7) == 'SELECT '){
			
				dbug($query,'SELECT : ORIGINAL QUERY ::::::: ','orange');
		
				$query_arr = explode('LIMIT',$query);
				if($query_arr[0] AND $query_arr[1]){
					
					$query_one = str_replace('SELECT ','',$query_arr[0]);
					
					$top_arr = explode(' ',$query_arr[1]);
					
					if($top_arr[1] && $top_arr[2]){
						$query = 'SELECT TOP '.$top_arr[2].' '.$query_one;
					}else{					
						$query = 'SELECT TOP '.$top_arr[1].' '.$query_one;
					}

					
					
					/*
					
					// TO BE DEVELOP 
					
					//LIMIT 1, 3

					SELECT * FROM (
						SELECT TOP 3 * FROM
							(SELECT TOP (1+3) * FROM wp_posts WHERE 1=1 AND post_type = 'post' AND (post_status = 'publish' OR post_status = 'private') ORDER BY post_date DESC)
						AS table1 ORDER BY post_date ASC
					) AS table2  ORDER BY post_date DESC					
					
					*/
					
					//pre($query_arr);
					//exit();
					
					
				}
		
		
					if(substr($query,0,23) == "SELECT TOP 1 comment_ID"){
						$query = str_replace(' = ',' = ',$query);
					}
		
		
				/* DUMP DIRTY SWEPT TEST */
				$query = str_replace('wp_posts.','',$query);
				
				
				/*
				hacks for query;
				- SELECT * FROM wp_posts WHERE (post_type = 'page' AND post_status = 'publish') ORDER BY menu_order, post_title ASC
				- SELECT * FROM wp_posts WHERE 1=1 AND post_type = 'page' AND (post_status = 'publish' OR post_status = 'future' OR post_status = 'draft' OR post_status = 'pending' OR post_status = 'private') ORDER BY menu_order,post_title asc
				notes;
				somehow Wordpress developer create this 'post_title' date-type as 'text' in database. the post_title should be varchar since the amount of character in title is generally less then 255 char.
				and in ms-sql, text cannot be sorted. so this is a big mistake. haihhh...
				*/
				$pattern = '/post_title asc/is';
				$replacement = 'cast(post_title as varchar(500)) ASC';
				$query = preg_replace($pattern, $replacement, $query);				
				
				
				
				
				/*
				hacks for query;
				- SELECT DISTINCT  YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM wp_posts WHERE post_type = 'post' ORDER BY post_date DESC
				notes;
				this is one another hacks for SELECT DISTINCT call. 
				The above query tries to sort by the column post_date. Because the keyword DISTINCT is also specified, column post_date must appear in the SELECT list
				expected return;
				 - SELECT DISTINCT  post_date, YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM wp_posts WHERE post_type = 'post' ORDER BY post_date DESC
				*/
				if(substr($query,0,15) == "SELECT DISTINCT"){
					$string = $query;
					$pattern = '/SELECT DISTINCT (.*)? ORDER BY (.*)? (asc|desc)/i';
					$replacement = 'cast(post_title as varchar(500)) ASC';
					preg_match_all($pattern, $string, $result);					
					$order_by_col = $result[2][0];
					$col_calls = $result[1][0];
					$order_by_prop = $result[3][0];
					$query = 'SELECT DISTINCT '.$order_by_col.', '.$col_calls.' ORDER BY '.$order_by_col.' '.$order_by_prop;				
				}
				
				
				
				
				/*
				hacks for query;
				- SELECT user_id FROM wp_usermeta WHERE meta_key = 'wp_user_level' AND meta_value != '0'
				notes;
				replace != into NOT <col_name> LIKE
				expected return;
				- SELECT user_id FROM wp_usermeta WHERE meta_key = 'wp_user_level' AND NOT meta_value LIKE '0'
				*/
				$pattern = "/ (?:[a-z][a-z0-9_]*)? \!\= /is";
				preg_match_all($pattern, $query, $result);
				$result_arr = $result[0];
				$query = str_replace('!=','',$query);
				foreach($result_arr as $result_item){
					$result_item = str_replace('!=','',$result_item);
					$result_item = trim($result_item);
					$query = str_replace($result_item,' NOT '.$result_item.' LIKE ',$query);
				}				
				
				
				
				/*
				hacks for;
				- SQL have 'GROUP BY'
				- SELECT DISTINCT post_date, YEAR(post_date) AS year, MONTH(post_date) AS month, count(ID) as posts FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish
				GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC
				*/
				if (preg_match("/GROUP BY/i", $query)) {
					$pattern = '/SELECT (.*) GROUP BY (.*)? ORDER BY (.*)/i';
					preg_match($pattern, $string, $output);	
					
					$query0 = 'SELECT '.$output[1].' ORDER BY '.$output[3];
				}
				
				
				$query = str_replace('SQL_CALC_FOUND_ROWS',' ',$query);
				if (preg_match("/SELECT FOUND_ROWS()/i", $query)) {
					$last_query = $this->last_query;
					$last_query_one_arr = explode('TOP',$last_query);
					$last_query_two = trim($last_query_one_arr[1]);
					$last_query_three_arr = explode(' ',$last_query_two);
					$last_query_four = str_replace($last_query_three_arr[0],'',$last_query_two);
					$query = 'SELECT '.$last_query_four;
				}				
				
				dbug($query,'SELECT : MODDED QUERY :::::::: ','orange');
		
			}elseif(substr($query,0,7) == 'INSERT '){

				dbug($query,'INSERT : ORIGINAL QUERY ::::::::: ','purple');

				$pattern = '/INSERT INTO (.*)?\((.*)\).*?VALUES.*?\((.*)\)/is';
				preg_match_all($pattern, $query, $output);
				$insert_table 	= trim($output[1][0]);
				$insert_cols 	= trim($output[2][0]);
				$insert_values 	= trim($output[3][0]);

				/* STRIP QUOTE FROM COLS NAME */
				$insert_cols = str_replace("'",'',$insert_cols);
				
				$query = $this->prepare($query);
				
				/* PROCESS THE $insert_values. NEED TO REPLACE THE INNER QUOTES */				
				preg_match_all("/([0-9]+|\'(.*?)\'[ ]*?),/is", $insert_values.',', $output);
				
				$insert_values_arr = array();	//PREPARE THE NEW FRESH VALUES
				foreach($output[0] as $insert_values_item){
					if(substr(trim($insert_values_item),0,1) == "'"){
						$insert_values_item = substr($insert_values_item,1);			// TAKE OUT THE FIRST 1 CHAR, WHICH IS QUOTE
					}				
					$insert_values_item = trim($insert_values_item);					// TRIM THE WHITESPACE
					if(substr(trim($insert_values_item),-1) == ","){
						$insert_values_item = substr($insert_values_item,0,-1);			// TAKE OUT THE LAST 1 CHAR, WHICH IS COMMA
					}
					if(substr(trim($insert_values_item),-1) == "'"){
						$insert_values_item = substr($insert_values_item,0,-1);			// TAKE OUT THE LAST 1 CHAR, WHICH IS QUOTE
					}						
					$insert_values_item = str_replace("'","''",$insert_values_item);	// NOW WE PUT THE EXTRA QUOTE ON IT
					$insert_values_item = trim($insert_values_item);					// TRIM THE WHITESPACE
					$insert_values_item = "'".$insert_values_item."'";					// WRAP THE VALUES WITH OUR SINGLE-QUOTE
					$insert_values_arr[] = $insert_values_item;
				}
				
				$insert_values = implode(',',$insert_values_arr);
				
				
				/* CONSTRUCT NEW INSERT CALL */
				$query = 'INSERT INTO '.$insert_table.' ('.$insert_cols.') VALUES ('.$insert_values.');';
				
				
				dbug($query,'INSERT : MODDED QUERY :::::::::: ','purple');
				
			}elseif(substr($query,0,7) == 'UPDATE '){
				
				
				dbug($query,'UPDATE : ORIGINAL QUERY ::::::::: ','blue');

				
				$pattern = '/UPDATE ([A-Za-z0-9_-]+) SET (.*)? WHERE (.*)?/is';
				preg_match_all($pattern, $query, $output);
				$update_table	= trim($output[1][0]);
				$update_values	= trim($output[2][0]);
				$update_where	= trim($output[3][0]);

				preg_match_all("/(.*?)[ *]?=[ *]?(\'(.*?)\'|[0-9]+),/is", $update_values.',', $output_vals);
				
				$update_vals_colname = $output_vals[1];
				$update_vals_values	= $output_vals[2];
				
				
				$update_vals_colname_mod = array();
				foreach($update_vals_colname as $update_vals_colname_item){
					$update_vals_colname_item = str_replace("'",'',$update_vals_colname_item);	// GREEDY REPLACE ALL QUOTES. NO QUOTES ALLOWED IN COL NAME
					$update_vals_colname_item = trim($update_vals_colname_item);				// TRIM THE WHITESPACE
					$update_vals_colname_mod[] = $update_vals_colname_item;
				}

				$update_vals_values_mod = array();
				foreach($update_vals_values as $update_vals_values_item){
					if(substr(trim($update_vals_values_item),0,1) == "'"){
						$update_vals_values_item = substr($update_vals_values_item,1);			// TAKE OUT THE FIRST 1 CHAR, WHICH IS QUOTE
					}
					if(substr(trim($update_vals_values_item),-1) == "'"){
						$update_vals_values_item = substr($update_vals_values_item,0,-1);		// TAKE OUT THE LAST 1 CHAR, WHICH IS QUOTE
					}
					$update_vals_values_item = trim($update_vals_values_item);					// TRIM THE WHITESPACE
					$update_vals_values_item = str_replace("'","''",$update_vals_values_item);		// ADD ADDITIONAL QUOTE TO  ESCAPE IT IN MSSQL
					$update_vals_values_mod[] = $update_vals_values_item;
				}
				
				$update_values_modded = array_combine($update_vals_colname_mod,$update_vals_values_mod);
				
				$update_values = '';
				$update_values_prepare_arr = '';
				$update_values_item = '';
				foreach($update_values_modded as $update_values_item_colname => $update_values_item_value){
					$update_values_prepare_arr[] = $update_values_item_colname." = '".$update_values_item_value."'";
				}
				
								
				$update_values = implode(',',$update_values_prepare_arr);
				
				
				
				
				
				/* CONSTRUCT NEW UPDATE CALL */
				$query = 'UPDATE '.$update_table.' SET '.$update_values.' WHERE '.$update_where.';';
				
				
				



		


				
				dbug($query,'UPDATE : MODDED QUERY :::::::::: ','blue');
				
			}elseif(substr($query,0,7) == 'DELETE '){
			

				
				dbug($query,'DELETE','orange');
			
			}elseif(substr($query,0,7) == 'CREATE '){
			
				$query_two = str_replace('auto_increment','IDENTITY(1,1)',$query);
				$query_two = str_replace('tinytext','text',$query_two);
				$query_two = str_replace('longtext','text',$query_two);
				$query_two = str_replace('mediumtext','text',$query_two);
				$query_two = str_replace('unsigned ','',$query_two);
				$query_three = preg_replace('/bigint\(\d+\)/i','int',$query_two);
				$query_three = preg_replace('/int\(\d+\)/i','int',$query_three);
				$query_four_arr = explode('PRIMARY KEY',$query_three);
				$query_five = $query_four_arr[0].' ';
				$query_six = $query_four_arr[1];
				$query_seven_arr = explode('),',$query_six);
				$query_eight_arr = explode('(',$query);
				$query_nine = trim($query_eight_arr[0]);
				$query_ten = str_replace($query_nine,'',$query_five);
				$table_name = trim(str_replace('CREATE TABLE','',$query_nine));
				
				
				$create_table_header = "				
IF EXISTS (SELECT * FROM dbo.sysobjects WHERE id = object_id(N'[tretinoin_wpuser].[".$table_name."]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
    DROP TABLE [tretinoin_wpuser].[".$table_name."]
GO
CREATE TABLE [tretinoin_wpuser].[".$table_name."]";
				
				$create_table_header_simple = "CREATE TABLE [tretinoin_wpuser].[".$table_name."]			
				
				";				
				
				$query = $create_table_header_simple . $query_ten . ' PRIMARY KEY ' . $query_seven_arr[0] .'))';
			
				dbug($query,'CREATE','purple');
			
			}elseif(substr($query,0,5) == 'SHOW '){
			
				$query = str_replace('SHOW TABLES;','',$query);
			
				dbug($query,'SHOW','yellow');
			
			}else{
			
			
				dbug($query);
			
			}
		
			
		
			// For reg expressions
			$query = trim($query); 

			// Flush cached values..
			$this->flush();

			// Log how the function was called
			$this->func_call = "\$db->query(\"$query\")";

			// Keep track of the last query for debug..
			$this->last_query = $query;

			// Perform the query via std mssql_query function..
			
			
			if(substr($query,0,7) == 'SELECT '){
				$this->result_check = @mssql_query($query, $this->dbh);
			}elseif(substr($query,0,7) == 'INSERT '){
				
			}elseif(substr($query,0,7) == 'UPDATE '){
				$this->result_check = @mssql_query($query, $this->dbh);
			}elseif(substr($query,0,7) == 'DELETE '){
			
			}elseif(substr($query,0,7) == 'CREATE '){
			
			}elseif(substr($query,0,5) == 'SHOW '){
			
			}
			
			
			
			
			// Unfortunately, PHP fuctions for MS SQL currently don't offer a decent way
			// to retrieve errors from MS SQL
			// Make sure not to run a query between the actual query and this one !
				
			$get_errorcode = "SELECT @@ERROR as errorcode";
			$error_res = @mssql_query($get_errorcode, $this->dbh);
			$errorcode = @mssql_result($error_res, 0, "errorcode");

			// ERROR LIST 
			// 402 : The data types text and varchar are incompatible in the equal to operator.
			// 306 : The text, ntext, and image data types cannot be compared or sorted, except when using IS NULL or LIKE operator. 
			
			
			if ($errorcode == '402') {
				$query_two = str_replace(' = ',' LIKE ',$query);
				
				
				/* NEED MORE IMPROVEMENT HERE */
				


			}else{
				$query_two = $query;
			}				
			dbug($query,$errorcode,'green');

			 
			$this->result = @mssql_query($query_two, $this->dbh);
			$this->num_queries++;
			
			
			// If there was an insert, delete or update see how many rows were affected
			// (Also, If there there was an insert take note of the last OID
			$query_type = array("insert","delete","update","replace");

			// loop through the above array
			foreach ( $query_type as $word )
			{
				// This is true if the query starts with insert, delete or update
				if ( preg_match("/^$word\s+/i",$query) )
				{
					$this->rows_affected = @mssql_rows_affected ($this->dbh);
					
					// This gets the insert ID
					if ( $word == "insert" || $word == "replace" )
					{
						$get_last_ident = "SELECT @@IDENTITY as id";
						$last_res = @mssql_query($get_last_ident, $this->dbh);
						$this->insert_id = @mssql_result($last_res, 0, "id");
						
						// If insert id then return it - true evaluation
						return $this->insert_id;
					}
					
					// Set to false if there was no insert id
					$this->result = false;
				}
			}

			
			if ($errorcode <> 0) {
				// there is an error
				$this->print_error();
			}
			else
			{
				
				// =======================================================
				// Take note of column info

				$i=0;
				while ($i < @mssql_num_fields($this->result))
				{
					$this->col_info[$i]->name = @mssql_field_name($this->result,$i);
					$this->col_info[$i]->type = @mssql_field_type($this->result,$i);
					$this->col_info[$i]->size = @mssql_field_length($this->result,$i);
					$i++;
				}

				// =======================================================
				// Store Query Results

				$i=0;
				while ( $row_arr = @mssql_fetch_array($this->result) )
				{
				
						$row = array_to_object($row_arr);
						$this->last_result[$i] = $row;
				
					// Store relults as an objects within main array
					

					$i++;
				}
				
				if($i == 0){
					$this->last_result = array();
				}

				//pre($this->last_result);
				
				// Log number of rows the query returned
				$this->num_rows = $i;

				//}

				@mssql_free_result($this->result);

				// If this was a select..
				if ( preg_match("/^(select|show|desc)\s+/i",$query) )
				{
					
					// If debug ALL queries
					$this->debug_all ? $this->debug() : null ;
					
					// If there were results then return true for $db->query
					if ( $i )
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					// If debug ALL queries
					$this->debug_all ? $this->debug() : null ;
					
					// Update insert etc. was good..
					return true;
				}

			}
		}
		
		
		
		
		// ====================================================================
		//	Format a string correctly for safe insert under all PHP conditions
		
		function escape($str)
		{
			
			// This deals with quote escaping
			//$str = str_replace("'","''",$str);
			//$str = str_replace("\'","'",$str);

			//$str = mysql_real_escape_string($str);
			
			//$str = str_replace("'", "''", $str);
			//$str = addslashes($str);

			//dbug($str,'ESCAPE RUN TWICE','red');
			
			// These values need to be escaped for ms sql
			$escape = array ( "\n"=>"\\\\012","\r"=>"\\\\015");
	
			// Firstly unescape
			/*
			foreach ( $escape as $match => $replace )
			{
				$str = str_replace($match,$replace,$str);
			}
			*/
			return $str;
				
		}

		// ==================================================================
		//	Print SQL/DB error.

		function print_error($str = "")
		{
			// All erros go to the global error array $EZSQL_ERROR..
			global $EZSQL_ERROR;

			// if no special error, take last mssql error	
			if ( !$str ) $str = mssql_get_last_message();

			// Log this error to the global array..
			$EZSQL_ERROR[] = array 
							(
								"query" => $this->last_query,
								"error_str"  => $str
							);

			// Is error output turned on or not..
			if ( $this->show_errors )
			{
				// If there is an error then take note of it
				/*
				print "<blockquote><font face=arial size=2 color=ff0000>";
				print "<b>SQL/DB Error --</b> ";
				print "[<font color=000000>".$this->last_query."</font>]";
				print "[<font color=000077>$str</font>]";
				print "</font></blockquote>";
				*/
				
				dbug($this->last_query.'<br />'.$str,'ERROR','red');
				return false;
			}
			else
			{
				return false;	
			}
			
			
			
			
			
		}

		// ==================================================================
		//	Turn error handling on or off..

		function show_errors()
		{
			$this->show_errors = true;
		}
		
		function hide_errors()
		{
			$this->show_errors = false;
		}

		// ==================================================================
		//	Kill cached query results

		function flush()
		{

			// Get rid of these
			$this->last_result = null;
			$this->col_info = null;
			$this->last_query = null;

		}



		// ==================================================================
		//	Get one variable from the DB - see docs for more detail

		function get_var($query=null,$x=0,$y=0)
		{
		
			// Log how the function was called
			$this->func_call = "\$db->get_var(\"$query\",$x,$y)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Extract var out of cached results based x,y vals
			if ( $this->last_result[$y] )
			{
				$values = array_values(get_object_vars($this->last_result[$y]));
			}

			// If there is a value return it else return null
			return (isset($values[$x]) && $values[$x]!=='')?$values[$x]:null;
		}

		// ==================================================================
		//	Get one row from the DB - see docs for more detail

		function get_row($query=null,$output=OBJECT,$y=0)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_row(\"$query\",$output,$y)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// If the output is an object then return object using the row offset..
			if ( $output == OBJECT )
			{
				return $this->last_result[$y]?$this->last_result[$y]:null;
			}
			// If the output is an associative array then return row as such..
			elseif ( $output == ARRAY_A )
			{
				return $this->last_result[$y]?get_object_vars($this->last_result[$y]):null;
			}
			// If the output is an numerical array then return row as such..
			elseif ( $output == ARRAY_N )
			{
				return $this->last_result[$y]?array_values(get_object_vars($this->last_result[$y])):null;
			}
			// If invalid output type was specified..
			else
			{
				$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
			}

		}

		// ==================================================================
		//	Function to get 1 column from the cached result set based in X index
		// se docs for usage and info

		function get_col($query=null,$x=0)
		{

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Extract the column values
			for ( $i=0; $i < count($this->last_result); $i++ )
			{
				$new_array[$i] = $this->get_var(null,$x,$i);
			}
			
			if($i == 0){
				$new_array = array();
			}			

			return $new_array;
		}

		// ==================================================================
		// Return the the query as a result set - see docs for more details

		function get_results($query=null, $output = OBJECT)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_results(\"$query\", $output)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Send back array of objects. Each row is an object
			if ( $output == OBJECT )
			{
			
				$out = get_object_vars($this->last_result);
			
				return $this->last_result;
			}
			elseif ( $output == ARRAY_A || $output == ARRAY_N )
			{
				if ( $this->last_result )
				{
					$i=0;
					foreach( $this->last_result as $row )
					{

						$new_array[$i] = get_object_vars($row);

						if ( $output == ARRAY_N )
						{
							$new_array[$i] = array_values($new_array[$i]);
						}

						$i++;
					}

					return $new_array;
				}
				else
				{
					return null;
				}
			}
		}


		// ==================================================================
		// Function to get column meta data info pertaining to the last query
		// see docs for more info and usage

		function get_col_info($info_type="name",$col_offset=-1)
		{

			if ( $this->col_info )
			{
				if ( $col_offset == -1 )
				{
					$i=0;
					foreach($this->col_info as $col )
					{
						$new_array[$i] = $col->{$info_type};
						$i++;
					}
					return $new_array;
				}
				else
				{
					return $this->col_info[$col_offset]->{$info_type};
				}

			}

		}


		// ==================================================================
		// Dumps the contents of any input variable to screen in a nicely
		// formatted and easy to understand way - any type: Object, Var or Array

		function vardump($mixed='')
		{

			echo "<p><table><tr><td bgcolor=ffffff><blockquote><font color=000090>";
			echo "<pre><font face=arial>";

			if ( ! $this->vardump_called )
			{
				echo "<font color=800080><b>ezSQL</b> (v".EZSQL_VERSION.") <b>Variable Dump..</b></font>\n\n";
			}

			$var_type = gettype ($mixed);
			print_r(($mixed?$mixed:"<font color=red>No Value / False</font>"));
			echo "\n\n<b>Type:</b> " . ucfirst($var_type) . "\n";
			echo "<b>Last Query</b> [$this->num_queries]<b>:</b> ".($this->last_query?$this->last_query:"NULL")."\n";
			echo "<b>Last Function Call:</b> " . ($this->func_call?$this->func_call:"None")."\n";
			echo "<b>Last Rows Returned:</b> ".count($this->last_result)."\n";
			echo "</font></pre></font></blockquote></td></tr></table>".$this->donation();
			echo "\n<hr size=1 noshade color=dddddd>";

			$this->vardump_called = true;

		}

		// Alias for the above function
		function dumpvar($mixed)
		{
			$this->vardump($mixed);
		}

		// ==================================================================
		// Displays the last query string that was sent to the database & a
		// table listing results (if there were any).
		// (abstracted into a seperate file to save server overhead).

		function debug()
		{

			echo "<blockquote>";

			// Only show ezSQL credits once..
			if ( ! $this->debug_called )
			{
				echo "<font color=800080 face=arial size=2><b>ezSQL</b> (v".EZSQL_VERSION.") <b>Debug..</b></font><p>\n";
			}
			echo "<font face=arial size=2 color=000099><b>Query</b> [$this->num_queries] <b>--</b> ";
			echo "[<font color=000000><b>$this->last_query</b></font>]</font><p>";

				echo "<font face=arial size=2 color=000099><b>Query Result..</b></font>";
				echo "<blockquote>";

			if ( $this->col_info )
			{

				// =====================================================
				// Results top rows

				echo "<table cellpadding=5 cellspacing=1 bgcolor=555555>";
				echo "<tr bgcolor=eeeeee><td nowrap valign=bottom><font color=555599 face=arial size=2><b>(row)</b></font></td>";


				for ( $i=0; $i < count($this->col_info); $i++ )
				{
					echo "<td nowrap align=left valign=top><font size=1 color=555599 face=arial>{$this->col_info[$i]->type} {$this->col_info[$i]->max_length}</font><br><span style='font-family: arial; font-size: 10pt; font-weight: bold;'>{$this->col_info[$i]->name}</span></td>";
				}

				echo "</tr>";

				// ======================================================
				// print main results

			if ( $this->last_result )
			{

				$i=0;
				foreach ( $this->get_results(null,ARRAY_N) as $one_row )
				{
					$i++;
					echo "<tr bgcolor=ffffff><td bgcolor=eeeeee nowrap align=middle><font size=2 color=555599 face=arial>$i</font></td>";

					foreach ( $one_row as $item )
					{
						echo "<td nowrap><font face=arial size=2>$item</font></td>";
					}

					echo "</tr>";
				}

			} // if last result
			else
			{
				echo "<tr bgcolor=ffffff><td colspan=".(count($this->col_info)+1)."><font face=arial size=2>No Results</font></td></tr>";
			}

			echo "</table>";

			} // if col_info
			else
			{
				echo "<font face=arial size=2>No Results</font>";
			}

			echo "</blockquote></blockquote>".$this->donation()."<hr noshade color=dddddd size=1>";


			$this->debug_called = true;
		}

		// =======================================================
		// Naughty little function to ask for some remuniration!

		function donation()
		{
			return "<font size=1 face=arial color=000000>If ezSQL has helped <a href=\"https://www.paypal.com/xclick/business=justin%40justinvincent.com&item_name=ezSQL&no_note=1&tax=0\" style=\"color: 0000CC;\">make a donation!?</a> &nbsp;&nbsp;[ go on! you know you want to! ]</font>";	
		}


		
		
		/**
		 * Sets the table prefix for the WordPress .
		 *
		 * Also allows for the CUSTOM_USER_TABLE and CUSTOM_USER_META_TABLE to
		 * override the WordPress users and usersmeta tables.
		 *
		 * @since 2.5.0
		 *
		 * @param string $prefix Alphanumeric name for the new prefix.
		 * @return string Old prefix
		 */
		function set_prefix($prefix) {

			if ( preg_match('|[^a-z0-9_]|i', $prefix) )
				return new WP_Error('invalid_db_prefix', /*WP_I18N_DB_BAD_PREFIX*/'Invalid database prefix'/*/WP_I18N_DB_BAD_PREFIX*/);

			$old_prefix = $this->prefix;
			$this->prefix = $prefix;

			foreach ( (array) $this->tables as $table )
				$this->$table = $this->prefix . $table;

			if ( defined('CUSTOM_USER_TABLE') )
				$this->users = CUSTOM_USER_TABLE;

			if ( defined('CUSTOM_USER_META_TABLE') )
				$this->usermeta = CUSTOM_USER_META_TABLE;

			return $old_prefix;
		}
		
		
		/**
		 * Whether to suppress database errors.
		 *
		 * @param unknown_type $suppress
		 * @return unknown
		 */
		function suppress_errors( $suppress = true ) {
			$errors = $this->suppress_errors;
			$this->suppress_errors = $suppress;
			return $errors;
		}		
		
		
		/**
		 * Escapes content by reference for insertion into the database, for security
		 *
		 * @since 2.3.0
		 *
		 * @param string $s
		 */
		function escape_by_ref(&$s) {
			$s = $this->escape($s);
		}		
		
		/**
		 * Generic function to determine if a database supports a particular feature
		 * @param string $db_cap the feature
		 * @param false|string|resource $dbh_or_table the databaese (the current database, the database housing the specified table, or the database of the mysql resource)
		 * @return bool
		 */
		function has_cap( $db_cap ) {
			

			return false;
		}		
		
		/**
		 * Insert an array of data into a table.
		 *
		 * @since 2.5.0
		 *
		 * @param string $table WARNING: not sanitized!
		 * @param array $data Should not already be SQL-escaped
		 * @return mixed Results of $this->query()
		 */
		function insert($table, $data) {
			$data = add_magic_quotes($data);
			$fields = array_keys($data);
			return $this->query("INSERT INTO $table (`" . implode('`,`',$fields) . "`) VALUES ('".implode("','",$data)."')");
		}		
		
		/**
		 * Prepares a SQL query for safe use, using sprintf() syntax.
		 *
		 * @link http://php.net/sprintf See for syntax to use for query string.
		 * @since 2.3.0
		 *
		 * @param null|string $args If string, first parameter must be query statement
		 * @param mixed $args,... If additional parameters, they will be set inserted into the query.
		 * @return null|string Sanitized query string
		 */
		function prepare($args=null) {
			if ( is_null( $args ) )
				return;
			$args = func_get_args();
			$query = array_shift($args);
			$query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted it
			$query = str_replace('"%s"', '%s', $query); // doublequote unquoting
			$query = str_replace('%s', "'%s'", $query); // quote the strings
			array_walk($args, array(&$this, 'escape_by_ref'));
			return @vsprintf($query, $args);
		}		
		
		
		
		/**
		 * Update a row in the table with an array of data.
		 *
		 * @since 2.5.0
		 *
		 * @param string $table WARNING: not sanitized!
		 * @param array $data Should not already be SQL-escaped
		 * @param array $where A named array of WHERE column => value relationships.  Multiple member pairs will be joined with ANDs.  WARNING: the column names are not currently sanitized!
		 * @return mixed Results of $this->query()
		 */
		function update($table, $data, $where){
			$data = add_magic_quotes($data);
			$bits = $wheres = array();
			foreach ( (array) array_keys($data) as $k )
				$bits[] = "`$k` = '$data[$k]'";

			if ( is_array( $where ) )
				foreach ( $where as $c => $v ){
					$v = str_replace("'","'",$v);
					//$wheres[] = "$c = '" . $this->escape( $v ) . "'";
					$wheres[] = "$c = '" . $v . "'";
				}
			else
				return false;

			return $this->query( "UPDATE $table SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres ) );
		}		
		
		/**
		 * Whether or not MySQL database is minimal required version.
		 *
		 * @since 2.5.0
		 * @uses $wp_version
		 *
		 * @return WP_Error
		 */
		function check_database_version()
		{
			global $wp_version;
			// Make sure the server has MySQL 4.0
			/*
			if ( version_compare($this->db_version(), '4.0.0', '<') )
				return new WP_Error('database_version',sprintf(__('<strong>ERROR</strong>: WordPress %s requires MySQL 4.0.0 or higher'), $wp_version));
				
			*/
			
		}		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}

// automatically create a new $db object

//$wpdb = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);




function array_to_object($array = array()) {
    if (!empty($array)) {
        $data = false;

        foreach ($array as $akey => $aval) {
            if(!is_numeric($akey)){
				$data -> {$akey} = trim($aval);
			}
		}

        return $data;
    }

    return false;
}

function pre($sting_to_pre = '',$ob=false){

	if($sting_to_pre){
	
		if($ob){
		
			ob_start();
				echo '<pre>';
		        print_r($sting_to_pre);
		        echo '</pre>';
			$the_return = ob_get_contents();
			ob_end_clean();
			return $the_return;
		
		}else{
			echo '<pre>';
			print_r($sting_to_pre);
			echo '</pre>';
		}
	
	}
}

function dbug($val,$prepend='',$prepend_color='red'){

	/*
	$fp = fopen('E:\wwwusr\vhosts\tretinoin.dev.lamp2win.com\httpdocs\debug\log.txt', 'a');
	fwrite($fp, '<span style="color:'.$prepend_color.';"><b>'.$prepend.'</b></span> '.$val."\n");
	fclose($fp);
	*/
}
?>
