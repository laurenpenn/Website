<?PHP
function backwpup_jobedit_metabox_save($jobvalue) {
  ?>
  <div class="submitbox" id="submitjobedit">
  <div id="minor-publishing">
  <div id="minor-publishing-actions">
  <div id="preview-action">
  </div>
  <div class="clear"></div>
  </div>
  <div id="misc-publishing-actions">
  <div class="misc-pub-section misc-pub-section-last">
  <?php
  foreach (backwpup_backup_types() as $type) {
    echo "<input class=\"jobtype-select checkbox\" id=\"jobtype-select-".$type."\" type=\"checkbox\"".checked(true,in_array($type,explode('+',$jobvalue['type'])),false)." name=\"type[]\" value=\"".$type."\"/> ".backwpup_backup_types($type);
  }
  if (!function_exists('curl_init'))
    echo '<br /><strong style="color:red;">'.__( 'PHP curl functions not available! Most backup destinations deaktivated!', 'backwpup' ).'</strong>';
  ?>
  </div>
  </div>
  </div>
  <div id="major-publishing-actions">
  <div id="delete-action">
    <a class="submitdelete deletion" href="<?PHP echo wp_nonce_url(backwpup_admin_url('admin.php').'?page=backwpup&action=delete&jobs[]='.$jobvalue['jobid'], 'bulk-jobs'); ?>" onclick="if ( confirm('<?PHP echo esc_js(__("You are about to delete this Job. \n  'Cancel' to stop, 'OK' to delete.","backwpup")); ?>') ) { return true;}return false;"><?php _e('Delete', 'backwpup'); ?></a>
  </div>
  <div id="publishing-action">
    <?php submit_button( __('Save Changes', 'backwpup'), 'primary', 'savebackwpup', false, array( 'tabindex' => '2', 'accesskey' => 'p' ) ); ?>
  </div>
  <div class="clear"></div>
  </div>
  </div>
  <?PHP
}

function backwpup_jobedit_metabox_backupfile($jobvalue) {
  ?>
  <b><?PHP _e('File Prefix:','backwpup'); ?></b><br />
  <input name="fileprefix" type="text" value="<?PHP echo $jobvalue['fileprefix'];?>" class="large-text" /><br />
  <b><?PHP _e('File Formart:','backwpup'); ?></b><br />
  <?PHP
  if (function_exists('gzopen') or class_exists('ZipArchive'))
    echo '<input class="radio" type="radio"'.checked('.zip',$jobvalue['fileformart'],false).' name="fileformart" value=".zip" />'.__('Zip','backwpup').'<br />';
  else
    echo '<input class="radio" type="radio"'.checked('.zip',$jobvalue['fileformart'],false).' name="fileformart" value=".zip" disabled="disabled" />'.__('Zip','backwpup').'<br />';
  echo '<input class="radio" type="radio"'.checked('.tar',$jobvalue['fileformart'],false).' name="fileformart" value=".tar" />'.__('Tar','backwpup').'<br />';
  if (function_exists('gzopen'))
    echo '<input class="radio" type="radio"'.checked('.tar.gz',$jobvalue['fileformart'],false).' name="fileformart" value=".tar.gz" />'.__('Tar GZip','backwpup').'<br />';
  else
    echo '<input class="radio" type="radio"'.checked('.tar.gz',$jobvalue['fileformart'],false).' name="fileformart" value=".tar.gz" disabled="disabled" />'.__('Tar GZip','backwpup').'<br />';
  if (function_exists('bzopen'))
    echo '<input class="radio" type="radio"'.checked('.tar.bz2',$jobvalue['fileformart'],false).' name="fileformart" value=".tar.bz2" />'.__('Tar BZip2','backwpup').'<br />';
  else
    echo '<input class="radio" type="radio"'.checked('.tar.bz2',$jobvalue['fileformart'],false).' name="fileformart" value=".tar.bz2" disabled="disabled" />'.__('Tar BZip2','backwpup').'<br />';
  _e('Preview:','backwpup');
  echo '<br /><i><span id="backupfileprefix">'.$jobvalue['fileprefix'].'</span>'.backwpup_date_i18n('Y-m-d_H-i-s').'<span id="backupfileformart">'.$jobvalue['fileformart'].'</span></i>';
}

function backwpup_jobedit_metabox_sendlog($jobvalue) {
  _e('E-Mail-Adress:','backwpup'); ?>
  <input name="mailaddresslog" id="mailaddresslog" type="text" value="<?PHP echo $jobvalue['mailaddresslog'];?>" class="large-text" /><br />
  <input class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['mailerroronly'],true); ?> name="mailerroronly" /> <?PHP _e('Only send an e-mail if there are errors.','backwpup'); ?>
  <?PHP
}

function backwpup_jobedit_metabox_schedule($jobvalue) {
    list($cronstr['minutes'],$cronstr['hours'],$cronstr['mday'],$cronstr['mon'],$cronstr['wday'])=explode(' ',$jobvalue['cron'],5);
    if (strstr($cronstr['minutes'],'*/'))
      $minutes=explode('/',$cronstr['minutes']);
    else
      $minutes=explode(',',$cronstr['minutes']);
    if (strstr($cronstr['hours'],'*/'))
      $hours=explode('/',$cronstr['hours']);
    else
      $hours=explode(',',$cronstr['hours']);
    if (strstr($cronstr['mday'],'*/'))
      $mday=explode('/',$cronstr['mday']);
    else
      $mday=explode(',',$cronstr['mday']);
    if (strstr($cronstr['mon'],'*/'))
      $mon=explode('/',$cronstr['mon']);
    else
      $mon=explode(',',$cronstr['mon']);
    if (strstr($cronstr['wday'],'*/'))
      $wday=explode('/',$cronstr['wday']);
    else
      $wday=explode(',',$cronstr['wday']);
    backwpup_get_cron_text(array('cronstamp'=>$jobvalue['cron']));
    ?>
    <br />
    <b><input class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['activated'],true); ?> name="activated" /> <?PHP _e('Activate scheduling', 'backwpup'); ?></b><br /><br />
    <?PHP   echo '<input class="radio" type="radio"'.checked("advanced",$jobvalue['cronselect'],false).' name="cronselect" value="advanced" />'.__('advanced','backwpup').'&nbsp;';
        echo '<input class="radio" type="radio"'.checked("basic",$jobvalue['cronselect'],false).' name="cronselect" value="basic" />'.__('basic','backwpup');?>
    <br /><br />
    <div id="schedadvanced" <?PHP if ($jobvalue['cronselect']!='advanced') echo 'style="display:none;"';?>>
      <div id="cron-min-box">
        <b><?PHP _e('Minutes: ','backwpup'); ?></b><br />
        <?PHP
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("*",$minutes,true),true,false).' name="cronminutes[]" value="*" /> '.__('Any (*)','backwpup').'<br />';
        echo '<div id="cron-min">';
        for ($i=0;$i<60;$i=$i+5) {
          echo '<input class="checkbox" type="checkbox"'.checked(in_array("$i",$minutes,true),true,false).' name="cronminutes[]" value="'.$i.'" /> '.$i.'<br />';
        }
        ?>
        </div>
      </div>
      <div id="cron-hour-box">
        <b><?PHP _e('Hours:','backwpup'); ?></b><br />
        <?PHP

        echo '<input class="checkbox" type="checkbox"'.checked(in_array("*",$hours,true),true,false).' name="cronhours[]" value="*" /> '.__('Any (*)','backwpup').'<br />';
        echo '<div id="cron-hour">';
        for ($i=0;$i<24;$i++) {
          echo '<input class="checkbox" type="checkbox"'.checked(in_array("$i",$hours,true),true,false).' name="cronhours[]" value="'.$i.'" /> '.$i.'<br />';
        }
        ?>
        </div>
      </div>
      <div id="cron-day-box">
        <b><?PHP _e('Day of Month:','backwpup'); ?></b><br />
        <?PHP
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("*",$mday,true),true,false).' name="cronmday[]" value="*" /> '.__('Any (*)','backwpup').'<br />';
        echo '<div id="cron-day">';
        for ($i=1;$i<=31;$i++) {
          echo '<input class="checkbox" type="checkbox"'.checked(in_array("$i",$mday,true),true,false).' name="cronmday[]" value="'.$i.'" /> '.$i.'<br />';
        }
        ?>
        </div>
      </div>
      <div id="cron-month-box">
        <b><?PHP _e('Month:','backwpup'); ?></b><br />
        <?PHP
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("*",$mday,true),true,false).' name="cronmon[]" value="*" /> '.__('Any (*)','backwpup').'<br />';
        echo '<div id="cron-month">';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("1",$mday,true),true,false).' name="cronmon[]" value="1" /> '.__('January','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("2",$mday,true),true,false).' name="cronmon[]" value="2" /> '.__('February','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("3",$mday,true),true,false).' name="cronmon[]" value="3" /> '.__('March','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("4",$mday,true),true,false).' name="cronmon[]" value="4" /> '.__('April','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("5",$mday,true),true,false).' name="cronmon[]" value="5" /> '.__('May','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("6",$mday,true),true,false).' name="cronmon[]" value="6" /> '.__('June','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("7",$mday,true),true,false).' name="cronmon[]" value="7" /> '.__('July','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("8",$mday,true),true,false).' name="cronmon[]" value="8" /> '.__('Augest','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("9",$mday,true),true,false).' name="cronmon[]" value="9" /> '.__('September','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("10",$mday,true),true,false).' name="cronmon[]" value="10" /> '.__('October','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("11",$mday,true),true,false).' name="cronmon[]" value="11" /> '.__('November','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("12",$mday,true),true,false).' name="cronmon[]" value="12" /> '.__('December','backwpup').'<br />';
        ?>
        </div>
      </div>
      <div id="cron-weekday-box">
        <b><?PHP _e('Day of Week:','backwpup'); ?></b><br />
        <?PHP
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("*",$wday,true),true,false).' name="cronwday[]" value="*" /> '.__('Any (*)','backwpup').'<br />';
        echo '<div id="cron-weekday">';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("0",$wday,true),true,false).' name="cronwday[]" value="0" /> '.__('Sunday','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("1",$wday,true),true,false).' name="cronwday[]" value="1" /> '.__('Monday','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("2",$wday,true),true,false).' name="cronwday[]" value="2" /> '.__('Tuesday','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("3",$wday,true),true,false).' name="cronwday[]" value="3" /> '.__('Wednesday','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("4",$wday,true),true,false).' name="cronwday[]" value="4" /> '.__('Thursday','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("5",$wday,true),true,false).' name="cronwday[]" value="5" /> '.__('Friday','backwpup').'<br />';
        echo '<input class="checkbox" type="checkbox"'.checked(in_array("6",$wday,true),true,false).' name="cronwday[]" value="6" /> '.__('Saturday','backwpup').'<br />';
        ?>
        </div>
      </div>
      <br class="clear" />
    </div>
    <div id="schedbasic" <?PHP if ($jobvalue['cronselect']!='basic') echo 'style="display:none;"';?>>
      <table>
      <tr>
      <th>
      <?PHP _e('Type','backwpup')   ?>
      </th>
      <th>
      </th>
      <th>
      <?PHP _e('Hour','backwpup')   ?>
      </th>
      <th>
      <?PHP _e('Minute','backwpup')   ?>
      </th>
      </tr>
      <tr>
      <td><?PHP echo '<input class="radio" type="radio"'.checked(true,is_numeric($mday[0]),false).' name="cronbtype" value="mon" />'.__('monthly','backwpup'); ?></td>
      <td><select name="moncronmday"><?PHP for ($i=1;$i<=31;$i++) {echo '<option '.selected(in_array("$i",$mday,true),true,false).'  value="'.$i.'" />'.__('on','backwpup').' '.$i.'.</option>';} ?></select></td>
      <td><select name="moncronhours"><?PHP for ($i=0;$i<24;$i++) {echo '<option '.selected(in_array("$i",$hours,true),true,false).'  value="'.$i.'" />'.$i.'</option>';} ?></select></td>
      <td><select name="moncronminutes"><?PHP for ($i=0;$i<60;$i=$i+5) {echo '<option '.selected(in_array("$i",$minutes,true),true,false).'  value="'.$i.'" />'.$i.'</option>';} ?></select></td>
      </tr>
      <tr>
      <td><?PHP echo '<input class="radio" type="radio"'.checked(true,is_numeric($wday[0]),false).' name="cronbtype" value="week" />'.__('weekly','backwpup'); ?></td>
      <td><select name="weekcronwday">
          <?PHP   echo '<option '.selected(in_array("0",$wday,true),true,false).'  value="0" />'.__('Sunday','backwpup').'</option>';
              echo '<option '.selected(in_array("1",$wday,true),true,false).'  value="1" />'.__('Monday','backwpup').'</option>';
              echo '<option '.selected(in_array("2",$wday,true),true,false).'  value="2" />'.__('Tuesday','backwpup').'</option>';
              echo '<option '.selected(in_array("3",$wday,true),true,false).'  value="3" />'.__('Wednesday','backwpup').'</option>';
              echo '<option '.selected(in_array("4",$wday,true),true,false).'  value="4" />'.__('Thursday','backwpup').'</option>';
              echo '<option '.selected(in_array("5",$wday,true),true,false).'  value="5" />'.__('Friday','backwpup').'</option>';
              echo '<option '.selected(in_array("6",$wday,true),true,false).'  value="6" />'.__('Saturday','backwpup').'</option>'; ?>
        </select></td>
      <td><select name="weekcronhours"><?PHP for ($i=0;$i<24;$i++) {echo '<option '.selected(in_array("$i",$hours,true),true,false).'  value="'.$i.'" />'.$i.'</option>';} ?></select></td>
      <td><select name="weekcronminutes"><?PHP for ($i=0;$i<60;$i=$i+5) {echo '<option '.selected(in_array("$i",$minutes,true),true,false).'  value="'.$i.'" />'.$i.'</option>';} ?></select></td>
      </tr>
      <tr>
      <td><?PHP echo '<input class="radio" type="radio"'.checked("**",$mday[0].$wday[0],false).' name="cronbtype" value="day" />'.__('daily','backwpup'); ?></td>
      <td></td>
      <td><select name="daycronhours"><?PHP for ($i=0;$i<24;$i++) {echo '<option '.selected(in_array("$i",$hours,true),true,false).'  value="'.$i.'" />'.$i.'</option>';} ?></select></td>
      <td><select name="daycronminutes"><?PHP for ($i=0;$i<60;$i=$i+5) {echo '<option '.selected(in_array("$i",$minutes,true),true,false).'  value="'.$i.'" />'.$i.'</option>';} ?></select></td>
      </tr>
      <tr>
      <td><?PHP echo '<input class="radio" type="radio"'.checked("*",$hours[0],false,false).' name="cronbtype" value="hour" />'.__('hourly','backwpup'); ?></td>
      <td></td>
      <td></td>
      <td><select name="hourcronminutes"><?PHP for ($i=0;$i<60;$i=$i+5) {echo '<option '.selected(in_array("$i",$minutes,true),true,false).'  value="'.$i.'" />'.$i.'</option>';} ?></select></td>
      </tr>
      </table>
    </div>
    <?PHP
}

function backwpup_jobedit_metabox_destfolder($jobvalue) {
  ?>
  <b><?PHP _e('Full Path to folder for Backup Files:','backwpup'); ?></b><br />
  <input name="backupdir" id="backupdir" type="text" value="<?PHP echo $jobvalue['backupdir'];?>" class="large-text" /><br />
  <span class="description"><?PHP _e('A sampel Folder is:','backwpup'); echo ' '.trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) ) . trailingslashit( sanitize_file_name( get_bloginfo( 'name' ) ) );?></span><br />&nbsp;<br />
  <?PHP _e('Max. backup files in folder:','backwpup'); ?> <input name="maxbackups" id="maxbackups" type="text" size="3" value="<?PHP echo $jobvalue['maxbackups'];?>" class="small-text" /><span class="description"><?PHP _e('(Oldest files will deleted first.)','backwpup');?></span>
  <?PHP
}

function backwpup_jobedit_metabox_destftp($jobvalue) {
  ?>
  <b><?PHP _e('Hostname:','backwpup'); ?></b><br />
  <input name="ftphost" type="text" value="<?PHP echo $jobvalue['ftphost'];?>" class="large-text" /><br />
  <b><?PHP _e('Port:','backwpup'); ?></b><br />
  <input name="ftphostport" type="text" value="<?PHP echo $jobvalue['ftphostport'];?>" class="small-text" /><br />
  <b><?PHP _e('Username:','backwpup'); ?></b><br />
  <input name="ftpuser" type="text" value="<?PHP echo $jobvalue['ftpuser'];?>" class="user large-text" autocomplete="off" /><br />
  <b><?PHP _e('Password:','backwpup'); ?></b><br />
  <input name="ftppass" type="password" value="<?PHP echo backwpup_base64($jobvalue['ftppass']);?>" class="password large-text" autocomplete="off" /><br />
  <b><?PHP _e('Folder on Server:','backwpup'); ?></b><br />
  <input name="ftpdir" type="text" value="<?PHP echo $jobvalue['ftpdir'];?>" class="large-text" /><br />
  <?PHP if (!is_numeric($jobvalue['ftpmaxbackups'])) $jobvalue['ftpmaxbackups']=0; ?>
  <?PHP _e('Max. backup files in FTP folder:','backwpup'); ?> <input name="ftpmaxbackups" type="text" size="3" value="<?PHP echo $jobvalue['ftpmaxbackups'];?>" class="small-text" /><span class="description"><?PHP _e('(Oldest files will be deleted first.)','backwpup');?></span><br />
  <input class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['ftpssl'],true); ?> name="ftpssl" /> <?PHP _e('Use SSL-FTP Connection.','backwpup'); ?><br />
  <input class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['ftppasv'],true); ?> name="ftppasv" /> <?PHP _e('Use FTP Passive mode.','backwpup'); ?><br />
  <?PHP
}

function backwpup_jobedit_metabox_dests3($jobvalue) {
  ?>
  <div class="dests">
    <b><?PHP _e('Access Key ID:','backwpup'); ?></b>
    <input id="awsAccessKey" name="awsAccessKey" type="text" value="<?PHP echo $jobvalue['awsAccessKey'];?>" class="large-text" /><br />
    <b><?PHP _e('Secret Access Key:','backwpup'); ?></b><br />
    <input id="awsSecretKey" name="awsSecretKey" type="password" value="<?PHP echo $jobvalue['awsSecretKey'];?>" class="large-text" /><br />
    <b><?PHP _e('Bucket:','backwpup'); ?></b><br />
    <input id="awsBucketselected" name="awsBucketselected" type="hidden" value="<?PHP echo $jobvalue['awsBucket'];?>" />
    <?PHP if (!empty($jobvalue['awsAccessKey']) and !empty($jobvalue['awsSecretKey'])) backwpup_get_aws_buckets(array('awsAccessKey'=>$jobvalue['awsAccessKey'],'awsSecretKey'=>$jobvalue['awsSecretKey'],'awsselected'=>$jobvalue['awsBucket'])); ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?PHP _e('Create bucket:','backwpup'); ?><input name="newawsBucket" type="text" value="" class="text" /> <select name="awsRegion" title="<?php _e('Bucket Region', 'backwpup'); ?>"><option value="s3.amazonaws.com"><?php _e('US-Standard (Northern Virginia & Washington State)', 'backwpup'); ?></option><option value="s3-us-west-1.amazonaws.com"><?php _e('US-West 1 (Northern California)', 'backwpup'); ?></option><option value="s3-us-west-2.amazonaws.com"><?php _e('US-West 2 (Oregon)', 'backwpup'); ?></option><option value="s3-eu-west-1.amazonaws.com"><?php _e('EU (Ireland)', 'backwpup'); ?></option><option value="s3-ap-southeast-1.amazonaws.com"><?php _e('Asia Pacific (Singapore)', 'backwpup'); ?></option><option value="s3-ap-northeast-1.amazonaws.com"><?php _e('Asia Pacific (Japan)', 'backwpup'); ?></option><option value="s3-sa-east-1.amazonaws.com"><?php _e('South America (Sao Paulo)', 'backwpup'); ?></option><option value="s3-us-gov-west-1.amazonaws.com"><?php _e('United States GovCloud', 'backwpup'); ?></option><option value="s3-fips-us-gov-west-1.amazonaws.com"><?php _e('United States GovCloud FIPS 140-2', 'backwpup'); ?></option></select><br />
    <b><?PHP _e('Folder in bucket:','backwpup'); ?></b><br />
    <input name="awsdir" type="text" value="<?PHP echo $jobvalue['awsdir'];?>" class="large-text" /><br />
    <?PHP _e('Max. backup files in bucket folder:','backwpup'); ?><input name="awsmaxbackups" type="text" size="3" value="<?PHP echo $jobvalue['awsmaxbackups'];?>" class="small-text" /><span class="description"><?PHP _e('(Oldest files will be deleted first.)','backwpup');?></span><br />
    <input class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['awsrrs'],true); ?> name="awsrrs" /> <?PHP _e('Save Backups with reduced redundancy!','backwpup'); ?><br />
  </div>
  <div class="destlinks">
    <a href="http://www.amazon.de/gp/redirect.html?ie=UTF8&location=http%3A%2F%2Fwww.amazon.com%2Fgp%2Faws%2Fregistration%2Fregistration-form.html&site-redirect=de&tag=hueskennet-21&linkCode=ur2&camp=1638&creative=6742" target="_blank"><?PHP _e('Create Account','backwpup'); ?></a><br />
    <a href="http://aws-portal.amazon.com/gp/aws/developer/account/index.html?action=access-key" target="_blank"><?PHP _e('Find Keys','backwpup'); ?></a><br />
    <a href="https://console.aws.amazon.com/s3/home" target="_blank"><?PHP _e('Webinterface','backwpup'); ?></a><br />
  </div>
  <br class="clear" />
  <?PHP
}

function backwpup_jobedit_metabox_destgstorage($jobvalue) {
  ?>
  <div class="dests">
    <b><?PHP _e('Access Key:','backwpup'); ?></b><br />
    <input id="GStorageAccessKey" name="GStorageAccessKey" type="text" value="<?PHP echo $jobvalue['GStorageAccessKey'];?>" class="large-text" /><br />
    <b><?PHP _e('Secret:','backwpup'); ?></b><br />
    <input id="GStorageSecret" name="GStorageSecret" type="password" value="<?PHP echo $jobvalue['GStorageSecret'];?>" class="large-text" /><br />
    <b><?PHP _e('Bucket:','backwpup'); ?></b><br />
    <input id="GStorageselected" name="GStorageselected" type="hidden" value="<?PHP echo $jobvalue['GStorageBucket'];?>" />
    <?PHP if (!empty($jobvalue['GStorageAccessKey']) and !empty($jobvalue['GStorageSecret'])) backwpup_get_gstorage_buckets(array('GStorageAccessKey'=>$jobvalue['GStorageAccessKey'],'GStorageSecret'=>$jobvalue['GStorageSecret'],'GStorageselected'=>$jobvalue['GStorageBucket'])); ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?PHP _e('Create bucket:','backwpup'); ?><input name="newGStorageBucket" type="text" value="" class="text" /><br />
    <b><?PHP _e('Folder in bucket:','backwpup'); ?></b><br />
    <input name="GStoragedir" type="text" value="<?PHP echo $jobvalue['GStoragedir'];?>" class="large-text" /><br />
    <?PHP _e('Max. backup files in bucket folder:','backwpup'); ?><input name="GStoragemaxbackups" type="text" size="3" value="<?PHP echo $jobvalue['GStoragemaxbackups'];?>" class="small-text" /><span class="description"><?PHP _e('(Oldest files will be deleted first.)','backwpup');?></span><br />
  </div>
  <div class="destlinks">
    <a href="http://code.google.com/apis/storage/docs/signup.html" target="_blank"><?PHP _e('Create Account','backwpup'); ?></a><br />
    <a href="https://code.google.com/apis/console/" target="_blank"><?PHP _e('Find Keys','backwpup'); ?></a><br />
    <a href="https://storage.cloud.google.com/" target="_blank"><?PHP _e('Webinterface','backwpup'); ?></a><br />
  </div>
  <br class="clear" />
  <?PHP
}

function backwpup_jobedit_metabox_destazure($jobvalue) {
  ?>
  <div class="dests">

    <b><?PHP _e('Host:','backwpup'); ?></b><br />
    <input id="msazureHost" name="msazureHost" type="text" value="<?PHP echo $jobvalue['msazureHost'];?>" class="large-text" /><span class="description"><?PHP _e('Normely: blob.core.windows.net','backwpup');?></span><br />
    <b><?PHP _e('Account Name:','backwpup'); ?></b><br />
    <input id="msazureAccName" name="msazureAccName" type="text" value="<?PHP echo $jobvalue['msazureAccName'];?>" class="large-text" /><br />
    <b><?PHP _e('Access Key:','backwpup'); ?></b><br />
    <input id="msazureKey" name="msazureKey" type="password" value="<?PHP echo $jobvalue['msazureKey'];?>" class="large-text" /><br />
    <b><?PHP _e('Container:','backwpup'); ?></b><br />
    <input id="msazureContainerselected" name="msazureContainerselected" type="hidden" value="<?PHP echo $jobvalue['msazureContainer'];?>" />
    <?PHP if (!empty($jobvalue['msazureAccName']) and !empty($jobvalue['msazureKey'])) backwpup_get_msazure_container(array('msazureHost'=>$jobvalue['msazureHost'],'msazureAccName'=>$jobvalue['msazureAccName'],'msazureKey'=>$jobvalue['msazureKey'],'msazureselected'=>$jobvalue['msazureContainer'])); ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?PHP _e('Create Container:','backwpup'); ?><input name="newmsazureContainer" type="text" value="" class="text" /> <br />
    <b><?PHP _e('Folder in Container:','backwpup'); ?></b><br />
    <input name="msazuredir" type="text" value="<?PHP echo $jobvalue['msazuredir'];?>" class="large-text" /><br />
    <?PHP _e('Max. backup files in container folder:','backwpup'); ?><input name="msazuremaxbackups" type="text" size="3" value="<?PHP echo $jobvalue['msazuremaxbackups'];?>" class="small-text" /><span class="description"><?PHP _e('(Oldest files will be deleted first.)','backwpup');?></span><br />
  </div>
  <div class="destlinks">
    <a href="http://www.microsoft.com/windowsazure/offers/" target="_blank"><?PHP _e('Create Account','backwpup'); ?></a><br />
    <a href="http://windows.azure.com/" target="_blank"><?PHP _e('Find Key','backwpup'); ?></a><br />
  </div>
  <br class="clear" />
  <?PHP
}

function backwpup_jobedit_metabox_destrsc($jobvalue) {
  ?>
  <div class="dests">
    <b><?PHP _e('Username:','backwpup'); ?></b><br />
    <input id="rscUsername" name="rscUsername" type="text" value="<?PHP echo $jobvalue['rscUsername'];?>" class="large-text" autocomplete="off"/><br />
    <b><?PHP _e('API Key:','backwpup'); ?></b><br />
    <input id="rscAPIKey" name="rscAPIKey" type="text" value="<?PHP echo $jobvalue['rscAPIKey'];?>" class="large-text" autocomplete="off" /><br />
    <b><?PHP _e('Container:','backwpup'); ?></b><br />
    <input id="rscContainerselected" name="rscContainerselected" type="hidden" value="<?PHP echo $jobvalue['rscContainer'];?>" />
    <?PHP if (!empty($jobvalue['rscUsername']) and !empty($jobvalue['rscAPIKey'])) backwpup_get_rsc_container(array('rscUsername'=>$jobvalue['rscUsername'],'rscAPIKey'=>$jobvalue['rscAPIKey'],'rscselected'=>$jobvalue['rscContainer'])); ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?PHP _e('Create Container:','backwpup'); ?><input name="newrscContainer" type="text" value="" class="text" /> <br />
    <b><?PHP _e('Folder in container:','backwpup'); ?></b><br />
    <input name="rscdir" type="text" value="<?PHP echo $jobvalue['rscdir'];?>" class="large-text" /><br />
    <?PHP _e('Max. backup files in container folder:','backwpup'); ?><input name="rscmaxbackups" type="text" size="3" value="<?PHP echo $jobvalue['rscmaxbackups'];?>" class="small-text" /><span class="description"><?PHP _e('(Oldest files will be deleted first.)','backwpup');?></span><br />
  </div>
  <div class="destlinks">
    <a href="http://www.rackspacecloud.com/2073.html" target="_blank"><?PHP _e('Create Account','backwpup'); ?></a><br />
    <a href="https://manage.rackspacecloud.com/APIAccess.do" target="_blank"><?PHP _e('Find Key','backwpup'); ?></a><br />
    <a href="https://manage.rackspacecloud.com/CloudFiles.do" target="_blank"><?PHP _e('Webinterface','backwpup'); ?></a><br />
  </div>
  <br class="clear" />
  <?PHP
}

function backwpup_jobedit_metabox_destdropbox($jobvalue) {
  ?>
  <div class="dests">
    <b><?PHP _e('Login:','backwpup'); ?></b>&nbsp;
    <?PHP if (empty($jobvalue['dropetoken']) and empty($jobvalue['dropesecret'])) { ?>
      <span style="color:red;"><?php _e('Not authenticated!', 'backwpup'); ?></span> <input type="submit" name="dropboxauth" class="button-primary" accesskey="d" value="<?php _e('Authenticate!', 'backwpup'); ?>" /><br />
    <?PHP } else  { ?>
      <span style="color:green;"><?php _e('Authenticated!', 'backwpup'); ?></span> <input type="submit" name="dropboxauthdel" class="button-primary" accesskey="d" value="<?php _e('Delete!', 'backwpup'); ?>" /><br />
    <?PHP } ?><br />
    <b><?PHP _e('Root:','backwpup'); ?></b><br />
    <select name="droperoot" id="droperoot">
    <option <?PHP selected($jobvalue['droperoot'],'dropbox',true); ?> value="dropbox"><?php _e('dropbox', 'backwpup'); ?></option>
    <option <?PHP selected($jobvalue['droperoot'],'sandbox',true); ?> value="sandbox" disabled="disabled"><?php _e('sandbox (disabled by DropBox)', 'backwpup'); ?></option>
    </select><br />
    <b><?PHP _e('Folder:','backwpup'); ?></b><br />
    <input name="dropedir" type="text" value="<?PHP echo $jobvalue['dropedir'];?>" class="user large-text" /><br />
    <?PHP _e('Max. backup files in Dropbox folder:','backwpup'); ?><input name="dropemaxbackups" type="text" size="3" value="<?PHP echo $jobvalue['dropemaxbackups'];?>" class="small-text" /><span class="description"><?PHP _e('(Oldest files will be deleted first.)','backwpup');?></span><br />
  </div>
  <div class="destlinks">
    <a name="dropbox" href="http://db.tt/8irM1vQ0" target="_blank"><?PHP _e('Create Account','backwpup'); ?></a><br />
    <a href="https://www.dropbox.com/" target="_blank"><?PHP _e('Webinterface','backwpup'); ?></a><br />
  </div>
  <br class="clear" />
  <?PHP
}

function backwpup_jobedit_metabox_destsugarsync( $jobvalue ) {
	?>
	<div class="dests">
		<?php if ( ! $jobvalue['sugarrefreshtoken'] ) { ?>
			<b><?php _e( 'E-mail address:', 'backwpup' ); ?></b><br />
			<input id="sugaremail" name="sugaremail" type="text" value="<?php if (isset($_POST['sugaremail'])) echo $_POST['sugaremail'];?>" class="large-text" /><br />
			<b><?php _e( 'Password:', 'backwpup' ); ?></b><br />
			<input id="sugarpass" name="sugarpass" type="password" value="<?php if (isset($_POST['sugarpass'])) echo $_POST['sugarpass'];?>" class="large-text" /><br />
			<br />
			<input type="submit" name="authbutton" class="button-primary" accesskey="d" value="<?php _e( 'Sugarsync authenticate!', 'backwpup' ); ?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="authbutton" class="button" value="<?php _e( 'Create Sugarsync Account', 'backwpup' ); ?>" />
			<br />
		<?php } else { ?>
			<b><?php _e( 'Login:', 'backwpup' ); ?></b>&nbsp;
			<span style="color:green;"><?php _e( 'Authenticated!', 'backwpup' ); ?></span>
			<input type="submit" name="authbutton" class="button-primary" accesskey="d" value="<?php _e( 'Delete Sugarsync authentication!', 'backwpup' ); ?>" />
			<br />
			<b><?php _e( 'Root:', 'backwpup' ); ?></b>
			<?php
			if (!class_exists('SugarSync'))
				include_once(realpath(dirname(__FILE__).'/../libs/sugarsync.php'));
			try {
				$sugarsync   = new SugarSync($jobvalue['sugarrefreshtoken']);
				$user        = $sugarsync->user();
				$syncfolders = $sugarsync->get( $user->syncfolders );
				if ( ! is_object( $syncfolders ) )
					echo '<span style="color:red;">'.__( 'No Syncfolders found!', 'backwpup' ).'</span>';
			} catch ( Exception $e ) {
				echo '<span style="color:red;">'.$e->getMessage().'</span>';
			}
			if ( isset($syncfolders) && is_object( $syncfolders ) ) {
				echo '<select name="sugarroot" id="sugarroot">';
				foreach ( $syncfolders->collection as $roots ) {
					echo "<option " . selected( strtolower($jobvalue['sugarroot'] ), strtolower( $roots->ref ), false ) . " value=\"" . $roots->ref . "\">" . $roots->displayName . "</option>";
				}
				echo '</select>';
			}
			?>
		<?php } ?>

		<br />
		<b><?php _e( 'Folder:', 'backwpup' ); ?></b><br />
		<input name="sugardir" type="text" value="<?php echo $jobvalue['sugardir'];?>" class="large-text" /><br />
		<span class="nosync"><?php _e( 'Max. backup files in folder:', 'backwpup' ); ?>
			<input name="sugarmaxbackups" type="text" size="3" value="<?php echo $jobvalue['sugarmaxbackups'];?>" class="small-text" /><span class="description"><?php _e( '(Oldest files will be deleted first.)', 'backwpup' );?></span><br /></span>
			<br /></span>
	</div>
	<div class="destlinks">
		<a href="http://www.anrdoezrs.net/click-5425765-10671858" target="_blank"><?php _e( 'Create Account', 'backwpup' ); ?></a><br />
		<a href="https://sugarsync.com" target="_blank"><?php _e( 'Webinterface', 'backwpup' ); ?></a><br />
	</div>
	<br class="clear" />
	<?php
}

function backwpup_jobedit_metabox_destmail($jobvalue) {
  ?>
  <b><?PHP _e('E-mail address:','backwpup'); ?></b><br />
  <input name="mailaddress" id="mailaddress" type="text" value="<?PHP echo $jobvalue['mailaddress'];?>" class="large-text" /><br />
  <?PHP if (!is_numeric($jobvalue['mailefilesize'])) $jobvalue['mailefilesize']=0; ?>
  <?PHP echo __('Max. File Size for sending Backups with mail:','backwpup').'<input name="mailefilesize" type="text" value="'.$jobvalue['mailefilesize'].'" class="small-text" />MB<br />';?>
  <?PHP
}

//ever display boxes
function backwpup_jobedit_metabox_displayneeded($hidden) {
	$newhidden=array();
	foreach($hidden as $hiddenid) {
		if (!strstr($hiddenid,'backwpup_jobedit_'))
			$newhidden[]=$hiddenid;
	}
	return $newhidden;
}
add_filter( 'hidden_meta_boxes', 'backwpup_jobedit_metabox_displayneeded' );

//ajax/normal get cron text
function backwpup_get_cron_text($args='') {
  if (is_array($args)) {
    extract($args);
    $ajax=false;
  } else {
    check_ajax_referer('backwpupeditjob_ajax_nonce');
    if (!current_user_can(BACKWPUP_USER_CAPABILITY))
      die('-1');
    if (empty($_POST['cronminutes']) or $_POST['cronminutes'][0]=='*') {
      if (!empty($_POST['cronminutes'][1]))
        $_POST['cronminutes']=array('*/'.$_POST['cronminutes'][1]);
      else
        $_POST['cronminutes']=array('*');
    }
    if (empty($_POST['cronhours']) or $_POST['cronhours'][0]=='*') {
      if (!empty($_POST['cronhours'][1]))
        $_POST['cronhours']=array('*/'.$_POST['cronhours'][1]);
      else
        $_POST['cronhours']=array('*');
    }
    if (empty($_POST['cronmday']) or $_POST['cronmday'][0]=='*') {
      if (!empty($_POST['cronmday'][1]))
        $_POST['cronmday']=array('*/'.$_POST['cronmday'][1]);
      else
        $_POST['cronmday']=array('*');
    }
    if (empty($_POST['cronmon']) or $_POST['cronmon'][0]=='*') {
      if (!empty($_POST['cronmon'][1]))
        $_POST['cronmon']=array('*/'.$_POST['cronmon'][1]);
      else
        $_POST['cronmon']=array('*');
    }
    if (empty($_POST['cronwday']) or $_POST['cronwday'][0]=='*') {
      if (!empty($_POST['cronwday'][1]))
        $_POST['cronwday']=array('*/'.$_POST['cronwday'][1]);
      else
        $_POST['cronwday']=array('*');
    }
    $cronstamp=implode(",",$_POST['cronminutes']).' '.implode(",",$_POST['cronhours']).' '.implode(",",$_POST['cronmday']).' '.implode(",",$_POST['cronmon']).' '.implode(",",$_POST['cronwday']);
    $ajax=true;
  }
  echo '<div id="cron-text">';
  _e('Working as <a href="http://wikipedia.org/wiki/Cron" target="_blank">Cron</a> job schedule:','backwpup'); echo ' <i><b><nobr>'.$cronstamp.'</nobr></b></i><br />';
  list($cronstr['minutes'],$cronstr['hours'],$cronstr['mday'],$cronstr['mon'],$cronstr['wday'])=explode(' ',$cronstamp,5);
  if (false !== strpos($cronstr['minutes'],'*/') or ($cronstr['minutes']=='*')) {
    $repeatmins=str_replace('*/','',$cronstr['minutes']);
    if ($repeatmins=='*' or empty($repeatmins))
      $repeatmins=5;
    echo '<span style="color:red;">'.str_replace('%d',$repeatmins,__('ATTENTION: Job runs every %d mins.!!!','backwpup')).'</span><br />';
  }
  if (false !== strpos($cronstr['hours'],'*/') or ($cronstr['hours']=='*')) {
    $repeathouer=str_replace('*/','',$cronstr['hours']);
    if ($repeathouer=='*' or empty($repeathouer))
      $repeathouer=1;
    echo '<span style="color:red;">'.str_replace('%d',$repeathouer,__('ATTENTION: Job runs every %d hours.!!!','backwpup')).'</span><br />';
  }
  $nextrun=backwpup_cron_next($cronstamp);
  if (2147483647==$nextrun) {
    echo '<span style="color:red;">'.__('ATTENTION: Can\'t calculate cron!!!','backwpup').'</span><br />';
  } else {
    _e('Next runtime:','backwpup'); echo ' <b>'.date_i18n('D, j M Y, H:i',backwpup_cron_next($cronstamp)).'</b>';
  }
  echo "</div>";
  if ($ajax)
    die();
  else
    return;
}

//ajax/normal get buckests select box
function backwpup_get_aws_buckets($args='') {
  if (is_array($args)) {
    extract($args);
    $ajax=false;
  } else {
    check_ajax_referer('backwpupeditjob_ajax_nonce');
    if (!current_user_can(BACKWPUP_USER_CAPABILITY))
      die('-1');
    $awsAccessKey=$_POST['awsAccessKey'];
    $awsSecretKey=$_POST['awsSecretKey'];
    $awsselected=$_POST['awsselected'];
    $ajax=true;
  }
  if (!class_exists('CFRuntime'))
    require_once(dirname(__FILE__).'/../libs/aws/sdk.class.php');
  if (empty($awsAccessKey)) {
    echo '<span id="awsBucket" style="color:red;">'.__('Missing access key!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (empty($awsSecretKey)) {
    echo '<span id="awsBucket" style="color:red;">'.__('Missing secret access key!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  try {
    $s3 = new AmazonS3(array('key'=>$awsAccessKey,'secret'=>$awsSecretKey,'certificate_authority'=>true));
    $buckets=$s3->list_buckets();
  } catch (Exception $e) {
    echo '<span id="awsBucket" style="color:red;">'.$e->getMessage().'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if ($buckets->status<200 or $buckets->status>=300) {
    echo '<span id="awsBucket" style="color:red;">'.$buckets->status.': '.$buckets->body->Message.'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (count($buckets->body->Buckets->Bucket)<1) {
    echo '<span id="awsBucket" style="color:red;">'.__('No bucket found!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  echo '<select name="awsBucket" id="awsBucket">';
  foreach ($buckets->body->Buckets->Bucket as $bucket) {
    echo "<option ".selected(strtolower($awsselected),strtolower($bucket->Name),false).">".$bucket->Name."</option>";
  }
  echo '</select>';
  if ($ajax)
    die();
  else
    return;
}

//ajax/normal get buckests select box
function backwpup_get_gstorage_buckets($args='') {
  if (is_array($args)) {
    extract($args);
    $ajax=false;
  } else {
    check_ajax_referer('backwpupeditjob_ajax_nonce');
    if (!current_user_can(BACKWPUP_USER_CAPABILITY))
      die('-1');
    $GStorageAccessKey=$_POST['GStorageAccessKey'];
    $GStorageSecret=$_POST['GStorageSecret'];
    $GStorageselected=$_POST['GStorageselected'];
    $ajax=true;
  }
  if (!class_exists('CFRuntime'))
    require_once(dirname(__FILE__).'/../libs/aws/sdk.class.php');
  if (empty($GStorageAccessKey)) {
    echo '<span id="GStorageBucket" style="color:red;">'.__('Missing access key!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (empty($GStorageSecret)) {
    echo '<span id="GStorageBucket" style="color:red;">'.__('Missing secret access key!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  try {
    $gstorage = new AmazonS3(array('key'=>$GStorageAccessKey,'secret'=>$GStorageSecret,'certificate_authority'=>true));
    $gstorage->set_hostname('storage.googleapis.com');
    $gstorage->allow_hostname_override(false);
    $buckets=$gstorage->list_buckets();
  } catch (Exception $e) {
    echo '<span id="GStorageBucket" style="color:red;">'.$e->getMessage().'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if ($buckets->status<200 or $buckets->status>=300) {
    echo '<span id="GStorageBucket" style="color:red;">'.$buckets->status.': '.$buckets->body->Message.'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (count($buckets->body->Buckets->Bucket)<1) {
    echo '<span id="GStorageBucket" style="color:red;">'.__('No bucket found!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  echo '<select name="GStorageBucket" id="GStorageBucket">';
  foreach ($buckets->body->Buckets->Bucket as $bucket) {
    echo "<option ".selected(strtolower($GStorageselected),strtolower($bucket->Name),false).">".$bucket->Name."</option>";
  }
  echo '</select>';
  if ($ajax)
    die();
  else
    return;
}

//ajax/normal get Container for RSC select box
function backwpup_get_rsc_container($args='') {
  if (is_array($args)) {
    extract($args);
    $ajax=false;
  } else {
    check_ajax_referer('backwpupeditjob_ajax_nonce');
    if (!current_user_can(BACKWPUP_USER_CAPABILITY))
      die('-1');
    $rscUsername=$_POST['rscUsername'];
    $rscAPIKey=$_POST['rscAPIKey'];
    $rscselected=$_POST['rscselected'];
    $ajax=true;
  }
  if (!class_exists('CF_Authentication'))
    require_once(dirname(__FILE__).'/../libs/rackspace/cloudfiles.php');

  if (empty($rscUsername)) {
    echo '<span id="rscContainer" style="color:red;">'.__('Missing Username!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (empty($rscAPIKey)) {
    echo '<span id="rscContainer" style="color:red;">'.__('Missing API Key!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }

  try {
    $auth = new CF_Authentication($rscUsername, $rscAPIKey);
    $auth->authenticate();
    $conn = new CF_Connection($auth);
    $containers=$conn->get_containers();
  } catch (Exception $e) {
    echo '<span id="rscContainer" style="color:red;">'.$e->getMessage().'</span>';
    if ($ajax)
      die();
    else
      return;
  }

  if (!is_array($containers)) {
    echo '<span id="rscContainer" style="color:red;">'.__('No Containerss found!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  echo '<select name="rscContainer" id="rscContainer">';
  foreach ($containers as $container) {
    echo "<option ".selected(strtolower($rscselected),strtolower($container->name),false).">".$container->name."</option>";
  }
  echo '</select>';
    if ($ajax)
      die();
    else
      return;
}

//ajax/normal get buckests select box
function backwpup_get_msazure_container($args='') {
  if (is_array($args)) {
    extract($args);
    $ajax=false;
  } else {
    check_ajax_referer('backwpupeditjob_ajax_nonce');
    if (!current_user_can(BACKWPUP_USER_CAPABILITY))
      die('-1');
    $msazureHost=$_POST['msazureHost'];
    $msazureAccName=$_POST['msazureAccName'];
    $msazureKey=$_POST['msazureKey'];
    $msazureselected=$_POST['msazureselected'];
    $ajax=true;
  }
  if (!class_exists('Microsoft_WindowsAzure_Storage_Blob'))
    require_once(dirname(__FILE__).'/../libs/Microsoft/WindowsAzure/Storage/Blob.php');
  if (empty($msazureHost)) {
    echo '<span id="msazureContainer" style="color:red;">'.__('Missing Hostname!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (empty($msazureAccName)) {
    echo '<span id="msazureContainer" style="color:red;">'.__('Missing Account Name!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (empty($msazureKey)) {
    echo '<span id="msazureContainer" style="color:red;">'.__('Missing Access Key!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  try {
    $storageClient = new Microsoft_WindowsAzure_Storage_Blob($msazureHost,$msazureAccName,$msazureKey);
    $Containers=$storageClient->listContainers();
  } catch (Exception $e) {
    echo '<span id="msazureContainer" style="color:red;">'.$e->getMessage().'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (empty($Containers)) {
    echo '<span id="msazureContainer" style="color:red;">'.__('No Container found!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  echo '<select name="msazureContainer" id="msazureContainer">';
  foreach ($Containers as $Container) {
    echo "<option ".selected(strtolower($msazureselected),strtolower($Container->Name),false).">".$Container->Name."</option>";
  }
  echo '</select>';
  if ($ajax)
    die();
  else
    return;
}

//ajax/normal get SugarSync roots select box
function backwpup_get_sugarsync_root($args='') {
  if (is_array($args)) {
    extract($args);
    $ajax=false;
  } else {
    check_ajax_referer('backwpupeditjob_ajax_nonce');
    if (!current_user_can(BACKWPUP_USER_CAPABILITY))
      die('-1');
    $sugaruser=$_POST['sugaruser'];
    $sugarpass=$_POST['sugarpass'];
    $sugarrootselected=$_POST['sugarrootselected'];
    $ajax=true;
  }
  if (!class_exists('SugarSync'))
    require_once(dirname(__FILE__).'/../libs/sugarsync.php');

  if (empty($sugaruser)) {
    echo '<span id="sugarroot" style="color:red;">'.__('Missing Username!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  if (empty($sugarpass)) {
    echo '<span id="sugarroot" style="color:red;">'.__('Missing Password!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }

  try {
    $sugarsync = new SugarSync($sugaruser,$sugarpass);
    $user=$sugarsync->user();
    $syncfolders=$sugarsync->get($user->syncfolders);
  } catch (Exception $e) {
    echo '<span id="sugarroot" style="color:red;">'.$e->getMessage().'</span>';
    if ($ajax)
      die();
    else
      return;
  }

  if (!is_object($syncfolders)) {
    echo '<span id="sugarroot" style="color:red;">'.__('No Syncfolders found!','backwpup').'</span>';
    if ($ajax)
      die();
    else
      return;
  }
  echo '<select name="sugarroot" id="sugarroot">';
  foreach ($syncfolders->collection as $roots) {
    echo "<option ".selected(strtolower($sugarrootselected),strtolower($roots->ref),false)." value=\"".$roots->ref."\">".$roots->displayName."</option>";
  }
  echo '</select>';
    if ($ajax)
      die();
    else
      return;
}
//add ajax function
add_action('wp_ajax_backwpup_get_cron_text', 'backwpup_get_cron_text');
add_action('wp_ajax_backwpup_get_aws_buckets', 'backwpup_get_aws_buckets');
add_action('wp_ajax_backwpup_get_gstorage_buckets', 'backwpup_get_gstorage_buckets');
add_action('wp_ajax_backwpup_get_rsc_container', 'backwpup_get_rsc_container');
add_action('wp_ajax_backwpup_get_msazure_container', 'backwpup_get_msazure_container');
add_action('wp_ajax_backwpup_get_sugarsync_root', 'backwpup_get_sugarsync_root');