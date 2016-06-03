/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PaymentMethodSwitcher () {
  jQuery('#payment-method-status').change(function (event)
  {
    event.stopImmediatePropagation();

    var switchWrapper = jQuery('.payment-status');
    if (switchWrapper.length) {
      assignShadeOverlay(switchWrapper);
    }

    var method = switchWrapper.data('method');

    core.get(
      URLHandler.buildURL({
        target: 'payment_settings',
        action: 'switch',
        id: method
      }),
      function () {
        if (switchWrapper) {
          unassignShadeOverlay(switchWrapper);
          switchWrapper.find('.alert').toggleClass('alert-success').toggleClass('alert-warning');
        }
      }
    );

    return false;
  });
}

core.autoload(PaymentMethodSwitcher);