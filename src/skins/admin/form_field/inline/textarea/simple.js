/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Price field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-textarea',
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

      var getViewValueElements = this.getViewValueElements;
      this.getViewValueElements = function()
      {
        return getViewValueElements.apply(this).find('.value');
      }

    }
  }
);
