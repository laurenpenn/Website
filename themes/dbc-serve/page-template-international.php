<?php
/**
 * Template Name: International
 *
 * This template is for the International page
 *
 * @package Hybrid
 * @subpackage Template
 */
global $blog_ID;

get_header(); ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>
<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.google.com/maps/ms?msa=0&amp;msid=218291070217784568425.0004a8788f11f86b685bd&amp;ie=UTF8&amp;ll=20.593684,78.96288&amp;spn=23.875,57.630033&amp;output=embed"></iframe><br /><small>View <a href="http://www.google.com/maps/ms?msa=0&amp;msid=218291070217784568425.0004a8788f11f86b685bd&amp;ie=UTF8&amp;ll=20.593684,78.96288&amp;spn=23.875,57.630033&amp;source=embed" style="color:#0000FF;text-align:left">Serve</a> in a larger map</small>

		<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		  <script type='text/javascript'>
		   google.load('visualization', '1', {'packages': ['geomap']});
		   google.setOnLoadCallback(drawMap);
		
		    function drawMap() {
		      var data = new google.visualization.DataTable();
		      data.addRows(9);
		      data.addColumn('string', 'Country');
		      data.addColumn('number', 'Missionaries');
		      data.setValue(0, 0, 'Argentina');
		      data.setValue(0, 1, 4);
		      data.setValue(1, 0, 'India');
		      data.setValue(1, 1, 2);
		      data.setValue(2, 0, 'Kenya');
		      data.setValue(2, 1, 3);
		      data.setValue(3, 0, 'Mexico');
		      data.setValue(3, 1, 4);
		      data.setValue(4, 0, 'Romania');
		      data.setValue(4, 1, 1);
		      data.setValue(5, 0, 'RU');
		      data.setValue(5, 1, 1);
		      data.setValue(6, 0, 'Spain');
		      data.setValue(6, 1, 1);
		      data.setValue(7, 0, 'France');
		      data.setValue(7, 1, 9);
		      data.setValue(8, 0, 'Central Europe');
		      data.setValue(8, 1, 5);
		      	      		      		
		      var options = {};
		      options['dataMode'] = 'regions';
		      options['width'] = '655px';
          		
		      var container = document.getElementById('map_canvas');
		      var geomap = new google.visualization.GeoMap(container);
		      geomap.draw(data, options);
		  };
		  </script>
		<div id='map_canvas'></div>
		  
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<p class="page-links pages">' . __( 'Pages:', 'hybrid' ), 'after' => '</p>' ) ); ?>
				</div><!-- .entry-content -->

			</div><!-- .hentry -->

			<?php do_atomic( 'after_singular' ); // prototype_after_singular ?>

			<?php endwhile; ?>

		<?php else: ?>

			<p class="no-data">
				<?php _e( 'Apologies, but no results were found.', 'hybrid' ); ?>
			</p><!-- .no-data -->

		<?php endif; ?>
			
		<?php do_atomic( 'close_content' ); // prototype_close_content ?>

	</div><!-- #content -->
		
	<?php get_sidebar( 'home' ); ?>

	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>