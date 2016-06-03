{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Address selector for the saved card 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

{if:getAddressList()}

  {if:isSingleAddress()}
    <div class="single">
      {getSingleAddress()}
    </div>    
  {else:}
    <select name="address_id[{entity.getId()}]" value="{getCardAddressId(entity)}">
      {if:!getCardAddressId(entity)}
        <option value="0" selected="selected"></option>
      {end:}
      {foreach:getAddressList(),addressId,address}
        <option value="{addressId}" {if:addressId=getCardAddressId(entity)}selected="selected"{end:}>{address}</option>
      {end:}
    </select>
  {end:}
{end:}
