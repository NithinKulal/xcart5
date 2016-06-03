{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Credit cards on the invoice
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="invoice.bottom.address.billing", weight="30")
 *}
  <div class="xpc-card-box" IF="order.getCCData()">

    <strong>{t(#Used credit cards#)}</strong>
    {foreach:order.getCCData(),cc}
      <div class="xpc-card">
        <span class="card {cc.card_type_css}">
          <img src="skins/default/en/images/spacer.gif" alt="{cc.card_type}" />
        </span> 
        {cc.card_number}
        <span IF="cc.expire">({cc.expire})</span>
      </div>
      <br />
    {end:}

</div>
