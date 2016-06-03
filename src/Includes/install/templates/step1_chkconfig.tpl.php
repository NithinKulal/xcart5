<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/*
 * Output a configuration checking page body
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}


function get_lc_config_file_description()
{
    return xtr(
        'lc_config_file_description',
        array(
            ':dir'   => LC_DIR_CONFIG,
            ':file1' => constant('LC_DEFAULT_CONFIG_FILE'),
            ':file2' => constant('LC_CONFIG_FILE'),
        )
    );
}

function get_lc_php_version_description()
{
    return xtr('lc_php_version_description', array(':phpver' => phpversion()));
}

function get_lc_php_disable_functions_description()
{
    return xtr('lc_php_disable_functions_description');
}

function get_lc_php_memory_limit_description()
{
    return xtr('lc_php_memory_limit_description', array(':minval' => constant('LC_PHP_MEMORY_LIMIT_MIN')));
}

function get_lc_php_pdo_mysql_description()
{
    return xtr('lc_php_pdo_mysql_description');
}

function get_lc_file_permissions_description($requirements)
{
    return $requirements['lc_file_permissions']['description'];
}

function get_lc_php_file_uploads_description()
{
    return xtr('lc_php_file_uploads_description');
}

function get_lc_php_upload_max_filesize_description()
{
    return xtr('lc_php_upload_max_filesize_description');
}

function get_lc_php_gdlib_description()
{
    return xtr('lc_php_gdlib_description');
}

function get_lc_php_phar_description()
{
    return xtr('lc_php_phar_description');
}

function get_lc_https_bouncer_description()
{
    return xtr('lc_https_bouncer_description');
}

function get_lc_xml_support_description()
{
    return xtr('lc_xml_support_description');
}

function get_lc_docblocks_support_description()
{
    return xtr('lc_docblocks_support_description');
}

function get_kb_lc_file_permissions_description($requirements)
{
    return xtr('kb_lc_file_permissions_description');
}

function get_kb_lc_php_disable_functions_description()
{
    return xtr('kb_lc_php_disable_functions_description');
}

function get_kb_lc_php_pdo_mysql_description()
{
    return xtr('kb_lc_php_pdo_mysql_description');
}

function get_kb_lc_https_bouncer_description()
{
    return xtr('kb_lc_https_bouncer_description');
}

?>

<div class="requirements-report">

<div class="requirements-list">

<?php

$reqsNotes = array();

// Go through steps list...
foreach ($steps as $stepData) {

    // Index for colouring table rows
    $colorNumber = '1';

?>

    <div class="section-title"><?php echo $stepData['title']; ?></div>

<?php

    // Go through requirements list of current step...
    foreach ($stepData['requirements'] as $reqName) {

        $reqData = $requirements[$reqName];

        $errorsFound = ($errorsFound || (!$reqData['status'] && $reqData['critical']));
        $warningsFound = ($warningsFound || (!$reqData['status'] && !$reqData['critical']));

?>

    <div class="list-row color-<?php echo $colorNumber; ?>">
        <div class="field-left"><?php echo $reqData['title']; ?> ... <?php echo $reqData['value']; ?></div>
        <div class="field-right">
<?php

        echo isset($reqData['skipped']) ? status_skipped() : status($reqData['status'], $reqName);

        if (!$reqData['status']) {

            if (isHardError($reqName)) {
                ga_event('error', 'reqs', $reqName);

            } else {
                ga_event('warning', 'reqs', $reqName);
            }
?>

            <img id="failed-image-<?php echo $reqName; ?>" class="link-expanded" style="display: none;" src="<?php echo $skinsDir; ?>images/arrow_red.png" alt="" />

<?php
        }
?>
        </div>
    </div>

<?php

        $colorNumber = ('2' === $colorNumber) ? '1' : '2';

        // Prepare data for requirement notes displaying
        $label = $reqName . '_description';
        $labelText = null;
        $funcname = 'get_' . $label;

        if (function_exists($funcname)) {
            $labelText = $funcname($requirements);

        } else {

            $labelText = xtr($label);

            if ($label === $labelText) {
                $labelText = null;
            }
        }

        $kbNoteGetter = 'get_kb_' . $label;

        $kbNote = function_exists($kbNoteGetter) ? $kbNoteGetter($requirements) : '';

        if (!is_null($labelText)) {
            $reqsNotes[] = array(
                'reqname' => $reqName,
                'title'   => $stepData['error_msg'],
                'text'    => $labelText,
                'kb_note' => $kbNote,
            );
        }

    } // foreach ($stepData['requirements']...

} // foreach ($steps...

?>


</div>

<div class="requirements-notes">

<div id="headerElement"></div>

<div id="status-report" class="status-report-box" style="display: none;">

    <div id="status-report-detailsElement"></div>

    <div id="detailsElement"></div>

    <div class="status-report-box-text">
        <?php echo xtr('requirements_failed_text'); ?>
    </div>

    <input id="re-check-button" name="try_again" type="button" class="btn btn-default" value="<?php echo xtr('Re-check'); ?>" onclick="javascript:document.ifrm.go_back.value='2'; document.ifrm.current.value='2'; ga('send', 'event', 'button', 'click', 'try'); document.ifrm.submit();" />

    <input type="button" class="btn btn-warning" value="<?php echo xtr('Send a report'); ?>" onclick="javascript: document.getElementById('report-layer').style.display = 'block'; ga('send', 'event', 'button', 'click', 'send report popup');" />

</div>

<?php

x_display_help_block();

foreach ($reqsNotes as $reqNote) {

?>

    <div id="<?php echo $reqNote['reqname']; ?>" style="display: none">
        <div id="<?php echo $reqNote['reqname']; ?>-error-title"><div class="error-title <?php echo $reqNote['reqname']; ?>"><?php echo $reqNote['title']; ?></div></div>
        <div id="<?php echo $reqNote['reqname']; ?>-error-text">
            <div class="error-text <?php echo $reqNote['reqname']; ?>"><?php echo $reqNote['text']; ?></div>
            <?php if($reqNote['kb_note']): ?>
            <div class="error-text kb-note"><?php echo $reqNote['kb_note']; ?></div>
            <?php endif; ?>
        </div>
    </div>

<?php

}

?>

<div class="requirements-success" style="display: none;" id="test_passed_icon">
   <img class="requirements-success-image" src="<?php echo $skinsDir; ?>images/passed_icon.png" border="0" alt="" />
   <br />
   <?php echo xtr('Passed'); ?>
</div>

</div>

<div class="clear"></div>

</div>


<script type="text/javascript">
    var first_code = '<?php echo ($first_error) ? $first_error : ''; ?>';
    showDetails(first_code, <?php echo isHardError($first_error) ? 'true' : 'false'; ?>);
</script>

<?php

    if (!$requirements['lc_file_permissions']['status']) {

?>

<P>
<?php $requirements['lc_file_permissions']['description'] ?>
</P>

<?php

    }

	// Save report to file if errors found
	if ($errorsFound || $warningsFound) {

?>

        <script type="text/javascript">visibleBox("status-report", true);</script>

<?php

	}

    if (false && !$errorsFound && $warningsFound) {

?>

<div class="requirements-warning-text"><?php echo xtr('requirement_warning_text'); ?></div>

<span class="checkbox-field">
    <input type="checkbox" id="continue" onclick="javascript: setNextButtonDisabled(!this.checked);" />
    <label for="continue"><?php echo xtr('Yes, I want to continue the installation.'); ?></label>
</span>

<?php
    }
?>
