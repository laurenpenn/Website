<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
require('client.inc.php');
//We are only showing landing page to users who are not logged in.
if($thisclient && is_object($thisclient) && $thisclient->isValid()) {
    require('tickets.php');
    exit;
}


require(CLIENTINC_DIR.'header.inc.php');
?>
<div id="index">
<h1>Welcome to the support center</h1>
<p class="big">In order to streamline support requests and better serve you, we utilize a support ticket system. Every support request is assigned a unique ticket number which you can use to track the progress and responses online. For your reference we provide complete archives and history of all your support requests. A valid email address is required.</p>
<hr />
<br />
<div class="lcol">
  <img src="./images/new_ticket_icon.jpg" width="48" height="48" align="left" style="padding-bottom:150px;">
  <h3>Open A New Ticket</h3>
  Please provide as much detail as possible so we can best assist you. To update a previously submitted ticket, please use the form to the right.
  <br /><br />
  <form method="link" action="open.php">
  <input type="submit" class="button2" value="Open New Ticket">
  </form>
</div>
<div class="rcol">
  <img src="./images/ticket_status_icon.jpg" width="48" height="48" align="left" style="padding-bottom:150px;">
  <h3>Check Ticket Status</h3>We provide archives and history of all your support requests complete with responses.
  <br /><br />
  <form class="status_form" action="login.php" method="post">
    <fieldset>
      <label>Email:</label>
      <input type="text" name="lemail">
    </fieldset>
    <fieldset>
     <label>Ticket#:</label>
     <input type="text" name="lticket">
    </fieldset>
    <fieldset>
        <label>&nbsp;</label>
         <input type="submit" class="button2" value="Check Status">
    </fieldset>
  </form>
</div>
<div class="clear"></div>
<br />
</div>
<br />
<?require(CLIENTINC_DIR.'footer.inc.php'); ?>
