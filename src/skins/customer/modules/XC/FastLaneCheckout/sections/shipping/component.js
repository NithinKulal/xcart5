/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/sections/shipping', 
  ['vue/vue',
   'checkout_fastlane/sections',
   'checkout_fastlane/sections/section_mixin'],
  function(Vue, Sections, SectionMixin){

  var ShippingSection = Vue.extend({
    mixins: [SectionMixin],
    name: 'shipping-section',
    replace: false,

    vuex: {
      getters: {
        complete: function(state) {
          return state.sections.list.shipping.complete;
        }
      },
    },

    data: function () {
      return {
        index: 1,
        name: 'shipping',
        endpoint: {
          target: 'checkout',
          action: 'shipping'
        },
        emailEndpoint: {
          target: 'checkout',
          action: 'update_profile'
        },
      }
    },

    ready: function() {
      if (!_.isUndefined(window.PopupButtonAddressBook)) {
        core.autoload(PopupButtonAddressBook);
      }
    },

    events: {
      trigger_email_check: function(event) {
        var data = event;

        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        $.when(this.xhr).then(_.bind(function() {
          this.xhr = core.post(
            this.emailEndpoint,
            null,
            data,
            this.request_options
          )
          .fail(function(){
            core.showError('Server connection error. Please check your Internet connection.');
          });
        }, this));
      }
    },
  });

  Vue.registerComponent(Sections, ShippingSection);

  return ShippingSection;
});
