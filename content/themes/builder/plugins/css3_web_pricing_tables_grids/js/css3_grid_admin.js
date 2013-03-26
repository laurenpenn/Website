jQuery(document).ready(function($){
	$("#css3_grid_settings")[0].reset();
	$(".css3_grid_less, .css3_grid_more").click(function(event){
		event.preventDefault();
		if($(this).hasClass("css3_grid_less"))
			$(this).prev().val(parseInt($(this).prev().val())-1).trigger("change");
		else
			$(this).prev().prev().val(parseInt($(this).prev().prev().val())+1).trigger("change");
	});
	$("#kind").change(function(){
		if($(this).val()=="1")
		{
			$("#styleForTable2, #hoverTypeForTable2").css("display", "none");
			$("#styleForTable1, #hoverTypeForTable1").css("display", "inline");
		}
		else if($(this).val()=="2")
		{
			$("#styleForTable1, #hoverTypeForTable1").css("display", "none");
			$("#styleForTable2, #hoverTypeForTable2").css("display", "inline");
		}
	});
	$("[name='inset']").live("change", function(){
		var textField = $(this).prev().prev();
		if(parseInt($(this).val())==-1)
			textField.val("");
		else if($(this).val()=="caption")
			textField.val("<h2 class='caption'>choose <span>your</span> plan</h2>");
		else if($(this).val()=="header_title")
			textField.val("<h2 class='col1'>sample title</h2>");
		else if($(this).val()=="price")
			textField.val("<h1 class='col1'>$<span>10</span></h1><h3 class='col1'>per month</h3>");
		else if($(this).val()=="button")
			textField.val("<a href='" + config.siteUrl + "?plan=sample_param' class='sign_up radius3'>sign up!</a>");
		else if($(this).val()=="button_orange")
			textField.val("<a href='" + config.siteUrl + "?plan=sample_param' class='sign_up sign_up_orange radius3'>sign up!</a>");
		else if($(this).val()=="button_yellow")
			textField.val("<a href='" + config.siteUrl + "?plan=sample_param' class='sign_up sign_up_yellow radius3'>sign up!</a>");
		else if($(this).val()=="button_lightgreen")
			textField.val("<a href='" + config.siteUrl + "?plan=sample_param' class='sign_up sign_up_lightgreen radius3'>sign up!</a>");
		else if($(this).val()=="button_green")
			textField.val("<a href='" + config.siteUrl + "?plan=sample_param' class='sign_up sign_up_green radius3'>sign up!</a>");
		else if($(this).val()=="caption2")
			textField.val("<h1 class='caption'>Hosting <span>Plans</span></h1>");
		else if($(this).val()=="header_title2")
			textField.val("<h2>sample title</h2>");
		else if($(this).val()=="price2")
			textField.val("<h1>$3.95</h1><h3>per month</h3>");
		else if($(this).val()=="button1")
			textField.val("<a class='button_1 radius5' href='" + config.siteUrl + "?plan=sample_param'>sign up</a>");
		else if($(this).val()=="button2")
			textField.val("<a class='button_2 radius5' href='" + config.siteUrl + "?plan=sample_param'>sign up</a>");
		else if($(this).val()=="button3")
			textField.val("<a class='button_3 radius5' href='" + config.siteUrl + "?plan=sample_param'>sign up</a>");
		else if($(this).val()=="button4")
			textField.val("<a class='button_4 radius5' href='" + config.siteUrl + "?plan=sample_param'>sign up</a>");
		else if($(this).val().substr(0,4)=="tick" || $(this).val().substr(0,5)=="cross")
			textField.val("<img src='" + config.imgUrl + $(this).val() + ".png' alt='" + ($(this).val().substr(0,4)=="tick" ? "yes":"no") + "' />");
	});
	$("#editShortcodeId").change(function(){
		if($(this).val()!="-1")
		{
			var id = $("#editShortcodeId :selected").html();
			$("#shortcodeId").val(id).trigger("paste");
			$("#ajax_loader").css("display", "inline");
			$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'json',
					data: 'action=css3_grid_get_settings&id='+id,
					success: function(json){
						$("#columns").val(json.columns).trigger("change");
						$("#rows").val(json.rows).trigger("change");
						$.each(json, function(key, val){
							if(key!="columns" && key!="rows")
							{
								if(key=="widths")
								{
									$("[name='widths[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="aligments")
								{
									$("[name='aligments[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="actives")
								{
									$("[name='actives[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="hiddens")
								{
									$("[name='hiddens[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="ribbons")
								{
									$("[name='ribbons[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="heights")
								{
									$("[name='heights[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="paddingsTop")
								{
									$("[name='paddingsTop[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="paddingsBottom")
								{
									$("[name='paddingsBottom[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="texts")
								{
									$("[name='texts[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else if(key=="tooltips")
								{
									$("[name='tooltips[]']").each(function(index){
										$(this).val(val[index]);
									});
								}
								else
									$("#" + key).val(val);
							}
						});
						$("#kind").trigger("change");
						$("#preview").trigger("click");
						$("#ajax_loader").css("display", "none");
						$("#deleteButton").css("display", "inline");
					}
			});
		}
		else
		{
			$("#css3_grid_settings")[0].reset();
			$("#deleteButton").css("display", "none");
		}
	});
	$("#deleteButton").click(function(){
		var id = $("#editShortcodeId").val();
		$("#deleteButton").css("display", "none");
		$("#ajax_loader").css("display", "inline");
		$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'json',
					data: 'action=css3_grid_delete&id='+id,
					success: function(data){
						if(parseInt(data)==1)
						{
							$("#editShortcodeId [value='" + id + "']").remove();
							$("#css3_grid_settings")[0].reset();
							$("#columns").trigger("change");
							$("#rows").trigger("change");
							$("#kind").trigger("change");
							$("#preview").trigger("click");
							$("#ajax_loader").css("display", "none");
						}
					}
		});
	});
	$("#preview").click(function(){
		var data = $("#css3_grid_settings").serializeArray();
		data.push({name: "action", value: "css3_grid_preview"});
		$.ajax({
					url: ajaxurl,
					type: 'post',
					data: data,
					success: function(data){
						$("#previewContainer").html(data);
					}
		});
	});
	$("#columns, #rows").bind("keyup change", function(event){
		var previousColumns = $("#textsTable thead tr th").length;
		var previousRows = $("#textsTable tbody tr").length;
		var columns = parseInt($("#columns").val())+1;
		var rows = parseInt($("#rows").val());
		var html = "";
		var shortcodesSelect = "";
		var i;
		shortcodesSelect += "<br />";
		shortcodesSelect += "	<select name='inset'>";
		shortcodesSelect += "		<option value='-1'>choose shortcode...</option>";
		shortcodesSelect += "		<optgroup label='Table 1'>";
		shortcodesSelect += "			<option value='caption'>caption</option>";
		shortcodesSelect += "			<option value='header_title'>header title</option>";
		shortcodesSelect += "			<option value='price'>price</option>";
		shortcodesSelect += "			<option value='button'>button</option>";
		shortcodesSelect += "			<option value='button_orange'>button orange</option>";
		shortcodesSelect += "			<option value='button_yellow'>button yellow</option>";
		shortcodesSelect += "			<option value='button_lightgreen'>button lightgreen</option>";
		shortcodesSelect += "			<option value='button_green'>button green</option>";
		shortcodesSelect += "		</optgroup>";
		shortcodesSelect += "		<optgroup label='Table 2'>";
		shortcodesSelect += "			<option value='caption2'>caption</option>";
		shortcodesSelect += "			<option value='header_title2'>header title</option>";
		shortcodesSelect += "			<option value='price2'>price</option>";
		shortcodesSelect += "			<option value='button1'>button style 1</option>";
		shortcodesSelect += "			<option value='button2'>button style 2</option>";
		shortcodesSelect += "			<option value='button3'>button style 3</option>";
		shortcodesSelect += "			<option value='button4'>button style 4</option>";
		shortcodesSelect += "		</optgroup>";
		shortcodesSelect += "		<optgroup label='Yes icons'>";
		for(i=0; i<21; i++)
			shortcodesSelect += "		<option value='tick_" + (i<9 ? "0" : "") + (i+1) + "'>style " + (i+1) + "</option>";
		shortcodesSelect += "		</optgroup>";
		shortcodesSelect += "		<optgroup label='No icons'>";
		for(i=0; i<21; i++)
			shortcodesSelect += "		<option value='cross_" + (i<9 ? "0" : "") + (i+1) + "'>style " + (i+1) + "</option>";
		shortcodesSelect += "		</optgroup>";
		shortcodesSelect += "	</select>";
		shortcodesSelect += "	<span class='css3_grid_tooltip css3_grid_admin_info'>";
		shortcodesSelect += "		<span>";
		shortcodesSelect += "		<div class='css3_grid_tooltip_column'>";
		shortcodesSelect += "			<strong>Yes icons</strong>";
		for(i=0; i<11; i++)
			shortcodesSelect += "		<img src='" + config.imgUrl + "tick_" + (i<9 ? "0" : "") + (i+1) + ".png' /><label>&nbsp;style " + (i+1) + "</label><br />";
		shortcodesSelect += "		</div>";
		shortcodesSelect += "		<div class='css3_grid_tooltip_column'>";
		shortcodesSelect += "			<strong>Yes icons</strong>";
		for(i=11; i<21; i++)
			shortcodesSelect += "		<img src='" + config.imgUrl + "tick_" + (i+1) + ".png' /><label>&nbsp;style " + (i+1) + "</label><br />";
		shortcodesSelect += "		</div>";
		shortcodesSelect += "		<div class='css3_grid_tooltip_column'>";
		shortcodesSelect += "			<strong>No icons</strong>";
		for(i=0; i<11; i++)
			shortcodesSelect += "		<img src='" + config.imgUrl + "cross_" + (i<9 ? "0" : "") + (i+1) + ".png' /><label>&nbsp;style " + (i+1) + "</label><br />";
		shortcodesSelect += "		</div>";
		shortcodesSelect += "		<div class='css3_grid_tooltip_column'>";
		shortcodesSelect += "			<strong>No icons</strong>";
		for(i=11; i<21; i++)
			shortcodesSelect += "		<img src='" + config.imgUrl + "cross_" + (i+1) + ".png' /><label>&nbsp;style " + (i+1) + "</label><br />";
		shortcodesSelect += "		</div>";
		shortcodesSelect += "	</span>";
		shortcodesSelect += "	</span>";
		shortcodesSelect += "	<br />";
		shortcodesSelect += "	<label>tooltip: </label><input class='css3_grid_tooltip_input' type='text' name='tooltips[]' value='' />";
		if(columns>0 && rows>0 && columns<200 && rows<200)
		{
			i=0;
			if($(event.target).attr("id")=="rows")
			{
				//rows
				for(i=rows; i<previousRows; i++)
					$("#textsTable tbody .css3_grid_admin_row"+(i+1)).remove();
				if(rows>previousRows)
				{
					var rowHtml = "";
					rowHtml += "<tr>";
					for(var j=0; j<columns; j++)
					{
						rowHtml += "<td class='css3_grid_admin_column"+(j+1)+"'>";
						if(j==0)
							rowHtml += "<div class='css3_grid_arrows_row'><a href='#' class='css3_grid_sort_up' title='up'></a><a href='#' class='css3_grid_sort_down' title='down'></a></div><div class='css3_grid_row_config'><input class='css3_grid_short' type='text' name='heights[]' value='' /><label>height (optional in px)</label><br /><input class='css3_grid_short' type='text' name='paddingsTop[]' value='' /><label>padding top (optional in px)</label><input class='css3_grid_short' type='text' name='paddingsBottom[]' value='' /><label>padding bottom (optional in px)</label></div>";
						else
							rowHtml += "<input type='text' name='texts[]' value='' />"+shortcodesSelect;
						html += "</td>";
					}
					rowHtml += "</tr>";
				}
				for(i=previousRows; i<rows; i++)
					$("#textsTable tbody").append($(rowHtml).addClass("css3_grid_admin_row"+(i+1)));
			}
			else
			{
				//columns
				for(i=columns; i<previousColumns; i++)
					$("#textsTable .css3_grid_admin_column"+(i+1)).remove();
				for(i=previousColumns; i<columns; i++)
				{
					if(i==0)
					{
						$("#textsTable thead tr").append("<th class='css3_grid_admin_column1'>Rows configuration</th>");
						$("#textsTable tbody tr").append("<td class='css3_grid_admin_column1'><label>height (optional in px)</label><input class='css3_grid_short' type='text' name='heights[]' value='' /><br /><label>padding top (optional in px)</label><input class='css3_grid_short' type='text' name='paddingsTop[]' value='' /><label>padding bottom (optional in px)</label><input class='css3_grid_short' type='text' name='paddingsBottom[]' value='' /></td>");
					}
					else
					{
						$("#textsTable thead tr").append("<th class='css3_grid_admin_column"+(i+1)+"'><div class='css3_grid_sort_column css3_clearfix'><div class='css3_grid_arrows'><a href='#' class='css3_grid_sort_left' title='left'></a><a href='#' class='css3_grid_sort_right' title='right'></a></div></div>Column "+i+"<br /><label>width (optional in px): </label><input type='text' name='widths[]' value='' /><br /><label>aligment (optional): </label><select name='aligments[]'><option value='-1'>choose...</option><option value='left'>left</option><option value='center'>center</option><option value='right'>right</option></select><br /><label>active (optional): </label><select name='actives[]'><option value='-1'>no</option><option value='1'>yes</option></select><br /><label>disable/hidden (optional): </label><select name='hiddens[]'><option value='-1'>no</option><option value='1'>yes</option></select><br /><label>ribbon (optional): </label><select name='ribbons[]'><option value='-1'>choose...</option><optgroup label='Style 1'><option value='style1_best'>best</option><option value='style1_buy'>buy</option><option value='style1_free'>free</option><option value='style1_free_caps'>free (uppercase)</option><option value='style1_fresh'>fresh</option><option value='style1_gift_caps'>gift (uppercase)</option><option value='style1_heart'>heart</option><option value='style1_hot'>hot</option><option value='style1_hot_caps'>hot (uppercase)</option><option value='style1_new'>new</option><option value='style1_new_caps'>new (uppercase)</option><option value='style1_no1'>no. 1</option><option value='style1_off5'>5% off</option><option value='style1_off10'>10% off</option><option value='style1_off15'>15% off</option><option value='style1_off20'>20% off</option><option value='style1_off25'>25% off</option><option value='style1_off30'>30% off</option><option value='style1_off35'>35% off</option><option value='style1_off40'>40% off</option><option value='style1_off50'>50% off</option><option value='style1_off75'>75% off</option><option value='style1_pack'>pack</option><option value='style1_pro'>pro</option><option value='style1_sale'>sale</option><option value='style1_save'>save</option><option value='style1_save_caps'>save (uppercase)</option><option value='style1_top'>top</option><option value='style1_top_caps'>top (uppercase)</option><option value='style1_trial'>trial</option></optgroup><optgroup label='Style 2'><option value='style2_best'>best</option><option value='style2_buy'>buy</option><option value='style2_free'>free</option><option value='style2_free_caps'>free (uppercase)</option><option value='style2_fresh'>fresh</option><option value='style2_gift_caps'>gift (uppercase)</option><option value='style2_heart'>heart</option><option value='style2_hot'>hot</option><option value='style2_hot_caps'>hot (uppercase)</option><option value='style2_new'>new</option><option value='style2_new_caps'>new (uppercase)</option><option value='style2_no1'>no. 1</option><option value='style2_off5'>5% off</option><option value='style2_off10'>10% off</option><option value='style2_off15'>15% off</option><option value='style2_off20'>20% off</option><option value='style2_off25'>25% off</option><option value='style2_off30'>30% off</option><option value='style2_off35'>35% off</option><option value='style2_off40'>40% off</option><option value='style2_off50'>50% off</option><option value='style2_off75'>75% off</option><option value='style2_pack'>pack</option><option value='style2_pro'>pro</option><option value='style2_sale'>sale</option><option value='style2_save'>save</option><option value='style2_save_caps'>save (uppercase)</option><option value='style2_top'>top</option><option value='style2_top_caps'>top (uppercase)</option><option value='style2_trial'>trial</option></optgroup></select></th>");
						$("#textsTable tbody tr").append("<td class='css3_grid_admin_column"+(i+1)+"'><input type='text' name='texts[]' value='' />"+shortcodesSelect+"</td>");
					}
				}
			}
		}
	});
	$("#css3_grid_settings").one("submit", submitConfigForm);
	function submitConfigForm(event)
	{
		event.preventDefault();
		if($("#shortcodeId").val()!="")
			$(this).submit();
		else
		{
			$("#shortcodeId").addClass("css3_grid_input_error");
			var offset = $("#shortcodeId").offset();
			$(document).scrollTop(offset.top-10);
			$("#css3_grid_settings").one("submit", submitConfigForm);
		}
		
	}
	$("#shortcodeId").bind("keyup paste", function(){
		if($(this).val()!="")
			$(this).removeClass("css3_grid_input_error");
	});
	if(config.selectedShortcodeId!="")
		$("#editShortcodeId").val("css3_grid_shortcode_settings_" + config.selectedShortcodeId).trigger("change");
	//sorting
	$(".css3_grid_sort_left").live("click", function(event){
		event.preventDefault();
		$("." + $(this).parent().parent().parent().attr("class")).each(function(){
			$(this).insertBefore($(this).prev(":not('.css3_grid_admin_column1')"));
		});
	});
	$(".css3_grid_sort_right").live("click", function(event){
		event.preventDefault();
		$("." + $(this).parent().parent().parent().attr("class")).each(function(){
			$(this).insertAfter($(this).next());
		});
	});
	$(".css3_grid_sort_up").live("click", function(event){
		event.preventDefault();
		$("." + $(this).parent().parent().parent().attr("class")).each(function(){
			$(this).insertBefore($(this).prev());
		});
	});
	$(".css3_grid_sort_down").live("click", function(event){
		event.preventDefault();
		$("." + $(this).parent().parent().parent().attr("class")).each(function(){
			$(this).insertAfter($(this).next());
		});
	});
});
