{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Customer's address selector at add new card page 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="customer.account.add_new_card.address", weight="100")
 *}

{if:getAddressList()}

  <widget class="\XLite\Module\CDev\XPaymentsConnector\View\Form\ZeroAuthAddress" name="zero_auth_address" />
    <input type="hidden" name="action" value="update_address">

    <div class="zero-auth-address">

      <strong>{t(#Billing address#)}:</strong>

      <select name="address_id" value="{getAddressId()}" onchange="javascript: shadeIframe(); this.form.submit();">
        {foreach:getAddressList(),addressId,address}
          <option value="{addressId}" {if:addressId=getAddressId()}selected="selected"{end:}>{address}</option>
        {end:}
      </select>

      <widget class="\XLite\Module\CDev\XPaymentsConnector\View\Button\AddAddress" label="New address" profileId="{getProfileId()}" />

    </div>
  <widget name="zero_auth_address" end />

{else:}

  <div class="alert alert-danger add-new-card-error">
    <strong class="important-label">{t(#Important#)}!</strong>
    {t(#No addresses for the profile.#)}
    <widget class="\XLite\Module\CDev\XPaymentsConnector\View\Button\AddAddress" label="Setup address" profileId="{getProfileId()}" />
  </div>

{end:}
