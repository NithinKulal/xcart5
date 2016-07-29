/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping address controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ShippingAddressView(base)
{
  core.bind('createShippingAddress', _.bind(this.handleCreateAddress, this));

  ShippingAddressView.superclass.constructor.apply(this, arguments);
}

extend(ShippingAddressView, CheckoutAddressView);

ShippingAddressView.prototype.addressBoxPattern = '.step-shipping-address';

// Widget class name
ShippingAddressView.prototype.widgetClass = '\\XLite\\View\\Checkout\\ShippingAddress';

ShippingAddressView.prototype.shippingCalculationFields = ['field-zipcode', 'field-country_code', 'field-state_id'];

ShippingAddressView.prototype.shippingCalculationReady = false;

ShippingAddressView.prototype.assignHandlers = function(event, state)
{

  ShippingAddressView.superclass.assignHandlers.apply(this, arguments);

  if (state.isSuccess) {

    this.isShippingCalculationReadinessChanged();

    this.getForm().get(0).getElements()
      .change(_.bind(this.processShippingCalculationReadiness, this));
  }
}

ShippingAddressView.prototype.isShippingCalculationReadinessChanged = function()
{
  var pattern = '.' + this.shippingCalculationFields.join(',.')
  var count = this.getForm().get(0).getElements()
    .filter(pattern)
    .filter(function() {
      return this.validate(true) && jQuery(this).val();
    })
    .length;

  var tmp = this.shippingCalculationReady;
  this.shippingCalculationReady = count == this.shippingCalculationFields.length;

  return tmp != this.shippingCalculationReady;
}

ShippingAddressView.prototype.processShippingCalculationReadiness = function()
{
  if (this.isShippingCalculationReadinessChanged()) {
    if (this.shippingCalculationReady) {
      this.triggerVent('shippingCalculationReady', this);

    } else {
      this.triggerVent('shippingCalculationUnready', this);
    }
  } 
}

ShippingAddressView.prototype.handleUpdateCart = function(event, data)
{
  if (data.shippingAddressId && !this.blockLoadByUpdateCart) {
    this.loadByUpdateCartTO = setTimeout(
      _.bind(function() { this.load(); }, this),
      300
    );
  }

  this.blockLoadByUpdateCart = false;
}

// Get event namespace (prefix)
ShippingAddressView.prototype.getEventNamespace = function()
{
  return 'checkout.shippingAddress';
}

// Load
core.bind(
  'checkout.main.postprocess',
  function () {
    new ShippingAddressView();
  }
);

