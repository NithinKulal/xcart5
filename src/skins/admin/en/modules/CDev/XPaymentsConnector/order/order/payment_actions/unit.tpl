{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Payment actions unit
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

{if:isXpc(1)}
  {if:is_transaction}

  <div class="payment-action-unit">
	  <input IF="isDisplayAmountField()" type="text" id="{getAmountFieldId()}" class="amount-field" value="{getAmountFieldValue()}"/>
	  <widget class="\XLite\View\Button\Regular" label="{getUnitName()}" jsCode="{getJsCode()}" style="{getCSSClass()}" />
  </div>

  <div class="payment-action-unit-border"></div>
  {end:}
{else:}
  <widget template="order/order/payment_actions/unit.tpl" />
{end:}
