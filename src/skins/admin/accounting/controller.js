/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'MarketplaceSearch',
  '.marketplace-search',
  function (event) {
    jQuery(this).click(function() {
      jQuery.ajax({
        async: false,
        type: 'GET',
        url: URLHandler.buildURL({
          target:         'addons_list_marketplace',
          clearCnd:       '1',
          clearSearch:    '1'
        }),
        data: ''
      });
    });
  }
);