/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define(
  'Checkout.AddressSection',
  ['Checkout.SectionMixin', 'Checkout.ShippingAddressForm', 'Checkout.BillingAddressForm', 'Checkout.NextButton', 'Checkout.CartItems'],
  function(){

  Checkout.AddressSection = Vue.extend({
    mixins: [Checkout.SectionMixin],
    name: 'address-section',
    replace: false,

    vuex: {
      getters: {
        fields: function(state) {
          return state.sections.list.address.fields;
        },
        complete: function(state) {
          return state.sections.list.address.complete;
        }
      },
    },

    data: function () {
      return {
        name: 'address',
        endpoint: {
          target: 'checkout',
          action: 'update_profile'
        },
      };
    },

    ready: function() {
      UpdateStatesList();
    },

    components: {
      ShippingAddressForm: Checkout.ShippingAddressForm,
      BillingAddressForm: Checkout.BillingAddressForm,
      NextButton: Checkout.NextButton,
      CartItems: Checkout.CartItems,
    }
  });

});