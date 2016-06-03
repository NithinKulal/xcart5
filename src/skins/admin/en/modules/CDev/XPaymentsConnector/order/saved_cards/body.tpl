{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Saved cards 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<widget class="\XLite\Module\CDev\XPaymentsConnector\View\Form\PopupSavedCards" name="savedcards" />

  <p>{t(#Use one of the following cards to#)} <span id="init-action-name"></span> <strong>{getAmount()}</strong> {t(#for this order#)}:</p>

  <br />

  <select name="trn_id" class="cards-list" onchange="javascript: updateActionForSavedCard($('.cards-list').val());">
  {foreach:getCards(),card}
    <option value="{card.transaction_id}">
      {getCardName(card)}
    </option>
  {end:}
  </select>

  <br /><br />

  <widget class="\XLite\View\Button\Submit" id="init-action-button" style="main" />

<widget name="savedcards" end />

<script type="text/javascript">
  var saved_card_actions = [];
  {foreach:getCards(),card}
    saved_card_actions[{card.transaction_id}] = '{card.init_action}';
  {end:}

  updateActionForSavedCard($('.cards-list').val());
</script>
