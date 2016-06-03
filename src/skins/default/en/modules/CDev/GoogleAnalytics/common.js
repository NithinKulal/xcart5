/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Drupal-specific controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var _ga = {};

/**
 * Registers the search text event
 *
 * @param string substring Search text
 *
 * @return void
 */
_ga.gaRegisterSearchEvent = function (substring)
{
  _ga.gaRegisterEvent('search', 'do_search', substring);
};

/**
 * Registers the event
 *
 * @param string category Typically the object that was interacted with (e.g. button)
 * @param string action   The type of interaction (e.g. click)
 * @param string label    Useful for categorizing events (e.g. nav buttons)
 * @param number value    Values must be non-negative. Useful to pass counts (e.g. 4 times)
 *
 * @return void
 */
_ga.gaRegisterEvent = function (category, action, label, value)
{
  if ('undefined' != typeof(window._gaq)) {
    _gaq.push(['_trackEvent', category, action, label, value]);

  } else if ('undefined' != typeof(window.ga)) {
    ga('send', 'event', category, action, label, value);
  }
};

_ga.searchTextPattern = ".search-product-form input[name='substring']";

jQuery().ready(
  function() {

    // Detect add to cart
    core.bind(
      'updateCart',
      function(event, data) {

        if (data.items) {
          for (var i = 0; i < data.items.length; i++) {
            var item = data.items[i];

            if (item.quantity_change > 0 && item.quantity_change == item.quantity) {

              // Add to cart
              _ga.gaRegisterEvent('cart', 'add', item.key, item.quantity_change);

            } else if (item.quantity_change < 0 && (item.quantity == 0 || (item.quantity + item.quantity_change) <= 0)) {

              // Remove from cart
              _ga.gaRegisterEvent('cart', 'remove', item.key, item.quantity_change);

            } else {

              // Change quantity
              _ga.gaRegisterEvent('cart', 'change', item.key, item.quantity_change);
            }
          }
        }

        if (data.shippingMethodId) {

         // Change shipping method
         _ga.gaRegisterEvent('cart', 'changeShippingMethod', data.shippingMethodId);
        }

        if (data.paymentMethodId) {

         // Change payment method
         _ga.gaRegisterEvent('cart', 'changePaymentMethod', data.paymentMethodId);
        }

      }
    );

  }
);
