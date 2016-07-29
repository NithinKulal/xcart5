/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Products list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ProductsListView(base)
{
  ProductsListView.superclass.constructor.apply(this, arguments);
}

extend(ProductsListView, ListView);

// Products list class
function ProductsListController(base)
{
  ProductsListController.superclass.constructor.apply(this, arguments);

  this.dragDropCart = core.getCommentedData(jQuery('body'), 'dragDropCart');

  core.bind(
    'updateCart',
    _.bind(
    function(event, data) {
      var productPattern, product;
      for (var i = 0; i < data.items.length; i++) {
        if (data.items[i].object_type == 'product') {

          // Added mark
          productPattern = '.product.productid-' + data.items[i].object_id;
          product = jQuery(productPattern, base);
          if (data.items[i].quantity > 0) {
            product.addClass('product-added');
            if (this.block) {
              this.block.triggerVent('item.addedToCart', {'view': this, 'item': product});
            }

          } else {
            product.removeClass('product-added');
            if (this.block) {
              this.block.triggerVent('item.removedFromCart', {'view': this, 'item': product});
            }
          }

          // Check inventory limit
          if (data.items[i].is_limit) {
            product
              .addClass('out-of-stock');

            product.each(function(){
              if (jQuery(this).hasClass('ui-draggable')) {
                jQuery(this).draggable('disable');
              }
            });

            if (this.block) {
              this.block.triggerVent('item.outOfStock', {'view': this, 'item': product});
            }

          } else {
            product
              .removeClass('out-of-stock');

            // We add the draggable product if 'dragDropCart' flag is on (currently it is on if non-mobile device is used)
            if (product.parents('.ui-draggable').length && dragDropCart) {
              product.draggable('enable');
            }

            if (this.block) {
              this.block.triggerVent('item.stockIncrease', {'view': this, 'item': product});
            }
          }

        }
      }
    },
    this
    )
  );
}

extend(ProductsListController, ListsController);

ProductsListController.prototype.name = 'ProductsListController';

ProductsListController.prototype.findPattern += '.items-list-products';

ProductsListController.prototype.getListView = function()
{
  return new ProductsListView(this.base);
};

ProductsListView.prototype.touchProcess = false;

ProductsListView.prototype.postprocess = function(isSuccess, initial)
{
  ProductsListView.superclass.postprocess.apply(this, arguments);

  var o = this;
  this.dragDropCart = core.getCommentedData(jQuery('body'), 'dragDropCart');

  if (isSuccess) {

    // Column switcher for 'table' display mode
    jQuery('.products-table .column-switcher', this.base).commonController('markAsColumnSwitcher');

    // Must be done before any event handled on 'A' tags. IE fix
    if (jQuery.browser.msie) {
      jQuery(draggablePattern, this.base).find('a')
        .each(function() {
          this.defferHref = this.href;
          this.href = 'javascript:void(0);';
        })
        .click(function() {
          if (!o.base.hasClass('ie-link-blocker')) {
            self.location = this.defferHref;
          }
        });
    }

    // Register "Changing display mode" handler
    jQuery('.display-modes a', this.base).click(
      function() {
        core.clearHash('pageId');
        return !o.load({'displayMode': jQuery(this).attr('class')});
      }
    );

    // Register "Sort by" selector handler
    jQuery('.sort-crit a', this.base).click(
      function () {
        core.clearHash('pageId');
        return !o.load({
          'sortBy': jQuery(this).data('sort-by'),
          'sortOrder': jQuery(this).data('sort-order')
        });
      }
    );

    // Register "Quick look" button handler
    jQuery('.quicklook a.quicklook-link', this.base).click(
      function () {
        popup.openAsWait();

        return !popup.load(
          URLHandler.buildURL({
            target:      'quick_look',
            action:      '',
            product_id:  core.getValueFromClass(this, 'quicklook-link'),
            only_center: 1
          }),
          function () {
            jQuery('.formError').hide();
          },
          50000
        );
      }
    );

    core.bind(
      'afterPopupPlace',
      function() {
        new ProductDetailsController(jQuery('.ui-dialog div.product-quicklook'));
      }
    );

    var cartTrayFadeOutDuration = 400;
    var draggablePattern = '.products-grid .product, .products-list .product, .products-sidebar .product';
    var cartTray = jQuery('.cart-tray-box', this.base).eq(0);
    var countRequests = 0;

    cartTray.data('isproductdrag', false);

    this.dragDropCart ? jQuery(draggablePattern, this.base).draggable(
    {
      revert:         'invalid',
      revertDuration: 300,
      zIndex:         50000,
      distance:       10,
      containment:    'body',

      helper: function()
      {
        var base = jQuery(this);
        var clone = base
          .clone()
          .css(
            {
              'width':  base.parent().width() + 'px',
              'height': base.parent().height() + 'px',
              'position': 'fixed'
            }
          );

        base.addClass('drag-owner');
        base.parent().addClass('current');
        var currentStyle = base.parent().attr('style');
        base.parent().attr('style', currentStyle + '; z-index: auto !important;');

        if (jQuery.browser.msie) {
          base.addClass('ie-link-blocker');
        }

        clone.find('a').click(
          function() {
            return false;
          }
        );

        return clone.get(0);
      }, // helper()

      start: function(event, ui)
      {
        if (!o.touchProcess) {
          cartTray.data('isproductdrag', true);
          cartTray.not('.cart-tray-adding, .cart-tray-added')
            .addClass('cart-tray-active cart-tray-moving')
            .attr('style', 'display:block');

          if (!cartTray.parents('.ui-dialog').length) {
            cartTray.insertAfter(this);
          }
        }

        return !o.touchProcess;
      }, // start()

      stop: function(event, ui)
      {
        cartTray.data('isproductdrag', false);
        cartTray.not('.cart-tray-adding, .cart-tray-added')
          .fadeOut(
            cartTrayFadeOutDuration,
            function() {
              if (cartTray.data('isproductdrag')) {
                jQuery(this).show();

              } else {
                jQuery(this)
                  .removeClass('cart-tray-active cart-tray-moving cart-tray-added');
              }
            }
          );
        if (!cartTray.parents('.ui-dialog').length) {
          cartTray.prependTo(o.base);
        }

        jQuery('.drag-owner').removeClass('drag-owner');
        jQuery('.product-cell.current').removeClass('current');

        if (jQuery.browser.msie) {
          var downer = jQuery('.drag-owner');
          setTimeout(
            function() {
              downer.removeClass('ie-link-blocker');
            },
            1000
          );
        }

      } // stop()

    }
    ) : false; // jQuery(draggablePattern, this.base).draggable

    if (this.dragDropCart) {
      // Disable out-of-stock product to drag
      var draggableDisablePattern = '.products-grid .product.out-of-stock, .products-list .product.out-of-stock, .products-sidebar .product.out-of-stock';
      jQuery(draggableDisablePattern, this.base).draggable('disable');

      // Disable not-available product to drag
      draggableDisablePattern = '.products-grid .product.not-available, .products-list .product.not-available, .products-sidebar .product.not-available';
      jQuery(draggableDisablePattern, this.base).draggable('disable');

      // Disable dragging the products when the customer need to choose the product options for them first
      draggableDisablePattern = '.products-grid .product.need-choose-options, .products-list .product.need-choose-options, .products-sidebar .product.need-choose-options';
      jQuery(draggableDisablePattern, this.base).draggable('disable');

      jQuery(draggablePattern, this.base).off('touchstart');
      jQuery(draggablePattern, this.base).on(
        'touchstart',
        _.bind(
          function(event) {
            this.touchProcess = true;
          },
          this
        )
      );

      jQuery('body').on(
        'touchend',
        _.bind(
          function (event) {
            this.touchProcess = false;
          },
          this
        )
      );
    }

    cartTray.droppable(
    {
      tolerance: 'touch',

      over: function(event, ui)
      {
        cartTray.addClass('droppable');
      },

      out: function(event, ui)
      {
        cartTray.removeClass('droppable');
      },

      drop: function(event, ui)
      {
        if ( !jQuery(o.base).has(ui.draggable).length ) {
          return;
        }
        var pid = core.getValueFromClass(ui.draggable, 'productid');
        if (pid) {
          cartTray
            .removeClass('cart-tray-moving cart-tray-added')
            .addClass('cart-tray-adding')
            .removeClass('droppable');

          countRequests++;

          core.trigger('addToCartViaDrop', {widget: o});

          core.post(
            URLHandler.buildURL(
              {
                target: 'cart',
                action: 'add'
              }
            ),
            function(XMLHttpRequest, textStatus, data, isValid)
            {
              countRequests--;
              if (!isValid) {
                core.trigger(
                  'message',
                  {
                    text: 'An error occurred during adding the product to cart. Please refresh the page and try to drag the product to cart again or contact the store administrator.',
                    type: 'error'
                  }
                );
              }

              if (0 == countRequests) {
                if (isValid) {
                  cartTray
                    .removeClass('cart-tray-adding')
                    .addClass('cart-tray-added');

                  setTimeout(
                    function() {
                      if (cartTray.data('isproductdrag')) {
                        cartTray
                          .removeClass('cart-tray-added')
                          .addClass('cart-tray-moving');

                      } else {
                        cartTray.not('.cart-tray-adding')
                         .fadeOut(
                            cartTrayFadeOutDuration,
                            function() {
                              if (cartTray.data('isproductdrag')) {
                                jQuery(this)
                                  .removeClass('cart-tray-added')
                                  .addClass('cart-tray-moving')
                                  .show();

                              } else {
                                jQuery(this)
                                .removeClass('cart-tray-active cart-tray-added');
                              }
                            }
                          );
                      }
                    },
                    4000
                  ); // setTimeout()

                } else {
                  cartTray
                    .removeClass('cart-tray-adding cart-tray-active');

                }
              } // if (0 == countRequests)
            },
            {
              target:     'cart',
              action:     'add',
              product_id: pid
            },
            {
              rpc: true
            }
          ); // core.post()
        } // if (isProductDrag)
      } // drop()
    }
    ); // cartTray.droppable()

    // Manual set cell's height
    this.base.find('table.products-grid tr').each(
      function () {
        var height = 0;
        jQuery('div.product', this).each(
          function() {
            height = Math.max(height, jQuery(this).height());
          }
        );
      }
    );

    // Process click on 'Add to cart' buttons by AJAX
    jQuery('.add-to-cart', this.base).not('.link').each(
      function (index, elem) {
        jQuery(elem).click(function() {
          o.addToCart(elem);
        });
      }
    );
  } // if (isSuccess)
}; // ProductsListView.prototype.postprocess()

// Post AJAX request to add product to cart
ProductsListView.prototype.addToCart = function (elem) {
  elem = jQuery(elem);
  var pid = core.getValueFromClass(elem, 'productid');

  if (pid && !this.isLoading) {
    if (elem.parents('.need-choose-options').length) {
      self.location = URLHandler.buildURL({ target: 'product', product_id: pid });

    } else {
      var btnStateHolder = elem.prop('disabled');
      elem.prop('disabled', true);
      core.post(
        URLHandler.buildURL({ target: 'cart', action: 'add' }),
        _.bind(this.handleAddToCart, this),
        {
          target:     'cart',
          action:     'add',
          product_id: pid
        },
        {
          rpc: true
        }
      ).always(function() {
        elem.prop('disabled', btnStateHolder);
      });
    }
  }
};

ProductsListView.prototype.handleAddToCart = function (XMLHttpRequest, textStatus, data, isValid) {
  if (!isValid) {
    core.trigger(
      'message',
      {
        text: 'An error occurred during adding the product to cart. Please refresh the page and try to drag the product to cart again or contact the store administrator.',
        type: 'error'
      }
    );
  }
};

// Get event namespace (prefix)
ProductsListView.prototype.getEventNamespace = function () {
  return 'list.products';
};

/**
 * Load product lists controller
 */
core.autoload(ProductsListController);
