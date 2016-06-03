/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Zone details form controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  'load',
  function(event) {
    jQuery('.table-value .listbox').each(function () {
      var obj = jQuery(this);

      var inputField = jQuery('input[type="hidden"]', obj);
      var inputFieldId = jQuery(inputField).attr('id');

      inputField
        .parents('form')
        .submit(
          function() {
            var id = inputFieldId.replace('-store', '');
            saveSelects(new Array(id));
  
            return true;
          }
        );
    });
  }
);
