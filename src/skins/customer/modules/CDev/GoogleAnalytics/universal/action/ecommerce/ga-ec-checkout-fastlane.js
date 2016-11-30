/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'googleAnalytics/checkout_fastlane/sections/payment/place_order', 
  ['checkout_fastlane/sections/payment/place_order',
   'googleAnalytics/eCommerceCheckoutFastlaneEvent',
   'checkout_fastlane/sections/section_change_button',
   'ready'],
  function(PlaceOrder, eCommerceCheckoutFastlaneEvent, SectionChangeButton){
    var oldPlaceOrder = PlaceOrder.options.methods.placeOrder;

    var PlaceOrder = PlaceOrder.extend({
      methods: {
        placeOrder: function() {
          var self = this;
          eCommerceCheckoutFastlaneEvent.instance.paymentSectionCompleted(function(){
            oldPlaceOrder.apply(self, arguments);
          });
        },
      }
    });

    Vue.registerComponent(SectionChangeButton, PlaceOrder);

    return PlaceOrder;
  }
);

define('googleAnalytics/eCommerceCheckoutFastlaneEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceCheckoutFastlaneEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'fastlane_section_switched':  this.sectionChanged,
          };
        },

        paymentSectionCompleted: function(callback) {

          this.registerCompletedSection(
            Checkout.instance.getState().sections.current
          );

          core.bind('ga-option-sent', callback);
        },

        sectionChanged: function (event, data) {
          if (!_.isUndefined(data.oldSection) && !_.isUndefined(Checkout.instance)) {
            this.registerCompletedSection(data.oldSection);
            this.registerNewSection(data.newSection);
          }
        },

        registerCompletedSection: function (section) {
          var step = section.index + 1;

          core.trigger('ga-ec-checkout-option', {
            step: step,
            option: this.getOptionBySection(section.name),
          });
        },
        
        registerNewSection: function (section) {
          var step = section.index + 1;
          var checkoutActionData = _.first(
              this.getActions('checkout')
          );
          var data = {
            products:     checkoutActionData.data.products,
            actionData:   { step: step },
            message:      'Checkout continue'
          };

          core.trigger('ga-ec-checkout', data);
        },

        getOptionBySection: function (sectionName) {
          var order = Checkout.instance.getState().order;

          if (sectionName === 'address') {
            return 'Address chosen';

          } else if (sectionName === 'shipping') {
            return this.getShippingMethodName(order.shipping_method);

          } else if (sectionName === 'payment') {
            return this.getPaymentMethodName(order.payment_method);
          }

          return sectionName + ' completed';
        },

        getPaymentMethodName: function (id) {
          return jQuery('#pmethod' + parseInt(id)).siblings('.payment-title').text();
        },

        getShippingMethodName: function (id) {
          return window.shippingMethodsList[parseInt(id)];
        },

      });

      eCommerceCheckoutFastlaneEvent.instance = new eCommerceCheckoutFastlaneEvent();

      return eCommerceCheckoutFastlaneEvent;
    }
);