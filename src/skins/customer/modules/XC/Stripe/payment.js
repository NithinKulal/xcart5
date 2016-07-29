/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Stripe initialize
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  'checkout.main.initialize',
  function() {
    var handler = null;

    core.bind(
      'checkout.paymentTpl.postprocess',
      function(event, data) {
        var box = jQuery('.stripe-box');
        if (box.length && typeof(window.StripeCheckout) != 'undefined' && !handler) {

          // Configure Stripe handler
          var options = {
            key:   box.data('key'),
            token: function(token, args) {
              jQuery('.stripe-box .token').val(token.id);
              jQuery('body').css('overflow', 'visible');
              jQuery('form.place').submit();
            }
          };
          if (box.data('image')) {
            options.image = box.data('image');
          }
          handler = StripeCheckout.configure(options);

          if (!_.isUndefined(data) && !_.isUndefined(data.widget)) {
            // Update payment template by change of cart total
            PaymentTplView.prototype.handleUpdateCartStripe = function (event, data)
            {
              if (!this.isLoading && 'undefined' != typeof(data.total)) {
                this.load();
              }
            }

            core.bind(
              'updateCart',
              _.bind(data.widget.handleUpdateCartStripe, data.widget)
            );
          };
        }
      }
    );

    core.bind(
      'checkout.common.ready',
      function(event, state) {
        var box = jQuery('.stripe-box');
        if (handler && box.length && !box.find('.token').val()) {
          var email = jQuery('#email').val();
          handler.open({
            name:        box.data('name'),
            description: box.data('description'),
            amount:      box.data('total'),
            currency:    box.data('currency'),
            email:       email ? email : box.data('email')
          });

          state.state = false;
        }
      }
    );

  }
);
