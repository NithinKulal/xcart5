/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/changePaymentMethod', [ 'googleAnalytics/event' ], function(Event) {
  GAChangePaymentEvent = Event.extend({
    namespace: 'Checkout',

    getListeners: function() {
      return {
        'updateCart': this.updateCartHandler
      };
    },

    updateCartHandler: function(event, data) {
      if (data.paymentMethodName && core.getTarget() === 'checkout') {
        this.sendEvent('changePaymentMethod', data.paymentMethodName);
      }
    },
  });

  GAChangePaymentEvent.instance = new GAChangePaymentEvent();

  return GAChangePaymentEvent;
});