/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Discount value field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-discountValue',
    handler: function () {

      this.viewValuePattern = '.view';

      this.saveField = function()
      {
        var input = jQuery('.field :input', this).eq(0);
        var inpSelect = input.parents('.cell').next().find('select').get(0);

        var result = '';

        if (input && inpSelect) {
          result = input.val() + ' ' + inpSelect.options[inpSelect.selectedIndex].text;
        }

        jQuery(this).find(this.viewValuePattern).html(result);
      }

    }
  }
);
