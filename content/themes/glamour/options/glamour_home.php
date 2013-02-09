<style>
	.widefat td{
		vertical-align:middle;
		padding-top:20px;
		padding-bottom:20px;
	}
</style>

<div class="wrap nosubsub">
	<div id="icon-link-manager" class="icon32"><br /></div><h2>The Glamour Home Page Widgets</h2><br  />
	
	<div class="clear"></div>

	<form action="options.php" method="post">
	<?php wp_nonce_field('update-options'); ?>

	<table class="widefat" cellspacing="0">
	
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name" colspan="3">Table Of Home Page Widgets</th>
			</tr>
			</thead>
			
			<tfoot>
			<tr>
				<th scope="col"  class="manage-column column-name" colspan="3"></th>
			</tr>
		</tfoot>
		<tbody>
		
		
		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

				<tr class="alternate">
			<td class="column-name" colspan="3">
				Some Text Enable ? <input name="disable_some_text" type="radio" value="0" <?php if(get_option('disable_some_text') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
				<input name="disable_some_text" type="radio" value="1" <?php if(get_option('disable_some_text', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
			</td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
				<?php $some_text = '<div class="intro_text"><h3>WOULD YOU LIKE WORDPRESS ?</h3>WordPress started in 2003 with a single bit of code to enhance the typogra phy of everyday writing and with fewer users than you can count on  your fingers and toes.  Since then it has grown to be the largest self-hosted blogging tool in the world, used on millions of sites and seen by tens of millions of people every day.</div><div class="intro_image"><img src="'.get_bloginfo('template_url').'/images/readytoget.png" alt="" align="right"/></div>'; ?>
				<textarea rows='5' style="width:100%;" name='some_text'><?php echo get_option('some_text', $some_text); ?></textarea>	
			</td>
		</tr>

		<tr>
			<td class="column-name" colspan="3"></td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
				All Widgets Enable ? <input name="disable_widgets" type="radio" value="0" <?php if(get_option('disable_widgets') == 0){ echo "checked=\"checked\""; } ?>/> No, Thanks &nbsp;&nbsp; 
				<input name="disable_widgets" type="radio" value="1" <?php if(get_option('disable_widgets', 1 ) == 1){ echo "checked=\"checked\""; } ?>/> Yes, please
			</td>
		</tr>
		
		<tr class="alternate">
			<td class="column-name">
				<?php $widgets1 = '<img src="'.get_bloginfo('template_url').'/images/icons/some_icon1.png" class="some_widget_image" alt=""/>
<h5>WHERE WE USE IT</h5>
<p>
Lorem Ipsum is simply dummy text of the printing and typeses industry. 
</p>';
?>
				<textarea rows='10' style="width:100%;" name='widgets1'><?php echo get_option('widgets1', $widgets1); ?></textarea>	
			</td>
		</tr>
		<tr class="alternate">
				<td class="column-name">
				<?php $widgets2 = '<img src="'.get_bloginfo('template_url').'/images/icons/some_icon1.png" class="some_widget_image" alt=""/>
<h5>WHAT IS LOREM IPSUM</h5>
<p>
Lorem Ipsum has been the industrys standard dummy text ever since the 1500s.
</p>'; ?>
<textarea rows='10' style="width:100%;" name='widgets2'><?php echo get_option('widgets2', $widgets2); ?></textarea>	
			</td>
		</tr>
		<tr class="alternate">
				<td class="column-name">
				<?php $widgets3 = '<img src="'.get_bloginfo('template_url').'/images/icons/some_icon1.png" class="some_widget_image" alt=""/>
<h5>GRAPH OF MONTH</h5>
<p>		
It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
</p>'; ?>
				<textarea rows='10' style="width:100%;" name='widgets3'><?php echo get_option('widgets3', $widgets3); ?></textarea>
			</td>
		</tr>

		<tr class="alternate">
			<td class="column-name" colspan="3">
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="some_text, widgets1, widgets2, widgets3, disable_some_text, disable_widgets" />
				<input type="submit" name="publish" id="publish" accesskey="p" tabindex="5" class="button-primary" value="Save" />
				</form>
			</td>
		</tr>
	
	</tbody>
	</table>
	
</div>
