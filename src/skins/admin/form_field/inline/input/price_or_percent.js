/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Price or percent inline field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-price-or-percent',
    handler: function () {

      var field = jQuery(this);

      // Sanitize-and-set value into field
      this.sanitize = function()
      {
      }

      // Save field into view
      this.saveField = function()
      {
        var type = $(this).find('input[type=hidden]').val();
        var value = $(this).find('input[type=text]').val();
        var view = field.find(this.viewValuePattern);

        if (type == 'a') {
          view.find('.symbol').removeClass('hidden');
        } else {
          view.find('.symbol').addClass('hidden');
          value = value.concat('%');
        }

        view.find('.value').html(value);
      }

    }
  }
);
