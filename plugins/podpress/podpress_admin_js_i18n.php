<?php
function podpress_print_localized_admin_js_vars() {
?>
<script type="text/javascript">
//<![CDATA[
var podpressL10 = {
	Show : '<?php echo js_escape(__('Show', 'podpress')); ?>',
	Hide : '<?php echo js_escape(__('Hide', 'podpress')); ?>',
	detecting : '<?php echo js_escape(__('DETECTING...', 'podpress')); ?>',
	unknown : '<?php echo js_escape(__('UNKNOWN', 'podpress')); ?>',
	test_successful : '<?php echo js_escape(__('Test: Successful', 'podpress')); ?>',
	test_failed : '<?php echo js_escape(__('Test: Failed', 'podpress')); ?>',
	loadingID3msg : '<?php echo js_escape(__('Loading ID3 tag information. If the file is remote this may take several seconds.', 'podpress')); ?>',
	noiTunesSummary : '<?php echo js_escape(__('NO DESCRIPTION IS SET. APPLE WILL REJECT THIS FEED.', 'podpress')); ?>',
	nothing : '<?php echo js_escape(__('nothing', 'podpress')); ?>',
	notxmlhttpinstance : '<?php echo js_escape(__('It is not possible to create an XMLHTTP instance.', 'podpress')); ?>',
	ajaxerror : '<?php echo js_escape(__('Error: There was a problem with the request - ', 'podpress')); ?>',
	viewerrormsg : '<?php echo js_escape(__('Do you like see the complete error message?', 'podpress')); ?>',
	openblogagain : '<?php echo js_escape(__('Back to the blog', 'podpress')); ?>'
}
//]]>
</script>
<?php
}
?>