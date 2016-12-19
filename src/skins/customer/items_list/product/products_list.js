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

  this.requests = [];
}

extend(ProductsListView, ListView);

// Products list class
function ProductsListController(base)
{
  ProductsListController.superclass.constructor.apply(this, arguments);

  this.dragDropCart = core.getCommentedData(jQuery('body'), 'dragDropCart');

  core.bind('updateCart', _.bind(this.updateCartHandler, this));
}

extend(ProductsListController, ListsController);

ProductsListController.prototype.name = 'ProductsListController';

ProductsListController.prototype.findPattern += '.items-list-products';

ProductsListController.prototype.getListView = function()
{
  return new ProductsListView(this.base);
};

ProductsListController.prototype.updateCartHandler = function(event, data) {
  var productPattern, product;

  if (_.isUndefined(data.items)) {
    return;
  }

  for (var i = 0; i < data.items.length; i++) {
    if (data.items[i].object_type == 'product') {

      // Added mark
      productPattern = '.product.productid-' + data.items[i].object_id;
      product = jQuery(productPattern, this.base);

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

      this.processDragNDropHandle(product);
    }
  }
};

ProductsListController.prototype.processDragNDropHandle = function($product)
{
  if(!$product.length) {
    return;
  }

  var $handle = jQuery('.drag-n-drop-handle', $product);

  if (!$handle.length) {
    return;
  }

  var labels = core.getCommentedData($handle);

  $handle.find('span').remove();

  var template = _.template('<span class="<%= style %>"><%= text %></span>');

  _.each(labels, function (el) {
    var hideCondMet =  !_.isUndefined(el.hideCondClass)
        && $product.hasClass(el.hideCondClass);

    if ($product.hasClass(el.showCondClass)
        && !hideCondMet
    ) {
      var label = template({
        style:  el.style,
        text:   el.text
      });
      $handle.append(label);
    }
  });
};

ProductsListView.prototype.touchProcess = false;

ProductsListView.prototype.postprocess = function(isSuccess, initial)
{
  ProductsListView.superclass.postprocess.apply(this, arguments);

  var self = this;

  if (isSuccess) {

    // Column switcher for 'table' display mode
    jQuery('.products-table .column-switcher', this.base).commonController('markAsColumnSwitcher');

    // Must be done before any event handled on 'A' tags. IE fix
    if (jQuery.browser.msie) {
      jQuery(this.draggablePattern, this.base).find('a')
        .each(function() {
          this.defferHref = this.href;
          this.href = 'javascript:void(0);';
        })
        .click(function() {
          if (!self.base.hasClass('ie-link-blocker')) {
            window.self.location = this.defferHref;
          }
        });
    }

    // Register "Changing display mode" handler
    jQuery('.display-modes a', this.base).click(
      function() {
        core.clearHash('pageId');
        return !self.load({'displayMode': jQuery(this).attr('class')});
      }
    );

    // Register "Sort by" selector handler
    jQuery('.sort-crit a', this.base).click(
      function () {
        core.clearHash('pageId');
        return !self.load({
          'sortBy': jQuery(this).data('sort-by'),
          'sortOrder': jQuery(this).data('sort-order')
        });
      }
    );

    // Register "Quick look" button handler
    jQuery('.quicklook a.quicklook-link', this.base).click(
      function () {
        popup.openAsWait();

        return self.openQuickLook(core.getValueFromClass(this, 'quicklook-link'));
      }
    );

    core.bind(
      'afterPopupPlace',
      function() {
        new ProductDetailsController(jQuery('.ui-dialog div.product-quicklook'));
      }
    );

    this.dragDropCart = core.getCommentedData(jQuery('body'), 'dragDropCart');

    if (this.dragDropCart) {
      this.initializeDragDropCart(this.base);
    }

    this.initializeCartTray(this.base);

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
          var product = $(elem).closest('.product-cell').find('.product');
          if (!product.length) {
            product = $(elem).closest('.product-cell');
          }

          var pid = core.getValueFromClass(product, 'productid');
          var forceOptions = product.is('.need-choose-options');
          var btnStateHolder = $(elem).prop('disabled');

          if (pid && !self.isLoading) {
            $(elem).prop('disabled', true);
          }

          if (forceOptions) {
            $(elem).prop('disabled', btnStateHolder);
            self.openQuickLook(pid);
          } else {
            core.trigger('addToCartViaClick', {productId: pid});
            self.addToCart(elem)
              .always(function() {
                $(elem).prop('disabled', btnStateHolder);
              });
          }
        });
      }
    );
  } // if (isSuccess)
}; // ProductsListView.prototype.postprocess()

/**
 * Draggable behaviour
 */

ProductsListView.prototype.initializeDragDropCart = function (element) {
  jQuery(this.draggablePattern, element)
    .draggable(this.draggableOptions());

  jQuery(this.draggablePattern, element)
    .filter(this.draggableExcluded.join(', '))
    .draggable('disable');

  jQuery(this.draggablePattern, element).off('touchstart');
  jQuery(this.draggablePattern, element).on(
    'touchstart',
    _.bind(function(event) {
      this.touchProcess = true;
    }, this)
  );

  jQuery('body').on(
    'touchend',
    _.bind(function (event) {
      this.touchProcess = false;
    }, this)
  );
}

ProductsListView.prototype.draggableExcluded = [
  '.product.out-of-stock',
  '.product.not-available',
];

ProductsListView.prototype.draggablePattern = '.products-grid .product, .products-list .product, .products-sidebar .product';

ProductsListView.prototype.draggableOptions = function () {
  var self = this;
  return {
    revert:         'invalid',
    revertDuration: 300,
    zIndex:         50000,
    distance:       10,
    containment:    'body',

    helper: function() {
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

    start: function(event, ui) {
      self.initializeCartTray(self.base);
      if (!self.touchProcess) {
        self.cartTray.data('isproductdrag', true);
        self.cartTray.not('.cart-tray-adding, .cart-tray-added')
          .addClass('cart-tray-active cart-tray-moving')
          .attr('style', 'display:block');
      }

      return !self.touchProcess;
    }, // start()

    stop: function(event, ui) {
      self.cartTray.data('isproductdrag', false);
      self.cartTray.not('.cart-tray-adding, .cart-tray-added')
        .fadeOut(
          self.cartTrayFadeOutDuration,
          function() {
            if (self.cartTray.data('isproductdrag')) {
              self.cartTray.show();

            } else {
              self.cartTray.removeClass('cart-tray-active cart-tray-moving cart-tray-added');
            }
          }
        );

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
};

/**
 * Droppable behaviour
 */

ProductsListView.prototype.initializeCartTray = function (element) {
  this.cartTrayFadeOutDuration = 400;
  this.cartTray = jQuery('.cart-tray-box', this.base).length
        ? jQuery('.cart-tray-box', this.base).eq(0) // From popup
        : jQuery('.cart-tray-box').eq(0);           // Common
  this.cartTray.data('isproductdrag', false);
  this.cartTray.droppable(this.droppableOptions());
}

ProductsListView.prototype.droppableOptions = function() {
  var self = this;
  return {
    tolerance: 'touch',

    over: function(event, ui)
    {
      $(this).addClass('droppable');
    },

    out: function(event, ui)
    {
      $(this).removeClass('droppable');
    },

    drop: function(event, ui)
    {
      self.droppableDrop.call(self, event, ui, this);
    }
  };
};

ProductsListView.prototype.droppableDrop = function(event, ui, tray)
{
  var self = this;
  var pid = core.getValueFromClass(ui.draggable, 'productid');
  var forceOptions = $(ui.draggable).is('.need-choose-options');

  if (pid && !forceOptions) {
    $(tray)
      .removeClass('cart-tray-moving cart-tray-added')
      .addClass('cart-tray-adding')
      .removeClass('droppable');

    core.trigger('addToCartViaDrop', {widget: self, productId: pid});

    var xhr = self.addToCart(ui.draggable)
      .done(function(data, status, xhr) {
        var notValid = !!xhr.getResponseHeader('not-valid');

        if (!notValid) {
          $.when(self.requests).then(function() {
            self.onDropToCart.call(self, tray);
          });
        }
      })
      .fail(function(xhr, status, error) {
        var notValid = !!xhr.getResponseHeader('not-valid');
        if (notValid) {
          this.cartTray.removeClass('cart-tray-adding cart-tray-active');
        }
      });

    this.requests.push(xhr);
  }

  if (pid && forceOptions) {
    $(tray).removeClass('cart-tray-moving cart-tray-added droppable');

    popup.openAsWait();

    var xhr = self.openQuickLook(pid);

    this.requests.push(xhr);
  }
};

ProductsListView.prototype.onDropToCart = function() {
  var self = this;
  this.cartTray
    .removeClass('cart-tray-adding')
    .addClass('cart-tray-added');

  setTimeout(
    function() {
      if (self.cartTray.data('isproductdrag')) {
        self.cartTray
          .removeClass('cart-tray-added')
          .addClass('cart-tray-moving');

      } else {
        self.cartTray.not('.cart-tray-adding')
         .fadeOut(
          self.cartTrayFadeOutDuration,
          function() {
            if (self.cartTray.data('isproductdrag')) {
              self.cartTray
                .removeClass('cart-tray-added')
                .addClass('cart-tray-moving')
                .show();

            } else {
              self.cartTray.removeClass('cart-tray-active cart-tray-added');
            }
          }
        );
      }
    },
    4000
  );
};

// Post AJAX request to add product to cart
ProductsListView.prototype.addToCart = function (elem) {
  elem = jQuery(elem);
  var pid = core.getValueFromClass(elem, 'productid');

  var xhr = new $.Deferred();

  if (pid && !this.isLoading) {

    if (this)
    xhr = core.post(
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
    );
  } else {
    xhr.reject();
  }

  return xhr;
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

ProductsListView.prototype.focusOnFirstOption = _.once(function() {
  core.bind('afterPopupPlace', function(event, data){
    if (popup.currentPopup.box.hasClass('ctrl-customer-quicklook')) {
      var option = popup.currentPopup.box.find('.editable-attributes select, input').filter(':visible').first();
      option.focus();
    }
  })
});

ProductsListView.prototype.openQuickLook = function(product_id) {
  this.focusOnFirstOption();
  return !popup.load(
    URLHandler.buildURL({
      target:      'quick_look',
      action:      '',
      product_id:  product_id,
      only_center: 1
    }),
    function () {
      jQuery('.formError').hide();
    },
    50000
  );
};

// Get event namespace (prefix)
ProductsListView.prototype.getEventNamespace = function () {
  return 'list.products';
};

/**
 * Load product lists controller
 */
core.autoload(ProductsListController);
