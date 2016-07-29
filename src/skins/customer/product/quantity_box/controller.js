/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product quantity box
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

function ProductQuantityBoxView(base)
{
  this.base = jQuery(base);

  if (this.base && this.base.length && this.base.find('input.quantity').length > 0) {

    this.block = this.base.find('input.quantity');

    var form = this.block.get(0).form;
    form.commonController.switchControlReadiness(true);

    form.commonController
      .bind('local.ready', _.bind(this.handleFormReady, this))
      .bind('local.unready', _.bind(this.handleFormUnready, this));

    this.block.on(
      'input',
      _.bind(this.handleQuantityChange, this)
    );

    this.isCartPage = jQuery('div#cart').length > 0;

    if (!this.isCartPage) {

      this.actionButton = jQuery(this.base)
        .parents('div.product-buttons')
        .find('button.add2cart, button.buy-more');

    } else {

      this.actionButton = jQuery('ul.totals li.button button');

    }
  }

  Base.apply(this, arguments);

  this.initialize();
}

extend(ProductQuantityBoxView, Base);

// Controller associated main widget
ProductQuantityBoxView.prototype.base = null;

// Controller associated main widget
ProductQuantityBoxView.prototype.block = null;

// Controller associated main widget
ProductQuantityBoxView.prototype.isCartPage = false;

// Initialize controller
ProductQuantityBoxView.prototype.initialize = function()
{
};

ProductQuantityBoxView.prototype.handleQuantityChange = function(event)
{
  jQuery(event.currentTarget).change();
}

ProductQuantityBoxView.prototype.handleFormReady = function(event)
{
  this.actionButton
    .removeProp('disabled')
    .removeClass('disabled add2cart-disabled');
}

ProductQuantityBoxView.prototype.handleFormUnready = function(event)
{
  this.actionButton
    .prop('disabled', 'disabled')
    .addClass('disabled add2cart-disabled');
}

// Get event namespace (prefix)
ProductQuantityBoxView.prototype.getEventNamespace = function()
{
  return 'product.details.quantityBox';
}

core.registerTriggersBind(
  'update-product-page',
  function()
  {
    var container = jQuery('span.quantity-box-container');

    new ProductQuantityBoxView(container);
    if (container.parents('form').get(0) && container.parents('form').get(0).commonController) {
      container.parents('form').get(0).commonController.bindElements();
    }
  }
);

core.registerShadowWidgets(
  'update-product-page',
  function()
  {
    return '.widget-fingerprint-product-quantity, .widget-fingerprint-product-add-button';
  }
);
