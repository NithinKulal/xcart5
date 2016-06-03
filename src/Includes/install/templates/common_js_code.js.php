<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/*
 * Output a common Javascript block
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}

?>

<script type="text/javascript">
function visibleBox(id, status)
{
    var Element = document.getElementById(id);
    if (Element) {
        Element.style.display = ((status) ? "" : "none");
    }
}

var visibleBoxId = false;
function setBoxVisible(id)
{
    var box = document.getElementById(id);
    if (box) {
        if (box.style.display == "none") {
            if (visibleBoxId) {
                setBoxVisible(visibleBoxId);
            }
            box.style.display = "";
            visibleBoxId      = id;
        } else {
            box.style.display = "none";
            visibleBoxId      = false;
        }
    }
}

var failedCodes = new Array();
var isDOM = false;
var isDocAll = false;
var isDocW3C = false;
var isOpera = false;
var isOpera5 = false;
var isOpera6 = false;
var isOpera7 = false;
var isMSIE = false;
var isIE = false;
var isNC = false;
var isNC4 = false;
var isNC6 = false;
var isMozilla = false;
var isLayers = false;
isDOM = isDocW3C = (document.getElementById) ? true : false;
isDocAll = (document.all) ? true : false;
isOpera = isOpera5 = window.opera && isDOM;
isOpera6 = isOpera && navigator.userAgent.indexOf("Opera 6") > 0 || navigator.userAgent.indexOf("Opera/6") >= 0;
isOpera7 = isOpera && navigator.userAgent.indexOf("Opera 7") > 0 || navigator.userAgent.indexOf("Opera/7") >= 0;
isMSIE = isIE = document.all && document.all.item && !isOpera;
isNC = navigator.appName=="Netscape";
isNC4 = isNC && !isDOM;
isNC6 = isMozilla = isNC && isDOM;

function getWindowWidth(w)
{
    if ( !w ) w = self;
    if ( isMSIE  ) return w.document.body.clientWidth;
    if ( isNC || isOpera  ) return w.innerWidth;
}

function getWindowHeight(w)
{
    if ( !w ) w = self;
    if ( isMSIE  ) return w.document.body.clientHeight;
    if ( isNC || isOpera  ) return w.innerHeight;
}

function setLeft(elm, x)
{
    if ( isOpera)
    {
        elm.style.pixelLeft = x;
    }
    else if ( isNC4 )
    {
        elm.object.x = x;
    }
    else
    {
        elm.style.left = x;
    }
}

function setTop(elm, y)
{
    if ( isOpera )
    {
        elm.style.pixelTop = y;
    }
    else if ( isNC4 )
    {
        elm.object.y = y;
    }
    else
    {
        elm.style.top = y;
    }
}

function showDetails(code, showCloudBox)
{
    if (code == "" && document.getElementById('test_passed_icon')) {
        document.getElementById('test_passed_icon').style.display = '';

        return;
    }

    failedCodes.push(code);

    var headerElement = document.getElementById('headerElement');
    var detailsElement;

    if (showCloudBox) {
        detailsElement = document.getElementById('status-report-detailsElement');
        document.getElementById('detailsElement').innerHTML = '';
    } else {
        detailsElement = document.getElementById('detailsElement');
        document.getElementById('status-report-detailsElement').innerHTML = '';
    }

    var hiddenElementHeader  = document.getElementById(code + '-error-title');
    var hiddenElementDetails = document.getElementById(code + '-error-text');

    var failedElement = document.getElementById('failed-' + code);
    var failedImageElement = document.getElementById('failed-image-' + code);

    detailsElement.innerHTML = hiddenElementDetails ? hiddenElementDetails.innerHTML : '';
    headerElement.innerHTML = hiddenElementHeader ? hiddenElementHeader.innerHTML : '';
    document.getElementById('suppose-cloud').style.display = showCloudBox ? '' : 'none';

    // failedElement.style.textDecoration = '';
    failedElement.className = 'status-failed-link-active';

    failedImageElement.style.display = 'inline';

    for (var i = 0; i < failedCodes.length; i++) {
        if (failedCodes[i] != code) {
            failedElement = document.getElementById('failed-' + failedCodes[i]);
            if (failedElement) {
                // failedElement.style.textDecoration = 'underline';
                failedElement.className = 'status-failed-link';
            }

            failedImageElement = document.getElementById('failed-image-' + failedCodes[i]);

            if (failedImageElement) {
                failedImageElement.style.display = 'none';
            }

        }
    }

}

function checkEmailAddress(field) {

    var goodEmail = field.value.search(/^(\S+@)[^\.][A-Za-z0-9_\-\.]+(\.[A-Za-z]+)[ ]*$/gi);

    if (0 < field.value.length && goodEmail != -1) {
        return true;
    } else {
        field.className += ' error';
        alert("<?php echo xtr('Please, specify a valid e-mail address!'); ?>");
        field.focus();
        field.select();
        return false;
    }
}

function toggleSection(section) {
    jQuery('.section.section-' + section).each(function() {
       jQuery(this).toggle();
    });
}

jQuery().ready(
    function() {

        // Show clipboard tooltip
        function showClipboardTooltip(elem, success) {
            var alertBox = jQuery('.copy2clipboard-alert', elem.parentElement).get(0);
            var msg = '<?php echo xtr('Copied to clipboard'); ?>';
            if (!success) {
                msg = '<?php echo xtr('Press Ctrl+C to copy selected'); ?>';
            }
            if (0 < jQuery(alertBox).length) {
                alertBox.innerHTML = msg;
                jQuery(alertBox).fadeIn();
            }
            setTimeout(
                function() {
                    jQuery('.copy2clipboard-alert').fadeOut();
                },
                2000
            );
        }

        // Create clipboard
        var clipboard = new Clipboard('.copy2clipboard', {
            text: function(trigger) {
                var result = '';
                var box = jQuery('.permissions-list', trigger.parentElement);
                if (0 < box.length) {
                    result = jQuery(box).html();
                }
                return result;
            }
        });

        // Actions on clipboard success
        clipboard.on('success', function(e) {
            console.info('Trigger:', e.trigger);
            showClipboardTooltip(e.trigger, 1);
            e.clearSelection();
        });

        // Actions on clipboard error
        clipboard.on('error', function(e) {
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);
            showClipboardTooltip(e.trigger);
        });
    }
);

</script>
