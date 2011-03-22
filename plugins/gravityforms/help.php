<?php
class GFHelp{
    public static function help_page(){
        if(!GFCommon::ensure_wp_version())
                return;

            echo GFCommon::get_remote_message();

            ?>
            <link rel="stylesheet" href="<?php echo GFCommon::get_base_url()?>/css/admin.css" />
            <div class="wrap">
                <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" style="margin: 15px 7px 0pt 0pt; float: left;" src="<?php echo GFCommon::get_base_url() ?>/images/gravity-title-icon-32.png"/>
                <h2><?php _e("Gravity Forms Help", "gravityforms"); ?></h2>

                <div style="margin-top:10px;">

                <div class="gforms_help_alert"><?php _e("<strong>IMPORTANT NOTICE:</strong> We do not provide support via e-mail. Please post any support queries in our <a href='http://forum.gravityhelp.com/'>support forums</a>.", "gravityforms") ?></div>

                <div><?php _e("Please review the plugin documentation and frequently asked questions (FAQ) first. If you still can't find the answer you need visit our <a href='http://forum.gravityhelp.com/'>support forums</a> where we will be happy to answer your questions and assist you with any problems. <strong>Please note:</strong> If you have not <a href='http://www.gravityforms.com/purchase-gravity-forms/'>purchased a license</a> from us, you won't have access to these help resources.", "gravityforms"); ?></div>


                <div class="hr-divider"></div>

                <h3><?php _e("Gravity Forms Documentation", "gravityforms"); ?></h3>
                <?php _e("<strong>Note:</strong> Only licensed Gravity Forms customers are granted access to the documentation section.", "gravityforms"); ?>
                <ul style="margin-top:15px;">
                    <li>
                    <div class="gforms_helpbox">
                    <form name="jump">
                    <select name="menu">

                        <!-- begin documentation listing -->
                        <option selected><?php _e("Documentation (please select a topic)", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/installing-gravity-forms/"><?php _e("Installing Gravity Forms", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/new-form/"><?php _e("New Form", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/form-settings/"><?php _e("Form Settings", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/form-fields/"><?php _e("Form Fields", "gravityforms"); ?></option>

                            <option value="http://www.gravityhelp.com/documentation/form-fields/address/">&nbsp;&nbsp;&nbsp;<?php _e("Address", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/checkboxes/">&nbsp;&nbsp;&nbsp;<?php _e("Checkboxes", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/date/">&nbsp;&nbsp;&nbsp;<?php _e("Date", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/drop-down/">&nbsp;&nbsp;&nbsp;<?php _e("Drop Down", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/email/">&nbsp;&nbsp;&nbsp;<?php _e("Email", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/file-upload/">&nbsp;&nbsp;&nbsp;<?php _e("File Upload", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/multiple-choice/">&nbsp;&nbsp;&nbsp;<?php _e("Multiple Choice", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/name/">&nbsp;&nbsp;&nbsp;<?php _e("Name", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/number/">&nbsp;&nbsp;&nbsp;<?php _e("Number", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/paragraph-text/">&nbsp;&nbsp;&nbsp;<?php _e("Paragraph Text", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/phone/">&nbsp;&nbsp;&nbsp;<?php _e("Phone", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/post-body/">&nbsp;&nbsp;&nbsp;<?php _e("Post Body", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/post-category/">&nbsp;&nbsp;&nbsp;<?php _e("Post Category", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/post-custom-field/">&nbsp;&nbsp;&nbsp;<?php _e("Post Custom Field", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/post-excerpt/">&nbsp;&nbsp;&nbsp;<?php _e("Post Excerpt", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/post-image/">&nbsp;&nbsp;&nbsp;<?php _e("Post Image", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/post-tags/">&nbsp;&nbsp;&nbsp;<?php _e("Post Tags", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/post-title/">&nbsp;&nbsp;&nbsp;<?php _e("Post Title", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/recaptcha/">&nbsp;&nbsp;&nbsp;<?php _e("reCAPTCHA", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/section-break/">&nbsp;&nbsp;&nbsp;<?php _e("Section Break", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/single-line-text/">&nbsp;&nbsp;&nbsp;<?php _e("Single Line Text", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/time/">&nbsp;&nbsp;&nbsp;<?php _e("Time", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/form-fields/website/">&nbsp;&nbsp;&nbsp;<?php _e("Website", "gravityforms"); ?></option>

                        <option value="http://www.gravityhelp.com/documentation/embedding-a-form/"><?php _e("Embedding A Form", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/edit-forms/"><?php _e("Edit Forms", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/edit-forms/notifications/">&nbsp;&nbsp;&nbsp;<?php _e("Notifications", "gravityforms"); ?></option>

                        <option value="http://www.gravityhelp.com/documentation/entries/"><?php _e("Entries", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/settings/"><?php _e("Settings", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/export/"><?php _e("Export", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/dashboard-widget/"><?php _e("Dashboard Widget", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/role-management/"><?php _e("Role Management", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/hooks-and-filters/"><?php _e("Hooks and Filters", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/visual-css-guide/"><?php _e("Visual CSS Guide", "gravityforms"); ?></option>
                        <option value="http://www.gravityhelp.com/documentation/add-ons/"><?php _e("Add-Ons", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/add-ons/campaign-monitor-add-on/">&nbsp;&nbsp;&nbsp;<?php _e("Campaign Monitor Add-On", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/add-ons/freshbooks-add-on/">&nbsp;&nbsp;&nbsp;<?php _e("FreshBooks Add-On", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/documentation/add-ons/mailchimp-add-on/">&nbsp;&nbsp;&nbsp;<?php _e("MailChimp Add-On", "gravityforms"); ?></option>

                    <!-- end documentation listing -->
                    </select>
                    <input type="button" class="button" onClick="location=document.jump.menu.options[document.jump.menu.selectedIndex].value;" value="<?php _e("GO", "gravityforms"); ?>">
                </form>
                </div>

                    </li>
                   </ul>

                <div class="hr-divider"></div>

                <h3><?php _e("Gravity Forms FAQ", "gravityforms"); ?></h3>
                <?php _e("<strong>Please Note:</strong> Only licensed Gravity Forms customers are granted access to the FAQ section.", "gravityforms"); ?>
                <ul style="margin-top:15px;">
                    <li>
                    <div class="gforms_helpbox">
                    <form name="jump1">
                    <select name="menu1">

                        <!-- begin faq listing -->
                        <option selected><?php _e("FAQ (please select a topic)", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/frequently-asked-questions/#faq_installation"><?php _e("Installation Questions", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/frequently-asked-questions/#faq_styling"><?php _e("Formatting/Styling Questions", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/frequently-asked-questions/#faq_notifications"><?php _e("Notification Questions", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/frequently-asked-questions/#faq_general"><?php _e("General Questions", "gravityforms"); ?></option>

                        <!-- end faq listing -->
                    </select>
                    <input type="button" class="button" onClick="location=document.jump1.menu1.options[document.jump1.menu1.selectedIndex].value;" value="<?php _e("GO", "gravityforms"); ?>">
                </form>
                    </div>

                    </li>

                </ul>

                <div class="hr-divider"></div>

                <h3><?php _e("Gravity Forms Support Forums", "gravityforms"); ?></h3>
                <?php _e("<strong>Please Note:</strong> Only licensed Gravity Forms customers are granted access to the support forums.", "gravityforms"); ?>
                <ul style="margin-top:15px;">
                    <li>
                    <div class="gforms_helpbox">
                    <form name="jump2">
                    <select name="menu2">

                        <!-- begin forums listing -->
                        <option selected><?php _e("Forums (please select a topic)", "gravityforms"); ?></option>
                        <option value="http://forum.gravityhelp.com/forum/general"><?php _e("General", "gravityforms"); ?></option>
                            <option value="http://forum.gravityhelp.com/forum/news-and-announcements">&nbsp;&nbsp;&nbsp;<?php _e("News &amp; Announcements", "gravityforms"); ?></option>
                            <option value="http://forum.gravityhelp.com/forum/pre-sale-questions">&nbsp;&nbsp;&nbsp;<?php _e("Pre-Sale Questions", "gravityforms"); ?></option>

                            <option value="http://forum.gravityhelp.com/forum/feature-requests">&nbsp;&nbsp;&nbsp;<?php _e("Feature Requests", "gravityforms"); ?></option>
                            <option value="http://forum.gravityhelp.com/forum/testimonials">&nbsp;&nbsp;&nbsp;<?php _e("Testimonials", "gravityforms"); ?></option>

                        <option value="http://forum.gravityhelp.com/forum/plugin-support"><?php _e("Plugin Support", "gravityforms"); ?></option>
                            <option value="http://forum.gravityhelp.com/forum/gravity-forms">&nbsp;&nbsp;&nbsp;<?php _e("Gravity Forms", "gravityforms"); ?></option>
                            <option value="http://forum.gravityhelp.com/forum/gravity-forms-mailchimp-add-on">&nbsp;&nbsp;&nbsp;<?php _e("Gravity Forms MailChimp Add-On", "gravityforms"); ?></option>

                            <option value="http://forum.gravityhelp.com/forum/gravity-forms-campaign-monitor-add-on">&nbsp;&nbsp;&nbsp;<?php _e("Gravity Forms Campaign Monitor Add-On", "gravityforms"); ?></option>
                            <option value="http://forum.gravityhelp.com/forum/gravity-forms-freshbooks-add-on">&nbsp;&nbsp;&nbsp;<?php _e("Gravity Forms FreshBooks Add-On", "gravityforms"); ?></option>

                    <!-- end forums listing -->
                    </select>
                    <input type="button" class="button" onClick="location=document.jump2.menu2.options[document.jump2.menu2.selectedIndex].value;" value="<?php _e("GO", "gravityforms"); ?>">
                </form>
                    </div>

                    </li>

                </ul>

                <div class="hr-divider"></div>

                <h3><?php _e("Gravity Forms Downloads", "gravityforms"); ?></h3>
                <?php _e("<strong>Please Note:</strong> Only licensed Gravity Forms customers are granted access to the downloads section.", "gravityforms"); ?>
                <ul style="margin-top:15px;">
                    <li>
                    <div class="gforms_helpbox">
                    <form name="jump3">
                    <select name="menu3">

                        <!-- begin downloads listing -->
                        <option selected><?php _e("Downloads (please select a product)", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/downloads/"><?php _e("Gravity Forms", "gravityforms"); ?></option>
                            <option value="http://www.gravityhelp.com/downloads/add-ons/"><?php _e("Gravity Forms Add-Ons", "gravityforms"); ?></option>

                        <!-- end downloads listing -->
                    </select>
                    <input type="button" class="button" onClick="location=document.jump3.menu3.options[document.jump3.menu3.selectedIndex].value;" value="<?php _e("GO", "gravityforms"); ?>">
                </form>
                    </div>

                    </li>

                </ul>


                <div class="hr-divider"></div>

                <h3><?php _e("Gravity Forms Tutorials &amp; Resources", "gravityforms"); ?></h3>
                <?php _e("<strong>Please note:</strong> The Gravity Forms support team does not provide support for third party scripts, widgets, etc.", "gravityforms"); ?>

                <div class="gforms_helpbox" style="margin:15px 0;">
                <ul class="resource_list">
                <li><a href="http://www.gravityhelp.com/">Gravity Forms Blog</a></li>
                    <li><a href="http://www.gravityhelp.com/gravity-forms-css-visual-guide/">Gravity Forms Visual CSS Guide</a></li>
                    <li><a href="http://www.gravityhelp.com/creating-a-modal-form-with-gravity-forms-and-fancybox/">Creating a Modal Form with Gravity Forms and FancyBox</a></li>
                    <li><a href="http://yoast.com/gravity-forms-widget-update/">Gravity Forms Widget (Third Party Release)</a></li>
                    <li><a href="http://wordpress.org/extend/plugins/wp-mail-smtp/">WP Mail SMTP Plugin</a></li>
                    <li><a href="http://wordpress.org/extend/plugins/members/">Members Plugin (Role Management - Integrates with Gravity Forms)</a></li>
                    <li><a href="http://wordpress.org/extend/plugins/really-simple-captcha/">Really Simple Captcha Plugin (Integrates with Gravity Forms)</a></li>
                </ul>

                </div>



                </div>
            </div>


            <?php
    }
}
?>