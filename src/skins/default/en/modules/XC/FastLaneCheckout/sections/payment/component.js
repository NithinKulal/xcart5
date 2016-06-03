/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.PaymentSection', ['Checkout.SectionMixin', 'Checkout.PaymentMethods', 'Checkout.Address', 'Checkout.CartItems', 'Checkout.PlaceOrder'], function(){

  Checkout.PaymentSection = Vue.extend({
    mixins: [Checkout.SectionMixin],
    name: 'payment-section',
    replace: false,

    vuex: {
      getters: {
        fields: function(state) {
          return state.sections.list.payment.fields;
        },
        complete: function(state) {
          return state.sections.list.payment.complete;
        }
      },
    },

    data: function () {
      return {
        name: 'payment',
        endpoint: {
          target: 'checkout',
          action: 'payment'
        }
      }
    },

    components: {
      PaymentMethods: Checkout.PaymentMethods,
      Address: Checkout.Address,
      CartItems: Checkout.CartItems,
      PlaceOrder: Checkout.PlaceOrder
    }
  });
});