/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Upgrade buttons controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var UpgradeButtonsBox = function (submitFormFlag) {
  var agreeCheckboxes = jQuery('.incompatible-list .alert.agree input[type="checkbox"], .incompatible-list-actions .alert.agree input[type="checkbox"]');
  jQuery('.incompatible-list-actions button.submit').click(
    function () {
      var result = true;

      agreeCheckboxes.each(function () {
        if (!$(this).is(':checked')) {
          result = false;
        }
      });

      return result;
    }
  );

  agreeCheckboxes.change(function () {
    var state = true;
    var button = jQuery('.incompatible-list-actions button.submit').eq(0);

    agreeCheckboxes.each(function () {
      if (!$(this).is(':checked')) {
        state = false;
      }
    });

    if (button) {
      if (state) {
        jQuery(button).removeClass('disabled');
        jQuery(button).prop('disabled', false);

      } else {
        jQuery(button).addClass('disabled');
        jQuery(button).prop('disabled', true);
      }
    }
  });

  agreeCheckboxes.change();
};

jQuery().ready(UpgradeButtonsBox);
