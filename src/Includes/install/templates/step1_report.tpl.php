<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


/**
 * X-Cart (standalone edition) web installation wizard: Report page
 */

if (!defined('XLITE_INSTALL_MODE')) {
	die('Incorrect call of the script. Stopped.');
}


global $requirements;

if (!empty($requirements) && is_array($requirements)) {

    $report = make_check_report($requirements);

?>

<div id="report-layer" class="report-layer" style="display:none;">

    <div id="report-window" class="report-window">

<a class="report-close" href="#" onclick="javascript: document.getElementById('report-layer').style.display='none'; return false;"><img src="<?php echo $skinsDir; ?>images/spacer.gif" width="10" height="10" border="0" alt="" /><span class="report-close" style="display: none;">close</span></a>


<form method="post" name="report_form" action="https://secure.x-cart.com/service.php">

<input type="hidden" name="target" value="install_feedback_report" />
<input type="hidden" name="product_type" value="XC5" />

<div class="report-title"><?php echo xtr('Technical problems report'); ?></div>

<br />

<textarea name="report" class="report-details form-control" rows="5" cols="70" readonly="readonly"><?php echo $report; ?></textarea>

<br />

<div class="section-title"><?php echo xtr('Email:'); ?></div>

<div style="width:25%">
  <input type="text" name="user_email" class="form-control" value="<?php echo $params['login']; ?>" size="30" />
</div>
<?php echo xtr('user_email_hint'); ?>

<br />
<br />

<div class="section-title"><?php echo xtr('Additional comments'); ?></div>

<textarea name="user_note" class="report-notes form-control" rows="4" cols="70"></textarea>

<br />

<div style="text-align: center;">
    <input type="submit" class="btn btn-warning" value="<?php echo xtr('Send report and get a help'); ?>" onclick="javascript: ga('send', 'event', 'button', 'click', 'send-report');" />
</div>

</form>

</div>

<div class="clear"></div>

</div>

<?php

}
