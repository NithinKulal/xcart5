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
    data: function() {
      var parent = Checkout.BillingAddressForm.super.options.data.apply(this, arguments);
      return _.extend(parent, {
        same_address: null
      });
    },

    watch: {
      same_address: function(value, oldValue) {
        this.toggle(!value);

        if (oldValue !== null) {
          // wait for DOM update
          this.$nextTick(function(){
            jQuery('#billingaddress-country-code').change();
            this.$emit('modify');
          });
        }
      }
    },

    methods: {
      toDataObject: function() {
        var parent = Checkout.BillingAddressForm.super.options.methods.toDataObject.apply(this, arguments);
        return _.extend(parent, {
          same_address: this.same_address ? 1 : 0
        });
      }
    }
  });
});