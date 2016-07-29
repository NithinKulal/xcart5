/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * next_button.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.SectionChangeButton', ['Checkout.PlaceOrder', 'Checkout.NextButton'], function(){
  Checkout.SectionChangeButton = Vue.extend({
    name: 'section-change-button',
    replace: false,

    vuex: {
      getters: {
        current: function(state) {
          return state.sections.current;
        },
        total_text: function(state) {
          return state.order.total_text;
        },
      },
    },

    components: {
      PlaceOrder: Checkout.PlaceOrder,
      NextButton: Checkout.NextButton,
    },

    ready: function() {
      $('.checkout_fastlane_section-buttons', this.$el).removeClass('loading');
    },

    methods: {
      scrollToDetails: function () {
        var top = $('.checkout_fastlane_details_box:visible').offset().top - 80;
        $(window).scrollTop( top );
      },
    },

    computed: {
      showPlaceOrder: function() {
        if (this.current) {
          return this.current.name === 'payment';
        } else {
          return false;
        };
      },
      complete: function(state) {
        if (this.current) {
          return this.current.complete;
        } else {
          return false;
        };
      },
      index: function(state) {
        if (this.current) {
          return this.current.index;
        } else {
          return 0;
        };
      }
    },
  });
});
