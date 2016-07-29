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

      var options = {
        onShow: function (colpkr) {
          jQuery(this).data('colorpicker-show', true);
          jQuery(this).ColorPickerSetColor(this.value);
          jQuery(colpkr).fadeIn(500);

          return false;
        },
        onHide: function (colpkr) {
          jQuery(this).data('colorpicker-show', false);
          jQuery(colpkr).fadeOut(500);

          return false;
        },
        onSubmit: function(hsb, hex, rgb, el) {
          var inp = jQuery('.colorpicker').get(0).owner;
          jQuery(inp).val(hex)
            .ColorPickerHide();
        },
        onBeforeShow: function () {
          jQuery('.colorpicker').get(0).owner = this;
          jQuery(this).ColorPickerSetColor(this.value);
        }
      };
      jQuery(this).ColorPicker(options);

    }
  }
);

