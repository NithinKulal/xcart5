/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * navigation.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.Navigation', ['Checkout.NavigationItem'], function(){
  Checkout.Navigation = Vue.extend({
    name: 'navigation',
    replace: false,

    vuex: {
      getters: {
        sections: function(state) {
          return state.sections;
        }
      },
      actions: {
        dispatchSwitch: function(state, target) {
          state.dispatch('SWITCH_SECTION', target);
        },
        toggleSection: function(state, name, value) {
          state.dispatch('TOGGLE_SECTION', name, value);
        }
      }
    },

    computed: {
      currentIndex: function() {
        var item = this.$children.find(function(item) {
          return item.isActive;
        });

        return item ? item.index : 0;
      }
    },

    methods: {
    },

    events: {
      requestNext: function() {
        if (this.sections.current.complete) {
          var self = this;
          var next = this.$children.find(function(item) {
            return item.index > self.currentIndex;
          });

          if (!next.isEnabled) {
            this.toggleSection(next.name, true);
          }

          this.$nextTick(function() {
            this.$emit('switchTo', next);
          })
        }
      },
      switchTo: function(target) {
        if (_.isObject(target)) {
          target = target.name;
        }

        this.dispatchSwitch(target);
      }
    },

    directives: {

    },

    components: {
      NavigationItem: Checkout.NavigationItem
    },
  });
});