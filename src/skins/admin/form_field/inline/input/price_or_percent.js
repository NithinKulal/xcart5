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
      var obj = this;

      var inputs = jQuery('.field :input', this);

      // Sanitize-and-set value into field
      this.sanitize = function () {
      };

      this.startEditInline = function () {
        field.find('input[type="text"]').focus();
      };

      // Save field into view
      this.saveField = function () {
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
      };

      inputs.unbind('escPress');
      inputs.bind(
        'escPress',
        function (currentEvent, event, result) {
          inputs.each(function () {
            var input = $(this);

            if (input.get(0).commonController && !input.is('button')) {
              input.val(input.get(0).commonController.element.initialValue);
              input.change();
            }
          });
          obj.saveField();

          setTimeout(function () {
            field.parents('form').eq(0).change();
          }, 100);

          jQuery(this).trigger('blur');
        }
      );

    }
  }
);
