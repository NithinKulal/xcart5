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
      this.isDashed = jQuery('.field :input', this).eq(0).data('dashed');

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
          var value = input.get(0).sanitizeValue(input.val(), input);
          input.val(value);
          if (!value && this.isDashed) {
            jQuery('.view .symbol', this).addClass('hidden');
          } else if (value && this.isDashed) {
            jQuery('.view .symbol', this).removeClass('hidden');
          };
        }
      };

      var getFieldFormattedValue = this.getFieldFormattedValue;
      this.getFieldFormattedValue = function (input)
      {
        var input = jQuery('.field :input', this).eq(0);

        var value = getFieldFormattedValue.apply(this, arguments);
        if (!core.stringToNumber(
            value,
            input.data('decimal-delim'),
            input.data('thousand-delim')) && this.isDashed
        ) {
          return ' &mdash;';
        } else {
          return input.length
            ? core.numberToString(
              value,
              input.data('decimal-delim'),
              input.data('thousand-delim'),
              input.data('e')
            )
            : undefined;
        };
      };
    }
  }
);
