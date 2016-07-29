/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Price field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-text',
    handler: function () {

      var field = jQuery(this);

      var inputs = jQuery('.field :input', this);

      // Sanitize-and-set value into field
      this.sanitize = function()
      {
        inputs.each(
          function () {
            this.value = this.value.replace(/^ +/, '').replace(/ +$/, '');
          }
        );
      }

      // Save field into view
      this.saveField = function()
      {
        var value = this.getFieldFormattedValue();
        field.find(this.viewValuePattern).find('.value').html(htmlspecialchars("" == value ? " " : value, null, null, false));
      }

    }
  }
);
