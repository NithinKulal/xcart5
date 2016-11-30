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
      core.bind(
          'checkout.common.ready',
          function(event, state) {
            var box = jQuery('.paypal-in-context-box');

            var data = {
              notes: jQuery('textarea[name="notes"]').val()
            };

            var subscribe = null;
            var subscribeSelect = jQuery('select[name="subscribe"]');
            var subscribeCheckboxes = jQuery('input[name^="subscribe["]:checkbox');

            if (subscribeSelect.length > 0) {
              subscribe = jQuery(subscribeSelect[0]).val();
            } else if (jQuery(subscribeCheckboxes).length > 0) {
              subscribe = [];

              jQuery(subscribeCheckboxes).each(function(index,elem) {
                subscribe[jQuery(elem).attr('name').replace(/subscribe\[(.*)\]/, '$1')] = jQuery(elem).is(':checked') ? 1 : 0;
              });
            }

            if (subscribe != null) {
              data.subscribe = subscribe;
            }

            if (box.length) {
              form = box.closest('form').get(0);

              data[form.commonController.formIdName] = form.commonController.getFormId();
              core.post(
                  URLHandler.buildURL({
                    target: 'checkout',
                    action: 'setOrderNote'
                  }),
                  null,
                  data
              );

              // Initialize Paypal checkout
              paypal.checkout.initXO();

              var postOptions = {
                target: 'checkout',
                action: 'startExpressCheckout',
                inContext: true
              };
              postOptions[form.commonController.formIdName] = form.commonController.getFormId();
              core.post(URLHandler.buildURL(postOptions), null, {ignoreCheckout: true});

              core.bind('paypaltoken', function (event, result) {
                if (result.token) {
                  paypal.checkout.startFlow(result.token);
                } else {
                  paypal.checkout.closeFlow();
                }
              });

              state.state = false;
            }
          }
      );
    }
);
