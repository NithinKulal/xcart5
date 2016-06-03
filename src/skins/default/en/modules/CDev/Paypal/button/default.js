/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var paypalExpressCheckout = function (element, isAdd2Cart) {
  if (isAdd2Cart) {
    element.commonController.backgroundSubmit = false;
    jQuery(element).removeAttr('onsubmit');
    element['expressCheckout'].value = 1
  }

  return true;
};
