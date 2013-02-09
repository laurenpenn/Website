<?php
function podpress_print_localized_frontend_js_vars() {
?>
<script type="text/javascript">
//<![CDATA[
var podpressL10 = {
	openblogagain : '<?php echo js_escape(__('back to:', 'podpress')); ?>',
	theblog : '<?php echo js_escape(__('the blog', 'podpress')); ?>',
	close : '<?php echo js_escape(__('close', 'podpress')); ?>',
	playbutton : '<?php echo js_escape(__('Play &gt;', 'podpress')); ?>'
}
//]]>
</script>
<?php
}
?>