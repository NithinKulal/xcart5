{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Payment actions unit
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="order.actions.paymentActionsRow", weight="19000") 
 *}

<div class="recharge-button-container" IF="isAllowRecharge()">

<button type="button" class="btn regular-button create-inline" onclick="javascript: {getRechargeJsCode()};">
  {t(#Authorize/charge the difference#)}
</button>

</div>
