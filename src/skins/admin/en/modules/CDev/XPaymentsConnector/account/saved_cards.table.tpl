{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * iframe 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="admin.account.saved_cards.table", weight="100")
 *}

<div IF="getSavedCards()">

<widget class="\XLite\Module\CDev\XPaymentsConnector\View\Form\SavedCards" name="savedcards" />

  <widget class="XLite\Module\CDev\XPaymentsConnector\View\ItemsList\Model\SavedCards" />

<widget name="savedcards" end />

</div>
