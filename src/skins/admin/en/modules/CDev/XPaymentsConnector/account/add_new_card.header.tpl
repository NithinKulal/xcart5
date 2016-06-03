{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Message about zero-auth (card setup) amount 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="admin.account.add_new_card.before", weight="200")
 *}

{* TODO: Use widget class="XLite\View\Surcharge" surcharge="getAmount()"  when it's implemented in admin backend. See BT:45950 *}

<div class="alert alert-warning add-new-card-header" role="alert" IF={getAmount()}>
  <strong class="important-label">{t(#Important!#)}</strong>
  {t(#We will authorize#)}
  <strong class="highlight-label">{getAmount()}</strong>
  {t(#on this credit card in order to link it to the customer's profile.#)}
  {if:{getDescription()}
    <br/>
    {t(#The transaction will be marked as#)}
    <strong class="highlight-label">&ldquo;{getDescription()}&rdquo;</strong>.
  {end:}
</div>
