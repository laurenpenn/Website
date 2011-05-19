<?php
if(!defined('OSTCLIENTINC')) die('Kwaheri rafiki!'); //Say bye to our friend..

$info=($_POST && $errors)?Format::input($_POST):array(); //on error...use the post data
?>
<div>
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="center" id="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p id="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div>Please fill in the form below to open a new ticket.</div><br>
<form action="open.php" method="POST" enctype="multipart/form-data">
<table align="left" cellpadding=2 cellspacing=1 width="90%">
    <tr>
        <th width="20%">Full Name:</th>
        <td>
            <?if ($thisclient && ($name=$thisclient->getName())) {
                ?>
                <input type="hidden" name="name" value="<?=$name?>"><?=$name?>
            <?}else {?>
                <input type="text" name="name" size="25" value="<?=$info['name']?>">
	        <?}?>
            &nbsp;<font class="error">*&nbsp;<?=$errors['name']?></font>
        </td>
    </tr>
    <tr>
        <th nowrap >Email Address:</th>
        <td>
            <?if ($thisclient && ($email=$thisclient->getEmail())) {
                ?>
                <input type="hidden" name="email" size="25" value="<?=$email?>"><?=$email?>
            <?}else {?>             
                <input type="text" name="email" size="25" value="<?=$info['email']?>">
            <?}?>
            &nbsp;<font class="error">*&nbsp;<?=$errors['email']?></font>
        </td>
    </tr>
    <tr>
        <td>Telephone:</td>
        <td><input type="text" name="phone" size="25" value="<?=$info['phone']?>">
             &nbsp;Ext&nbsp;<input type="text" name="phone_ext" size="6" value="<?=$info['phone_ext']?>">
            &nbsp;<font class="error">&nbsp;<?=$errors['phone']?></font></td>
    </tr>
    <tr height=2px><td align="left" colspan=2 >&nbsp;</td</tr>
    <tr>
        <th>Help Topic:</th>
        <td>
            <select name="topicId">
                <option value="" selected >Select One</option>
                <?
                 $services= db_query('SELECT topic_id,topic FROM '.TOPIC_TABLE.' WHERE isactive=1 ORDER BY topic');
                 if($services && db_num_rows($services)) {
                     while (list($topicId,$topic) = db_fetch_row($services)){
                        $selected = ($info['topicId']==$topicId)?'selected':''; ?>
                        <option value="<?=$topicId?>"<?=$selected?>><?=$topic?></option>
                        <?
                     }
                 }else{?>
                    <option value="0" >General Inquiry</option>
                <?}?>
            </select>
            &nbsp;<font class="error">*&nbsp;<?=$errors['topicId']?></font>
        </td>
    </tr>
    <tr>
        <th>Subject:</th>
        <td>
            <input type="text" name="subject" size="35" value="<?=$info['subject']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['subject']?></font>
        </td>
    </tr>
    <tr>
        <th valign="top">Message:</th>
        <td>
            <? if($errors['message']) {?> <font class="error"><b>&nbsp;<?=$errors['message']?></b></font><br/><?}?>
            <textarea name="message" cols="35" rows="8" wrap="soft" style="width:85%"><?=$info['message']?></textarea></td>
    </tr>
    <?
    if($cfg->allowPriorityChange() ) {
      $sql='SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE.' WHERE ispublic=1 ORDER BY priority_urgency DESC';
      if(($priorities=db_query($sql)) && db_num_rows($priorities)){ ?>
      <tr>
        <td>Priority:</td>
        <td>
            <select name="pri">
              <?
                $info['pri']=$info['pri']?$info['pri']:$cfg->getDefaultPriorityId(); //use system's default priority.
                while($row=db_fetch_array($priorities)){ ?>
                    <option value="<?=$row['priority_id']?>" <?=$info['pri']==$row['priority_id']?'selected':''?> ><?=$row['priority_desc']?></option>
              <?}?>
            </select>
        </td>
       </tr>
    <? }
    }?>

    <?if(($cfg->allowOnlineAttachments() && !$cfg->allowAttachmentsOnlogin())  
                || ($cfg->allowAttachmentsOnlogin() && ($thisclient && $thisclient->isValid()))){
        
        ?>
    <tr>
        <td>Attachment:</td>
        <td>
            <input type="file" name="attachment"><font class="error">&nbsp;<?=$errors['attachment']?></font>
        </td>
    </tr>
    <?}?>
    <?if($cfg && $cfg->enableCaptcha() && (!$thisclient || !$thisclient->isValid())) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']='Please re-enter the text again';
        ?>
    <tr>
        <th valign="top">Captcha Text:</th>
        <td><img src="captcha.php" border="0" align="left">
        <span>&nbsp;&nbsp;<input type="text" name="captcha" size="7" value="">&nbsp;<i>Enter the text shown on the image.</i></span><br/>
                <font class="error">&nbsp;<?=$errors['captcha']?></font>
        </td>
    </tr>
    <?}?>
    <tr height=2px><td align="left" colspan=2 >&nbsp;</td</tr>
    <tr>
        <td></td>
        <td>
            <input class="button" type="submit" name="submit_x" value="Submit Ticket">
            <input class="button" type="reset" value="Reset">
            <input class="button" type="button" name="cancel" value="Cancel" onClick='window.location.href="index.php"'>    
        </td>
    </tr>
</table>
</form>
