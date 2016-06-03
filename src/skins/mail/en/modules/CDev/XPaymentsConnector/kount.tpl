{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Kount result on the invoice
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="invoice.bottom", weight="21")
 *}
</tr>
<tr IF="isDisplayKountResult()">
  <td colspan="3" style="background: #f9f9f9; padding: 15px;">

  <h2 style="font-weight: normal; color: #69a4c9; font-size: 24px; margin: 18px 0;">{t(#KOUNT Antifraud screening result#)}</h2>

  {if:getKountErrors()}
    {foreach:getKountErrors(),error}
      <div style="{getKountErrorStyle()}">
        <strong>{t(#Error#)}!</strong>
          {error:h}
      </div>
    {end:}
  {end:}

  <p style="" IF="getKountMessage()">
    {getKountMessage():h}. {t(#Score#)}:
    <span class="lead {getKountScoreClass()}">
      {getKountScore()}
    </span>
  </p>

  <p IF="getKountTransactionId()">{t(#Transaction ID#)}: {getKountTransactionId()}</p>

  {if:getKountRules()}

    <h3 style="margin-bottom: 15px; font-size: 18px; font-weight: normal; color: #69a4c9;">{t(#Rules triggered#)}:</h3>

    <ul class="kount-result-lines">
      {foreach:getKountRules(),title,value}
        <li>{value:h}</li>
      {end:}
    </ul>

  {end:}

  </td>
</tr>
<tr>

