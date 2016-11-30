/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/blocks/email',
  ['vue/vue',
   'vue/vue.loadable',
   'checkout_fastlane/sections/shipping'],
  function(Vue, VueLoadableMixin, ShippingSection) {

  var Email = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'email',
    replace: false,

    loadable: {
      transferState: false,
      cacheSimultaneous: true,
      loader: function() {
        this.$root.$broadcast('reloadingBlock', 3);
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\Email'
        }, undefined, undefined, { timeout: 45000 });
      },
      resolve: function() {
        this.$root.$broadcast('reloadingUnblock', 3);
      },
      reject: function() {
        this.$root.$broadcast('reloadingUnblock', 3);
      }
    },

    data: function() {
      return {
        email: null,
        isEdited: false
      };
    },

    ready: function() {
      this.input = $(this.$el).find('input').get(0);
      new CommonElement(this.input);
      this.initial = JSON.parse(JSON.stringify(this.email));
    },

    computed: {
      isValid: {
        cache: false,
        get: function() {
          var silent = !this.input.commonController.isChanged();
          return !_.isUndefined(this.input.commonController) && this.input.commonController.validate(silent);
        }
      },
      btnTitle: function() {
        return core.t("Edit email");
      }
    },

    methods: {
      undoEdit: function() {
        this.email = this.initial;
        this.toggle();
      },
      toggle: function() {
        this.isEdited = !this.isEdited;

        if (this.isEdited) {
          var input = $(this.$el).find('input');
          this.$nextTick(function() {
            input.focus();
          });
        }
      },
      save: function () {
        if (this.isValid && this.initial !== this.email) {
          this.$dispatch('trigger_email_check', {
            email: this.email,
          });
          this.initial = JSON.parse(JSON.stringify(this.email));
        }

        this.toggle();
      }
    }

  });

  Vue.registerComponent(ShippingSection, Email);

  return Email;
});
