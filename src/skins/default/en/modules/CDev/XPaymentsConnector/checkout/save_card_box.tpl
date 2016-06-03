{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Checkout : order review step : selected state : button
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<br />

<div class="xpc-box"{if:!checkCheckoutAction()} style="display: none;"{end:}>
  <widget class="\XLite\Module\CDev\XPaymentsConnector\View\Checkout\Iframe" />
  <div class="{getSaveCardBoxClass()}" {if:!showSaveCardBox()} style="display: none;"{end:}>
    <label for="save-card">
      <input type="checkbox" name="save_card" value="Y" id="save-card" />
      {t(#I want to use this credit card for my future orders in this shop.#):h}
    </label>
  </div>
</div>
