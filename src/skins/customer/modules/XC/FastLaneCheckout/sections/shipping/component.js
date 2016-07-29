/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.ShippingSection', ['Checkout.Sections', 'Checkout.SectionMixin', 'Checkout.ShippingMethods', 'Checkout.Address', 'Checkout.CartItems', 'Checkout.OrderNotes', 'Checkout.NextButton'], function(){

  Checkout.ShippingSection = Vue.extend({
    mixins: [Checkout.SectionMixin],
    name: 'shipping-section',
    replace: false,

    vuex: {
      getters: {
        complete: function(state) {
          return state.sections.list.shipping.complete;
        }
      },
    },

    data: function () {
      return {
        index: 1,
        name: 'shipping',
        endpoint: {
          target: 'checkout',
          action: 'shipping'
        }
      }
    },

    components: {
      ShippingMethods: Checkout.ShippingMethods,
      Address: Checkout.Address,
      CartItems: Checkout.CartItems,
      OrderNotes: Checkout.OrderNotes,
      NextButton: Checkout.NextButton,
    }
  });

  Checkout.Sections = Checkout.Sections.extend({
    components: _.extend(Checkout.Sections.options.components, {
      ShippingSection: Checkout.ShippingSection,
    })
  });

});
