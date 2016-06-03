{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Redirect page
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">

function postMessageToParent(msg) {
  if (window.parent !== window && window.JSON) {
    window.parent.postMessage(JSON.stringify(msg), '*');
  }
}

</script>
</head>
<body onload="javascript: postMessageToParent({ message: 'ready', params: { height: $(document).height() } });">
{getFunctionName()}('{getFunctionParams()}');
</body>
</html>
