jQuery(function() {
    jQuery('.w3tc-widget-ps-view-all').click(function() {
        window.open('admin.php?page=w3tc_general&w3tc_action=pagespeed_results', 'pagespeed_results', 'width=800,height=600,status=no,toolbar=no,menubar=no,scrollbars=yes');

        return false;
    });

    jQuery('.w3tc-widget-ps-refresh').click(function() {
        document.location.href = 'index.php?w3tc_widget_pagespeed_force=1';
    });
});
