/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping settings controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'additionalSettings',
  'h2#additionalsettingsseparator',
  function () {
    jQuery(this).bind('click', function () {
      jQuery(this).toggleClass('expanded').closest('li').nextAll().toggle();
    });

    jQuery(this).closest('li').nextAll().hide();
  }
);
