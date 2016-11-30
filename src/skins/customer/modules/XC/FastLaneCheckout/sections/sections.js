/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * sections.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/sections',
  ['vue/vue'],
  function(Vue){

    var Sections = Vue.extend({
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

      methods: {
        switchTo: function(target) {
          this.dispatchSwitch(target);
        },
      },

      computed: {
        classes: function() {
          var obj = {};
          if (this.current !== null) {
            obj['section-' + this.current.name] = true;
          }

          return obj;
        },
      },
    });

    return Sections;
  }
);
