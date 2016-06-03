/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Cart controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Main widget
 */

function CartView(base)
{
  this.bind('local.postprocess', _.bind(this.assignHandlers, this));

  this.callSupermethod('constructor', arguments);

  core.bind('updateCart', _.bind(this.handleUpdateCart, this));
  core.bind('reassignEstimator', _.bind(this.assignEstimatorHandler, this));

  this.validate();
}

extend(CartView, ALoadable);

CartView.autoload = function()
{
  jQuery('#cart').each(
    function() {
      new CartView(this);
    }
  );
};

// Shade widget
CartView.prototype.shadeWidget = true;

// Update page title
CartView.prototype.updatePageTitle = true;

// Update breadcrumb last node from loaded request or not
CartView.prototype.updateBreadcrumb = true;

// Widget target
CartView.prototype.widgetTarget = 'cart';

// Widget class name
CartView.prototype.widgetClass = '\\XLite\\View\\Cart';

// Checkout button
CartView.prototype.checkoutButton = jQuery('#cart ul.totals li.button button');

// Widget updated status
CartView.prototype.selfUpdated = false;

// Cart silence updated status
CartView.prototype.cartUpdated = false;

// Postprocess widget
CartView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {

    // Item subtotal including scharges
    jQuery('td.item-subtotal div.including-modifiers', this.base).each(
      function() {
        attachTooltip(
          jQuery(this).parents('td.item-subtotal').find('.subtotal'),
          jQuery(this).html()
        );
      }
    );

    // Remove item
    this.base.find('.selected-product form .remove').parents('form')
      .commonController(
        'enableBackgroundSubmit',
        _.bind(this.preprocessAction, this),
        _.bind(this.postprocessAction, this)
      );

    // Update item
    jQuery('.selected-product form.update-quantity', this.base)
      .commonController(
        'enableBackgroundSubmit',
        _.bind(this.preprocessAction, this),
        _.bind(this.postprocessAction, this)
      )
      .commonController('submitOnlyChanged', true);

    // Clear cart
    jQuery('form .clear-bag', this.base).parents('form').eq(0)
      .commonController(
        'enableBackgroundSubmit',
        _.bind(this.preprocessAction, this),
        _.bind(this.postprocessAction, this)
      ); 

    this.assignEstimatorHandler();
  }
};

CartView.prototype.assignEstimatorHandler = function() {  
    jQuery('button.estimate', this.base).parents('form').eq(0).submit(
      _.bind(
        function(event) {
          return this.openShippingEstimator(event, event.currentTarget);
        },
        this
      )
    );
    jQuery('a.estimate', this.base).click(
      _.bind(
        function(event) {
          return this.openShippingEstimator(event, event.currentTarget);
        },
        this
      )
    );
};

// Call 'updateCart' event on close popup 'Estimate shipping cost'
CartView.prototype.forceUpdateCartOnClose = false;

// Open Shipping estimator popup
CartView.prototype.openShippingEstimator = function(event, elm)
{
  if (!this.selfUpdated) {

    this.selfUpdated = true;

    core.bind('afterPopupPlace', function() {
      // Refresh list of states
      UpdateStatesList();
      // Enable forceUpdateCartOnClose option
      this.forceUpdateCartOnClose = true;
      jQuery('form.estimator, .estimate-methods form.method-change').submit(
        function() {
          core.bind('popup.close', _.once(function(){
            core.trigger('updateCart', {items:[]});
          }));
        }
      );
    });

    // This need to remove previous ui-dialog. BUG-713
    jQuery('.ctrl-customer-shippingestimate').closest('.ui-dialog').remove();

    popup.load(
      elm,
      _.bind(
        function(event) {
          this.closePopupHandler();
        },
        this
      )
    );
  }

  return false;
};

// Close Shipping estimator popup handler
CartView.prototype.closePopupHandler = function()
{
  if (this.cartUpdated) {
    this.load();
  }

  this.selfUpdated = false;
  this.cartUpdated = false;
};

CartView.prototype.preprocessAction = function()
{
  var result = false;

  if (!this.selfUpdated) {
    this.selfUpdated = true;
    this.shade();

    // Remove validation errors from other quantity boxes
    jQuery('form.validationEngine', this.base).validationEngine('hide');

    result = true;
  }

  return result;
};



// Validate using validation engine plugin
CartView.prototype.validate = function()
{
  if (!jQuery('form.validationEngine', this.base).validationEngine('validate')) {
    this.checkoutButton.prop('disabled','disabled')
      .addClass('disabled add2cart-disabled');
  }
};

// Form POST processor
CartView.prototype.postprocessAction = function(event, data)
{
  this.selfUpdated = false;
  this.cartUpdated = false;

  if (data.isValid) {
    this.load();

  } else {
    this.unshade();
  }
};

CartView.prototype.handleUpdateCart = function(event, data)
{
  if (this.selfUpdated) {
    this.cartUpdated = true;

  } else {
    this.load();
  }
};

// Get event namespace (prefix)
CartView.prototype.getEventNamespace = function()
{
  return 'cart.main';
};

core.autoload(CartView);
