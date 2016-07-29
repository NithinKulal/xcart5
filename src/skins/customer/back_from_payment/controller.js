/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Back from payment controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Main widget
 */

decorate(
  'CheckoutView',
  'postprocess',
  function(isSuccess, initial)
  {
    arguments.callee.previousMethod.apply(this, arguments);

    if (isSuccess) {
      popup.load(URLHandler.buildURL({ 'target': 'back_from_payment'}));
    }
  }
);

