/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Price field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-price',
    handler: function () {

      jQuery('.field :input', this).eq(0).bind('sanitize', function () {
        var input = jQuery(this);
        var value = core.stringToNumber(input.val(), '.', '');

        input.val(value);
      });

      this.viewValuePattern = '.view .value';

      this.sanitize = function ()
      {
        var input = jQuery('.field :input', this).eq(0);

        if (input.length) {

          input.val(input.get(0).sanitizeValue(input.val(), input));
        }
      };

      var getFieldFormattedValue = this.getFieldFormattedValue;
      this.getFieldFormattedValue = function (input)
      {
        var input = jQuery('.field :input', this).eq(0);

        return input.length
          ? core.numberToString(
            getFieldFormattedValue.apply(this, arguments),
            input.data('decimal-delim'),
            input.data('thousand-delim'),
            input.data('e')
          )
          : undefined;
      };
    }
  }
);
