/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order-notes.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define(
  'checkout_fastlane/blocks/shipping_details',
  ['vue/vue', 
   'checkout_fastlane/sections'], 
  function(Vue, Sections) {

  var ShippingDetails = Vue.extend({
    name: 'shipping-details',
    replace: false,

    vuex: {
      getters: {
        order_shipping_method: function(state) {
          return state.order.shipping_method;
        },
      },
    },

    computed: {
      shipping_method: function() {
        return this.shippingMethodString();
      }
    },

    methods: {
      shippingMethodString: function() {
        return window.shippingMethodsList[parseInt(this.order_shipping_method, 10)];        
      }
    },

    watch: {
      isValid: function() {
        return true;
      }
    },
  });

  Vue.registerComponent(Sections, ShippingDetails);

  return ShippingDetails;
});
