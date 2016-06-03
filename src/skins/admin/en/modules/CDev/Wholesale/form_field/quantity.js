/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Subtotal field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-quantityRangeBegin',
    handler: function () {

      this.viewValuePattern = '.view .value';

      this.saveField = function(input)
      {
        var inputs = jQuery('.field :input', this);

        var input = inputs.eq(0);

        var result = '';

        if (input) {
          result = input.val();
        }

        jQuery(this).find(this.viewValuePattern).html(result);
      }

    }
  }
);
