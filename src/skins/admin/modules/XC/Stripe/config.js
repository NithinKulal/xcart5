/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'stripe-help-switcher',
  '.stripe',
  function() {
    var box = jQuery(this).find('.instruction');

    box.find('.switcher-show').click(
      function() {
        box.removeClass('non-visible');

        return false;
      }
    );

    box.find('.switcher-hide').click(
      function() {
        box.addClass('non-visible');

        return false;
      }
    );
  }
);

