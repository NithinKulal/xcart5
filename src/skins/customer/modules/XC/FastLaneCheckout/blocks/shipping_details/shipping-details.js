/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order-notes.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.ShippingDetails', ['Checkout.Sections'], function() {

  Checkout.ShippingDetails = Vue.extend({
    name: 'shipping-details',
    replace: false,

    vuex: {
      getters: {
        order_shipping_method: function(state) {
          return state.order.shipping_method;
        },
        order_notes: function(state) {
          return state.order.notes;
        },
      },
    },

    computed: {
      shipping_method: function() {
        return Checkout.shippingMethodString.apply(this, arguments);
      }
    },

    watch: {
      isValid: function() {
        return true;
      }
    },
  });

  Checkout.shippingMethodString = function() {
    return window.shippingMethodsList[parseInt(this.order_shipping_method, 10)];
  }

  Checkout.Sections = Checkout.Sections.extend({
    components: _.extend(Checkout.Sections.options.components, {
      ShippingDetails: Checkout.ShippingDetails,
    })
  });

});
