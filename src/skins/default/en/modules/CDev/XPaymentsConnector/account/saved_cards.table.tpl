{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * iframe 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="customer.account.saved_cards.table", weight="100")
 *}


{if:profile.getSavedCards()}
<div>

  <widget class="\XLite\Module\CDev\XPaymentsConnector\View\Form\SavedCards" name="savedcards" />

  <table class="saved-cards">
    <tr>
      <th>{t(#Order#)}</th>
      <th>{t(#Credit card#)}</th>
      <th>{t(#Billing Address#)}</th>
      <th>{t(#Default#)}</th>
      <th></th>
    </tr>
    {foreach:profile.getSavedCards(),cc}
      <tr>
        <td class="orderid"><a href="{buildURL(#order#,##,_ARRAY_(#order_number#^cc.invoice_id))}">#{cc.invoice_id}</a></td>
        <td>

          <div class="saved-card">
            <div class="card-icon-container">
              <span class="card {cc.card_type_css}"><img src="images/spacer.gif" alt="{cc.card_type}"/></span>
            </div>
            <div class="card-number">
              {cc.card_number}
            </div>
            <div class="card-expire" IF="{cc.expire}">
              {cc.expire}
            </div>
          </div>

        </td>
        <td class="address">
          {if:getAddressList()}

            {if:isSingleAddress()}
              <div class="single">
                {getSingleAddress()}
              </div>
            {else:}
              <select name="address_id[{cc.card_id}]" value="{cc.address_id}">
                {if:!cc.address_id}
                  <option value="0" selected="selected"></option>
                {end:}
                {foreach:getAddressList(),addressId,address}
                  <option value="{addressId}" {if:addressId=cc.address_id}selected="selected"{end:}>{address}</option>
                {end:}
              </select>
            {end:}
          {end:}
        </td>
        <td class="default-column">
          {if:cc.is_default}
            <input checked type="radio" name="default_card_id" value="{cc.card_id}" />
          {else:}
            <input type="radio" name="default_card_id" value="{cc.card_id}" />
          {end:}
        </td>
        <td class="remove-column">
          <widget template="{getRemoveTemplate(cc.card_id)}" />
        </td>
      </tr>  
    {end:}
  </table>

  <widget class="\XLite\View\Button\Submit" label="{t(#Update saved credit cards#)}" style="main" />

  {if:allowZeroAuth()}
    &nbsp;&nbsp;<a href="{buildURL(#add_new_card#)}">{t(#Add new credit card#)}</a>
  {end:}

  <widget name="savedcards" end />

</div>
{else:}

  {if:allowZeroAuth()}
    <br/>
    &nbsp;&nbsp;<a href="{buildURL(#add_new_card#)}">{t(#Add new credit card#)}</a>
  {end:}

{end:}
