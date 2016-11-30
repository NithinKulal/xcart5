/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function(){
  var shippingStepBox = jQuery('.shipping-step');

  var processHelpClasses = function(data) {
    shippingStepBox.removeClass('help-create-profile-warning')
        .removeClass('help-create-profile-note')
        .removeClass('help-allow-create-profile');

    if (data.value) {
      if (data.agree) {
        shippingStepBox.addClass('help-create-profile-note');

      } else {
        shippingStepBox.addClass('help-create-profile-warning');
      }

    } else {
      shippingStepBox.addClass('help-allow-create-profile');
    }
  };

  if (shippingStepBox.length) {
    processHelpClasses({});
  }

  decorate(
      'CheckoutAddressView',
      'handleLoginExists',
      function(event, data) {
        processHelpClasses(data);

        return arguments.callee.previousMethod.apply(this, arguments);
      }
  );

  decorate(
      'CheckoutAddressView',
      'handleEmailInvalid',
      function(event, data) {
        shippingStepBox.addClass('help-email-invalid');

        return arguments.callee.previousMethod.apply(this, arguments);
      }
  );
  
  decorate(
      'CheckoutAddressView',
      'handleEmailValid',
      function(event, data) {
        shippingStepBox.removeClass('help-email-invalid');

        return arguments.callee.previousMethod.apply(this, arguments);
      }
  );
})();
