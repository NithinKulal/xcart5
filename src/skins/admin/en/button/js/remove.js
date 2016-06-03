/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Remove button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.line .actions .remove-wrapper',
    handler: function () {

      jQuery('button.remove', this).click(
        function () {
          jQuery(this).parents('.remove-wrapper').eq(0).find('input').click();
        }
      );

      jQuery('input', this).eq(0).change(
        function () {
          var inp = jQuery(this);
          var btn = inp.parents('.remove-wrapper').eq(0).find('button.remove');
          var cell = inp.parents('.line').eq(0);

          if (inp.is(':checked')) {
            btn.addClass('mark');
            cell.addClass('remove-mark');

          } else {
            btn.removeClass('mark');
            cell.removeClass('remove-mark');
          }

          cell.parents('form').change();
        }
      );
    }
  }
);
