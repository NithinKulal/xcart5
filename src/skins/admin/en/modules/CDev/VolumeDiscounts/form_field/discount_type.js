/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Discount type field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-discountType',
    handler: function () {

      this.viewValuePattern = '.view';

      this.saveField = function()
      {
        jQuery(this).find(this.viewValuePattern).html('');
      }
    }
  }
);
