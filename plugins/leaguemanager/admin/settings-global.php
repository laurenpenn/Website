<form action='' method='post' name='colors'>
<?php wp_nonce_field( 'leaguemanager_manage-global-league-options' ); ?>

<div class='wrap'>
	<h2><?php _e( 'Leaguemanager Global Settings', 'leaguemanager' ) ?></h2>
	<h3><?php _e( 'Color Scheme', 'leaguemanager' ) ?></h3>
	<table class='form-table'>
	<tr valign='top'>
		<th scope='row'><label for='color_headers'><?php _e( 'Table Headers', 'leaguemanager' ) ?></label></th><td><input type='text' name='color_headers' id='color_headers' value='<?php echo $options['colors']['headers'] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_headers,"pick_color_headers"); return false;' name='pick_color_headers' id='pick_color_headers'>&#160;&#160;&#160;</a></td>
	</tr>
	<tr valign='top'>
		<th scope='row'><label for='color_rows'><?php _e( 'Table Rows', 'leaguemanager' ) ?></label></th>
		<td>
			<p class='table_rows'><input type='text' name='color_rows_alt' id='color_rows_alt' value='<?php echo $options['colors']['rows']['alternate'] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_rows_alt,"pick_color_rows_alt"); return false;' name='pick_color_rows_alt' id='pick_color_rows_alt'>&#160;&#160;&#160;</a></p>
			<p class='table_rows'><input type='text' name='color_rows' id='color_rows' value='<?php echo $options['colors']['rows']['main'] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_rows,"pick_color_rows"); return false;' name='pick_color_rows' id='pick_color_rows'>&#160;&#160;&#160;</a></p>
		</td>
	</tr>
	<tr valign='top'>
		<th scope='row'><label for='color_rows_ascend'><?php _e( 'Teams Ascend', 'leaguemanager' ) ?></label></th><td><input type='text' name='color_rows_ascend' id='color_rows_ascend' value='<?php echo $options['colors']['rows']['ascend'] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_rows_ascend,"pick_color_rows_ascend"); return false;' name='pick_color_rows_ascend' id='pick_color_rows_ascend'>&#160;&#160;&#160;</a></td>
	</tr>
	<tr valign='top'>
		<th scope='row'><label for='color_rows_descend'><?php _e( 'Teams Descend', 'leaguemanager' ) ?></label></th><td><input type='text' name='color_rows_descend' id='color_rows_descend' value='<?php echo $options['colors']['rows']['descend'] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_rows_descend,"pick_color_rows_descend"); return false;' name='pick_color_rows_descend' id='pick_color_rows_descend'>&#160;&#160;&#160;</a></td>
	</tr>
	<tr valign='top'>
		<th scope='row'><label for='color_rows_relegation'><?php _e( 'Teams Relegation', 'leaguemanager' ) ?></label></th><td><input type='text' name='color_rows_relegation' id='color_rows_relegation' value='<?php echo $options['colors']['rows']['relegation'] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_rows_relegation,"pick_color_rows_relegation"); return false;' name='pick_color_rows_relegation' id='pick_color_rows_relegation'>&#160;&#160;&#160;</a></td>
	</tr>
	</table>
	
	<input type='hidden' name='page_options' value='color_headers,color_rows,color_rows_alt,color_rows_ascend,color_rows_descend,color_rows_relegation' />
	<p class='submit'><input type='submit' name='updateLeagueManager' value='<?php _e( 'Save Preferences', 'leaguemanager' ) ?> &raquo;' class='button' /></p>
</div>
</form>
	
<script language='javascript'>
	syncColor("pick_color_headers", "color_headers", document.getElementById("color_headers").value);
	syncColor("pick_color_rows", "color_rows", document.getElementById("color_rows").value);
	syncColor("pick_color_rows_alt", "color_rows_alt", document.getElementById("color_rows_alt").value);
	syncColor("pick_color_rows_ascend", "color_rows_ascend", document.getElementById("color_rows_ascend").value);
	syncColor("pick_color_rows_descend", "color_rows_descend", document.getElementById("color_rows_descend").value);
	syncColor("pick_color_rows_relegation", "color_rows_relegation", document.getElementById("color_rows_relegation").value);
</script>
