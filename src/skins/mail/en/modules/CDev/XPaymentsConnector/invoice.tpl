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

<div IF="order.getCCData()">
  <br />
  <strong>{t(#Used credit cards#)}</strong>
  <br />

  {foreach:order.getCCData(),cc}
    {cc.card_type} {cc.card_number} {if:cc.expire}({cc.expire}){end:}<br />
  {end:}

</div>
