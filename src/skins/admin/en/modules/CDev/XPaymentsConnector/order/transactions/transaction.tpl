{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * XPC transaction details 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<div class="card-container">

  <div class="card-icon">
    <span class="card"><img src="images/spacer.gif" alt="{entity.getCardType()}"/></span>
  </div>

  <div class="card-number">
      {getCardNumber(entity)}
    <span class="card-expire">{entity.getCardExpire()}</span>

  </div>

</div>

<div class="actions-container">

  <div class="unit" FOREACH="getTransactionUnits(entity),id,unit">
    <widget class="\XLite\View\Order\Details\Admin\PaymentActionsUnit" transaction="{getTransaction(entity)}" unit="{unit}" is_transaction=1 />
  </div>

</div>

<div style="clear: both"></div>

{if:isFraudStatus(entity)}
  <div class="warning-container">

    <p class="alert alert-warning">
      <strong>{t(#Warning#)}!</strong>
      {t(#X-Payments considers this transaction as potentially fraudulent.#)}
    </p>

    <a class="btn regular-button" href="{buildURL(#order#,#accept#,_ARRAY_(#order_number#^order.getOrderNumber(),#trn_id#^getTransactionId(entity)))}">{t(#Accept#)}</a>

    <a class="btn regular-button" href="{buildURL(#order#,#decline#,_ARRAY_(#order_number#^order.getOrderNumber(),#trn_id#^getTransactionId(entity)))}">{t(#Decline#)}</a>

    <widget template="modules/CDev/XPaymentsConnector/order/transactions/links.tpl" />

  </div>
{else:}
  <widget template="modules/CDev/XPaymentsConnector/order/transactions/links.tpl" />
{end:}
