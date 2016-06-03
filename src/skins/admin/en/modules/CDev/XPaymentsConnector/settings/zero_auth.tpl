{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Zero-dolar authrization (card setup) settings 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<br/>

{if:!hasActiveMethodsSavingCards()}
<div class="alert alert-warning no-zero-auth-methods" role="alert">
  <strong>Warning!</strong>
  {t(#No active X-Payments payment methods, or saving credit cards is not activated.#)}
</div>
{end:}
