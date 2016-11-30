/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('checkout_fastlane/store/order', [], function(){
	return {
    state: {
      total: 0.0,
      total_text: "",
      shipping_method: null,
      payment_method: null,
      notes: null,
      address: {
        shipping: null,
        billing: null,
      },
      same_address: null,
      paymentData: null
    },

    mutations: {
      UPDATE_TOTAL: function (state, number, text) {
        state.total = number;
        state.total_text = text;
      },

      UPDATE_SHIPPING_METHOD: function (state, value) {
        state.shipping_method = value;
      },

      UPDATE_SHIPPING_ADDRESS: function (state, value) {
        state.address = _.extend(state.address, {
          shipping: value
        });
      },

      UPDATE_BILLING_ADDRESS: function (state, value) {
        state.address = _.extend(state.address, {
          billing: value
        });
      },

      UPDATE_SAME_ADDRESS: function (state, value) {
        state.same_address = value;
      },

      UPDATE_NOTES: function (state, value) {
        state.notes = value;
      },

      UPDATE_PAYMENT_DATA: function (state, value) {
        state.paymentData = value;
      },
      UPDATE_PAYMENT_METHOD: function (state, value) {
        state.payment_method = value;
      },
    }
	}
});
