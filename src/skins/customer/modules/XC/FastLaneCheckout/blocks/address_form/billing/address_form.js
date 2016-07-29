/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address_form.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.BillingAddressForm', ['Checkout.AddressForm'], function(){
  Checkout.BillingAddressForm = Checkout.AddressForm.extend({
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
      return _.extend(Checkout.BillingAddressForm.super.options.data.apply(this, arguments), {
        same_address: window.WidgetData['same_address'],
        visible: !window.WidgetData['same_address']
      });
    },

    created: function() {
      this.shortType = 'b';
    },

    ready: function() {
      this.updateBillingAddress(this.toDataObject());
    },

    watch: {
      'same_address': function(value, oldValue) {
        this.toggle(!value);

        this.$nextTick(function(){
          var force = oldValue !== null;

          jQuery('#billingaddress-country-code').change();
          this.$emit('modify', value, oldValue, force);
        });
      }
    },

    events: {
      global_selectcartaddress: function(data) {
        Checkout.BillingAddressForm.super.options.events.global_selectcartaddress.apply(this, arguments);
        if (data.same != !!this.fields.same_address) {
          this.nonPersistMode = true;
          this.same_address = data.same ? 1 : 0;
        }
      }
    },

    methods: {
      preprocess: function(data) {
        if (this.same_address == 1 || !this.isValid) {
          return {
            'same_address': this.same_address
          };
        } else {
          return Checkout.BillingAddressForm.super.options.methods.preprocess.apply(this, arguments);
        }
      },

      triggerUpdate: function(options) {
        this.updateBillingAddress(this.toDataObject());
        this.updateSameAddressFlag(this.same_address);
        Checkout.BillingAddressForm.super.options.methods.triggerUpdate.apply(this, arguments);
      },

      toDataObject: function() {
        return _.extend(Checkout.BillingAddressForm.super.options.methods.toDataObject.apply(this, arguments), {
          'same_address': this.same_address
        });
      },
    }
  });

});