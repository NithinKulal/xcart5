/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Payment methods list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Payment template widget
 */

function PaymentTplView(base)
{
  var args = Array.prototype.slice.call(arguments, 0);
  if (!base) {
    args[0] = '.payment-tpl';
  }

  if (args[0].length) {
    core.bind('updateCart', _.bind(this.handleUpdateCart, this));
    this.bind('local.loaded', _.bind(this.handleLoaded, this));
    this.bind('local.loadingError', _.bind(this.unblock, this));
  }

  PaymentTplView.superclass.constructor.apply(this, args);
};

extend(PaymentTplView, ALoadable);

// Shade widget
PaymentTplView.prototype.shadeWidget = true;

// Update page title
PaymentTplView.prototype.updatePageTitle = false;

// Widget target
PaymentTplView.prototype.widgetTarget = 'checkout';

// Widget class name
PaymentTplView.prototype.widgetClass = '\\XLite\\View\\Checkout\\Payment';

PaymentTplView.prototype.getLoaderOptions = function()
{
  var list = ALoadable.prototype.getLoaderOptions.apply(this, arguments);
  list.timeout = 45000;

  return list;
}

PaymentTplView.prototype.handleUpdateCart = function(event, data)
{
  if ('undefined' != typeof(data.paymentMethodId)) {
    this.load();
    core.trigger('checkout.common.block');
  }
};

PaymentTplView.prototype.handleLoaded = function(event)
{
  this.unblock();
  core.trigger('checkout.common.anyChange', this);
};

PaymentTplView.prototype.unblock = function(event)
{
  core.trigger('checkout.common.unblock');
};

// Get event namespace (prefix)
PaymentTplView.prototype.getEventNamespace = function()
{
  return 'checkout.paymentTpl';
};

// Load
core.bind(
  'checkout.main.postprocess',
  function () {
    new PaymentTplView();
  }
);
