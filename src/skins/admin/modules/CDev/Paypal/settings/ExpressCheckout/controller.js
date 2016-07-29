/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Express Checkout controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
(function () {

  var checkAuthType = function (authType) {
    if ('email' == authType) {
      jQuery('.section_api').hide();
      jQuery('#email')
        .prop('disabled', false)
        .removeClass('no-validate');
      jQuery('li.input-text-email .star')
        .css('visibility', 'visible');

    } else {
      jQuery('.section_api').show();
      jQuery('#email')
        .prop('disabled', true)
        .addClass('no-validate');
      jQuery('li.input-text-email .star')
        .css('visibility', 'hidden');
    }
  };

  jQuery().ready(
    function () {
      var authTypeRadioButtons = jQuery('input:radio[name="api_type"]');

      authTypeRadioButtons.change(
        function () {
          checkAuthType(authTypeRadioButtons.filter(':checked').val());
        }
      );

      checkAuthType(authTypeRadioButtons.filter(':checked').val());
    }
  );

})();
