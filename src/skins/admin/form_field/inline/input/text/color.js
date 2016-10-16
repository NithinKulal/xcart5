/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Price field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-color',
    handler: function () {

      var field = jQuery(this);
      var input = jQuery('.field :input.color', this).eq(0);

      input.focus(function(event) {
        jQuery(this).ColorPickerShow();
      });

      // Check - process blur event or not
      this.isProcessBlur = function()
      {
        return !input.data('colorpicker-show');
      }

      // Save field into view
      this.saveField = function()
      {
        field.find(this.viewValuePattern).find('.value').css({
          'background-color': '#' + this.getFieldFormattedValue()
        });
      }

    }
  }
);
