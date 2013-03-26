/**
 *
 * WPBakery Theme admin.js
 *
 */
jQuery(document).ready(function($){
    /**
     * Theme options reset defaults button as confirmation  before submit
     */
    $('#nhp-opts-footer > input:last, #nhp-opts-header > input:last').click(function(e){

        if(confirm('Are you sure you want to reset to defaults?')) {
        } else {
            e.preventDefault();
        }
    });
});
