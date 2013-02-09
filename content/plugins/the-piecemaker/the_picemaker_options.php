<script type="text/javascript">
  jQuery(document).ready(function() {
    // hides as soon as the DOM is ready
    jQuery('div.tp-option-body').hide();
    // shows on clicking the noted link
    jQuery('h3').click(function() {
      jQuery(this).toggleClass("open");
      jQuery(this).next("div").slideToggle('1000');
      return false;
    });
    /*jQuery("#colorpickerField1").click(function(){
      jQuery("#picker1").farbtastic(this);
    });
    jQuery("#colorpickerField2").click(function(){
      jQuery("#picker2").farbtastic(this);
    });*/
    jQuery("#picker1").farbtastic("#colorpickerField1");
    jQuery("#picker2").farbtastic("#colorpickerField2");
  });
</script>
<div id="tp-options" class="tp-option">
<h2>The Piecemaker Options</h2>

<div class="tp-info">
  <p>You can publish The Piecemaker widget, at any sidebar position you want, to display Piecemaker gallery as per
    configured here.
    If you can't find suitable position in sidebar, simply copy and paste following code at the place where you
    want to display piecemaker gallery inside your theme file.<br/><br/>
  <?php echo htmlentities("<?php if (function_exists(display_the_piecemaker())) display_the_piecemaker(); ?>"); ?>      
  </p>
  <p>For detailed documentation click <a title="The Piecemaker for WordPress &ndash; Documentation" href="http://www.vareen.co.cc/documentation/the-piecemaker-for-wordpress-%e2%80%93-documentation/" target="_blank">here</a>. Any issues or suggestons? feel free to drop comment <a title="The Piecemaker for WordPress &ndash; Documentation" href="http://www.vareen.co.cc/documentation/the-piecemaker-for-wordpress-%e2%80%93-documentation/" target="_blank">here</a>.</p>
</div>

<form method="post" action="options.php">
<?php settings_fields('the_piecemaker_options');
$tpopts = get_option('the_piecemaker'); ?>
<h3>General Options</h3>

<div class="tp-option-body">
  <table class="form-table">

    <tr valign="top">
      <th scope="row">Select Category</th>
      <td><?php wp_dropdown_categories(array('show_option_none' => 'Select Category', 'name' => 'the_piecemaker[category]', 'selected' => $tpopts['category'])); ?>

        <p>You can either select one category here, or setup manual slideshow with last two options ( Image URL's and Image Descriptions ).
          If you select category here, slideshow will be created using posts in that category. You have to add an extra
          custom field
          with name 'the_piecemaker_image' and relative url of image from wordpress installation root as value.
          Last two options will be irrelevant in that case.
        </p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Read More Link Text</th>
      <td><input type="text" name="the_piecemaker[readMore]" value="<?php echo ($tpopts['readMore'])?$tpopts['readMore']:'Read More'; ?>"/>

        <p>This text will be used in description of the image as link to the post.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Image URL's</th>
      <td><textarea name="the_piecemaker[image_url]" style="width:100%"
                    rows="5"><?php echo $tpopts['image_url']; ?></textarea>

        <p>Add list of image URLs separated by line break.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Image Descriptions</th>
      <td><textarea name="the_piecemaker[image_description]" style="width:100%" cols="50"
                    rows="5"><?php echo $tpopts['image_description']; ?></textarea>

        <p>Add list of image descriptions separated by line break. Checkout following code for help<br/>
          <code><?php echo htmlentities('<headline>Description 1</headline><break>?</break><paragraph>Here you can add a description text for every single image.</paragraph><break>?</break><inline>This is HTML text loaded from the external XML file and formatted with an external CSS file. So it\'s pretty simple to set this text. You can also easily add </inline><a href="http://www.modularweb.net/piecemaker" target="_blank">?hyperlinks</a><paragraph>. This one leads you to the official Piecemaker website, by the way.</paragraph>'); ?></code>
        </p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Width</th>
      <td><input type="text" name="the_piecemaker[width]" value="<?php echo ($tpopts['width'])?$tpopts['width']:'560'; ?>"/>

        <p>Add width of slideshow in PX.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Height</th>
      <td><input type="text" name="the_piecemaker[height]" value="<?php echo ($tpopts['height'])?$tpopts['height']:'374'; ?>"/>

        <p>Add height of slide show in PX.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Shadow</th>
      <td>
        <input id="rebuildcache1" name="the_piecemaker[shadow]"
               value="1" <?php echo ($tpopts['shadow']) ? 'checked="checked"' : ''; ?> type="checkbox"/>

        <p>Select if you want shadow or not.</p>
      </td>
    </tr>
  </table>
</div>
<h3>Advanced Options</h3>

<div class="tp-option-body">
  <table class="form-table">
    <tr valign="top">
      <th scope="row">Segments</th>
      <td><input type="text" name="the_piecemaker[segments]"
                 value="<?php echo ($tpopts['segments']) ? $tpopts['segments'] : '7'; ?>"/>

        <p>Number of segments in which image will be sliced.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Tween Time</th>
      <td><input type="text" name="the_piecemaker[tweenTime]"
                 value="<?php echo ($tpopts['tweenTime']) ? $tpopts['tweenTime'] : '1.2'; ?>"/>

        <p>Number of seconds for each element to be turned.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Tween Delay</th>
      <td><input type="text" name="the_piecemaker[tweenDelay]"
                 value="<?php echo ($tpopts['tweenDelay']) ? $tpopts['tweenDelay'] : '0.1'; ?>"/>

        <p>Number of seconds between one element start to turn to the other element start to turn.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Tween Type</th>
      <td>
      <?php
                          $tweenTypes = array('linear', 'easeInQuad', 'easeOutQuad', 'easeInOutQuad',
        'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart',
        'easeOutQuart', 'easeInOutQuart', 'easeInQuint', 'easeOutQuint',
        'easeInOutQuint', 'easeInSine', 'easeOutSine', 'easeInOutSine',
        'easeInExpo', 'easeOutExpo', 'easeInOutExpo', 'easeInCirc',
        'easeOutCirc', 'easeInOutCirc', 'easeInElastic', 'easeOutElastic',
        'easeInOutElastic', 'easeInBack', 'easeOutBack', 'easeInOutBack',
        'easeInBounce', 'easeOutBounce', 'easeInOutBounce');
      echo '<select class="inputbox" name="the_piecemaker[tweenType]">';
      foreach ($tweenTypes as $tt) {
        if ($tpopts['tweenType'])
          $checked = ($tpopts['tweenType'] == $tt) ? 'selected="selected"' : '';
        else
          $checked = ('easeInOutBack' == $tt) ? 'selected="selected"' : '';
        echo '<option value="' . $tt . '" ' . $checked . ' >' . $tt . '</option>';
      }
      echo '</select>';
      ?>
        <p>Type of transition.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Z Distance</th>
      <td><input type="text" name="the_piecemaker[zDistance]"
                 value="<?php echo ($tpopts['zDistance']) ? $tpopts['zDistance'] : '0'; ?>"/>

        <p>To which extend are the cubes moved on z axis when being tweened. Negative values bring the cube closer
          to
          the camera, positive values take it further away. A good range is roughly between -200 and 700.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Expand</th>
      <td><input type="text" name="the_piecemaker[expand]"
                 value="<?php echo ($tpopts['expand']) ? $tpopts['expand'] : '20'; ?>"/>

        <p>To which extend are the cubes moved away from each other when tweening.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Inner Color</th>
      <td><input id="colorpickerField1" type="text" name="the_piecemaker[innerColor]"
                 value="<?php echo ($tpopts['innerColor']) ? $tpopts['innerColor'] : '#111111'; ?>"/><br/>

        <div style="position:relative;" id="picker1"></div>

        <p>Select color of the sides of the elements.</p>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">Text Background Color</th>
      <td><input id="colorpickerField2" type="text" name="the_piecemaker[textBackground]"
                 value="<?php echo ($tpopts['textBackground']) ? $tpopts['textBackground'] : '#0064C8'; ?>"/><br/>

        <div style="position:relative;" id="picker2">&nbsp;</div>

        <p>Select color of the description text background.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Shadow Darkness</th>
      <td><input type="text" name="the_piecemaker[shadowDarkness]"
                 value="<?php echo ($tpopts['shadowDarkness']) ? $tpopts['shadowDarkness'] : '100'; ?>"/>

        <p>To which extend are the sides shadowed, when the elements are tweening and the sided move towards the
          background. 100 is black, 0 is no darkening</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Text Distance</th>
      <td><input type="text" name="the_piecemaker[textDistance]"
                 value="<?php echo ($tpopts['textDistance']) ? $tpopts['textDistance'] : '25'; ?>"/>

        <p>Distance of the info text to the borders of its background</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Auto Play</th>
      <td><input type="text" name="the_piecemaker[autoPlay]"
                 value="<?php echo ($tpopts['autoPlay']) ? $tpopts['autoPlay'] : '12'; ?>"/>

        <p>Number of seconds to the next image, when autoplay is on. Set 0, if you do not want autoplay</p>
      </td>
    </tr>
  </table>
</div>
<div class="tp-notice">
  <table class="form-table">
    <tr valign="top">
      <th scope="row">Rebuild Cache</th>
      <td>
        <input id="rebuildcache0" name="the_piecemaker[cache]"
               value="1" <?php echo ($tpopts['cache']) ? 'checked="checked"' : ''; ?> type="checkbox"/>

        <p>If you change any of the above option, set it to 'Yes' to rebuild cache. Once confirmed that the changes
          are applied, set it back to 'No' to improve performance.</p>
      </td>
    </tr>
  </table>
</div>

<p class="submit">
  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
</p>

</form>
<p>If you find this plugin useful, please donate to support development of this and few other extensions.</p>
<p><form method="get" action="https://www.paypal.com/cgi-bin/webscr">
    <div class="paypal-donations"><input type="hidden" value="_donations" name="cmd">
        <input type="hidden" value="neeravdobaria@gmail.com" name="business">
        <input type="hidden" value="Support Development" name="item_name">
        <input type="hidden" value="5" name="amount">
        <input type="hidden" value="USD" name="currency_code">
        <input type="image" alt="PayPal - The safer, easier way to pay online." name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif">
    </div>
</form></p>
</div>