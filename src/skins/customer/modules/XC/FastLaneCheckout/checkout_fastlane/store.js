/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * store.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('checkout_fastlane/store', ['vue/vue', 'vue/vuex', 'checkout_fastlane/store/sections', 'checkout_fastlane/store/order'], function(Vue, Vuex, Sections, Order){
  Vue.use(Vuex);

  return new Vuex.Store({
    modules: {
      sections: Sections,
      order: Order
    }
  });
})