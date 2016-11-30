/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Text-bases input field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
jQuery.fn.extend({
  selectOnFocus: function() {
    return this.each(function() {
      if (this.focusedElement == jQuery(this)) {
        return;
      }
      this.focusedElement = jQuery(this);

      _.delay(
        _.bind(
          function() {
            this.setSelectionRange(0, this.value.length)
          },
          this
        ),
        50
      );
    });
  },
});

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      var parent = this.$element.parents('.input-field-wrapper');
      return parent.length > 0 && core.getCommentedData(parent, 'selectOnFocus') && this.$element.is('input');
    },
    handler: function () {
      this.$element.on('focus', function () {
          jQuery(this).selectOnFocus();
      });
   }
  }
);

core.bind(
  'load',
  function(event) {
    jQuery('.input-field-wrapper').each(function () {
      var obj = jQuery(this);

      var inputField = jQuery('input,textarea', obj);
      var defaultValue = core.getCommentedData(obj, 'defaultValue');
      var type = inputField.attr('type');

      if (
        '' !== defaultValue
        && null !== defaultValue
        && 'checkbox' != type
        && 'radio' != type
        && 'file' != type
        && 'image' != type
        && 'submit' != type
        && 'button' != type
      ) {

        if ('' === inputField.val()) {
          inputField.val(defaultValue).addClass('default-value');
        }

        inputField.click(
          function () {
            if (defaultValue === inputField.val()) {
              inputField.removeClass('default-value').val('');
            }
          }
        ).blur(
          function () {
            if ('' === inputField.val()) {
              inputField.addClass('default-value').val(defaultValue);
            }
          }
        );

        inputField
          .parents('form')
          .submit(
            function() {
              if (defaultValue === inputField.val()) {
                inputField.val('');
              }
  
              return true;
            }
          );
      }
    });
  }
);
