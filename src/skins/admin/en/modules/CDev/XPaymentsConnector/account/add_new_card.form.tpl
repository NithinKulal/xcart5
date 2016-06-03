{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Customer's saved credit cards header
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="admin.account.add_new_card.form", weight="100")
 *}

<div id="add_new_card_iframe_container">


  <iframe src="{buildUrl(#add_new_card#,#xpc_iframe#,_ARRAY_(#profile_id#^getCustomerProfileId()))}" width="300" height="100%" border="0" style="border: 0px" id="add_new_card_iframe"></iframe>

  <div class="clearfix"></div>

  <button id="submit-button" class="btn regular-main-button" />{t(#Save credit card#)}</button>

  <a href="{buildURL(#saved_cards#,##,_ARRAY_(#profile_id#^getCustomerProfileId()))}" class="back-to-cards" >{t(#Back to credit cards#)}</a>

</div>
