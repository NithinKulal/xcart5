<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


/**
 * X-Cart (standalone edition) web installation wizard
 */


define ('XLITE_INSTALL_MODE', 1);
define('LC_DO_NOT_REBUILD_CACHE', true);

if (version_compare(phpversion(), '5.4.0') < 0) {
    die('X-Cart cannot start on PHP version earlier than 5.4.0 (' . phpversion(). ' is currently used)');
}

if (empty($_COOKIE['xcInstallStarted']) || !empty($_COOKIE['xcInstallComplete'])) {
    define('INSTALL_STARTED', 1);
?>

<script type="text/javascript">
   document.cookie = 'xcInstallStarted=1';
   document.cookie = 'xcInstallComplete=; expires=-1';
</script>

<?php
}

$filesToInclude = array(
    'init.php',  // Installation initialization
    'install.php', // Installation functions
    'templates/common_html.php', // Installation common html blocks functions
);

foreach ($filesToInclude as $_file) {

    if (!file_exists($includeFuncsFile = __DIR__ . '/' . $_file)) {
        die('Fatal error: Couldn\'t find file ' . $includeFuncsFile);
    }

    include_once $includeFuncsFile;
}


// Installation modules (steps)
$modules = array (
	array( // 0
			"name"          => 'default',
			"comment"       => 'License agreement',
            "auth_required" => false,
			"js_back"       => 0,
			"js_next"       => 1,
            "remove_params" => array(
                'auth_code',
                'new_installation',
                'force_current',
                'start_at'
            )
        ),
	array( // 1
			"name"          => 'cfg_create_admin',
			"comment"       => 'Creating administrator account',
            "auth_required" => true,
			"js_back"       => 0,
            "js_next"       => 1,
            "remove_params" => array(
                'login',
                'password',
            ),
		),
	array( // 2
			"name"          => 'check_cfg',
			"comment"       => 'Environment checking',
            "auth_required" => true,
			"js_back"       => 0,
			"js_next"       => 0,
            "remove_params" => array(
                'xlite_http_host',
                'xlite_https_host',
                'xlite_web_dir',
                'mysqlhost',
                'mysqlbase',
                'mysqluser',
                'mysqlpass',
                'mysqlport',
                'mysqlsock',
            )
		),
	array( // 3
			"name"          => 'cfg_install_db',
			"comment"       => 'Configuring X-Cart',
            "auth_required" => true,
			"js_back"       => 1,
			"js_next"       => 1,
		    "remove_params" => array(
                'demo',
                'install_data',
                'images_to_fs'
            )
        ),
	array( // 4
			"name"          => 'install_dirs',
			"comment"       => 'Setting up directories',
            "auth_required" => true,
			"js_back"       => 0,
			"js_next"       => 0,
            "remove_params" => array()
        ),
	array( // 5
			"name"          => 'install_cache',
			"comment"       => 'Building cache',
            "auth_required" => true,
			"js_back"       => 0,
			"js_next"       => 0,
            "remove_params" => array()
		),
	array( // 6
			"name"          => 'install_done',
			"comment"       => 'Installation complete',
            "auth_required" => true,
			"js_back"       => 0,
			"js_next"       => 0,
            "remove_params" => array()
		)
);

/*
 * Process service requests
 */
if (isset($_GET['target']) && $_GET['target'] == 'install') {

    // Creating dirs action
    if (isset($_GET['action']) && $_GET['action'] == 'dirs') {

        $result = true;

        show_install_html_header();
        show_install_css();
?>

<script type="text/javascript">
    loaded = false;

    function refresh() {
        window.scroll(0, 100000);

        if (loaded == false)
           setTimeout('refresh()', 1000);
    }

    setTimeout('refresh()', 1000);
</script>

<body style="padding-top: 20px;">

<?php

        echo str_repeat(' ', 1000); flush();
        $result = doInstallDirs();

?>

<script type="text/javascript">
    loaded = true;
</script>

<div id="finish"></div>

</body>
</html>

<?php
        exit();

    }
}

// First error flag
$first_error = null;

// Error flag
$error = false;

// Check copyright file
define('COPYRIGHT_FILE', './LICENSE.txt');
define('COPYRIGHT_EXISTS', @file_exists(COPYRIGHT_FILE));

$current = 0;
$params = array();

if (COPYRIGHT_EXISTS) {
    $current = (isset($_POST['current']) ? intval($_POST['current']) : 0);
    $params = (isset($_POST['params']) && is_array($_POST['params']) ? $_POST['params'] : array());
}

$mysqlVersion = !empty($params['mysqlVersion']) ? $params['mysqlVersion'] : null;

// Process 'Go back' action: remove params
if (isset($_POST['go_back']) && $_POST['go_back'] === '1') {

    for ($i = $current; $i < count($modules); $i++) {

        for ($j = 0; $j < count($modules[$i]['remove_params']); $j++) {

            if(isset($params[$modules[$i]['remove_params'][$j]])) {
                unset($params[$modules[$i]['remove_params'][$j]]);
            }
        }
    }
}

// Force current step processing
if (isset($params['force_current']) && !isset($params['start_at']) ) {
    $params['start_at'] = $params['force_current'];
}

if (isset($params['force_current']) && $params['force_current'] == get_step('check_cfg')) {
	$params['new_installation'] = $params['force_current'];
	unset($params['force_current']);
}

if ($current < 0 || $current >= count($modules)) {
	die(xtr('Fatal error: Invalid current step. Stopped.'));
}

// check for the pre- and post- methods

if ($current) {

    if (isset($modules[$current - 1]['post_func'])) {

		check_authcode($params);
        $func = 'module_' . $modules[$current - 1]['name'] . '_post_func';

        if (function_exists($func)) {
            $func();

        } else {
            die(xtr('Internal error: function :funcname() not found', array(':funcname' => $func)));
        }
    }
}

// should the current be set here?
if (isset($params['force_current']) && (isset($_POST['go_back']) && $_POST['go_back'] === '0') ) {
	$current = $params['force_current'];
	check_authcode($params);
	unset($params['force_current']);
}

$skinsDir = 'skins/admin/';

// start html output

show_install_html_header();

show_install_css();

?>

<script src="skins/common/js/jquery.min.js"></script>
<script src="skins/common/bootstrap/js/bootstrap.min.js"></script>
<script src="skins/common/js/clipboard.min.js"></script>

<?php

include LC_DIR_ROOT . 'Includes/install/templates/common_js_code.js.php';

?>

  <script type="text/javascript">

<?php


// show module's pertinent scripts

// 'back' button's script
switch ($modules[$current]['js_back']) {
	case 0:
		default_js_back();
		break;
	case 1:
		$func = 'module_' . $modules[$current]['name'] . '_js_back';
		$func();
		break;
	default:
		die('Fatal error: Invalid js_back value for module ' . $modules[$current]['name']);
}

// 'next' button's script
switch ($modules[$current]['js_next']) {
	case 0:
		default_js_next();
		break;
	case 1:
		$func = 'module_' . $modules[$current]['name'] . '_js_next';
		$func();
		break;
	default:
}

/**
 * Generate an array for displaying installation steps
 */

$rows = array();
$currentStepTitle = '';

foreach ($modules as $id => $moduleData) {

    $index = $id + 1;
    $currentIndex = $current + 1;

    $divIndex = null;
    $stepTitle = (string)$index;

    $row = array();

    $row[] = 'step-row';

    if ($currentIndex > $index) {
        $arrowClass = 'prev-prev';

    } elseif ($currentIndex == $index) {
        $arrowClass = 'prev-next';
        $stepTitle = $currentStepTitle = xtr('Step :step', array(':step' => $index)) . ': ' . xtr($moduleData['comment']);
        $currentStepTitle = str_replace("'", "\\'", $currentStepTitle);

    } else {
        $row[] = 'next';
        $arrowClass = 'next-next';
    }

    if ($index == 1) {
        $row[] = 'first';

    } elseif ($index == count($modules)) {
        $row[] = 'last';
    }

    $rows[] = sprintf('<li class="%s">%s</li>', implode(' ', $row), $stepTitle);

    if ($index < count($modules)) {
        $rows[] = sprintf('<li class="step-row %s"></li>', $arrowClass);
    }
}

?>

function setNextButtonDisabled(flag, autoSubmit)
{
    if (flag) {
        document.getElementById('next-button').disabled = 'disabled';

    } else {
        document.getElementById('next-button').disabled = '';
        if (autoSubmit) {
            document.getElementById('install-form').submit();
        }
    }
}

function resetCacheWindowContent()
{
	setNextButtonDisabled(false, true);
	var content = '<div class="cach-built-message">Cache has been successfully built. Please proceed to the next step.</div>';
	document.getElementById('process_iframe').contentWindow.document.write(content);
}

function processCacheRebuildFailure(stepData)
{
    document.getElementById('cache-rebuild-failed').style.display = '';
    document.getElementById('process_iframe').style.borderColor = '#c11600';

    if (!isStopped) {
        var step = '';

        if (stepData[1] && stepData[2]) {
            step = stepData[1] + ' of ' + stepData[2]
        }

        ga('send', 'event', 'error', 'cache', 'cache deployment failed (' + step + ')');
    }
}

</script>

<!-- GA -->
<script type="text/javascript">

var gaIsCalled = false;

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', '<?php print constant('XC_INSTALL_GA'); ?>', 'auto');

ga('send', 'pageview', {
    'title': '<?php printf ('X-Cart v.%s Installation Wizard', LC_VERSION); ?>',
    'dimension1': '<?php print phpversion(); ?>',
    'dimension2': '<?php print LC_VERSION; ?>',
    'dimension3': '<?php print XLITE_EDITION_LNG; ?>',
<?php if (!empty($mysqlVersion)): ?>
    'dimension4': '<?php print str_replace("'", '\'', $mysqlVersion); ?>',
<?php endif; ?>
    'dimension5': '<?php print str_replace("'", '\'', php_uname('s')); ?>',
    'hitCallback': function() {
        gaIsCalled = true;
    }
});

</script>
<!-- GA -->

<?php

$gaStepNumber = $current + 1;

// Get list of repeated steps
$repeatedSteps = !empty($_COOKIE['passed_steps']) ? explode(',', $_COOKIE['passed_steps']) : array();

if ('cfg_install_db' == $modules[$current]['name'] && isset($_POST['cfg_install_db_step'])) {
    $stepGASuffix = ' (review)';
    $gaStepNumber += 100;

} else {
    $stepGASuffix = '';
}

// GA event category name
$stepGA = 'step';

// GA event action
$stepGAAction = sprintf('step-%d-%s%s', $current + 1, $modules[$current]['name'], $stepGASuffix);

// GA event label
$stepGALabel = sprintf('Step %d: %s%s', $current + 1, $modules[$current]['comment'], $stepGASuffix);

// GA event value
$gaValue = in_array($modules[$current]['name'], array('default', 'install_done')) ? 1 : 0;

$stepGAdata = array(
    'hitType' => 'event',
    'eventCategory' => $stepGA,
    'eventAction' => $stepGAAction,
    'eventLabel' => $stepGALabel,
    'eventValue' => $gaValue,
);

// Update passed steps list
$repeatedSteps[] = $gaStepNumber;
$repeatedSteps = array_unique($repeatedSteps);
$passedSteps = implode(',', $repeatedSteps);

?>

<script type="text/javascript">
   ga('send', <?php print json_encode($stepGAdata); ?>);
   document.cookie = 'passed_steps=<?php echo $passedSteps; ?>';
</script>

</head>

<body>

<div id="page-container" class="install-page <?php echo $modules[$current]['name']; ?>">

  <div id="header">

    <div class="logo"></div>

    <div class="sw-version">
      <div class="current"><?php echo xtr('X-Cart shopping cart software v. :version', array(':version' => LC_VERSION)); ?></div>
      <div class="upgrade-note">
        &copy; <?php echo date('Y'); ?> <a href="<?php echo xtr('xcart_site'); ?>" target="_blank">Qualiteam software Ltd</a>
      </div>
    </div>

    <h1><?php echo xtr('Installation wizard'); ?></h1>

  </div><!-- [/header] -->

  <div class="steps-bar">

    <ul class="steps">

<?php

// Display installation steps
foreach ($rows as $row) {
    echo $row . "\n";
}

?>

    </ul>

  </div>

  <div class="install-help-box">
    <img src="skins/admin/images/icon-install-help.svg" />
    <div>
      <?php echo xtr('Having trouble installing X-Cart? Check out our installation guide'); ?>
    </div>
  </div>

<noscript>
    <div class="ErrorMessage"><?php echo xtr('This installer requires JavaScript to function properly.<br />Please enable Javascript in your web browser.'); ?></div>
</noscript>


<div class="content">

<?php

/* common form */


// check whether the form encoding type is set
$enctype = (isset($modules[$current]['form_enctype']) ? 'enctype="' . $modules[$current]['form_enctype'] . '"'  : '');

?>

<form method="post" name="ifrm" id="install-form" action="install.php" <?php print $enctype ?>>

<?php

// get full function's name to call the corresponding module
$func = 'module_' . $modules[$current]['name'];

// check the auth code if required
if ($modules[$current]['auth_required']) {
	check_authcode($params);
}

// run module
$res = $func($params);

?>

<br />
<br />

<?php

// show navigation buttons
$prev = $current;

if (!$res) {
    $current += 1;
}

if ($current < count($modules)) {


    if (!empty($params)) {

        if (!empty($mysqlVersion)) {
            $params['mysqlVersion'] = $mysqlVersion;
        }

	    foreach ($params as $key => $val) {

?>

  <input type="hidden" name="params[<?php echo $key ?>]" value="<?php echo $val ?>" />

<?php

        }
    }

?>

  <input type="hidden" name="go_back" value="0" />
  <input type="hidden" name="current" value="<?php echo $current ?>" />

<?php

if (isset($autoPost)) {


?>

    <div><?php echo xtr('Redirecting to the next step...'); ?></div>

    <script type="text/javascript">
        setTimeout(autoSubmitPageForm, 500);
        var autoSubmitTTL = 5000;
        function autoSubmitPageForm() {
            if (gaIsCalled || 0 >= autoSubmitTTL) {
                document.ifrm.submit();
            } else {
                autoSubmitTTL = autoSubmitTTL - 500;
                setTimeout(autoSubmitPageForm, 500);
            }
        }
    </script>

<?php

} else {

    $displayHelpButtonclass = (isset($displayHelpButton) && true == $displayHelpButton) ? 'display-help' : '';
?>

<table class="buttons-bar <?php echo $displayHelpButtonclass; ?>" align="center" cellspacing="20">

<tr>

<td>
<?php
    if ($prev > 0) {
?>
  <input type="button" id="back-button" class="btn btn-default btn-lg" value="<?php echo xtr('Back'); ?>" onclick="javascript:document.ifrm.go_back.value='1'; return step_back();" />
<?php
    } else {
?>
  <input type="button" class="btn btn-default btn-lg" id="back-button" value="<?php echo xtr('Back'); ?>" disabled="disabled" />
<?php
    }
?>
</td>

<?php

    if (isset($tryAgain) && true == $tryAgain) {

 ?>

<td>
  <input id="try-button" name="try_again" type="button" class="btn btn-default btn-lg" value="<?php echo xtr('Try again'); ?>" onclick="javascript:document.ifrm.go_back.value='2'; document.ifrm.current.value='2'; ga('send', 'event', 'button', 'click', 'try'); document.ifrm.submit();" />
</td>

<?php

    }

    if (isset($displayHelpButton) && true == $displayHelpButton) {
?>

<td>
  <input type="button" class="btn btn-warning btn-lg" value="<?php echo xtr('Send a report'); ?>" onclick="javascript: document.getElementById('report-layer').style.display = 'block'; ga('send', 'event', 'button', 'click', 'send report popup');" />
</td>

<?php } else { ?>

<td>
  <input id="next-button" name="next_button" type="submit" class="btn btn-warning btn-lg" value="<?php echo xtr('Next'); ?>"<?php echo ($error || $current == 1 ? ' disabled="disabled"' : ''); ?> onclick="javascript: if (step_next()) { ifrm.submit(); return true; } else { return false; }" />
</td>

<?php } ?>

</tr>

</table>

<?php

}
}


?>

</form>

<?php

/* common bottom */

?>

<br />
<br />
<br />

</div><!-- [/content] -->

</div><!-- [/page-container] -->

<?php

if (!empty($modules[$current]) && !in_array($modules[$current]['name'], array('default', 'install_done'))) {
    include_once LC_DIR . '/Includes/install/templates/step1_report.tpl.php';
}

?>

<script type="text/javascript">

var element = document.getElementById('report-layer');

if (element) {
    element.style.height = (screen.availHeight + 200) + 'px';
}

$(window).load(
    function(){
        setTimeout(
            function() {
                $('input[autocomplete="off"]').removeAttr("readonly");
            },
            0
        );
    }
);

</script>
</body>
</html>
