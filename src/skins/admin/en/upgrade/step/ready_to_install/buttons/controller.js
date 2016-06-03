/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Upgrade buttons controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var UpgradeButtonsBox = function(submitFormFlag)
{
  jQuery('.ready-to-install-actions button.submit').click(
    function () {
      var result = false;
      var agreeCheckbox = jQuery('.ready-to-install-actions .alert.agree input[type="checkbox"]');

      if (agreeCheckbox) {
        result = jQuery(agreeCheckbox).is(':checked');
      }

      return result;
    }
  );

  jQuery('.ready-to-install-actions .alert.agree input[type="checkbox"]').change(
    function () {
      var state = jQuery(this).is(':checked');
      var button = jQuery('.ready-to-install-actions button.submit').eq(0);
      if (button) {
        var box = jQuery('.ready-to-install-actions .alert');
        if (state) {
          jQuery(button).removeClass('disabled');
          jQuery(button).prop('disabled', false);
          jQuery(box).removeClass('alert-warning');
          jQuery(box).addClass('alert-success');

        } else {
          jQuery(button).addClass('disabled')
          jQuery(button).prop('disabled', true);
          jQuery(box).removeClass('alert-success');
          jQuery(box).addClass('alert-warning');
        }
      }
    }
  );
}

jQuery().ready(UpgradeButtonsBox);
