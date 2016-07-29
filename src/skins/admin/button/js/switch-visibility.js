/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Remove button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.line .actions .switch-wrapper',
    handler: function () {

      jQuery('button.switch', this).click(
        function () {
          jQuery(this).parents('.switch-wrapper').eq(0).find('input[type=checkbox]').click();
        }
      );

      jQuery('input[type=checkbox]', this).eq(0).change(
        function () {
          var inp = jQuery(this);
          var btn = inp.parents('.switch-wrapper').eq(0).find('button.switch');
          var cell = inp.parents('.line').eq(0);

          if (inp.is(':checked')) {
            btn.addClass('mark');
            cell.addClass('switch-mark');
            inp.val(1);

          } else {
            btn.removeClass('mark');
            cell.removeClass('switch-mark');
            inp.val(0);
          }

          cell.parents('form').change();
        }
      );
    }
  }
);
