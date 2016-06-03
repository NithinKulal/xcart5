<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/*
 * Output a Shows Terms & Conditions page body
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}


if (COPYRIGHT_EXISTS) {

?>

<div id="copyright_notice">

<?php

    ob_start();

    require COPYRIGHT_FILE;

    $tmp = ob_get_contents();

    ob_end_clean();

    echo nl2br(str_replace('  ', '&nbsp;', htmlspecialchars($tmp)));

?>

</div>

<?php

    if (!is_null(get_authcode())) {

?>

<input type="hidden" name="params[force_current]" value="<?php print get_step("check_cfg") ?>" />

<br />

<table align="center">

    <tr>
        <td>
            <span id="auth-code" class="field-label"><?php echo xtr('Auth code'); ?>:</span>
        </td>
        <td>
            <input type="text" name="params[auth_code]" size="10" title="<?php echo xtr('Auth code'); ?>" class="form-control" />
        </td>

        <td class="field-notice">
            <span class="field-notice"><?php echo xtr('Prevents unauthorized use of installation script'); ?></span>
        </td>

    </tr>

</table>

<?php

    } else {

?>

<input type="hidden" name="params[new_installation]" value="<?php print get_step("check_cfg") ?>" />

<?php

    }

?>

<br />

<span class="checkbox-field">
<input type="checkbox" id="agree" name="agree" onclick="javascript:setNextButtonDisabled(!this.checked);" />
<label for="agree"><?php echo xtr('I accept the License Agreement'); ?></label>
<?php if (defined('INSTALL_STARTED')) { ?><img src="//www.x-cart.com/img/spacer1.gif" width="1" height="1" alt="" /><?php } ?>
</span>

<?php

} else {

    $error = true;

?>

    <div class="install_error"><?php echo xtr('Could not find license agreement file.<br />Aborting installation.'); ?></div>

<?php

}

?>
