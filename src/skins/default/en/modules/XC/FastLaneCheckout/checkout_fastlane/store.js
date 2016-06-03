/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * store.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.Store', ['Checkout.StoreSections', 'Checkout.StoreOrder'], function(){
  Vue.use(Vuex);

  Checkout.Store = new Vuex.Store({
    strict: true,
    modules: {
      sections: Checkout.StoreSections,
      order: Checkout.StoreOrder
    }

  })
})