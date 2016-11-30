/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * place-order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('mailchimp/checkout_fastlane/sections/payment/place_order',
  ['checkout_fastlane/sections/payment/place_order',
   'checkout_fastlane/sections/section_change_button'],
  function(PlaceOrder, SectionChangeButton) {

  var parent = PlaceOrder.options.methods.assignHandlers;
  
  var PlaceOrder = PlaceOrder.extend({
    methods: {
      assignHandlers: function () {
        parent.apply(this, arguments);
        this.form.bind('beforeSubmit', _.bind(this.mailchimpBeforeSubmit, this));
      },

      mailchimpBeforeSubmit: function () {
        var placeContainer = $('form.place div.subscriptions-list-container input[type="checkbox"]');
        if (!placeContainer.is(':visible')) {
          placeContainer.attr('checked', $('.checkout_fastlane_cart div.subscriptions-list-container input[type="checkbox"]').attr('checked'));
        }
      }
    }
  });

  Vue.registerComponent(SectionChangeButton, PlaceOrder);

  return PlaceOrder;
});