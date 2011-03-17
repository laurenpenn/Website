<?php if (shopp('product','found','load=prices,images,categories,specs')): ?>

	<h1><?php shopp('product','name'); ?></h1>
	<?php if(shopp('product','in-category','id=3')) : echo "<strong>Series</strong>"; endif; ?>

	<div class="categories">
		<?php shopp('catalog','breadcrumb'); ?>
	</div>

	<div class="clear"></div>
		
	<?php 
	if(shopp('product','has-specs')):
		$product_id = ($Shopp->Product->specs[ID]->content);
		if(file_exists('wp-content/uploads/video/'. $product_id .'.flv')):
	?>
		
	<h3>Video</h3>
    <div id="videoplayer">
        This text will be replaced
    </div>
								
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/player/swfobject.js">
    </script>
	<script type="text/javascript">

		<?php 
			if(!empty($Shopp->Product->images)):
				$image = ($Shopp->Product->images[1]->uri);
			endif;
			echo $image;
		?>

		var so = new SWFObject('<?php bloginfo("template_directory"); ?>/player/player.swf','videoplayer','650','400','7');
		so.addVariable('fullscreen','true');
		so.addParam("allowfullscreen","true");
		so.addParam("wmode","opaque");
		/*so.addVariable("file", "<?php if(shopp('product','has-categories')): ?><?php shopp('catalog','category','id='.shopp('product','category','show=id&echo=0').'&load=true'); ?><?php $feedurl = shopp('category','url','echo=0').'feed/'; endif; ?>");*/
		so.addVariable("file", "<?php bloginfo('url'); ?>/wp-content/uploads/video/<?php echo $product_id; ?>.flv");
		so.addVariable("image", "<?php echo $image; ?>");
		so.addVariable("skin", "<?php bloginfo("template_directory"); ?>/player/modeius/modieus.swf");
		so.addVariable('plugins', 'googlytics-1');

		so.write('videoplayer');

	</script>
	
	<?php endif; endif; ?>

	<?php if(shopp('product','has-images','type=small')): ?>
		<div class="contentdiv">
			<a href="<?php shopp('product','url'); ?>" title="<?php shopp('product','name'); ?>"><?php  shopp('product','image'); ?></a>
		</div>
	<?php endif; ?>

	<?php 
	if(shopp('product','has-specs')):
		$product_id = ($Shopp->Product->specs[ID]->content);
		if(file_exists('wp-content/uploads/audio/'. $product_id .'.mp3')):
	?>
	
	<h3>Audio</h3>
	<div id="audioplayer">
		This text will be replaced
	</div>
								
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/player/swfobject.js">
	</script>
	<script type="text/javascript">

		<?php 
			if(!empty($Shopp->Product->images)):
				$image = ($Shopp->Product->images[0]->uri);
			endif;
			echo $image;
		?>

		var so = new SWFObject('<?php bloginfo("template_directory"); ?>/player/player.swf','audioplayer','660','32','7');
		so.addVariable('fullscreen','true');
		so.addParam("allowfullscreen","true");
		so.addParam("wmode","opaque");
		so.addVariable("file", "<?php bloginfo('url'); ?>/wp-content/uploads/audio/<?php echo $product_id; ?>.mp3");
		so.addVariable("image", "<?php echo $image; ?>");
		so.addVariable("skin", "<?php bloginfo("template_directory"); ?>/player/modeius/modieus.swf");
		so.addVariable('plugins', 'googlytics-1');

		so.write('audioplayer');

	</script>

	<p class="note">
		<a class="floatright input" href="<?php bloginfo('url'); ?>/sermons/donate/"><span>Donate</span></a>
		<small><a href="<?php bloginfo('url'); ?>/wp-content/uploads/audio/<?php echo $product_id; ?>.mp3">Download MP3: <?php shopp('product','name'); ?></a></small><br />
		<small><em>To download, simply right-click on the link above and select "Save As..." to save to a location on your computer.</em></small>
	</p>
	<?php endif; endif; ?>
	
	<?php shopp('product','summary'); ?>
	
	<?php shopp('product','description'); ?>
		
<?php else: ?>
	<h3>Product Not Found</h3>
	<p>Sorry!  The product you requested is not found in our catalog!</p>
<?php endif; ?>