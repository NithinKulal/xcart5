{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * iframe
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="checkout.review.selected", weight="20") 
 *}
<script type="text/javascript">
var xpcPaymentIds = [];
{foreach:getXpcPaymentIds(),id}xpcPaymentIds[{id}] = '{id}';{end:}

var xpcSavedCardPaymentId = '{getXpcSavedCardPaymentId()}';

var xpcBillingAddressId = '{getXpcBillingAddressId()}'; 

{if:isCheckoutReady()}
var currentPaymentId = '{getPaymentId()}';
{else:}
var currentPaymentId = false;
{end:}
</script>

