/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * sections.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define(
  'Checkout.Sections',
  ['Checkout.AddressSection','Checkout.ShippingSection', 'Checkout.PaymentSection'],
  function(){
    Checkout.Sections = Vue.extend({
      name: 'sections',
    	replace: false,

      vuex: {
        getters: {
        }
      },

      created: function() {
      },

      components: {
        AddressSection: Checkout.AddressSection,
        ShippingSection: Checkout.ShippingSection,
        PaymentSection: Checkout.PaymentSection,
      },
    });
  }
);