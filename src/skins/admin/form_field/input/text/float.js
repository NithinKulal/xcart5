/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper input.float',
    handler: function () {

      this.sanitizeValue = function (value, input)
      {
        if (!input) {
          input = jQuery(this);
        }

        var e = input.data('e');

        value = core.stringToNumber(value, '.', ',');

        if ('undefined' == typeof(e)) {
          value = parseFloat(value);

        } else {
          var pow = Math.pow(10, e);
          value = Math.round(value * pow) / pow;
        }

        var range = this.getRange();

        return isNaN(value) ? (range.min || 0) : value;
      }

      this.getRange = function()
      {
        var input = jQuery(this);

        var min = parseFloat(input.data('min'));
        if (isNaN(min)) {
          min = null;
        }

        var max = parseFloat(input.data('max'));
        if (isNaN(max)) {
          max = null;
        }

        return { 'min': min, 'max': max };
      }

      this.commonController.isEqualValues = function (oldValue, newValue, element)
      {
        return this.element.sanitizeValue(oldValue, element) == this.element.sanitizeValue(newValue, element);
      }

      this.getOnPressPattern = function() {
        return /^[\-\+0-9\.]+$/;
      }

      this.getCharByEvent = function(event) {
        return event.charCode && !event.ctrlKey && !event.altKey
          ? String.fromCharCode(event.charCode)
          : null;
      }

      jQuery(this).keypress(
        function(event) {
          var result = true;

          var pattern = this.getOnPressPattern();
          if (pattern) {
            var c = this.getCharByEvent(event);
            if (c !== null) {
              result = -1 !== c.search(pattern);
            }
          }

          return result;
        }
      );

      this.getOnInputPattern = function() {
        return /^([\-\+]?[0-9,]+\.[0-9]+|[\-\+]?[0-9,]+\.|[\-\+]?[0-9,]+|)$/;
      }

      jQuery(this).bind(
        'input',
        function(event) {
          var result = true;

          var pattern = this.getOnInputPattern();
          if (pattern) {
            result = -1 !== this.value.search(pattern);
          }

          if (result) {
            this.oldValue = this.value;

          } else if (typeof(this.oldValue) != 'undefined') {
            this.value = this.oldValue;
          }

          return result;
        }
      );


    }
  }
);

