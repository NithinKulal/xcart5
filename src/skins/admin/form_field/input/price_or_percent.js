/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Price or percent field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-price-or-percent',
    handler: function () {

      var field = jQuery(this);
      var input = field.find('input[type="text"]');

      input.val(core.numberToString(input.val(), '.', '', input.data('e')));

      input.change(function () {
        $(this).val(core.numberToString($(this).val(), '.', '', $(this).data('e')));
      });
    }
  }
);
