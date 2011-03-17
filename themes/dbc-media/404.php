<?php get_header(); ?>
		   
	<div id="bd">

		<div id="yui-main">
			
			<div class="yui-b">
			
				<div id="page" class="yui-g">

					<div class="box-container">
						
						<h1>Oops!</h1>
						
						<div class="entry">
						
							<p>Looks like the page you're looking for isn't here anymore.</p>

							<p><strong>Try searching for what you're looking for...</strong></p>
							
							<div id="search-form">
							    <form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
							   		<div>
										<?php shopp('catalog','search'); ?>
										<input id="s" type="text" name="s" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}" />
								    	<input id="searchsubmit" type="submit" value="Search" />
							    	</div>
									
							    </form> 
							</div>
						
						</div><!-- end .entry -->
										
					</div><!-- end .box-container -->
						
				</div><!-- end #page -->
				
			</div><!-- end .yui-b -->
			
		</div><!-- end #yui-main -->
		
		<?php get_sidebar(); ?>

	</div><!-- end #bd -->        
        
<?php get_footer(); ?>    	