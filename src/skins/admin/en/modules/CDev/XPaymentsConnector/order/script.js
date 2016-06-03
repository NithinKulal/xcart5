/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sale widget controller
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

function showAddInfo(orderNumber, transactionId) {
	popup.load(
    URLHandler.buildURL(
			{
        'target': 'popup_add_info', 
        'order_number': orderNumber,
        'transaction_id': transactionId,
        'widget': '\\XLite\\Module\\CDev\\XPaymentsConnector\\View\\PopupAddInfo'
      }
    )
  );
}

function showRechargeBox(orderNumber, amount) {
  popup.load(
    URLHandler.buildURL(
      {
        'target': 'popup_saved_cards',
        'order_number': orderNumber,
        'amount': amount,
        'widget': '\\XLite\\Module\\CDev\\XPaymentsConnector\\View\\PopupSavedCards'
      }
    )
  );
}

String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

function updateActionForSavedCard(id) {
  jQuery('#init-action-name').text(saved_card_actions[id]);
  jQuery('#init-action-button').find('span').text(saved_card_actions[id].capitalizeFirstLetter());
}

function confirmOperation(operation, amount, location, currencyPrefix, currencySuffix) {

  amount = currencyPrefix + amount + currencySuffix;

  var confirmText = core.t("You\'re going to {{operation}} {{amount}}. Continue?", {"operation": operation, "amount": amount});

  jQuery.confirm({
    title: 'Confirmation required',
    dialogClass: 'modal-dialog confirm-operation',
    text: confirmText,
    confirmButtonClass: 'regular-main-button',
    confirm: function() {
        self.location = location;
    },
    cancel: function() {}
  });
}

jQuery(document).ready(
  function () {
    jQuery('.fraud-disabled').attr('disabled', 'disabled');
    jQuery('.fraud-disabled').prev('input').attr('disabled', 'disabled');
  }
);
