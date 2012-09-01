<?php
	global $wpdb;
	$table_name = $wpdb->prefix . "glamour_slider"; 
	
	include (TEMPLATEPATH . '/includes/get_database.php');
	
	if($_POST["saving"]){
		foreach($_POST["id"] as $id){
		
			$slider = $_POST["slider".$id];
			$url = $_POST["url".$id];
			$bgcolor = $_POST["bgcolor".$id];
			$target = $_POST["target".$id];
			
			$insert = "UPDATE ". $table_name ." SET 
			src = '".$slider."', 
			url = '".$url."',
			target = '".$target."'
			WHERE id = $id";
			
			$wpdb->query( $insert );
		}
	}
	
	if($_POST["adding"]){
			$id = $_POST["id"];
			$slider = $_POST["slider".$id];
			
			if($slider){
				$url = $_POST["url".$id];
				$bgcolor = $_POST["bgcolor".$id];
				$target = $_POST["target".$id];
				
				$lastDB = $_POST["last_id"]+1;
				if(!$lastDB) { $lastDB = 1; }

				$insert = "INSERT INTO ". $table_name ." SET 
				src = '".$slider."', 
				url = '".$url."',
				target = '".$target."',
				orderby = '".$lastDB."'";
				$wpdb->query( $insert );
			}
	}
	
	if($_GET["deleting"]){
		$id = $_GET["deleting"];
		$insert = "DELETE FROM ". $table_name ." WHERE id = '".$id."'";
		$wpdb->query( $insert );
	}
	
	if($_GET['mode']=="sortby" && $_GET['ID']!="" && $_GET['CID']!="" && $_GET['SORTBY']!="" && $_GET['CSORTBY']!=""){
		$sql_exec="UPDATE $table_name SET orderby = ".$_GET['CSORTBY']." WHERE ID=".$_GET['ID'];
		$wpdb->query( $sql_exec );
				
		$sql_exec="UPDATE $table_name SET orderby = ".$_GET['SORTBY']." WHERE ID=".$_GET['CID'];
		$wpdb->query( $sql_exec );
	}
	
	$array_count=0;
	$array_record=0;
	$clumns = $wpdb->get_results("SELECT * FROM $table_name ORDER BY orderby");
	foreach ($clumns as $clumn) {
		$rowid = $clumn->id;
		$roworder = $clumn->orderby;
		$array_id[$array_count]=$rowid;
		$array_sortby[$array_count]=$roworder;
		$array_count++;
		$array_record++;
	}
	$array_count=0;
	
		$slider_datas = $wpdb->get_results("SELECT * FROM $table_name ORDER BY orderby");

	foreach ($slider_datas as $data) { $lastID = $data->id+1; }
	if(!$lastID){ $lastID = 1; }
?>

<script language="JavaScript" type="text/javascript">
<!--
function checkform ( form )
{

  if (form.slider<?php echo $lastID;?>.value == "") {
    alert( "Please enter slider preview picture!" );
    form.slider<?php echo $lastID;?>.focus();
    return false ;
  }
  return true ;
}
//-->
</script>

<style>
	.widefat td{
		vertical-align:middle;
		padding-top:20px;
		padding-bottom:20px;
	}
</style>

<div class="wrap nosubsub">
	<div id="icon-link-manager" class="icon32"><br /></div><h2>The Glamour Slider</h2><br  />
	
	<form action="" method="POST">

	<?php if($slider_datas){ ?>
	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" name="saving" value="Save All Changes" class="button-secondary" />
		</div>
	</div>
	<?php } ?>

	<div class="clear"></div>

	<table class="widefat" cellspacing="0">
	
		<?php if($slider_datas){ ?>
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name" style="">Preview Picture</th>
				<th scope="col" id="url" class="manage-column column-url" style="">Link</th>
				<th scope="col"  class="manage-column column-visible" style="">Target</th>
				<th scope="col" id="visible" class="manage-column column-visible" style="">Order</th>
				<th scope="col"  class="manage-column column-rating" style=""></th>
			</tr>
			</thead>
			<?php } ?>
			
			<tfoot>
			<tr>
				<th scope="col"  class="manage-column column-name" style=""></th>
				<th scope="col"  class="manage-column column-url" style=""></th>
				<th scope="col"  class="manage-column column-visible" style=""></th>
				<th scope="col"  class="manage-column column-visible" style=""></th>
				<th scope="col"  class="manage-column column-visible" style=""></th>
			</tr>
		</tfoot>
		<tbody>
	
			<?php foreach ($slider_datas as $data) { ?>
			<tr class="alternate">
				<td class="column-name">
					<input type="text" name="slider<?php echo $data->id;?>" id="slider<?php echo $data->id;?>" class="upload_input" tabindex="1" value="<?php echo $data->src;?>"  style="width:68%;"/>
					<input type="hidden" name="id[]" value="<?php echo $data->id; ?>">
					<a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="slider<?php echo $data->id;?>" class="set_input thickbox button-primary" title='Add an Image' onclick="return false;">Upload</a>
				</td>
				<td class="manage-column">
					<input type="text" name="url<?php echo $data->id;?>" tabindex="1" value="<?php echo $data->url;?>"  style="width:100%;"/>
				</td>
				<td class="column-visible">
					<input name="target<?php echo $data->id;?>" size="8" maxlength="10" value="<?php echo $data->target;?>">  
				</td>
				<td class="column-rating">
					<?php if($array_count!=0) { 
					echo "<a href='admin.php?page=glamour_slider&mode=sortby&ID=".$array_id[$array_count]."&CID=".$array_id[$array_count-1]."&SORTBY=".$array_sortby[$array_count]."&CSORTBY=".$array_sortby[$array_count-1]."'>"; ?>
					<img src="<?php echo bloginfo('template_url')."/images/admin/arrow_up.gif"; ?>" alt="" /></a>
					<?php } ?>
					
					<?php if($array_count!=$array_record-1) { 
					echo "<a href='admin.php?page=glamour_slider&mode=sortby&ID=".$array_id[$array_count]."&CID=".$array_id[$array_count+1]."&SORTBY=".$array_sortby[$array_count]."&CSORTBY=".$array_sortby[$array_count+1]."'>"; ?>
					<img src="<?php echo bloginfo('template_url')."/images/admin/arrow_down.gif"; ?>" alt="" /></a><?php } ?>		</td>
				<td class="column-rating">
					<a href="?page=glamour_slider&deleting=<?php echo $data->id;?>"><img src="<?php echo bloginfo('template_url')."/images/admin/remove.gif"; ?>" alt="" /></a>
				</td>
			</tr>
			<?php $array_count++; } ?>
			
	</form>
		
			<form action="" method="POST" onsubmit="return checkform(this);">
			<tfoot>
				<tr>
					<th scope="col"  class="manage-column column-name" style="">Add New Slider Picture</th>
					<th scope="col"  class="manage-column column-url" style=""></th>
					<th scope="col"  class="manage-column column-visible" style=""></th>
					<th scope="col"  class="manage-column column-visible" style=""></th>
					<th scope="col"  class="manage-column column-visible" style=""></th>
				</tr>
			</tfoot>
		
			<tr>
				<td class="column-name">
					<input type="text" name="slider<?php echo $data->id+1;?>" id="slider<?php echo $data->id+1;?>" class="upload_input" tabindex="1" value=""  style="width:70%;"/>
					<input type="hidden" name="id" value="<?php echo $data->id+1; ?>">
					<input type="hidden" name="last_id" value="<?php echo $data->id; ?>">
					<a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true" id="slider<?php echo $data->id+1;?>" class="set_input thickbox button-primary" title='Add an Image' onclick="return false;">Upload</a>
					<br><font size="1" color="#ccc"><i>http://www.site.com/1.jpg</i></font>  
				</td>
				<td class="manage-column">
					<input type="text" name="url<?php echo $data->id+1;?>" tabindex="1" value=""  style="width:100%;"/>
					<br><font size="1" color="#ccc"><i>http://www.site.com/about</i></font>  
				</td>
				<td class="column-visible">
					<input name="target<?php echo $data->id+1;?>" size="8" maxlength="10" value=""><br><font size="1" color="#ccc"><i>_self</i></font>  
				</td>
				<td class="column-rating">
					<input type="submit" name="adding" value="Add New" class="button" />
				</td>
				<td class="column-rating">
				</td>
			</tr>
			</form>
	
		</tbody>
	</table>


	<?php if($slider_datas){ ?>
	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" name="saving" value="Save All Changes" class="button-secondary" />
		</div>
	</div>
	<?php } ?>
</div>