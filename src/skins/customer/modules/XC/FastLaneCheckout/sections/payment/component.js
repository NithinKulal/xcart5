/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/sections/payment', 
  ['vue/vue',
   'checkout_fastlane/sections',
   'checkout_fastlane/sections/section_mixin'],
  function(Vue, Sections, SectionMixin){

  var PaymentSection = Vue.extend({
    mixins: [SectionMixin],
    name: 'payment-section',
    replace: false,

    vuex: {
      getters: {
        complete: function(state) {
          return state.sections.list.payment.complete;
        }
      },
    },

    data: function () {
      return {
        index: 2,
        name: 'payment',
        endpoint: {
          target: 'checkout',
          action: 'payment'
        }
      }
    },

    ready: function() {
      if (!_.isUndefined(window.PopupButtonAddressBook)) {
        core.autoload(PopupButtonAddressBook);
      }
    },
  });

  Vue.registerComponent(Sections, PaymentSection);

  return PaymentSection;
});
