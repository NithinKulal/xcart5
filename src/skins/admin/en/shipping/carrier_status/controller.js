/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ShippingMethodSwitcher () {
  jQuery('#shipping-method-status').change(function (event)
  {
    event.stopImmediatePropagation();

    var switchWrapper = jQuery('.shipping-status');
    if (switchWrapper.length) {
      assignShadeOverlay(switchWrapper);
    }

    var processor = switchWrapper.data('processor');

    core.get(
      URLHandler.buildURL({
        target: 'shipping_settings',
        action: 'switch',
        processor: processor
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

core.autoload(ShippingMethodSwitcher);