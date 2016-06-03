/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Text-bases input field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

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
