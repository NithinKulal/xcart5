<!DOCTYPE html>
<html>
<!--
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-present Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
-->
<?php define('XLITE_EDITION_LNG', 'en'); define('XC_INSTALL_GA', 'UA-63353285-1'); if (basename(__FILE__) != 'install.php') die('Failed to start...'); require_once __DIR__ . '/Includes/install/main.php'; exit; ?>
<head>
  <title>X-Cart shopping cart installation wizard</title>
  <script type="text/javascript">
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-63353285-1', 'auto');
ga('send', 'pageview');
ga('send', 'event', 'error', 'no-php');
  </script>
</head>
<body>
<p>The installation script has not been able to detect <a href="http://php.net/" target="_blank">PHP</a> installed on your server. X-Cart requires [PHP] to operate, so, unfortunately, X-Cart installation may not be completed this time.</p>
<p>If you require assistance with this issue, simply <a href="http://www.x-cart.com/create-online-store.html?utm_source=XC5Install&utm_medium=noPHP&utm_campaign=XC5Install" target="_blank" onclick="javascript: ga('send', 'event', 'INFO', 'RequestPersonalDemo', 'no-php');">request a personal demo</a>, and our team will create a store for you.</p>
<p>If you are familiar with PHP and want to try your hand at installing X-Cart by yourself, consider using <a href="https://www.mamp.info/" target="_blank">MAMP</a> or <a href="http://wampserver.com/" target="_blank">WAMP</a> as your local server. You can also deploy your store on <a href="http://www.x-cart.com/hosting.html" target="_blank">X-Cart hosting</a> or deploy it on the servers of one of our <a href="http://partners.x-cart.com/hosting-companies?utm_source=XC5Install&utm_medium=noPHP&utm_campaign=XC5Install" target="_blank">hosting partners</a>.</p>
<p>Still got questions? Reach out to our sales team at <a href="mailto:sales@x-cart.com">sales@x-cart.com</a> and we will be glad to assist you.</p>
</body>
</html>
