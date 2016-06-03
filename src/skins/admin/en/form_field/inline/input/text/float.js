/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Price field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-float',
    handler: function () {

      this.sanitize = function ()
      {
        var input = jQuery('.field :input', this).eq(0);
        if (input.length) {
          input.val(input.get(0).sanitizeValue(input.val(), input));
        }
      };

    }
  }
);
