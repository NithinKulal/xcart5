/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define(
  'Checkout.AddressSection',
  ['Checkout.SectionMixin', 'Checkout.BillingAddressForm', 'Checkout.CartItems', 'Checkout.NextButton'],
  function(){

  Checkout.AddressSection = Vue.extend({
    mixins: [Checkout.SectionMixin],
    name: 'address-section',
    replace: false,

    vuex: {
      getters: {
        complete: function(state) {
          return state.sections.list.address.complete;
        }
      },
    },

    data: function () {
      return {
        index: 0,
        name: 'address',
        endpoint: {
          target: 'checkout',
          action: 'update_profile'
        },
      };
    },

    ready: function() {
      this.updateStates();
    },

    methods: {
      updateStates: function() {
        if (typeof StateSelector !== 'undefined') {
          StateSelector.updateStateValueOnce = true;
          UpdateStatesList();
        }
      }
    },

    events: {
      trigger_email_check: function(event) {
        var data = {
          'email': event.email
        };

        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          this.endpoint,
          null,
          data,
          this.request_options
        )
        .fail(function(){
          core.showError('Server connection error. Please check your Internet connection.');
        });
      },
    },

    components: {
      BillingAddressForm: Checkout.BillingAddressForm,
      CartItems: Checkout.CartItems,
      NextButton: Checkout.NextButton,
    }
  });

});
