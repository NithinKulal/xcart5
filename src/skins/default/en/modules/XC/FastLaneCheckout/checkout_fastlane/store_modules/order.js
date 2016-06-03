/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.StoreOrder', [], function(){
	Checkout.StoreOrder = {
    state: {
      total: 0.0,
      total_text: "",
      shipping_method: null,
      notes: null
    },

    mutations: {
      UPDATE_TOTAL: function (state, number, text) {
        state.total = number;
        state.total_text = text;
      },

      UPDATE_SHIPPING_METHOD: function (state, value) {
        state.shipping_method = value;
      },

      UPDATE_NOTES: function (state, value) {
        state.notes = value;
      },
    }
	}
});