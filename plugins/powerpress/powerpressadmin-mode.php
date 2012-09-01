<?php
	// powerpressadmin-mode.php
	
	function powerpress_admin_mode()
	{
?>


<input type="hidden" name="action" value="powerpress-save-mode" />
<h2><?php echo __('Welcome to Blubrry PowerPress', 'powerpress'); ?></h2>

<p style="margin-bottom: 0;">
	<?php echo __('Welcome to Blubrry PowerPress. In order to give each user the best experience, we designed two modes; Simple and Advanced. Please select the mode that is most appropriate for your needs.', 'powerpress'); ?>
</p>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Select Mode', 'powerpress'); ?></th> 
<td>
	
	<p><input name="General[advanced_mode]" type="radio" value="0" /> <strong><?php echo __('Simple Mode', 'powerpress'); ?></strong></p>
	<p><?php echo __('Simple Mode is intended for podcasters who are just starting out and feel a bit intimidated by all of the possible options and settings. This mode is perfect for someone who is recording in one format (e.g. mp3) and wants to keep things simple.', 'powerpress'); ?></p>
	<ul><li><?php echo __('Features Include', 'powerpress').':'; ?><ul>
		<li><?php echo __('Only the bare essential settings', 'powerpress'); ?></li>
		<li><?php echo __('Important feed and iTunes settings', 'powerpress'); ?></li>
		<li><?php echo __('Player and download links added to bottom of episode posts', 'powerpress'); ?></li>
	</ul></li></ul>
	
	<p><input name="General[advanced_mode]" type="radio" value="1" /> <strong><?php echo __('Advanced Mode', 'powerpress'); ?></strong></p>
	<p><?php echo __('Advanced Mode gives you all of the features packaged in Blubrry PowerPress. This mode is perfect for someone who may want to distribute multiple versions of their podcast, customize the web player and download links, or import data from a previous podcasting platform.', 'powerpress'); ?></p>
	<ul><li><?php echo __('Features Include', 'powerpress').':'; ?><ul>
		<li><em><?php echo __('Advanced Settings', 'powerpress'); ?></em> - <?php echo __('Tweak additional settings.', 'powerpress'); ?></li>
		<li><em><?php echo __('Presentation Settings', 'powerpress'); ?></em> - <?php echo __('Customize web player and media download links', 'powerpress'); ?></li>
		<li><em><?php echo __('Extensive Feed Settings', 'powerpress'); ?></em> -  <?php echo __('Tweak all available feed settings', 'powerpress'); ?></li>
	</ul></li></ul>
	
</td>
</tr>

</table>
<p class="submit">
	<input type="submit" name="Submit" id="powerpress_save_button" class="button-primary" value="<?php echo __('Set Mode and Continue', 'powerpress'); ?>" />
</p>

	<!-- start footer -->
<?php
	}

?>