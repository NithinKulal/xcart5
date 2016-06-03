{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Welcome section. Middle steps line. 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<div class="middle-line">

  <div class="step1">

    <span class="step-text">
      <strong>1.</strong>
      {t(#Open your X-Payments dashboard#)}
      <br />
      {t(#or#)}
    </span>

  </div>

  <div class="step2">

    <span class="step-text">
      <strong>2.</strong>

      {if:getPaymentMethod()}
        {t(#Configure#)}
        <strong>{getPaymentMethodName()}</strong>
        <span class="nowrap">{t(#in#)} <strong>{t(#X-Payments#)}</strong></span>
      {else:}
        {t(#Configure your gateway#)}
        <span class="nowrap">{t(#in#)} <strong>{t(#X-Payments#)}</strong></span>
      {end:}
    </span>

  </div>

  <div class="step3">

    <span class="step-text">
      <strong>3.</strong>
      {t(#Connect#)}
      <br />
      <strong class="nowrap">{t(#X-Payments#)}</strong> {t(#with#)} <strong class="nowrap">{t(#X-Cart#)}</strong>
    </span>

  </div>

</div>

<div class="clearfix"></div>

