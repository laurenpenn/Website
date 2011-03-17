<div id="search">
    <form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
   		<div>
			<?php shopp('catalog','search'); ?>
			<input id="s" type="text" name="s" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}" />
	    	<input id="searchsubmit" type="submit" value="Search" />
    	</div>
		
    </form> 
</div>