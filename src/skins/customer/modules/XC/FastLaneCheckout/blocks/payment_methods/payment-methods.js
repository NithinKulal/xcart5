/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * payment-methods.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/blocks/payment_methods',
 ['vue/vue',
  'vue/vue.loadable',
  'checkout_fastlane/sections/payment'],
  function(Vue, VueLoadableMixin, PaymentSection) {

  var PaymentMethods = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'payment-methods',
    replace: false,

    loadable: {
      transferState: false,
      loader: function() {
        this.$root.$broadcast('reloadingBlock', 2);
        this.$set('payment', {});
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\PaymentMethods'
        }, undefined, undefined, { timeout: 45000 });
      },
      resolve: function() {
        this.$root.$broadcast('reloadingUnblock', 2);
      },
      reject: function() {
        this.$root.$broadcast('reloadingUnblock', 2);
      }
    },

    vuex: {
      actions: {
        updatePaymentData: function(state, value) {
          state.dispatch('UPDATE_PAYMENT_DATA', value);
        },
        updatePaymentMethod: function(state, value) {
          state.dispatch('UPDATE_PAYMENT_METHOD', value);
        },
      }
    },

    created: function() {
      this.form = jQuery();
      this.isInitialChecked = false;
    },

    ready: function() {
      this.form = jQuery('form.payment-form');
      new CommonForm(this.form);
      core.trigger('checkout.paymentTpl.postprocess');
      core.trigger('checkout.paymentTpl.loaded');
    },

    data: function() {
      return {
        required: null,
        methodId: null,
        payment: {}
      };
    },

    computed: {
      paymentData: function() {
        return _.reduce(
          this.payment,
          function(result, value, key) {
            if (key.lastIndexOf('cc_', 0) === 0) {
              return result;
            };
            var newKey = 'payment[' + key + ']';
            result[newKey] = value;
            return result;
          }, {}
        );
      },

      classes: function () {
        return {
          'reloading': this.$reloading
        }
      },

      formIsValid: {
        cache: false,
        get: function() {
          return !_.isEmpty(this.payment)
            ? !_.isUndefined(this.form.get(0).commonController) && this.form.get(0).commonController.validate({
              silent: !this.form.get(0).commonController.wasFilledOnce(),
              focus: false
            })
            : true;
        }
      },

      isValid: {
        cache: false,
        get: function() {
          return this.methodId !== null && this.formIsValid || this.required === false;
        }
      }
    },

    watch: {
      required: function(value, oldValue) {
        this.triggerUpdate({
          silent: oldValue === null,
        });
      },
      methodId: function(value, oldValue){
        var silent = (oldValue === null);
        if (!silent) {
          this.$reloading = true;
          this.$root.$broadcast('reloadingBlock', 2);
        }
        this.triggerUpdate({
          silent: silent,
          force: true,
        });
      },
      paymentData: _.throttle(function(value, oldValue) {
        this.triggerUpdate({
          silent: true
        });
      }, 300)
    },

    events: {
      sectionSwitch: function(current) {
        if (!this.isInitialChecked && current == 'payment') {
          this.triggerUpdate({
            silent: true,
          });
          this.isInitialChecked = true;
        }
      },
      sectionPersist: function(data) {
        this.$reloading = false;
        this.$root.$broadcast('reloadingUnblock', 2);
      },
      global_updatecart: function(data) {
        var triggerKeys = ['paymentMethodsHash', 'paymentMethodId'];
        var needsUpdate = _.some(triggerKeys, function(key) {
          return _.has(data, key);
        });

        if (needsUpdate) {
          this.$reload();
        }
      },
    },

    methods: {
      triggerUpdate: function(options) {
        options = options || {};
        var eventArgs = _.extend({
          sender: this,
          isValid: this.isValid,
          fields: this.toDataObject()
        }, options);

        this.$dispatch('update', eventArgs);
        this.updatePaymentData(this.paymentData);
        this.updatePaymentMethod(this.methodId);
      },
      toDataObject: function() {
        return _.extend({
          methodId: this.methodId
        }, this.paymentData);
      },
    }
  });

  Vue.registerComponent(PaymentSection, PaymentMethods);

  return PaymentMethods;
});
