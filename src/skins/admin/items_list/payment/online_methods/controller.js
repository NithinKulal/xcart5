/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Payment methods controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Payment methods controller
 */

ItemsList.prototype.listeners.popup = function(handler)
{
  core.autoload(PopupButtonInstallAddon);
  jQuery('.no-payment-methods-appearance .no-payments-found .search-worldwide').click(function(event) {
    var box = jQuery(this).parents('.dialog-content.payment-gateways').eq(0);
    if (0 < box.length) {
      var form = jQuery('form', box);
      if (form) {
        jQuery('.search-conditions .country-condition select#country', form).val('');
        var text = jQuery('.search-conditions .country-condition select#country option:selected', form).text();
        jQuery('.search-conditions .country-selector .chosen-single span', form).text(text);
        jQuery('.search-conditions .country-selector .chosen-results .result-selected', form).each(function() {
          jQuery(this).removeClass('result-selected').removeClass('highlighted');
        });
        form.submit();
      }
    }
  });
};

jQuery().ready(
  function() {
    core.microhandlers.add(
      'NoPaymentMethodsFoundList',
      '.no-payment-methods-appearance',
      function (event) {
        jQuery(".marketplace-block").hide();
      }
    );
    core.microhandlers.add(
      'PaymentMethodsList',
      '.payments-list.items-list',
      function (event) {
        jQuery(".marketplace-block").show();
      }
    );
  }
);
