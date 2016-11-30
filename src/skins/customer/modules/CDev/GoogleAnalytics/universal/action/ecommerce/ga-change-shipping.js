/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/changeShippingEvent', [ 'googleAnalytics/event' ], function(Event) {
  GAChangeShippingEvent = Event.extend({
    namespace: 'Checkout  ',

    getListeners: function() {
      return {
        'updateCart': this.updateCartHandler
      };
    },

    updateCartHandler: function(event, data) {
      if (data.shippingMethodName) {
        this.sendEvent('changeShippingMethod', data.shippingMethodName);
      }
    },
  });

  GAChangeShippingEvent.instance = new GAChangeShippingEvent();

  return GAChangeShippingEvent;
});