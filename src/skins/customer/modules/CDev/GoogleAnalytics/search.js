/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Drupal-specific controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    if ('undefined' != typeof(window._gaq) || 'undefined' != typeof(window.ga)) {
      // Register "Search" event
      _ga.gaRegisterSearchEvent(jQuery(_ga.searchTextPattern).val());

      jQuery(".search-product-form button[type='submit']").click(function (event) {
        _ga.gaRegisterSearchEvent(jQuery(_ga.searchTextPattern).val());
      });
    }
  }
);

