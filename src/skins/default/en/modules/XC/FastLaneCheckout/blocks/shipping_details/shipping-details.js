/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order-notes.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.ShippingDetails', ['Checkout.PaymentSection'], function() {

  Checkout.ShippingDetails = Vue.extend({
    name: 'shipping-details',
    replace: false,

    vuex: {
      getters: {
        shipping_method: function(state) {
          return window.shippingMethodsList[state.order.shipping_method];
        },
        order_notes: function(state) {
          return state.order.notes;
        },
      },
    },

    watch: {
      isValid: function() {
        return true;
      }
    },
  });

  Checkout.PaymentSection = Checkout.PaymentSection.extend({
    components: _.extend(Checkout.PaymentSection.options.components, {
      ShippingDetails: Checkout.ShippingDetails,
    })
  });

});
