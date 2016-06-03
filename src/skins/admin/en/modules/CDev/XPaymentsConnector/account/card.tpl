{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * iframe 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<div class="saved-card">
  <div class="card-icon-container">
    <span class="card"><img src="images/spacer.gif" alt="{entity.getCardType()}"/></span>
  </div>
  <div class="card-number">
    {entity.getCardNumber()}
  </div>
  <div class="card-expire" IF="{entity.getCardExpire()}">
    {entity.getCardExpire()}
  </div>
</div>
