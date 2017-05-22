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
      'ProductsListView',
      'postprocess',
      function(isSuccess, initial)
      {
        arguments.callee.previousMethod.apply(this, arguments);

        if (isSuccess) {
          // TODO: remove the line below later
          // jQuery('.products-grid td.product-cell:first-child div.product').width(jQuery('.products-grid td.product-cell div.product').width());

          jQuery('div.product').mouseover(
            function() {
              jQuery(this).addClass('compare');
            }
          ).mouseout(
            function() {
              jQuery(this).removeClass('compare');
            }
          );
    
          jQuery('div.add-to-compare.products div.compare-popup').mouseleave(
            function() {
              jQuery(this).removeClass('visible');
            }
          );

          product_comparison();
        }
      }
    );
  }
);
