/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper input.integer-with-infinity',
    handler: function () {
      var infinitySign = jQuery('<div/>').html('&#x221E;').text();

      this.checkInfinity = function (value) {
        if (value === '999999999.00' || value === '999999999' || value === '') {
          return infinitySign;
        } else if (value !== infinitySign && value.search(infinitySign) !== -1) {
          return value.replace(infinitySign, '');
        }
      };

      this.sanitizeValue = function (value)
      {
        var initial = value;

        value = this.checkInfinity(value);

        if (value === infinitySign) {
          return infinitySign;

        } else {
          value = Math.round(value);

          var range = this.getRange();

          return isNaN(value) ? (range.min || 0) : initial;
        }
      };

      this.getRange = function()
      {
        var input = jQuery(this);

        var min = parseInt(input.data('min'));
        if (isNaN(min)) {
          min = null;
        }

        var max = parseInt(input.data('max'));
        if (isNaN(max)) {
          max = null;
        }

        return { 'min': min, 'max': max };
      };

      this.commonController.isEqualValues = function (oldValue, newValue, element)
      {
        return this.element.sanitizeValue(oldValue) == this.element.sanitizeValue(newValue);
      };

      this.getOnPressPattern = function() {
        return /^[\-\+0-9∞]+$/;
      };

      this.getCharByEvent = function(event) {
        return event.charCode && !event.ctrlKey && !event.altKey
          ? String.fromCharCode(event.charCode)
          : null;
      };

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
        return /^[\-\+]?[0-9∞]*$/;
      };

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

