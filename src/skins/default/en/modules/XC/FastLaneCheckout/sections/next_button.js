/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * next_button.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.NextButton', [], function(){
  Checkout.NextButton = Vue.extend({
    name: 'next-button',
    props: ['enabled'],

    methods: {
      requestNext: function() {
        if (this.ready) {
          this.$root.$broadcast('requestNext');
        }
      }
    },

    data: function() {
      return {
        blockers: []
      }
    },

    computed: {
      ready: {
        cache: false,
        get: function() {
          return this.enabled && _.isEmpty(this.blockers);
        }
      },
      classes: function() {
        return {
          'disabled': !this.ready
        }
      }
    },

    events: {
      reloadingBlock: function(sender) {
        this.blockers.push(sender);
      },
      reloadingUnblock: function(sender) {
        this.blockers.pop();
      },
    }
  });
});
