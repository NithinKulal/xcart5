{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Additional info. Transaction fields 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<table class="fields">

  <tr>
    <td class="name">{t(#Message#)}:</td>
    <td class="value">{tr.message:h}</td>
  </tr>

  <tr>
    <td class="name">{t(#Transaction ID#)}:</td>
    <td class="value">{tr.txnid}</td>
  </tr>

  {foreach:tr.fields,field}
    <tr>
      <td class="name">{field.name:h}:</td>
      <td class="value">{field.value:h}</td>
    </tr>
  {end:}

</table>

