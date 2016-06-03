/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Products list controller (search blox)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Decoration of the products list widget class
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
          var o = this;

          // handle "Search" button in the search products form
          if (jQuery(this.base).hasClass('products-search-result')) {
            jQuery('.search-product-form form').unbind('submit').submit(
              function (event)
              {
                if (
                  o.submitForm(
                    this,
                    function (XMLHttpRequest, textStatus, data, isValid) {
                      if (isValid) {
                        o.load();
                      } else {
                        o.unshade();
                      }
                    }
                  )
                ) {
                  o.shade();
                }

                return false;
              }
            );
          }

        } // if (isSuccess) {
      } // function(isSuccess, initial)
    ); // 'postprocess' method decoration (EXISTING method)
  }
); // core.bind()
