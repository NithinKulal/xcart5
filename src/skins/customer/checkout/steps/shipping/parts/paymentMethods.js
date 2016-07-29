/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Payment methods list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Payment methods list widget
 */

function PaymentMethodsView(base)
{
  var args = Array.prototype.slice.call(arguments, 0);
  if (!base) {
    args[0] = '.step-payment-methods';
  }

  if (args[0].length) {
    this.bind('local.postprocess', _.bind(this.assignHandlers, this))
      .bind('local.loaded', _.bind(this.triggerChange, this));

    core.bind('checkout.common.readyCheck', _.bind(this.handleCheckoutReadyCheck, this))
      .bind('updateCart', _.bind(this.handleUpdateCart, this))
      .bind('createBillingAddress', _.bind(this.handleCreateAddress, this));
  }

  PaymentMethodsView.superclass.constructor.apply(this, args);
};

extend(PaymentMethodsView, ALoadable);

// Shade widget
PaymentMethodsView.prototype.shadeWidget = true;

// Update page title
PaymentMethodsView.prototype.updatePageTitle = false;

// Widget target
PaymentMethodsView.prototype.widgetTarget = 'checkout';

// Widget class name
PaymentMethodsView.prototype.widgetClass = '\\XLite\\View\\Checkout\\PaymentMethodsList';

// Postprocess widget
PaymentMethodsView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {

    if (this.base.find('form').get(0)) {

      // Check and save payment methods
      this.base.find('li input')
        .change(_.bind(this.handleMethodChange, this));

      this.base.find('form').get(0).commonController
        .enableBackgroundSubmit()
        .bind('local.submit.preprocess', _.bind(this.triggerChange, this))
        .bind('local.submit.success', _.bind(this.triggerChange, this))
        .bind('local.submit.success', _.bind(this.unshadeDelayed, this))
        .bind('local.submit.error', _.bind(this.unshadeDelayed, this));
    }
  }
};

PaymentMethodsView.prototype.getLoaderOptions = function()
{
  var list = ALoadable.prototype.getLoaderOptions.apply(this, arguments);
  list.timeout = 45000;

  return list;
}

PaymentMethodsView.prototype.handleMethodChange = function(event)
{
  this.shade();

  return this.base.find('form').submit();
};

PaymentMethodsView.prototype.assignWaitOverlay = function(base)
{
  PaymentMethodsView.superclass.assignWaitOverlay.apply(this, arguments);

  var shippingMethods = jQuery('.step-shipping-methods');
  if (shippingMethods.length) {
    assignShadeOverlay(shippingMethods, true);
  }
};

PaymentMethodsView.prototype.unassignWaitOverlay = function(base)
{
  PaymentMethodsView.superclass.unassignWaitOverlay.apply(this, arguments);

  var shippingMethods = jQuery('.step-shipping-methods');
  if (shippingMethods.length) {
    unassignShadeOverlay(shippingMethods, true);
  }
};

PaymentMethodsView.prototype.unshadeDelayed = function()
{
  setTimeout(
    _.bind(this.unshade, this),
    500
  );
};

PaymentMethodsView.prototype.handleUpdateCart = function(event, data)
{
  if ('undefined' != typeof(data.paymentMethodsHash)) {
    this.load();
  }
};

PaymentMethodsView.prototype.handleCreateAddress = function()
{
  this.load();
};

PaymentMethodsView.prototype.handleCheckoutReadyCheck = function(event, state)
{
  if (state.result) {
    state.result = (
      0 < this.base.find('.payment-not-required').length
      || 0 < this.base.find('li input:checked').length
    );
  }

  if (!state.blocked && (0 == this.base.find('.payment-not-required').length)) {
    state.blocked = !this.base.find('form').get(0)
      || this.base.find('form').get(0).isBgSubmitting
      || this.base.find('form').get(0).commonController.isChanged()
      || this.isLoading;
  }
};

PaymentMethodsView.prototype.triggerChange = function()
{
  core.trigger('checkout.common.anyChange', this);
};

// Get event namespace (prefix)
PaymentMethodsView.prototype.getEventNamespace = function()
{
  return 'checkout.paymentMethods';
};

// Load
core.bind(
  'checkout.main.postprocess',
  function () {
    new PaymentMethodsView();
  }
);


