{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Additional info 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

{if:getXpcTransactionsAddInfo()}

<table class="add-info">
  <tr class="header">
    <th>{t(#Date, time#)}</th>
    <th>{t(#Type#)}</th>
    <th>{t(#Result payment status#)}</th>
    <th>{t(#Transaction result#)}</th>
    <th>{t(#Total#)}</th>
  </tr>

  <tr class="separator"><td colspan="5">&nbsp;</td></tr>

  {foreach:getXpcTransactionsAddInfo(),tr}

    <tr class="top-line">
      <td>{getTime(tr.date)}</td>
      <td><strong>{tr.action}</strong></td>
      <td>{tr.payment_status}</td>
      <td class="status-{tr.status:h}"><strong>{tr.status:h}</strong></td>
      <td>{tr.total}</td>
    </tr>

    <tr class="bottom-line">
      <td colspan="5">
        <widget template="{getDir()}/fields.tpl">
      </td>
    </tr>

    <tr class="separator"><td colspan="5">&nbsp;</td></tr>

  {end:}

</table>

{else:}
  <p class="error">{t(#The related payment not found. Apparently it was deleted.#)}</p>
{end:}
