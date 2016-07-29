/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper div.combobox-select',
    handler: function () {

      jQuery(this).click(
        function () {
          var input = jQuery(this).parent().find('input');
          var minLength = input.autocomplete('option', 'minLength');
          input.autocomplete('option', 'minLength', 0);
          input.autocomplete('search', '');
          input.autocomplete('option', 'minLength', minLength);
          input.focus();
        }
      );
    }
  }
);
