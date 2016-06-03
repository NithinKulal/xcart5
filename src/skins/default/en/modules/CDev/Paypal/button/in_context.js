/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
  var merchantId = core.getCommentedData(jQuery('body'), 'PayPalMerchantId');
  var environment = core.getCommentedData(jQuery('body'), 'PayPalEnvironment');

  paypal.checkout.setup(merchantId, {
    environment: environment,
    button: ['ec_minicart', 'ppc_minicart'],
    click: function () {
    }
  });
});

var paypalExpressCheckout = function (element, isAdd2Cart, url) {
  paypal.checkout.initXO();

  popup.close();
  if (isAdd2Cart) {
    element.commonController.backgroundSubmit = false;
    jQuery(element).removeAttr('onsubmit');
    element['expressCheckout'].value = 1
  }

  setTimeout(function () {
    element.target = "PPFrame";
    paypal.checkout.startFlow(url);
  }, 0);

  setTimeout(function () {
    if (isAdd2Cart) {
      element.commonController.backgroundSubmit = true;
      jQuery(element).attr('onsubmit', 'javascript: return false;');
      element['expressCheckout'].value = 0;
    }
    element.target = "";
  }, 500);

  return true;
};
