{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Form for the add new card 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="customer.account.add_new_card.form", weight="100")
 *}

<div id="add_new_card_iframe_container">

  <iframe src="{buildUrl(#add_new_card#,#xpc_iframe#)}" width="300" height="100%" border="0" style="border: 0px" id="add_new_card_iframe"></iframe>

  <div class="clearfix"></div>

  <input type="button" value="{t(#Save credit card#)}" id="submit-button" class="btn regular-button regular-main-button submit add-new-card-button" />

  <a href="{buildURL(#saved_cards#)}">{t(#Back to credit cards#)}</a>

</div>
