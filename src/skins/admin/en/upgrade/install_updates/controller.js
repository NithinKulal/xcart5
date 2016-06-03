/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var toogleChecked = function (value) {
  jQuery('.update-module-list input[type=checkbox]').attr('checked', value || false);
}

core.microhandlers.add(
  'Uncheck all updates',
  '.uncheck-all',
  function (event) {
    jQuery(this).click(function(event) {
      toogleChecked(false);
    });
  }
);

core.microhandlers.add(
  'Check all updates',
  '.check-all',
  function (event) {
    jQuery(this).click(function(event) {
      toogleChecked(true);
    });
  }
);
