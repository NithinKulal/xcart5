/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

jQuery().ready(
  function() {

    // Tabs
    jQuery('#useseparatebox').change(
      function () {

        if ('Y' == jQuery('#useseparatebox option:selected').val()) {
          jQuery('#block-use-separate-box').show();
        } else {
          jQuery('#block-use-separate-box').hide();
        }

        return true;
      }
    );

    jQuery('#shippable, #useseparatebox').change();
  }
);
