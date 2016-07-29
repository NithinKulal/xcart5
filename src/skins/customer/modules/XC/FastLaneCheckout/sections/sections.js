/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * sections.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define(
  'Checkout.Sections',
  ['Checkout.AddressSection', 'Checkout.PaymentSection', 'Checkout.SectionChangeButton'],
  function(){
    Checkout.Sections = Vue.extend({
      name: 'sections',
    	replace: false,

      vuex: {
        getters: {
          current: function(state) {
            return state.sections.current;
          }
        },
        actions: {
          dispatchSwitch: function(state, target) {
            state.dispatch('SWITCH_SECTION', target);
          },
        }
      },

      ready: function() {
        $(this.$el).find('.checkout_fastlane_details_box').removeClass('loading');
      },

      components: {
        AddressSection: Checkout.AddressSection,
        PaymentSection: Checkout.PaymentSection,
        Address: Checkout.Address,
        CartItems: Checkout.CartItems,
        OrderNotes: Checkout.OrderNotes,
        SectionChangeButton: Checkout.SectionChangeButton
      },

      methods: {
        switchTo: function(target) {
          this.dispatchSwitch(target);
        },
      },

      computed: {
        classes: function() {
          var obj = {};
          obj['section-' + this.current.name] = true;

          return obj;
        },
      },
    });
  }
);
