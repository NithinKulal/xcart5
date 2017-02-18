/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var toogleChecked = function (value) {
  var checkboxes = jQuery('.update-module-list input[type=checkbox]');
  checkboxes.attr('checked', value || false);
  checkboxes.change();
}

core.microhandlers.add(
  'Toggle unavailable',
  '#entry-core',
  function(event, element) {
    jQuery(element).change(function(){
      var state = jQuery(this).is(':checked');
      jQuery('.entry-unavailable-without-core').each(function(inx, elem) {
        jQuery(elem).find('input').attr('checked', state);
        jQuery(elem).find('input').attr('disabled', !state);
        if (!state) {
          jQuery(elem).addClass('not-selectable');
        } else {
          jQuery(elem).removeClass('not-selectable');
        }
      });
    });
  }
);

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
