<?php
/*
Plugin Name: CSS3 Web Pricing Tables Grids
Plugin URI: http://codecanyon.net/item/css3-web-pricing-tables-grids-for-wordpress/629172?ref=QuanticaLabs
Description: CSS3 Web Pricing Tables Grids plugin.
Author: QuanticaLabs
Author URI: http://codecanyon.net/user/QuanticaLabs/portfolio?ref=QuanticaLabs
Version: 6.5
*/

//settings link
function css3_grid_settings_link($links) 
{ 
  $settings_link = '<a href="options-general.php?page=css3_grid_admin" title="Settings">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links;
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'css3_grid_settings_link' );

//admin
if(is_admin())
{
	function css3_grid_admin_init()
	{
		wp_register_script('css3_grid_admin', get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/js/css3_grid_admin.js', array(), "1.0");
		wp_register_style('css3_grid_font_yanone', 'http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz');
		wp_register_style('css3_grid_style_admin', get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/admin/style.css');
		wp_register_style('css3_grid_table1_style', get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/table1/css3_grid_style.css');
		wp_register_style('css3_grid_table2_style', get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/table2/css3_grid_style.css');
	}
	add_action('admin_init', 'css3_grid_admin_init');

	function css3_grid_admin_print_scripts()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('css3_grid_admin');
		//pass data to javascript
		$data = array(
			'imgUrl' =>  get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/',
			'siteUrl' => get_site_url(),
			'selectedShortcodeId' => $_POST["shortcodeId"]
		);
		wp_localize_script('css3_grid_admin', 'config', $data);
		wp_enqueue_style('css3_grid_font_yanone');
		wp_enqueue_style('css3_grid_style_admin');
		wp_enqueue_style('css3_grid_table1_style');
		wp_enqueue_style('css3_grid_table2_style');
	}
	
	function css3_grid_admin_menu()
	{	
		$page = add_options_page('CSS3 Web Pricing Tables Grids', 'CSS3 Web Pricing Tables Grids', 'administrator', 'css3_grid_admin', 'css3_grid_admin_page');
		add_action('admin_print_scripts-' . $page, 'css3_grid_admin_print_scripts');
	}
	add_action('admin_menu', 'css3_grid_admin_menu');
	
	function css3_grid_stripslashes_deep($value)
	{
		$value = is_array($value) ?
					array_map('stripslashes_deep', $value) :
					stripslashes($value);

		return $value;
	}
	function css3_grid_ajax_get_settings()
	{
		echo json_encode(css3_grid_stripslashes_deep(get_option('css3_grid_shortcode_settings_' . $_POST["id"])));
		exit();
	}
	add_action('wp_ajax_css3_grid_get_settings', 'css3_grid_ajax_get_settings');
	
	function css3_grid_ajax_delete()
	{
		echo delete_option($_POST["id"]);
		exit();
	}
	add_action('wp_ajax_css3_grid_delete', 'css3_grid_ajax_delete');
	
	function css3_grid_ajax_preview()
	{
		$widths = "";
		for($i=0; $i<count($_POST["widths"]); $i++)
		{
			$widths .= (int)$_POST["widths"][$i];
			if($i+1<count($_POST["widths"]));
				$widths .= "|";
		}
		$aligments = "";
		for($i=0; $i<count($_POST["aligments"]); $i++)
		{
			$aligments .= $_POST["aligments"][$i];
			if($i+1<count($_POST["aligments"]));
				$aligments .= "|";
		}
		$actives = "";
		for($i=0; $i<count($_POST["actives"]); $i++)
		{
			$actives .= (int)$_POST["actives"][$i];
			if($i+1<count($_POST["actives"]));
				$actives .= "|";
		}
		$hiddens = "";
		for($i=0; $i<count($_POST["hiddens"]); $i++)
		{
			$hiddens .= (int)$_POST["hiddens"][$i];
			if($i+1<count($_POST["hiddens"]));
				$hiddens .= "|";
		}
		$ribbons = "";
		for($i=0; $i<count($_POST["ribbons"]); $i++)
		{
			$ribbons .= $_POST["ribbons"][$i];
			if($i+1<count($_POST["ribbons"]));
				$ribbons .= "|";
		}
		$heights = "";
		for($i=0; $i<count($_POST["heights"]); $i++)
		{
			$heights .= (int)$_POST["heights"][$i];
			if($i+1<count($_POST["heights"]));
				$heights .= "|";
		}
		$paddingsTop = "";
		for($i=0; $i<count($_POST["paddingsTop"]); $i++)
		{
			$paddingsTop .= (int)$_POST["paddingsTop"][$i];
			if($i+1<count($_POST["paddingsTop"]));
				$paddingsTop .= "|";
		}
		$paddingsBottom = "";
		for($i=0; $i<count($_POST["paddingsBottom"]); $i++)
		{
			$paddingsBottom .= (int)$_POST["paddingsBottom"][$i];
			if($i+1<count($_POST["paddingsBottom"]));
				$paddingsBottom .= "|";
		}
		$texts = "";
		for($i=0; $i<count($_POST["texts"]); $i++)
		{
			$texts .= str_replace("]", "&#93;", str_replace("[", "&#91;", str_replace("'", "&#39;", $_POST["texts"][$i])));
			if($i+1<count($_POST["texts"]));
				$texts .= "|";
		}
		$tooltips = "";
		for($i=0; $i<count($_POST["tooltips"]); $i++)
		{
			$tooltips .= str_replace("]", "&#93;", str_replace("[", "&#91;", str_replace("'", "&#39;", $_POST["tooltips"][$i])));
			if($i+1<count($_POST["tooltips"]));
				$tooltips .= "|";
		}
		echo do_shortcode("[css3_grid_print kind='" . (int)$_POST["kind"] . "' style='" . (int)$_POST["styleForTable" . (int)$_POST["kind"]] . "' hoverType='" . $_POST["hoverTypeForTable" . (int)$_POST["kind"]] . "' columns='" . (int)$_POST["columns"] . "' rows='" . (int)$_POST["rows"] . "' texts='" . $texts . "' tooltips='" . $tooltips . "' widths='" . $widths . "' aligments='" . $aligments . "' actives='" . $actives . "' hiddens='" . $hiddens . "' ribbons='" . $ribbons . "' heights='" . $heights . "' paddingstop='" . $paddingsTop . "' paddingsbottom='" . $paddingsBottom . "']");
		exit();
	}
	add_action('wp_ajax_css3_grid_preview', 'css3_grid_ajax_preview');
	
	function css3_grid_admin_page()
	{
		$error = "";
		if($_POST["action"]=="save_css3_grid")
		{
			if($_POST["shortcodeId"]!="")
			{
				$css3_grid_options = array(
					'columns' => $_POST['columns'],
					'rows' => $_POST['rows'],
					'kind' => $_POST['kind'],
					'styleForTable1' => $_POST["styleForTable1"],
					'styleForTable2' => $_POST["styleForTable2"],
					'hoverTypeForTable1' => $_POST["hoverTypeForTable1"],
					'hoverTypeForTable2' => $_POST["hoverTypeForTable2"],
					'widths' => $_POST['widths'],
					'aligments' => $_POST['aligments'],
					'actives' => $_POST['actives'],
					'hiddens' => $_POST['hiddens'],
					'ribbons' => $_POST['ribbons'],
					'heights' => $_POST['heights'],
					'paddingsTop' => $_POST['paddingsTop'],
					'paddingsBottom' => $_POST['paddingsBottom'],
					'texts' => $_POST['texts'],
					'tooltips' => $_POST['tooltips']
				);
				//add if not exist or update if exist
				$updated = true;
				if(!get_option('css3_grid_shortcode_settings_' . $_POST["shortcodeId"]))
					$updated = false;
				/*echo "<pre>";
				var_export($css3_grid_options);
				echo "</pre>";*/
				update_option('css3_grid_shortcode_settings_' . $_POST["shortcodeId"], $css3_grid_options);
				$message .= "Settings saved!" . ($updated ? " (overwritten)" : "");
				$message .= "<br />Please use <pre>[css3_grid id='" . $_POST["shortcodeId"] . "']</pre> shortcode to put css3 grid table on your page.";
			}
			else
			{
				$error .= "Please fill 'Shortcode id' field!";
			}
		}
		$css3GridAllShortcodeIds = array();
		/*if(function_exists('is_multisite') && is_multisite()) 
		{
			global $blog_id;
			global $wpdb;
			$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
			$query = "SELECT meta_key, meta_value FROM {$wpdb->sitemeta} WHERE site_id='" . $blog_id . "' AND meta_key LIKE '%css3_grid_shortcode_settings%'";
			$allOptions = $wpdb->get_results($query, ARRAY_A);
			foreach($allOptions as $key => $value)
			{
				if(substr($value["meta_key"], 0, 28)=="css3_grid_shortcode_settings")
					$css3GridAllShortcodeIds[] = $value["meta_key"];
			}
		}
		else
		{*/
			$allOptions = get_alloptions();
			foreach($allOptions as $key => $value)
			{
				if(substr($key, 0, 28)=="css3_grid_shortcode_settings")
					$css3GridAllShortcodeIds[] = $key;
			}
		//}
		//sort shortcode ids
		sort($css3GridAllShortcodeIds);
		?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2>CSS3 Web Pricing Tables Grids settings</h2>
		</div>
		<?php
		if($error!="" || $message!="")
		{
		?>
		<div class="<?php echo ($message!="" ? "updated" : "error"); ?> settings-error"> 
			<p>
				<strong style="line-height: 150%;">
					<?php echo ($message!="" ? $message : $error); ?>
				</strong>
			</p>
		</div>
		<?php
		}
		$shortcodesSelect = "<br />
			<select name='inset'>
				<option value='-1'>choose shortcode...</option>
				<optgroup label='Table 1'>
					<option value='caption'>caption</option>
					<option value='header_title'>header title</option>
					<option value='price'>price</option>
					<option value='button'>button</option>
					<option value='button_orange'>button orange</option>
					<option value='button_yellow'>button yellow</option>
					<option value='button_lightgreen'>button lightgreen</option>
					<option value='button_green'>button green</option>
				</optgroup>
				<optgroup label='Table 2'>
					<option value='caption2'>caption</option>
					<option value='header_title2'>header title</option>
					<option value='price2'>price</option>
					<option value='button1'>button style 1</option>
					<option value='button2'>button style 2</option>
					<option value='button3'>button style 3</option>
					<option value='button4'>button style 4</option>
				</optgroup>
				<optgroup label='Yes icons'>";
		for($i=0; $i<21; $i++)
			$shortcodesSelect .= "<option value='tick_" . ($i<9 ? "0" : "") . ($i+1) . "'>style " . ($i+1) . "</option>";
		$shortcodesSelect .= "</optgroup>
				<optgroup label='No icons'>";
		for($i=0; $i<21; $i++)
			$shortcodesSelect .= "<option value='cross_" . ($i<9 ? "0" : "") . ($i+1) . "'>style " . ($i+1) . "</option>";
		$shortcodesSelect .= "</optgroup>
			</select>
			<span class='css3_grid_tooltip css3_grid_admin_info'>
				<span>
					<div class='css3_grid_tooltip_column'>
						<strong>Yes icons</strong>";
						for($i=0; $i<11; $i++)
							$shortcodesSelect .= "<img src='" . get_template_directory_uri() . "/plugins/css3_web_pricing_tables_grids/img/tick_" . ($i<9 ? "0" : "") . ($i+1) . ".png' /><label>&nbsp;style " . ($i+1) . "</label><br />";
		$shortcodesSelect .= "
					</div>
					<div class='css3_grid_tooltip_column'>
						<strong>Yes icons</strong>";
						for($i=11; $i<21; $i++)
							$shortcodesSelect .= "<img src='" . get_template_directory_uri() . "/plugins/css3_web_pricing_tables_grids/img/tick_" . ($i+1) . ".png' /><label>&nbsp;style " . ($i+1) . "</label><br />";
		$shortcodesSelect .= "
					</div>
					<div class='css3_grid_tooltip_column'>
						<strong>No icons</strong>";
					for($i=0; $i<11; $i++)
							$shortcodesSelect .= "<img src='" . get_template_directory_uri() . "/plugins/css3_web_pricing_tables_grids/img/cross_" . ($i<9 ? "0" : "") . ($i+1) . ".png' /><label>&nbsp;style " . ($i+1) . "</label><br />";
		$shortcodesSelect .= "
					</div>
					<div class='css3_grid_tooltip_column'>
						<strong>No icons</strong>";
					for($i=11; $i<21; $i++)
							$shortcodesSelect .= "<img src='" . get_template_directory_uri() . "/plugins/css3_web_pricing_tables_grids/img/cross_" . ($i+1) . ".png' /><label>&nbsp;style " . ($i+1) . "</label><br />";
		$shortcodesSelect .= "
					</div>
				</span>
			</span>
			<br />
			<label>tooltip: </label><input class='css3_grid_tooltip_input' type='text' name='tooltips[]' value='' />";
		?>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="css3_grid_settings">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="editShortcodeId">Choose shortcode id</label>
						</th>
						<td>
							<select name="editShortcodeId" id="editShortcodeId">
								<option value="-1">choose...</option>
								<?php
									for($i=0; $i<count($css3GridAllShortcodeIds); $i++)
										echo "<option value='$css3GridAllShortcodeIds[$i]'>" . substr($css3GridAllShortcodeIds[$i], 29) . "</option>";
								?>
							</select>
							<img style="display: none; cursor: pointer;" id="deleteButton" src="<?php echo WP_PLUGIN_URL; ?>/css3_web_pricing_tables_grids/img/delete.png" alt="del" title="Delete this pricing table" />
							<span id="ajax_loader" style="display: none;"><img style="margin-bottom: -3px;" src="<?php echo WP_PLUGIN_URL; ?>/css3_web_pricing_tables_grids/img/ajax-loader.gif" /></span>
							<span class="description">Choose the shortcode id for editing</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="shortcodeId">Or type new shortcode id *</label>
						</th>
						<td>
							<input type="text" class="regular-text" value="" id="shortcodeId" name="shortcodeId">
							<span class="description">Unique identifier for css3_grid shortcode. Don't use special characters.</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="kind">Type</label>
						</th>
						<td>
							<select name="kind" id="kind">
								<option value="1">Table 1</option>
								<option value="2">Table 2</option>
							</select>
							<span class="description">One of two available kinds of table.</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="style">Style</label>
						</th>
						<td>
							<select name="styleForTable1" id="styleForTable1">
								<option value="1">Style 1</option>
								<option value="2">Style 2</option>
								<option value="3">Style 3</option>
								<option value="4">Style 4</option>
								<option value="5">Style 5</option>
								<option value="6">Style 6</option>
								<option value="7">Style 7</option>
								<option value="8">Style 8</option>
								<option value="9">Style 9</option>
								<option value="10">Style 10</option>
								<option value="11">Style 11</option>
								<option value="12">Style 12</option>
							</select>
							<select name="styleForTable2" id="styleForTable2" style="display: none;">
								<option value="1">Style 1</option>
								<option value="2">Style 2</option>
								<option value="3">Style 3</option>
								<option value="4">Style 4</option>
								<option value="5">Style 5</option>
								<option value="6">Style 6</option>
								<option value="7">Style 7</option>
								<option value="8">Style 8</option>
							</select>
							<span class="description">Specifies the style version of the table.</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="hoverType">Hover type</label>
						</th>
						<td>
							<select name="hoverTypeForTable1" id="hoverTypeForTable1">
								<option value="active">Active</option>
								<option value="light">Light</option>
								<option value="disabled">Disabled</option>
							</select>
							<select name="hoverTypeForTable2" id="hoverTypeForTable2" style="display: none;">
								<option value="active">Active</option>
								<option value="disabled">Disabled</option>
							</select>
							<span class="description">Specifies the hover effect for the columns.</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="columns">Columns</label>
						</th>
						<td>
							<input style="float: left;" type="text" class="regular-text" value="3" id="columns" name="columns" maxlength="2">
							<a href="#" class="css3_grid_less" title="less"></a>
							<a href="#" class="css3_grid_more" title="more"></a>
							<span style="float: left;margin-top: 2px;" class="description">Number of columns.</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="rows">Rows</label>
						</th>
						<td>
							<input style="float: left;" type="text" class="regular-text" value="9" id="rows" name="rows" maxlength="2">
							<a href="#" class="css3_grid_less" title="less"></a>
							<a href="#" class="css3_grid_more" title="more"></a>
							<span style="float: left;margin-top: 2px;" class="description">Number of rows.</span>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="textsTable">
				<table class="widefat css3_grid_widefat">
				<thead>
					<tr>
						<th class="css3_grid_admin_column1">
							<div class="css3_grid_column1_text">
								Rows configuration
							</div>
						</th>
						<th class="css3_grid_admin_column2">
							<div class="css3_grid_sort_column css3_clearfix">
								<div class="css3_grid_arrows">
									<a href="#" class="css3_grid_sort_left" title="left"></a>
									<a href="#" class="css3_grid_sort_right" title="right"></a>
								</div>
							</div>
							Column 1
							<br />
							<label>width (optional in px): </label><input type="text" name="widths[]" value="" />
							<br />
							<label>aligment (optional): </label>
							<select name="aligments[]">
								<option value="-1">choose...</option>
								<option value="left">left</option>
								<option value="center">center</option>
								<option value="right">right</option>
							</select>
							<br />
							<label>active (optional): </label>
							<select name="actives[]">
								<option value="-1">no</option>
								<option value="1">yes</option>
							</select>
							<br />
							<label>disable/hidden (optional): </label>
							<select name="hiddens[]">
								<option value="-1">no</option>
								<option value="1">yes</option>
							</select>
							<br />
							<label>ribbon (optional): </label>
							<select name="ribbons[]">
								<option value="-1">choose...</option>
								<optgroup label="Style 1">
									<option value="style1_best">best</option>
									<option value="style1_buy">buy</option>
									<option value="style1_free">free</option>
									<option value="style1_free_caps">free (uppercase)</option>
									<option value="style1_fresh">fresh</option>
									<option value="style1_gift_caps">gift (uppercase)</option>
									<option value="style1_heart">heart</option>
									<option value="style1_hot">hot</option>
									<option value="style1_hot_caps">hot (uppercase)</option>
									<option value="style1_new">new</option>
									<option value="style1_new_caps">new (uppercase)</option>
									<option value="style1_no1">no. 1</option>
									<option value="style1_off5">5% off</option>
									<option value="style1_off10">10% off</option>
									<option value="style1_off15">15% off</option>
									<option value="style1_off20">20% off</option>
									<option value="style1_off25">25% off</option>
									<option value="style1_off30">30% off</option>
									<option value="style1_off35">35% off</option>
									<option value="style1_off40">40% off</option>
									<option value="style1_off50">50% off</option>
									<option value="style1_off75">75% off</option>
									<option value="style1_pack">pack</option>
									<option value="style1_pro">pro</option>
									<option value="style1_sale">sale</option>
									<option value="style1_save">save</option>
									<option value="style1_save_caps">save (uppercase)</option>
									<option value="style1_top">top</option>
									<option value="style1_top_caps">top (uppercase)</option>
									<option value="style1_trial">trial</option>
								</optgroup>
								<optgroup label="Style 2">
									<option value="style2_best">best</option>
									<option value="style2_buy">buy</option>
									<option value="style2_free">free</option>
									<option value="style2_free_caps">free (uppercase)</option>
									<option value="style2_fresh">fresh</option>
									<option value="style2_gift_caps">gift (uppercase)</option>
									<option value="style2_heart">heart</option>
									<option value="style2_hot">hot</option>
									<option value="style2_hot_caps">hot (uppercase)</option>
									<option value="style2_new">new</option>
									<option value="style2_new_caps">new (uppercase)</option>
									<option value="style2_no1">no. 1</option>
									<option value="style2_off5">5% off</option>
									<option value="style2_off10">10% off</option>
									<option value="style2_off15">15% off</option>
									<option value="style2_off20">20% off</option>
									<option value="style2_off25">25% off</option>
									<option value="style2_off30">30% off</option>
									<option value="style2_off35">35% off</option>
									<option value="style2_off40">40% off</option>
									<option value="style2_off50">50% off</option>
									<option value="style2_off75">75% off</option>
									<option value="style2_pack">pack</option>
									<option value="style2_pro">pro</option>
									<option value="style2_sale">sale</option>
									<option value="style2_save">save</option>
									<option value="style2_save_caps">save (uppercase)</option>
									<option value="style2_top">top</option>
									<option value="style2_top_caps">top (uppercase)</option>
									<option value="style2_trial">trial</option>
								</optgroup>
							</select>
						</th>
						<th class="css3_grid_admin_column3">
							<div class="css3_grid_sort_column css3_clearfix">
								<div class="css3_grid_arrows">
									<a href="#" class="css3_grid_sort_left" title="left"></a>
									<a href="#" class="css3_grid_sort_right" title="right"></a>
								</div>
							</div>
							Column 2
							<br />
							<label>width (optional in px): </label><input type="text" name="widths[]" value="" />
							<br />
							<label>aligment (optional): </label>
							<select name="aligments[]">
								<option value="-1">choose...</option>
								<option value="left">left</option>
								<option value="center">center</option>
								<option value="right">right</option>
							</select>
							<br />
							<label>active (optional): </label>
							<select name="actives[]">
								<option value="-1">no</option>
								<option value="1">yes</option>
							</select>
							<br />
							<label>disable/hidden (optional): </label>
							<select name="hiddens[]">
								<option value="-1">no</option>
								<option value="1">yes</option>
							</select>
							<br />
							<label>ribbon (optional): </label>
							<select name="ribbons[]">
								<option value="-1">choose...</option>
								<optgroup label="Style 1">
									<option value="style1_best">best</option>
									<option value="style1_buy">buy</option>
									<option value="style1_free">free</option>
									<option value="style1_free_caps">free (uppercase)</option>
									<option value="style1_fresh">fresh</option>
									<option value="style1_gift_caps">gift (uppercase)</option>
									<option value="style1_heart">heart</option>
									<option value="style1_hot">hot</option>
									<option value="style1_hot_caps">hot (uppercase)</option>
									<option value="style1_new">new</option>
									<option value="style1_new_caps">new (uppercase)</option>
									<option value="style1_no1">no. 1</option>
									<option value="style1_off5">5% off</option>
									<option value="style1_off10">10% off</option>
									<option value="style1_off15">15% off</option>
									<option value="style1_off20">20% off</option>
									<option value="style1_off25">25% off</option>
									<option value="style1_off30">30% off</option>
									<option value="style1_off35">35% off</option>
									<option value="style1_off40">40% off</option>
									<option value="style1_off50">50% off</option>
									<option value="style1_off75">75% off</option>
									<option value="style1_pack">pack</option>
									<option value="style1_pro">pro</option>
									<option value="style1_sale">sale</option>
									<option value="style1_save">save</option>
									<option value="style1_save_caps">save (uppercase)</option>
									<option value="style1_top">top</option>
									<option value="style1_top_caps">top (uppercase)</option>
									<option value="style1_trial">trial</option>
								</optgroup>
								<optgroup label="Style 2">
									<option value="style2_best">best</option>
									<option value="style2_buy">buy</option>
									<option value="style2_free">free</option>
									<option value="style2_free_caps">free (uppercase)</option>
									<option value="style2_fresh">fresh</option>
									<option value="style2_gift_caps">gift (uppercase)</option>
									<option value="style2_heart">heart</option>
									<option value="style2_hot">hot</option>
									<option value="style2_hot_caps">hot (uppercase)</option>
									<option value="style2_new">new</option>
									<option value="style2_new_caps">new (uppercase)</option>
									<option value="style2_no1">no. 1</option>
									<option value="style2_off5">5% off</option>
									<option value="style2_off10">10% off</option>
									<option value="style2_off15">15% off</option>
									<option value="style2_off20">20% off</option>
									<option value="style2_off25">25% off</option>
									<option value="style2_off30">30% off</option>
									<option value="style2_off35">35% off</option>
									<option value="style2_off40">40% off</option>
									<option value="style2_off50">50% off</option>
									<option value="style2_off75">75% off</option>
									<option value="style2_pack">pack</option>
									<option value="style2_pro">pro</option>
									<option value="style2_sale">sale</option>
									<option value="style2_save">save</option>
									<option value="style2_save_caps">save (uppercase)</option>
									<option value="style2_top">top</option>
									<option value="style2_top_caps">top (uppercase)</option>
									<option value="style2_trial">trial</option>
								</optgroup>
							</select>
						</th>
						<th class="css3_grid_admin_column4">
							<div class="css3_grid_sort_column css3_clearfix">
								<div class="css3_grid_arrows">
									<a href="#" class="css3_grid_sort_left" title="left"></a>
									<a href="#" class="css3_grid_sort_right" title="right"></a>
								</div>
							</div>
							Column 3
							<br />
							<label>width (optional in px): </label><input type="text" name="widths[]" value="" />
							<br />
							<label>aligment (optional): </label>
							<select name="aligments[]">
								<option value="-1">choose...</option>
								<option value="left">left</option>
								<option value="center">center</option>
								<option value="right">right</option>
							</select>
							<br />
							<label>active (optional): </label>
							<select name="actives[]">
								<option value="-1">no</option>
								<option value="1">yes</option>
							</select>
							<br />
							<label>disable/hidden (optional): </label>
							<select name="hiddens[]">
								<option value="-1">no</option>
								<option value="1">yes</option>
							</select>
							<br />
							<label>ribbon (optional): </label>
							<select name="ribbons[]">
								<option value="-1">choose...</option>
								<optgroup label="Style 1">
									<option value="style1_best">best</option>
									<option value="style1_buy">buy</option>
									<option value="style1_free">free</option>
									<option value="style1_free_caps">free (uppercase)</option>
									<option value="style1_fresh">fresh</option>
									<option value="style1_gift_caps">gift (uppercase)</option>
									<option value="style1_heart">heart</option>
									<option value="style1_hot">hot</option>
									<option value="style1_hot_caps">hot (uppercase)</option>
									<option value="style1_new">new</option>
									<option value="style1_new_caps">new (uppercase)</option>
									<option value="style1_no1">no. 1</option>
									<option value="style1_off5">5% off</option>
									<option value="style1_off10">10% off</option>
									<option value="style1_off15">15% off</option>
									<option value="style1_off20">20% off</option>
									<option value="style1_off25">25% off</option>
									<option value="style1_off30">30% off</option>
									<option value="style1_off35">35% off</option>
									<option value="style1_off40">40% off</option>
									<option value="style1_off50">50% off</option>
									<option value="style1_off75">75% off</option>
									<option value="style1_pack">pack</option>
									<option value="style1_pro">pro</option>
									<option value="style1_sale">sale</option>
									<option value="style1_save">save</option>
									<option value="style1_save_caps">save (uppercase)</option>
									<option value="style1_top">top</option>
									<option value="style1_top_caps">top (uppercase)</option>
									<option value="style1_trial">trial</option>
								</optgroup>
								<optgroup label="Style 2">
									<option value="style2_best">best</option>
									<option value="style2_buy">buy</option>
									<option value="style2_free">free</option>
									<option value="style2_free_caps">free (uppercase)</option>
									<option value="style2_fresh">fresh</option>
									<option value="style2_gift_caps">gift (uppercase)</option>
									<option value="style2_heart">heart</option>
									<option value="style2_hot">hot</option>
									<option value="style2_hot_caps">hot (uppercase)</option>
									<option value="style2_new">new</option>
									<option value="style2_new_caps">new (uppercase)</option>
									<option value="style2_no1">no. 1</option>
									<option value="style2_off5">5% off</option>
									<option value="style2_off10">10% off</option>
									<option value="style2_off15">15% off</option>
									<option value="style2_off20">20% off</option>
									<option value="style2_off25">25% off</option>
									<option value="style2_off30">30% off</option>
									<option value="style2_off35">35% off</option>
									<option value="style2_off40">40% off</option>
									<option value="style2_off50">50% off</option>
									<option value="style2_off75">75% off</option>
									<option value="style2_pack">pack</option>
									<option value="style2_pro">pro</option>
									<option value="style2_sale">sale</option>
									<option value="style2_save">save</option>
									<option value="style2_save_caps">save (uppercase)</option>
									<option value="style2_top">top</option>
									<option value="style2_top_caps">top (uppercase)</option>
									<option value="style2_trial">trial</option>
								</optgroup>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
				<tr class="css3_grid_admin_row1">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="&lt;h2 class='col1'&gt;starter&lt;/h2&gt;" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="&lt;h2 class='col2'&gt;econo&lt;/h2&gt;" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row2">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="&lt;h2 class='caption'&gt;choose &lt;span&gt;your&lt;/span&gt; plan&lt;/h2&gt;" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="&lt;h1 class='col1'&gt;$&lt;span&gt;10&lt;/span&gt;&lt;/h1&gt;&lt;h3 class='col1'&gt;per month&lt;/h3&gt;" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="&lt;h1 class='col1'&gt;$&lt;span&gt;30&lt;/span&gt;&lt;/h1&gt;&lt;h3 class='col1'&gt;per month&lt;/h3&gt;" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row3">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="Amount of space" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="10GB" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="30GB" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row4">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="Bandwidth per month" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="100GB" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="200GB" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row5">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="No. of e-mail accounts" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="1" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="10" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row6">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="No. of MySql databases" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="1" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="10" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row7">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="24h support" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="Yes" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="Yes" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row8">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="Support tickets per mo." />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="1" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="3" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				<tr class="css3_grid_admin_row9">
					<td class="css3_grid_admin_column1">
						<div class="css3_grid_arrows_row">
							<a href="#" class="css3_grid_sort_up" title="up"></a>
							<a href="#" class="css3_grid_sort_down" title="down"></a>
						</div>
						<div class="css3_grid_row_config">
							<input class="css3_grid_short" type="text" name="heights[]" value="" /><label>height (optional in px)</label>
							<br />
							<input class="css3_grid_short" type="text" name="paddingsTop[]" value="" /><label>padding top (optional in px)</label>
							<input class="css3_grid_short" type="text" name="paddingsBottom[]" value="" /><label>padding bottom (optional in px)</label>
						</div>
					</td>
					<td class="css3_grid_admin_column2">
						<input type="text" name="texts[]" value="" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column3">
						<input type="text" name="texts[]" value="&lt;a href='<?php echo get_site_url(); ?>?plan=1' class='sign_up radius3'&gt;sign up!&lt;/a&gt;" />
						<?php echo $shortcodesSelect;?>
					</td>
					<td class="css3_grid_admin_column4">
						<input type="text" name="texts[]" value="&lt;a href='<?php echo get_site_url(); ?>?plan=2' class='sign_up radius3'&gt;sign up!&lt;/a&gt;" />
						<?php echo $shortcodesSelect;?>
					</td>
				</tr>
				</tbody>
				</table>
			</div>
			<p>
				<input type="button" id="preview" value="Preview" class="button-primary" name="Preview">
				<input type="submit" value="Save Options" class="button-primary" name="Submit">
			</p>
			<div id="previewContainer">
			<?php
			echo do_shortcode("[css3_grid_print]");
			?>
			</div>
			<p>
				<input type="hidden" name="action" value="save_css3_grid" />
				<input type="submit" value="Save Options" class="button-primary" name="Submit">
			</p>
		</form>
		<?php
	}
}

//activate plugin
function css3_grid_activate()
{
	$table_t1_s1 = array('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '1','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_best',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_01.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_01.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_01.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_01.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '10 accounts under one domain',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => 'test',  37 => '',  38 => '',  39 => 'Hight priority support!',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s1", $table_t1_s1);
	$table_t1_s2 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '2','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style2_heart',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_02.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_02.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => 'Your tooltip text!',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => 'You can have unlimited bandwidth for $10 surcharge!',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s2", $table_t1_s2);
	$table_t1_s3 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '3','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style1_off30',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_03.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_03.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_03.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_03.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => 'Support only in standard and professional plans!',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s3", $table_t1_s3);
	$table_t1_s4 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '4','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_04.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_04.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>'),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => 'Cool price!',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s4", $table_t1_s4);
	$table_t1_s5 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '5','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '170',  1 => '125',  2 => '150',  3 => '180',  4 => '210',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_new',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '55',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '40',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_05.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_05.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_05.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_05.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>'),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s5", $table_t1_s5);
	$table_t1_s6 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '6','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_06.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_06.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s6", $table_t1_s6);
	$table_t1_s7 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '7','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => 'style1_top_caps',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s7", $table_t1_s7);
	$table_t1_s8 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '8','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => 'style2_no1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_08.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_08.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_08.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_08.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s8", $table_t1_s8);
	$table_t1_s9 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '9','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => 'style1_hot_caps',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_11.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_11.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_11.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_11.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s9", $table_t1_s9);
	$table_t1_s10 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '10','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_fresh',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_06.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s10", $table_t1_s10);
	$table_t1_s11 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '11','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_save_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_02.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_04.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s11", $table_t1_s11);
	$table_t1_s12 = array ('columns' => '5','rows' => '9','kind' => '1','styleForTable1' => '12','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '1',  3 => '-1',  4 => '1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_off25',  3 => 'style1_off30',  4 => 'style1_off40',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',),'texts' => array (  0 => '',  1 => '<h2 class="col1">starter</h2>',  2 => '<h2 class="col2">econo</h2>',  3 => '<h2 class="col1">standard</h2>',  4 => '<h2 class="col1">professional</h2>',  5 => '<h2 class="caption">choose <span>your</span> plan</h2>',  6 => '<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>',  7 => '<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>',  8 => '<h1 class="col1">$<span>59</span></h1><h3 class="col1">per month</h3>',  9 => '<h1 class="col1">$<span>99</span></h1><h3 class="col1">per month</h3>',  10 => 'Amount of space',  11 => '10GB',  12 => '30GB',  13 => '100GB',  14 => 'Unlimited',  15 => 'Bandwidth per month',  16 => '100GB',  17 => '200GB',  18 => '500GB',  19 => '1000GB',  20 => 'No. of e-mail accounts',  21 => '1',  22 => '10',  23 => '50',  24 => 'Unlimited',  25 => 'No. of MySql databases',  26 => '1',  27 => '10',  28 => '50',  29 => 'Unlimited',  30 => '24h support',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_07.png" alt="no">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_07.png" alt="yes">',  35 => 'Support tickets per mo.',  36 => '1',  37 => '3',  38 => '5',  39 => '10',  40 => '',  41 => '<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>',  42 => '<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',  43 => '<a href="' . get_site_url() . '?plan=3" class="sign_up radius3">sign up!</a>',  44 => '<a href="' . get_site_url() . '?plan=4" class="sign_up radius3">sign up!</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => ''));
	update_option("css3_grid_shortcode_settings_Table_t1_s12", $table_t1_s12);
	$table_t2_s1 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '1','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style1_gift_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => 'Every additional database cost $3!',  27 => 'Every additional database cost $2!',  28 => 'Every additional database cost $1!',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s1", $table_t2_s1);
	$table_t2_s2 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '2','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style2_sale',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes"> 2 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_12.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_12.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s2", $table_t2_s2);
	$table_t2_s3 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '3','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style2_pack',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_18.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_18.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s3", $table_t2_s3);
	$table_t2_s4 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '4','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s4", $table_t2_s4);
	$table_t2_s5 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '5','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_new_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s5", $table_t2_s5);
	$table_t2_s6 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '6','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => 'style2_new_caps',  3 => '-1',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '35',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '20',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 2 domains',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes"> 3 domains',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_19.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_19.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s6", $table_t2_s6);
	$table_t2_s7 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '7','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => 'style1_pro',  4 => '-1',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_16.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_16.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => '',  12 => '',  13 => '',  14 => '',  15 => '',  16 => '',  17 => '',  18 => 'Sample tooltip text!',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => 'Your tooltip text!',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s7", $table_t2_s7);
	$table_t2_s8 = array ('columns' => '5','rows' => '11','kind' => '2','styleForTable1' => '1','styleForTable2' => '8','hoverTypeForTable1' => 'active','hoverTypeForTable2' => 'active','widths' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',),'aligments' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'actives' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '1',),'hiddens' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => '-1',),'ribbons' => array (  0 => '-1',  1 => '-1',  2 => '-1',  3 => '-1',  4 => 'style2_heart',),'heights' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsTop' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'paddingsBottom' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',),'texts' => array (  0 => '',  1 => '<h2>basic</h2>',  2 => '<h2>standard</h2>',  3 => '<h2>super</h2>',  4 => '<h2>ultimate</h2>',  5 => '<h1 class="caption">Hosting <span>Plans</span></h1>',  6 => '<h1>$3.95</h1><h3>per month</h3>',  7 => '<h1>$5.95</h1><h3>per month</h3>',  8 => '<h1>$7.95</h1><h3>per month</h3>',  9 => '<h1>$9.95</h1><h3>per month</h3>',  10 => 'Data Storage',  11 => '2GB Disk Space',  12 => '10GB Disk Space',  13 => '50GB Disk Space',  14 => 'Unlimited',  15 => 'Monthly Traffic',  16 => '10GB Bandwidth',  17 => '50GB Bandwidth',  18 => '100GB Bandwidth',  19 => 'Unlimited',  20 => 'Email Accounts',  21 => '5 Accounts',  22 => '10 Accounts',  23 => 'Unlimited',  24 => 'Unlimited',  25 => 'MySQL Databases',  26 => '2 Databases',  27 => '10 Databases',  28 => '20 Databases',  29 => 'Unlimited',  30 => 'Daily Backup',  31 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  32 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  33 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  34 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  35 => 'Free Domain',  36 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  37 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  38 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  39 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  40 => 'Website Statistics',  41 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  42 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  43 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  44 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  45 => 'Online Support',  46 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  47 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  48 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/cross_09.png" alt="no">',  49 => '<img src="' . get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/img/tick_09.png" alt="yes">',  50 => '',  51 => '<a class="button_1 radius5" href="' . get_site_url() . '?plan=1">sign up</a>',  52 => '<a class="button_2 radius5" href="' . get_site_url() . '?plan=2">sign up</a>',  53 => '<a class="button_3 radius5" href="' . get_site_url() . '?plan=3">sign up</a>',  54 => '<a class="button_4 radius5" href="' . get_site_url() . '?plan=4">sign up</a>',),'tooltips' => array (  0 => '',  1 => '',  2 => '',  3 => '',  4 => '',  5 => '',  6 => '',  7 => '',  8 => '',  9 => '',  10 => '',  11 => 'Every additonal 1GB of space cost $2!',  12 => 'Every additonal 1GB of space cost $2!',  13 => 'Every additonal 1GB of space cost $2!',  14 => '',  15 => '',  16 => '',  17 => '',  18 => '',  19 => '',  20 => '',  21 => '',  22 => '',  23 => '',  24 => '',  25 => '',  26 => '',  27 => '',  28 => '',  29 => '',  30 => '',  31 => '',  32 => '',  33 => '',  34 => '',  35 => '',  36 => '',  37 => '',  38 => '',  39 => '',  40 => '',  41 => '',  42 => '',  43 => '',  44 => '',  45 => '',  46 => '',  47 => '',  48 => '',  49 => '',  50 => '',  51 => '',  52 => '',  53 => '',  54 => ''));
	update_option("css3_grid_shortcode_settings_Table_t2_s8", $table_t2_s8);
}
register_activation_hook( __FILE__, 'css3_grid_activate');

function css3_grid_shortcode($atts)
{
	extract(shortcode_atts(array(
		'id' => ''
	), $atts));
	if($id!="")
	{
		if($shortcode_settings = get_option('css3_grid_shortcode_settings_' . $id))
		{
			$widths = "";
			for($i=0; $i<count($shortcode_settings["widths"]); $i++)
			{
				$widths .= (int)$shortcode_settings["widths"][$i];
				if($i+1<count($shortcode_settings["widths"]));
					$widths .= "|";
			}
			$aligments = "";
			for($i=0; $i<count($shortcode_settings["aligments"]); $i++)
			{
				$aligments .= $shortcode_settings["aligments"][$i];
				if($i+1<count($shortcode_settings["aligments"]));
					$aligments .= "|";
			}
			$actives = "";
			for($i=0; $i<count($shortcode_settings["actives"]); $i++)
			{
				$actives .= (int)$shortcode_settings["actives"][$i];
				if($i+1<count($shortcode_settings["actives"]));
					$actives .= "|";
			}
			$hiddens = "";
			for($i=0; $i<count($shortcode_settings["hiddens"]); $i++)
			{
				$hiddens .= (int)$shortcode_settings["hiddens"][$i];
				if($i+1<count($shortcode_settings["hiddens"]));
					$hiddens .= "|";
			}
			$ribbons = "";
			for($i=0; $i<count($shortcode_settings["ribbons"]); $i++)
			{
				$ribbons .= $shortcode_settings["ribbons"][$i];
				if($i+1<count($shortcode_settings["ribbons"]));
					$ribbons .= "|";
			}
			$heights = "";
			for($i=0; $i<count($shortcode_settings["heights"]); $i++)
			{
				$heights .= (int)$shortcode_settings["heights"][$i];
				if($i+1<count($shortcode_settings["heights"]));
					$heights .= "|";
			}
			$paddingsTop = "";
			for($i=0; $i<count($shortcode_settings["paddingsTop"]); $i++)
			{
				$paddingsTop .= (int)$shortcode_settings["paddingsTop"][$i];
				if($i+1<count($shortcode_settings["paddingsTop"]));
					$paddingsTop .= "|";
			}
			$paddingsBottom = "";
			for($i=0; $i<count($shortcode_settings["paddingsBottom"]); $i++)
			{
				$paddingsBottom .= (int)$shortcode_settings["paddingsBottom"][$i];
				if($i+1<count($shortcode_settings["paddingsBottom"]));
					$paddingsBottom .= "|";
			}
			$texts = "";
			for($i=0; $i<count($shortcode_settings["texts"]); $i++)
			{
				$texts .= str_replace("]", "&#93;", str_replace("[", "&#91;", str_replace("'", "&#39;", $shortcode_settings["texts"][$i])));
				if($i+1<count($shortcode_settings["texts"]));
					$texts .= "|";
			}
			$tooltips = "";
			for($i=0; $i<count($shortcode_settings["tooltips"]); $i++)
			{
				$tooltips .= str_replace("]", "&#93;", str_replace("[", "&#91;", str_replace("'", "&#39;", $shortcode_settings["tooltips"][$i])));
				if($i+1<count($shortcode_settings["tooltips"]));
					$tooltips .= "|";
			}
			$output = do_shortcode("[css3_grid_print kind='" . $shortcode_settings["kind"] . "' style='" . $shortcode_settings["styleForTable" . $shortcode_settings["kind"]] . "' hoverType='" . $shortcode_settings["hoverTypeForTable" . $shortcode_settings["kind"]] . "' columns='" . $shortcode_settings["columns"] . "' rows='" . $shortcode_settings["rows"] . "' texts='" . $texts . "' tooltips='" . $tooltips . "' widths='" . $widths . "' aligments='" . $aligments . "' actives='" . $actives . "' hiddens='" . $hiddens . "' ribbons='" . $ribbons . "' heights='" . $heights . "' paddingstop='" . $paddingsTop . "' paddingsbottom='" . $paddingsBottom . "']");
		}
		else
			$output = "Shortcode with given id not found!";
	}
	else
		$output = "Parameter id not specified!";
	return $output;
}
add_shortcode('css3_grid', 'css3_grid_shortcode');

/*function css3_grid_enqueue_scripts()
{
	wp_register_style('css3_grid_font_yanone', 'http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz');
	wp_register_style('css3_grid_table1_main', plugins_url('table1/main.css', __FILE__));
	wp_register_style('css3_grid_table1_style1', plugins_url('table1/style_1.css', __FILE__));
	wp_register_style('css3_grid_table1_style2', plugins_url('table1/style_2.css', __FILE__));
	wp_register_style('css3_grid_table1_style3', plugins_url('table1/style_3.css', __FILE__));
	wp_register_style('css3_grid_table1_style4', plugins_url('table1/style_4.css', __FILE__));
	wp_register_style('css3_grid_table1_style5', plugins_url('table1/style_5.css', __FILE__));
	wp_register_style('css3_grid_table1_style6', plugins_url('table1/style_6.css', __FILE__));
	wp_register_style('css3_grid_table1_style7', plugins_url('table1/style_7.css', __FILE__));
	wp_register_style('css3_grid_table1_style8', plugins_url('table1/style_8.css', __FILE__));
	wp_register_style('css3_grid_table1_style9', plugins_url('table1/style_9.css', __FILE__));
	wp_register_style('css3_grid_table1_style10', plugins_url('table1/style_10.css', __FILE__));
	wp_register_style('css3_grid_table1_style11', plugins_url('table1/style_11.css', __FILE__));
	wp_register_style('css3_grid_table1_style12', plugins_url('table1/style_12.css', __FILE__));
	wp_register_style('css3_grid_table2_main', plugins_url('table2/main.css', __FILE__));
	wp_register_style('css3_grid_table2_style1', plugins_url('table2/style_1.css', __FILE__));
	wp_register_style('css3_grid_table2_style2', plugins_url('table2/style_2.css', __FILE__));
	wp_register_style('css3_grid_table2_style3', plugins_url('table2/style_3.css', __FILE__));
	wp_register_style('css3_grid_table2_style4', plugins_url('table2/style_4.css', __FILE__));
	wp_register_style('css3_grid_table2_style5', plugins_url('table2/style_5.css', __FILE__));
	wp_register_style('css3_grid_table2_style6', plugins_url('table2/style_6.css', __FILE__));
	wp_register_style('css3_grid_table2_style7', plugins_url('table2/style_7.css', __FILE__));
	wp_register_style('css3_grid_table2_style8', plugins_url('table2/style_8.css', __FILE__));
	wp_print_styles(array(
			'css3_grid_font_yanone',
			'css3_grid_table1_main',
			'css3_grid_table1_style1',
			'css3_grid_table1_style2',
			'css3_grid_table1_style3',
			'css3_grid_table1_style4',
			'css3_grid_table1_style5',
			'css3_grid_table1_style6',
			'css3_grid_table1_style7',
			'css3_grid_table1_style8',
			'css3_grid_table1_style9',
			'css3_grid_table1_style10',
			'css3_grid_table1_style11',
			'css3_grid_table1_style12',
			'css3_grid_table2_main',
			'css3_grid_table2_style1',
			'css3_grid_table2_style2',
			'css3_grid_table2_style3',
			'css3_grid_table2_style4',
			'css3_grid_table2_style5',
			'css3_grid_table2_style6',
			'css3_grid_table2_style7',
			'css3_grid_table2_style8'
		));
}
add_action('wp_enqueue_scripts', 'css3_grid_enqueue_scripts');*/

function css3_grid_enqueue_scripts()
{
	wp_enqueue_style('css3_grid_font_yanone', 'http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz');
	/*
	wp_enqueue_style('css3_grid_table1_main', plugins_url('table1/main.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style1', plugins_url('table1/style_1.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style2', plugins_url('table1/style_2.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style3', plugins_url('table1/style_3.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style4', plugins_url('table1/style_4.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style5', plugins_url('table1/style_5.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style6', plugins_url('table1/style_6.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style7', plugins_url('table1/style_7.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style8', plugins_url('table1/style_8.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style9', plugins_url('table1/style_9.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style10', plugins_url('table1/style_10.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style11', plugins_url('table1/style_11.css', __FILE__));
	wp_enqueue_style('css3_grid_table1_style12', plugins_url('table1/style_12.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_main', plugins_url('table2/main.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style1', plugins_url('table2/style_1.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style2', plugins_url('table2/style_2.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style3', plugins_url('table2/style_3.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style4', plugins_url('table2/style_4.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style5', plugins_url('table2/style_5.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style6', plugins_url('table2/style_6.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style7', plugins_url('table2/style_7.css', __FILE__));
	wp_enqueue_style('css3_grid_table2_style8', plugins_url('table2/style_8.css', __FILE__));*/
	wp_enqueue_style('css3_grid_table1_style',get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/table1/css3_grid_style.css');
	wp_enqueue_style('css3_grid_table2_style', get_template_directory_uri() . '/plugins/css3_web_pricing_tables_grids/table2/css3_grid_style.css');
}
add_action('wp_enqueue_scripts', 'css3_grid_enqueue_scripts');

function css3_grid_print_shortcode($atts)
{
	extract(shortcode_atts(array(
		'columns' => '3',
		'rows' => '9',
		'kind' => '1',
		'style' => '1',
		'hovertype' => 'active',
		'widths' => '|||',
		'aligments' => '-1|-1|-1|',
		'actives' => '-1|-1|-1|',
		'hiddens' => '-1|-1|-1|',
		'ribbons' => '-1|-1|-1|',
		'heights' => '|||||||||',
		'paddingstop' => '|||||||||',
		'paddingsbottom' => '|||||||||',
		'texts' => '|<h2 class="col1">starter</h2>|<h2 class="col2">econo</h2>|<h2 class="caption">choose <span>your</span> plan</h2>|<h1 class="col1">$<span>10</span></h1><h3 class="col1">per month</h3>|<h1 class="col1">$<span>30</span></h1><h3 class="col1">per month</h3>|Amount of space|10GB|30GB|Bandwidth per month|100GB|200GB|No. of e-mail accounts|1|10|No. of MySql databases|1|10|24h support|Yes|Yes|Support tickets per mo.|1|3||<a href="' . get_site_url() . '?plan=1" class="sign_up radius3">sign up!</a>|<a href="' . get_site_url() . '?plan=2" class="sign_up radius3">sign up!</a>',
		'tooltips' => '|||||||||'
	), $atts));
	$widths = explode("|", $widths);
	$aligments = explode("|", $aligments);
	$actives = explode("|", $actives);
	$hiddens = explode("|", $hiddens);
	$ribbons = explode("|", $ribbons);
	$heights = explode("|", $heights);
	$paddingsTop = explode("|", $paddingstop);
	$paddingsBottom = explode("|", $paddingsbottom);
	$texts = explode("|", $texts);
	for($i=0; $i<count($texts); $i++)
		$texts[$i] = str_replace("&#93;", "]", str_replace("&#91;", "[", str_replace("&#39;", "'", $texts[$i])));
	$tooltips = explode("|", $tooltips);
	for($i=0; $i<count($tooltips); $i++)
		$tooltips[$i] = str_replace("&#93;", "]", str_replace("&#91;", "[", str_replace("&#39;", "'", $tooltips[$i])));
	//$output = '<link rel="stylesheet" type="text/css" href="' . plugins_url('table' . $kind . '/main.css', __FILE__) . '"/>';
	//$output .= '<link rel="stylesheet" type="text/css" href="' . plugins_url('table' . $kind . '/style_' . $style . '.css', __FILE__) . '"/>';
	$output .= '<div class="p_table_' . $kind . ' p_table_' . $kind . '_' . $style . ' css3_grid_clearfix' . ($hovertype!="active" ? ' p_table_hover_' . $hovertype : '') . '">';
	$countValues = array_count_values($hiddens);
	$totalColumns = $countValues["-1"];
	$currentColumn = 0;
	for($i=0; $i<$columns; $i++)
	{
		if($hiddens[$i]!=1)
		{
			if($i==0)
				$output .= '<div class="caption_column' . ((int)$actives[0]==1 ? ' active_column' : '') . '"' . ((int)$widths[0]>0 ? ' style="width: ' . (int)$widths[0] . 'px;"' : '') . '>';
			else
				$output .= '<div class="column_' . ($i%4==0 ? 4 : $i%4) . ((int)$actives[$i]==1 ? ' active_column' : '') . '"' . ((int)$widths[$i]>0 ? ' style="width: ' . (int)$widths[$i] . 'px;"' : '') . '>';
			if((int)$ribbons[$i]!=-1)
				$output .= '<div class="column_ribbon ribbon_' . $ribbons[$i] . '"></div>';
			$output .= '<ul>';
			for($j=0; $j<$rows; $j++)
			{
				if($j<2)
				{
					if($j==0)
						$output .= '<li' . ((int)$aligments[$i]!=-1 || (int)$heights[$j]>0 || (int)$paddingsTop[$j]>0 || (int)$paddingsBottom[$j]>0 ? ' style="' . ((int)$aligments[$i]!=-1 ? 'text-align: ' . $aligments[$i] . ';' : '') . ((int)$heights[$j]>0 ? 'height: ' . $heights[$j] . 'px;' : '') . ((int)$paddingsTop[$j]>0 ? 'padding-top: ' . $paddingsTop[$j] . 'px !important;' : '') . ((int)$paddingsBottom[$j]>0 ? 'padding-bottom: ' . $paddingsBottom[$j] . 'px !important;' : '') . '"' : '') . ' class="header_row_1 align_center' . ($currentColumn==0 && (int)$kind==1 ? ' radius5_topleft' : (($currentColumn==0 && $hiddens[0]==1) || ($currentColumn==1 && $hiddens[0]==-1) && (int)$kind==2 ? ' radius5_topleft' : '')) . ($currentColumn+1==$totalColumns ? ' radius5_topright' : '') . '">' . do_shortcode(($tooltips[$j*$columns+$i]!="" ? '<span class="css3_grid_tooltip"><span>' . $tooltips[$j*$columns+$i] . '</span>' : '' ) . $texts[$j*$columns+$i] . ($tooltips[$j*$columns+$i]!="" ? '</span>' : '' )) . '</li>';
					else if($j==1)
					{
						if((int)$kind==2)
							$output .= '<li class="decor_line"></li>';
						$output .= '<li' . ((int)$aligments[$i]!=-1 || (int)$heights[$j]>0 || (int)$paddingsTop[$j]>0 || (int)$paddingsBottom[$j]>0 ? ' style="' . ((int)$aligments[$i]!=-1 ? 'text-align: ' . $aligments[$i] . ';' : '') . ((int)$heights[$j]>0 ? 'height: ' . $heights[$j] . 'px;' : '') . ((int)$paddingsTop[$j]>0 ? 'padding-top: ' . $paddingsTop[$j] . 'px !important;' : '') . ((int)$paddingsBottom[$j]>0 ? 'padding-bottom: ' . $paddingsBottom[$j] . 'px !important;' : '') . '"' : '') . ' class="header_row_2' . (($currentColumn==0 && $hiddens[0]==1) || ($currentColumn==1 && $hiddens[0]==-1) && (int)$kind==2 ? ' radius5_bottomleft' : '') . ($currentColumn+1==$totalColumns && (int)$kind==2 ? ' radius5_bottomright' : '') . ($i!=0 ? ' align_center':'') . '"><span class="css3_grid_vertical_align_table"><span class="css3_grid_vertical_align">' . do_shortcode(($tooltips[$j*$columns+$i]!="" ? '<span class="css3_grid_tooltip"><span>' . $tooltips[$j*$columns+$i] . '</span>' : '' ) . $texts[$j*$columns+$i] . ($tooltips[$j*$columns+$i]!="" ? '</span>' : '' )) .  '</span></span></li>';
					}
				}
				else if($j+1==$rows)
				{
					$output .= '<li' . ((int)$aligments[$i]!=-1 || (int)$heights[$j]>0 || (int)$paddingsTop[$j]>0 || (int)$paddingsBottom[$j]>0 ? ' style="' . ((int)$aligments[$i]!=-1 ? 'text-align: ' . $aligments[$i] . ';' : '') . ((int)$heights[$j]>0 ? 'height: ' . $heights[$j] . 'px;' : '') . ((int)$paddingsTop[$j]>0 ? 'padding-top: ' . $paddingsTop[$j] . 'px !important;' : '') . ((int)$paddingsBottom[$j]>0 ? 'padding-bottom: ' . $paddingsBottom[$j] . 'px !important;' : '') . '"' : '') . ' class="footer_row' . ($currentColumn+1==$totalColumns && (int)$kind==2 ? ' radius5_bottomright' : '') . '"><span class="css3_grid_vertical_align_table"><span class="css3_grid_vertical_align">' . do_shortcode(($tooltips[$j*$columns+$i]!="" ? '<span class="css3_grid_tooltip"><span>' . $tooltips[$j*$columns+$i] . '</span>' : '' ) . $texts[$j*$columns+$i] . ($tooltips[$j*$columns+$i]!="" ? '</span>' : '' )) .  '</span></span></li>';
				}
				else
				{
					$output .= '<li' . ((int)$aligments[$i]!=-1 || (int)$heights[$j]>0 || (int)$paddingsTop[$j]>0 || (int)$paddingsBottom[$j]>0 ? ' style="' . ((int)$aligments[$i]!=-1 ? 'text-align: ' . $aligments[$i] . ';' : '') . ((int)$heights[$j]>0 ? 'height: ' . $heights[$j] . 'px;' : '') . ((int)$paddingsTop[$j]>0 ? 'padding-top: ' . $paddingsTop[$j] . 'px !important;' : '') . ((int)$paddingsBottom[$j]>0 ? 'padding-bottom: ' . $paddingsBottom[$j] . 'px !important;' : '') . '"' : '') . ' class="row_style_' . ($i%2==0 && $j%2==0 ? ((int)$kind==1 ? '4' : '1') : ($i%2==0 && $j%2==1 ? ((int)$kind==1 ? '2' : '3'): ($i%2==1 && $j%2==0 ? ((int)$kind==1 ? '3' : '1') : ((int)$kind==1 ? '1' : '2')))) . ($i>0 ? ' align_center' : '' ) . '"><span class="css3_grid_vertical_align_table"><span class="css3_grid_vertical_align"><span>' . do_shortcode(($tooltips[$j*$columns+$i]!="" ? '<span class="css3_grid_tooltip"><span>' . $tooltips[$j*$columns+$i] . '</span>' : '' ) . $texts[$j*$columns+$i] . ($tooltips[$j*$columns+$i]!="" ? '</span>' : '' )) .  '</span></span></span></li>';
				}
			}
			$output .= '</ul></div>';
			$currentColumn++;
		}
	}
	$output .= "</div>";
	return $output;
}
add_shortcode('css3_grid_print', 'css3_grid_print_shortcode');
?>