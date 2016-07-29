/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * navigation-item.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.NavigationItem', [], function(){
  Checkout.NavigationItem = Vue.extend({
    name: 'navigation-item',
    replace: false,

    vuex: {
      getters: {
        sections: function(state) {
          return state.sections;
        }
      },
    },

    data: function() {
      return {
        blockers: [],
        name: null,
        index: null
      };
    },

    watch: {
      isEnabled: function(state) {
        jQuery(this.$el).toggleClass('disabled', !state);
      },
      isActive: function(state) {
        if (state) {
          jQuery(this.$els.link).tab('show');
          $(window).scrollTop(0);
        }
      }
    },

    computed: {
      isActive: {
        cache: false,
        get: function() {
          return this.sections.current === this.sections.list[this.name];
        }
      },
      isEnabled: {
        cache: false,
        get: function() {
          return _.contains(this.sections.enabled, this.name) && _.isEmpty(this.blockers);
        }
      }
    },

    events: {
      reloadingBlock: function(level) {
        if (this.index > level) {
          this.blockers.push(level);
        }
      },
      reloadingUnblock: function(level) {
        if (this.index > level) {
          this.blockers.pop();
        }
      },
    },

    directives: {
      name: {
        update: function() {
          this.vm.name = this.expression;
        }
      },
      index: {
        update: function() {
          this.vm.index = parseInt(this.expression);
        }
      }
    },

    ready: function() {
      this.assignTabHandlers();
    },

    methods: {
      assignTabHandlers: function() {
      },
      triggerSwitch: function(){
        if (this.isEnabled && !this.isActive) {
          this.$dispatch('switchTo', this.name);
        }
      }
    }
  });
});