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
    props: ['enabled', 'index'],

    vuex: {
      getters: {
        current: function(state) {
          return state.sections.current;
        }
      }
    },

    methods: {
      requestNext: function() {
        if (this.ready) {
          this.$root.$broadcast('requestNext');
        } else {
          this.$root.$broadcast('requestNextNotReady');
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
      nextLabel: function() {
        if (this.current) {
          return this.current.nextLabel;
        } else {
          return core.t('Next step');
        };
      },
      classes: function() {
        return {
          'disabled': !this.ready,
          'reloading': !_.isEmpty(this.blockers),
          'reloading-animated': !_.isEmpty(this.blockers),
        }
      },
      btnTitle: function() {
        return !this.ready
            ? "Some of the required fields were not completed. Please check the form and try again"
            : "Click to proceed to the next step";
      }
    },

    events: {
      reloadingBlock: function(level) {
        if (this.index >= level) {
          this.blockers.push(level);
        }
      },
      reloadingUnblock: function(level) {
        if (this.index >= level) {
          this.blockers.pop();
        }
      },
    }
  });
});
