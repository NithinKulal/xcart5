/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal initialize Express Checkout on click 'Place order'
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  'checkout.main.initialize',
  function() {
    core.bind(
      'checkout.common.ready',
      function(event, state) {
        var box = jQuery('.paypal-in-context-box');
        if (box.length) {
          // Submit .place form to save fields in this form before start ExpressCheckout
          var form = box.closest('form').get(0);
          var actionElement = jQuery(form).find('input[type="hidden"][name="action"]');
          var oldAction = actionElement.val();
          actionElement.val('setOrderNote');
          form.submitBackground();
          actionElement.val(oldAction);

          // Initialize Paypal checkout
          paypal.checkout.initXO();

          var postOptions = {
            target: 'checkout',
            action: 'startExpressCheckout',
            inContext: true
          };
          postOptions[form.commonController.formIdName] = form.commonController.getFormId();
          core.post(
            URLHandler.buildURL(postOptions), null, {ignoreCheckout: true});

          core.bind('paypaltoken', function (event, result) {
            if (result.token) {
              paypal.checkout.startFlow(result.token);
            } else {
              paypal.checkout.closeFlow();
            }
          });

          state.state = false;
        }
      }
    );
  }
);
