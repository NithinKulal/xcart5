/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product comparison
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  'load',
  function() {
    decorate(
      'ProductDetailsView',
      'postprocess',
      function(isSuccess, initial)
      {
        arguments.callee.previousMethod.apply(this, arguments);

        if (isSuccess) {
          jQuery('div.add-to-compare.product').mouseleave(
            function() {
              jQuery(this).find('div.compare-popup').removeClass('visible');
            }
          );
    
          product_comparison();
        }
      }
    );
  }
);
