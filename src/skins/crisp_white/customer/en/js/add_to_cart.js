/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function(){

  function flyToCart(element) {
    var target = $('.lc-minicart:visible');
    var item = getProductRepresentationFor(element);

    if (target.length && item.element && item.element.length) {
      $(item.element).fly(target, {
        view: item.view
      });
    }

  }

  decorate(
    'ProductsListView',
    'addToCart',
    function(element) {
      flyToCart(element);
      return arguments.callee.previousMethod.apply(this, arguments);
    }
  );

  decorate(
    'ProductDetailsView',
    'addProductToCart',
    function() {
      if (!this.base.hasClass('product-quicklook')) {
        flyToCart(this.base.find('form.product-details'));
      }
      return arguments.callee.previousMethod.apply(this, arguments);
    }
  );
})();