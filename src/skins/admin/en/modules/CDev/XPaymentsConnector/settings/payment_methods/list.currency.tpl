{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Payment method currency 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

{if:isCurrencySupported(entity)}
  <div class="xpc-currency table-label">{getCurrencyTitle(getPaymentMethodCurrency(entity))}</div>
{else:}
  <div class="xpc-currency table-label text-danger">
    {getCurrencyTitle(getPaymentMethodCurrency(entity))}    
    <widget
      class="\XLite\View\Tooltip"
      text="{getCurrencyTooltipContent(entity):h}"
      isImageTag="true"
      imageClass="fa fa-exclamation-triangle text-danger"
      placement="top"
      className="warning" />
  </div>
{end:}
