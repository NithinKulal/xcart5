{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Welcome section. Scary note. 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<div class="scary-note">

  
  <div IF="{getPaymentMethod()}" class="xp-icon-circle">
  </div>

  <div class="logo {if:getPaymentMethod()}xp{end:}">
    <img src="skins/admin/en/{getLogoUrl()}" />
  </div>

  <div class="scary-text">
    {getScaryText():h}
  </div>

  <div class="pci-logo">
  </div>

</div>

