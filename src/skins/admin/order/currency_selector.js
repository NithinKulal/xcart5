/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order info form controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'CurrencySelector',
  '#currency',
  function (event) {
    var input = jQuery('#currency').get(0);

    jQuery(input).on('change', function (event) {
      input.form.submit();
    });
  }
);