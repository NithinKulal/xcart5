/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * place-order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/sections/order_total',
 ['vue/vue',
  'checkout_fastlane/sections'],
  function(Vue, Sections){

  var OrderTotal = Vue.extend({
    name: 'order-total',
    replace: false,

    vuex: {
      getters: {
        total_text: function(state) {
          return state.order.total_text;
        },
      },
    },

  });

  Vue.registerComponent(Sections, OrderTotal);

  return OrderTotal;
});
