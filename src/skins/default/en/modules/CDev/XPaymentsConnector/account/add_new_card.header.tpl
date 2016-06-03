{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Message about zero-auth (card setup) amount 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="customer.account.add_new_card.before", weight="200")
 *}

<div class="alert alert-warning add-new-card-header" role="alert" IF={getAmount()}>
  <strong class="important-label">{t(#Important!#)}</strong>
  {t(#We will authorize#)}
  <strong class="highlight-label"><widget class="XLite\View\Surcharge" surcharge="{getAmount()}" /></strong>
  {t(#on your credit card in order to attach this credit card to your account. The amount will be released back to your card after a while.#)}
  <span IF={getDescription()}>
    {t(#The transaction will be marked as#)}
    <strong class="highlight-label">&ldquo;{getDescription()}&rdquo;</strong>.
  </span>
</div>
