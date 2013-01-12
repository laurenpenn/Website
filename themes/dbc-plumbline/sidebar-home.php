<?php
/**
 * Sidebar Home Template
 *
 * Displays any widgets for the Home dynamic sidebar if they are available.
 *
 * @package Plumbline
 * @subpackage Template
 */

 ?>
 
<div id="sidebar-home" class="sidebar">

	<?php if ( hybrid_get_setting( 'info' ) == 'true' ){ ?>
	<div id="welcome">
	
		<div class="welcome-inner">
		
			<?php if ( hybrid_get_setting( 'info_title' ) != '' ) {               ?><h3 class="widget-title"><?php echo hybrid_get_setting( 'info_title' ) ?></h3><?php } ?>
			<?php if ( hybrid_get_setting( 'info_service_times_title' ) != '' ) { ?><div class="welcome-service-times"><?php echo  hybrid_get_setting( 'info_service_times_title' ) ?></div><?php } ?>
			<?php if ( hybrid_get_setting( 'info_service_times_data' ) != '' ) {  ?><div class="welcome-service-times-data"><?php echo hybrid_get_setting( 'info_service_times_data' ) ?></div><?php } ?>
			<?php if ( hybrid_get_setting( 'info_location' ) != '' ) {            ?><div class="welcome-directions"><a href="<?php echo hybrid_get_setting( 'info_location' ) ?>" rel="external">Directions</a> (from Google Maps)</div><?php } ?>
			<?php if ( hybrid_get_setting( 'info_link' ) != '' ) {                ?><div class="welcome-visiting-information"><a href="<?php echo hybrid_get_setting( 'info_link' )?>"><span>Visiting Information</span></a></div><?php } ?>
			
		</div>
	
	</div>
	
	<?php } ?>

	<?php dynamic_sidebar( 'home' ); ?>

</div><!-- #sidebar-home -->

<div class="clear"></div>
