/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product box controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    var base = jQuery('.product-block .product');

    // Register "Quick look" button handler
    jQuery('.quicklook a.quicklook-link', base).click(
      function () {
        return !popup.load(
          URLHandler.buildURL({
            target:      'quick_look',
            action:      '',
            product_id:  core.getValueFromClass(this, 'quicklook-link'),
            only_center: 1
          }),
          false,
          50000
        );
      }
    );
  }
);

