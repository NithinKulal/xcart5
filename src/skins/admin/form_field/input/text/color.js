/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper input.color',
    handler: function () {
      var $input = jQuery(this);

      var getOwner = function() {
        return jQuery('.colorpicker').get(0).owner;
      }
      var changeValue = _.throttle(
        function (owner, hex) {
          owner.val(hex);
          owner.change();
        },
        50
      );

      var options = {
        eventName: 'focus',
        onShow: function (colpkr) {
          getOwner().data('colorpicker-show', true);
          getOwner().ColorPickerSetColor(this.value);
          jQuery(colpkr).fadeIn(250);

          return false;
        },
        onHide: function (colpkr) {
          getOwner().data('colorpicker-show', false);
          getOwner().blur();

          jQuery(colpkr).fadeOut(250);

          return false;
        },
        onSubmit: function(hsb, hex, rgb, el) {
          getOwner().ColorPickerHide();
          changeValue(getOwner(), hex);
        },
        onChange: function(hsb, hex, rgb, el) {
          changeValue(getOwner(), hex);
        },
        onBeforeShow: function () {
          jQuery('.colorpicker').get(0).owner = jQuery(this);
          $input.ColorPickerSetColor(this.value);
        }
      };
      $input.ColorPicker(options);

    }
  }
);

