<?
function installTables() {
	GLOBAL $dbhost, $dbuser, $dbpass, $database, $nav;
	require_once("config.php");
	require_once("funcsv2.php");

if ($nav == "install") {
		$makenamemap= 'CREATE TABLE BTPHP_namemap (info_hash char(40) NOT NULL default "", filename varchar(250) NOT NULL default "", url varchar(250) NOT NULL default "", info varchar(250) NOT NULL default "", PRIMARY KEY(info_hash))'; 	
		$makesummary = 'CREATE TABLE BTPHP_summary (info_hash char(40) NOT NULL default "", dlbytes bigint unsigned NOT NULL default 0, seeds int unsigned NOT NULL default 0, leechers int unsigned NOT NULL default 0, finished int unsigned NOT NULL default 0, lastcycle int unsigned NOT NULL default "0", lastSpeedCycle int unsigned NOT NULL DEFAULT "0", speed bigint unsigned NOT NULL default 0, PRIMARY KEY (info_hash))';
		$maketimestamps = 'CREATE TABLE BTPHP_timestamps (info_hash char(40) not null, sequence int unsigned not null auto_increment, bytes bigint unsigned not null, delta smallint unsigned not null, primary key(sequence), key sorting (info_hash))';
		$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("Can't connect to database: ".mysql_error()); 
		mysql_select_db($database) or die("Can't select database: ".mysql_error());
		mysql_query($makesummary) or die("Can't make the summary table: ".mysql_error());
		mysql_query($makenamemap) or die("Can't make the namemap table: ".mysql_error());
		mysql_query($maketimestamps) or die("Can't make the timestamps table: ".mysql_error());
		print "<p>Tables installed in database, your tracker should now function.</p>";
	}
	else
		print "</p>Tracker tables not found in the database, click <a href=\"?nav=install\">here</a> to install them.</p>"; 
}
	
function cleanUp () {
	GLOBAL $dbhost, $dbuser, $dbpass, $database;
	require_once("config.php");
	require_once("funcsv2.php");
	
	$summaryupdate = array();
	
	// Non-persistant: we lock tables!
	$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("<p class=\"error\">Tracker error: can't connect to database - ".mysql_error() . "</p>");
	mysql_select_db($database) or die("<p class=\"error\">Tracker error: can't open database $database - ".mysql_error() . "</p>");
	
	if (isset($_GET["nolock"]))
		$locking = false;
	else
		$locking = true;
	
	// Assumes success
	if ($locking)
		quickQuery("LOCK TABLES BTPHP_summary WRITE, BTPHP_namemap READ");
	
	?>
	<table class="torrentlist" cellspacing="5">
	<!-- Column Headers -->
	<tr>
		<th>Name/Hash</th>
		<th>Leechs</th>
		<th>Seeds</th>
		<th>Bytes Transfered</th>
		<th>Stale clients</th>
		<th>Peer Cache</th>
	</tr>
	<tr>
		<td colspan="5" class="nodata"></td>
	</tr>
	<?php
	
	$results = mysql_query("SELECT BTPHP_summary.info_hash, seeds, leechers, dlbytes, BTPHP_namemap.filename FROM BTPHP_summary LEFT JOIN BTPHP_namemap ON BTPHP_summary.info_hash = BTPHP_namemap.info_hash");
	
	$i = 0;
	
	while ($row = mysql_fetch_row($results))
	{
		$writeout = "row" . $i % 2;
		list($hash, $seeders, $leechers, $bytes, $filename) = $row;
		if ($locking)
		{
			if ($GLOBALS["peercaching"])
				quickQuery("LOCK TABLES x$hash WRITE, y$hash WRITE, summary WRITE");
			else
				quickQuery("LOCK TABLES x$hash WRITE, summary WRITE");
		}
		$results2 = mysql_query("SELECT status, COUNT(status) from x$hash GROUP BY status");
		echo "<tr class=\"$writeout\"><td>";
		if (!is_null($filename))
			echo $filename;
		else
			echo $hash;
		echo "</td>";
		if (!$results2)
		{
			echo "<td colspan=\"4\">Unable to process: ".mysql_error()."</td></tr>";
			continue;
		}
	
		$counts = array();
		while ($row = mysql_fetch_row($results2))
			$counts[$row[0]] = $row[1];	
		if (!isset($counts["leecher"]))
			$counts["leecher"] = 0;
		if (!isset($counts["seeder"]))
			$counts["seeder"] = 0;
	
		if ($counts["leecher"] != $leechers)
		{
			quickQuery("UPDATE BTPHP_summary SET leechers=".$counts["leecher"]." WHERE info_hash=\"$hash\"");
			echo "<td>$leechers -> ".$counts["leecher"]."</td>";
		}
		else
			echo "<td align=center>$leechers</td>";
	
		if ($counts["seeder"] != $seeders)
		{
			quickQuery("UPDATE BTPHP_summary SET seeds=".$counts["seeder"]." WHERE info_hash=\"$hash\"");
			echo "<td align=center>$seeders -> ".$counts["seeder"]."</td>";
		}
		else
			echo "<td align=center>$seeders</td>";
	//	echo "<td align=center>$finished</td>";
		if ($bytes < 0)
		{
			quickQuery("UPDATE BTPHP_summary SET dlbytes=0 WHERE info_hash=\"$hash\"");
			echo "<td>$bytes -> Zero</td>";
		}
		else
			echo "<td align=center>". round($bytes/1048576/1024,3) ." GB</td>";
	
		myTrashCollector($hash, $report_interval, time(), $writeout);
		echo "</td><td>";
		
	
		if ($GLOBALS["peercaching"])
		{
	
			$result = mysql_query("SELECT x$hash.sequence FROM x$hash LEFT JOIN y$hash ON x$hash.sequence=y$hash.sequence WHERE y$hash.sequence IS NULL") or die(mysql_error());
			if (mysql_num_rows($result) > 0)
			{
				echo "Added ", mysql_num_rows($result);
				$row = array();
				
				while ($data = mysql_fetch_row($result))
						$row[] = "sequence=\"${data[0]}\"";
				$where = implode(" OR ", $row);
				$query = mysql_query("SELECT * FROM x$hash WHERE $where");
				
				while ($row = mysql_fetch_assoc($query))
				{
					$compact = mysql_escape_string(pack('Nn', ip2long($row["ip"]), $row["port"]));
						$peerid = mysql_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . '7:peer id20:' . hex2bin($row["peer_id"]) . "4:porti{$row["port"]}e");
					$no_peerid = mysql_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . "4:porti{$row["port"]}e");
					mysql_query("INSERT INTO y$hash SET sequence=\"{$row["sequence"]}\", compact=\"$compact\", with_peerid=\"$peerid\", without_peerid=\"$no_peerid\"");
				}
			}	
			else
				echo "Added: none";
		
			$result = mysql_query("SELECT y$hash.sequence FROM y$hash LEFT JOIN x$hash ON y$hash.sequence=x$hash.sequence WHERE x$hash.sequence IS NULL");
			if (mysql_num_rows($result) > 0)
			{
				echo ", Deleted: ",mysql_num_rows($result);
		
				$row = array();
				
				while ($data = mysql_fetch_row($result))
					$row[] = "sequence=\"${data[0]}\"";
				$where = implode(" OR ", $row);
				$query = mysql_query("DELETE FROM y$hash WHERE $where");
			}
			else
				echo "<br>Deleted: none";
		}
		else
			echo "N/A";
		echo "</td>";
		
		echo "</tr>\n";
		$i ++;
	
	//	Disabled because it's kinda not that important.
	//	quickQuery("OPTIMIZE TABLE x$hash");
	
		if ($locking)
			quickQuery("UNLOCK TABLES");
	
		// Finally, it's time to do stuff to the summary table.
		if (!empty($summaryupdate))
		{
			$stuff = "";
			foreach ($summaryupdate as $column => $value)
			{
				$stuff .= ', '.$column. ($value[1] ? "=" : "=$column+") . $value[0];
			}
			mysql_query("UPDATE BTPHP_summary SET ".substr($stuff, 1)." WHERE info_hash=\"$hash\"");
			$summaryupdate = array();
		}
	}
}

function myTrashCollector($hash, $timeout, $now, $writeout)
{
 	
 	$peers = loadLostPeers($hash, $timeout);
 	for ($i=0; $i < $peers["size"]; $i++)
	        killPeer($peers[$i]["peer_id"], $hash, $peers[$i]["bytes"], $peers[$i]);
	if ($i != 0)
		echo "<td>Removed $i</td>";
	else
		echo "<td>Removed 0</td>";
 	quickQuery("UPDATE BTPHP_summary SET lastcycle='$now' WHERE info_hash='$hash'");
}





function delTorrent() {
	GLOBAL $dbhost, $dbuser, $dbpass, $database;
	GLOBAL $_POST, $_FILES;
	require_once("config.php");
	require_once("funcsv2.php");
	
	$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("<p class=\"error\">Couldn't connect to database. contact the administrator</p>");
	mysql_select_db($database) or die("Error selecting database.");
	print " <form enctype=\"multipart/form-data\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?nav=delete\"> ";
	foreach ($_POST as $left => $right)
	{
		if (strlen($left) == 41 && $left[0] == 'x')
		{
			if (!stristr($right,'y') || !verifyHash(substr($left, 1)))
				continue;
			$hash = substr($left, 1);
			@mysql_query("DELETE FROM BTPHP_summary WHERE info_hash=\"$hash\"");
			@mysql_query("DELETE FROM BTPHP_namemap WHERE info_hash=\"$hash\""); 
			@mysql_query("DELETE FROM BTPHP_timestamps WHERE info_hash=\"$hash\"");
			@mysql_query("DROP TABLE y$hash");
			@mysql_query("DROP TABLE x$hash");
		}
	}
	
	?>
	<h1>Torrents</h1>
	<table class="torrentlist" cellspacing="5" cellpadding=0 border=0>
	<tr>
		<th>Name/Hash</th>
		<th>Seeds</th>
		<th>Leeches</th>
		<th>Completed</th>
		<th>Bytes Transfered</th>
		<th>Delete?</th>
	</tr>
	<tr>
		<td style="background-color: #ffffff" colspan="6"></td>
	</tr>
	<?php
	
	$results = mysql_query("SELECT BTPHP_summary.info_hash, BTPHP_summary.seeds, BTPHP_summary.leechers, format(BTPHP_summary.finished,0), format(BTPHP_summary.dlbytes/1073741824,3),BTPHP_namemap.filename FROM BTPHP_summary LEFT JOIN BTPHP_namemap ON BTPHP_summary.info_hash = BTPHP_namemap.info_hash ORDER BY BTPHP_namemap.filename")
	or die(mysql_error());
	
	$i = 0;
	
	while ($data = mysql_fetch_row($results)) {
		$writeout = "row" . $i % 2;
		$hash = $data[0];
		if (is_null($data[5]))
			$data[5] = $data[0];
		if (strlen($data[5]) == 0)
			$data[5] = $data[0];
			
		echo "<tr class=\"$writeout\">\n";
		echo "\t<td>".$data[5]."</td>\n";
		for ($j=1; $j < 4; $j++)
			echo "\t<td align=center>$data[$j]</td>\n";
		echo "\t<td align=center>$data[4] GB</td>\n";
		
		echo "\t<td align=center><input type=\"checkbox\" name=\"x$hash\" value=\"y\" /></td>\n";
		echo "</tr>\n";
		$i++;
	}
	
	?>
	</table>
	<p class="error">Warning: there is <u>no confirmation</u> when deleting torrents.<br>
	.torrent files will not be deleted from server directory, only removed from the tracker.<br>
	Clicking this button is final.</p>
	<p class="center"><input type="submit" value="Delete" /></p>
	</form> 
<? // End Function
}

function doCrash($msg)
{
	echo "</table></table><p class=\"error\">Script error: $msg</p></body></html>";
	exit(1);
}

function clean($input)
{
	if (get_magic_quotes_gpc())
		return stripslashes($input);
	return $input;
} 

function addTorrent() {
	GLOBAL $dbhost, $dbuser, $dbpass, $database;
	GLOBAL $_POST, $_FILES;
	require_once ("funcsv2.php");
	require_once ("BDecode.php");
	require_once ("BEncode.php");
	
		$hash = strtolower($_POST["hash"]);
	
		$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("<p class=\"error\">Couldn't connect to database. contact the administrator</p>");
		mysql_select_db($database) or die("<p class=\"error\">Can't open the database.</p>");
	
	
		if (isset($_FILES["torrent"]))
		{
		   if ($_FILES["torrent"]["error"] != 4)	
		   {
				$fd = fopen($_FILES["torrent"]["tmp_name"], "rb") or die("<p class=\"error\">File upload error 1</p>\n");
				is_uploaded_file($_FILES["torrent"]["tmp_name"]) or die("<p class=\"error\">File upload error 2</p>\n");
				$alltorrent = fread($fd, filesize($_FILES["torrent"]["tmp_name"]));
				$array = BDecode($alltorrent);
				if (!$array)
				{
					echo "<p class=\"error\">There was an error handling your uploaded torrent. The parser didn't like it.</p>";
					endOutput();
					exit;
				}
				$hash = @sha1(BEncode($array["info"]));
				fclose($fd);
				unlink($_FILES["torrent"]["tmp_name"]);
		   }
		}
	
		if (isset($_POST["filename"]))
			$filename= clean($_POST["filename"]);
		else
			$filename = "";
		
		if (isset($_POST["url"]))
			$url = clean($_POST["url"]);
		else
			$url = "";
	
		if (isset($_POST["info"]))
			$info = clean($_POST["info"]);
		else
			$info = "";
	
		if (isset($_POST["autoset"])) {
		if (strcmp($_POST["autoset"], "enabled") == 0)
		{
			if (strlen($filename) == 0 && isset($array["info"]["name"]))
				$filename = $array["info"]["name"];
			if (strlen($info) == 0 && isset($array["info"]["piece length"]))
			{
				$info = $array["info"]["piece length"] / 1024 * (strlen($array["info"]["pieces"]) / 20) /1024;
				$info = round($info, 2) . " MB";
				if (isset($array["comment"]))
					$info .= " - ".$array["comment"];
			}
		}
		
		$filename = mysql_escape_string($filename);
		$url = mysql_escape_string($url);
		$info = mysql_escape_string($info);
	
		if ((strlen($hash) != 40) || !verifyHash($hash))
		{
			echo("<p class=\"error\">Error: Info hash must be exactly 40 hex bytes.</p>");
			endOutput();
		}
	
		$query = "INSERT INTO BTPHP_namemap (info_hash, filename, url, info) VALUES (\"$hash\", \"$filename\", \"$url\", \"$info\")";
		$status = makeTorrent($hash, true);
		quickQuery($query);
		if ($status)
			echo "<p class=\"error\">Torrent was added successfully.</p>";
		else
			echo "<p class=\"error\">There were some errors. Check if this torrent had been added previously.</p>";
	
	}
	endOutput();
}
	
function endOutput() {
	// Switch out of PHP mode. Much easier to output a large wad of HTML.
	?>
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?nav=add">
	<table>
	<tr>
		<td class="right">Torrent file:</td>
		<td class="left"><?php
		if (function_exists("sha1"))
			echo "<input type=\"file\" name=\"torrent\" size=\"30\"/>";
		else
			echo '<i>File uploading not available - no SHA1 function.</i>';
		?></td>
	</tr>
	<?php if (function_exists("sha1")) 
		echo "<tr><td class=\"center\" colspan=\"2\"><input type=\"checkbox\" name=\"autoset\" value=\"enabled\" checked=\"checked\" /> Fill in fields below automatically using data from the torrent file.</td></tr>\n"; ?>
	<tr>
		<td class="right">Info Hash:</td>
		<td class="left"><input type="text" name="hash" size="40"/></td>
	</tr>
	<tr>
		<td class="right">File name (optional): </td>
		<td class="left"><input type="text" name="filename" size="50" maxlength="200"/></td>
	</tr>
	<tr>
		<td class="right">Torrent's URL (optional): </td>
		<td class="left"><input type="text" name="url" size="50" maxlength="200"/></td>
	</tr>
	<tr>
		<td class="right">Short description(optional): </td>
		<td class="left"><input type="text" name="info" size="50" maxlength="200"/></td>
	</tr>
	<tr>
		<td class="right"><input type="submit" value="Create"></td>
		<td class="left"><input type="reset" value="Clear Settings"/></td>
	</tr>
	</table>
	</form>
	</div>
	</body></html>
	<?php 	
	exit;
}


function myStats() {
	GLOBAL $dbhost, $dbuser, $dbpass, $database;
	$scriptname = $_SERVER["PHP_SELF"];
	if (!isset($GLOBALS["countbytes"]))
		$GLOBALS["countbytes"] = true;
	?>
	<table>
	<tr>
		<?php 
		if (!isset($_GET["activeonly"])) 
			echo "<td><a href=\"$scriptname?activeonly=yes\">Show only active torrents</a></td>\n";
		else echo "<td><a href=\"$scriptname\">Show all torrents</a></td>\n";
		if (!isset($_GET["seededonly"])) 
			echo "<td style=\"text-align: right;\"><a href=\"$scriptname?seededonly=yes\">Show only seeded torrents</a></td>\n";
		else echo "<td style=\"text-align: right;\"><a href=\"$scriptname\">Show all torrents</a></td>\n";
		?>
	</tr>
	<tr>
		<td colspan="2">
		<table class="torrentlist">
	
		<!-- Column Headers -->
		<tr>
			<th>Name/Info Hash</th><th>Seeds</th><th>Leeches</th><th>Completed D/Ls</th>
			<?php
			// Bytes mode off? Ignore the columns
			if ($GLOBALS["countbytes"])
				echo '<th>Bytes Transferred</th><th>Speed</th>';
			?>
		</tr>
		
	<?php  
		$db = mysql_connect($dbhost, $dbuser, $dbpass) or doCrash("Tracker error: can't connect to database - ".mysql_error());
	mysql_select_db($database) or doCrash("Tracker error: can't open database $database - ".mysql_error());
	
	
	if (isset($_GET["seededonly"]))
		$where = " WHERE seeds > 0";
	else if (isset($_GET["activeonly"]))
		$where = " WHERE leechers+seeds > 0";
	else
		$where = " ";
	
	// Grab dummy column for dlbytes so we can skip doing format()
	if ($GLOBALS["countbytes"])
		$bytes = 'format(BTPHP_summary.dlbytes/1073741824,3)';
	else
		$bytes = '0';
	$query = "SELECT BTPHP_summary.info_hash, BTPHP_summary.seeds, BTPHP_summary.leechers, format(BTPHP_summary.finished,0), $bytes, BTPHP_namemap.filename, BTPHP_namemap.url, BTPHP_namemap.info, BTPHP_summary.speed FROM BTPHP_summary LEFT JOIN BTPHP_namemap ON BTPHP_summary.info_hash = BTPHP_namemap.info_hash $where ORDER BY BTPHP_namemap.filename";
	$results = mysql_query($query) or doCrash("Can't do SQL query - ".mysql_error());
	$i = 0;
	
	while ($data = mysql_fetch_row($results)) {
		// NULLs are such a pain at times. isset($nullvar) == false
		if (is_null($data[5]))
			$data[5] = $data[0];
		if (is_null($data[6]))
			$data[6] = "";
		if (is_null($data[7]))
			$data[7]="";
		if (strlen($data[5]) == 0)
			$data[5]=$data[0];
		$myhash = $data[0];
		$writeout = "row" . $i % 2;
		echo "<tr class=\"$writeout\">\n";
		echo "\t<td align=center>";
		if (strlen($data[6]) > 0)
			echo "<a href=\"${data[6]}\">${data[5]}</a>";
		else
			echo $data[5];
		if (strlen($data[7]) > 0)
			echo "<br/>(${data[7]})";
		echo "</td>\n";
		for ($j=1; $j < 4; $j++)
			echo "\t<td class=\"center\">$data[$j]</td>\n";
	
		if ($GLOBALS["countbytes"])
		{
			echo "\t<td align=center>$data[4] GB</td>\n";
	
			// The SPEED column calcultions.
			if ($data[8] <= 0)
				$speed = "Zero";
			else if ($data[8] > 2097152)
				$speed = round($data[8]/1048576,2) . " MB/sec";
			else
				$speed = round($data[8] / 1024, 2) . " KB/sec";
			echo "\t<td align=center>$speed</td>\n";
		}
		echo "</tr>\n";
		$i++;
	}
	
	if ($i == 0)
		echo "<tr class=\"row0\"><td style=\"text-align: center;\" colspan=\"6\">No data</td></tr>";
	?>
		</table></td></tr>
	</table>
 
 <?
// End Function 
};
?>
