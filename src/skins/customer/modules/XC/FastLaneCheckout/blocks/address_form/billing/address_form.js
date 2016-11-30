/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address_form.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define(
  'checkout_fastlane/blocks/address_form/billing',
 ['vue/vue',
  'checkout_fastlane/blocks/address_form',
  'checkout_fastlane/sections/address'],
  function(Vue, AddressForm, AddressSection) {

  var BillingAddressForm = AddressForm.extend({
    name: 'billing-address-form',

    vuex: {
      actions: {
        updateSameAddressFlag: function(state, value) {
          state.dispatch('UPDATE_SAME_ADDRESS', value);
        },
        updateBillingAddress: function(state, data) {
          state.dispatch('UPDATE_BILLING_ADDRESS', data);
        },
      },
    },

    data: function() {
      return _.extend(BillingAddressForm.super.options.data.apply(this, arguments), {
        same_address: window.WidgetData['same_address'],
        visible: !window.WidgetData['same_address']
      });
    },

    created: function() {
      this.shortType = 'b';
      this.fullType = 'billing';
    },

    ready: function() {
      this.updateBillingAddress(this.toDataObject());
    },

    watch: {
      'same_address': function(value, oldValue) {
        this.toggle(!value);

        this.$nextTick(function(){
          jQuery('#billingaddress-country-code').change();

          this.persistSameAsShipping(value, oldValue);
        });
      }
    },

    events: {
      global_selectcartaddress: function(data) {
        if (data.same != !!this.fields.same_address) {
          this.nonPersistMode = true;
          this.same_address = data.same ? 1 : 0;
        }
      },
      global_updatecart: function(data) {
        if (_.has(data, 'sameAddress') && data['sameAddress'] != !!this.fields.same_address) {
          this.nonPersistMode = true;
          this.same_address = data['sameAddress'] ? 1 : 0;
        }
      }
    },

    methods: {
      persistSameAsShipping: _.debounce(
        function(value, oldValue){
          var force = oldValue !== null;
          this.$emit('modify', value, oldValue, force);
        },
        100
      ),

      preprocess: function(data) {
        if (this.same_address == 1 || !this.isValid) {
          return {
            'same_address': this.same_address
          };
        } else {
          return BillingAddressForm.super.options.methods.preprocess.apply(this, arguments);
        }
      },

      triggerUpdate: function(options) {
        this.updateBillingAddress(this.toDataObject());
        this.updateSameAddressFlag(this.same_address);
        BillingAddressForm.super.options.methods.triggerUpdate.apply(this, arguments);
      },

      toDataObject: function() {
        return _.extend(BillingAddressForm.super.options.methods.toDataObject.apply(this, arguments), {
          'same_address': this.same_address
        });
      },
    }
  });

  Vue.registerComponent(AddressSection, BillingAddressForm);

  return BillingAddressForm;
});
