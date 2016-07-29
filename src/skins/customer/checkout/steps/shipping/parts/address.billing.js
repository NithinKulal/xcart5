/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Billing address controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function BillingAddressView(base)
{
  core.bind('createShippingAddress', _.bind(this.handleCreateAddress, this));

  BillingAddressView.superclass.constructor.apply(this, arguments);
}

extend(BillingAddressView, CheckoutAddressView);

BillingAddressView.prototype.shadeWidget = true;

BillingAddressView.prototype.addressBoxPattern = '.step-billing-address';

// Widget class name
BillingAddressView.prototype.widgetClass = '\\XLite\\View\\Checkout\\BillingAddress';

// Assign handlers
BillingAddressView.prototype.assignHandlers = function(event, state)
{
  BillingAddressView.superclass.assignHandlers.apply(this, arguments);

  if (state.isSuccess) {

    // Process 'Same address' checkbox
    this.base.find('#same_address').change(_.bind(this.handleSameAddress, this));

    var field = this.getForm().get(0).getElements().filter('#same_address');
    if (field.length) {
      field.get(0).commonController.bind('local.validate', _.bind(this.handleSameAddressValidate, this));
    }

  }
};

BillingAddressView.prototype.handleSameAddress = function(event)
{
  if (CheckoutAddressView.preventSameAddressChange == true) {
    event.stopPropagation();

    var state = this.base.find('#same_address:checked').length;
    var element = this.base.find('#same_address');
    setTimeout(function () { element.prop('checked', !state)}, 0);

    setTimeout(function () { CheckoutAddressView.preventSameAddressChange = false;}, 0);

    return;
  }

  if (this.base.find('#same_address:checked').length) {
    // The billing address IS the same one with the shipping - hide the billing section
    this.base.addClass('address-invisible')
      .removeClass('address-visible');

    // Mark the billing section as VALID in JS (since there is no sensitive information)
    jQuery('input,select', this.base.find('#same_address').closest('form')).each(function (index, elem) {elem.commonController.unmarkAsInvalid()});
    this.getForm().get(0).commonController.isReady = true;

  } else {
    // The billing address IS NOT the same one with the shipping - show the billing section
    this.base.addClass('address-visible')
      .removeClass('address-invisible');
    this.base.find('#billingaddress-country-code').change();
  }

  this.triggerChange();
};

BillingAddressView.prototype.handleUpdateCart = function(event, data)
{
  if ('undefined' != typeof(data.sameAddress)) {

    // Load if same address flag changed
    var inp = jQuery('#same_address').eq(0);
    if (inp.length && data.sameAddress != this.isSameAddress()) {
      this.load();
    }

  } else if (
    data.billingAddressId
    && (!data.shippingAddressId || data.shippingAddressId != data.billingAddressId)
    && !this.blockLoadByUpdateCart
  ) {

    // Load if billing address is changed
    this.loadByUpdateCartTO = setTimeout(
      _.bind(function() { this.load(); }, this),
      300
    );
  }

  this.blockLoadByUpdateCart = false;
};

BillingAddressView.prototype.handleSameAddressValidate = function(event, state)
{
  if (
    !state.widget.$element.is(':checked')
    && 0 < jQuery('.address-item', state.widget.element.form).length
    && 0 == jQuery('.address-item:visible', state.widget.element.form).length
  ) {

    // Invalid if checkbox disabled and address form hide
    state.result = false;
  }
};

BillingAddressView.prototype.isSameAddress = function()
{
  return 0 < jQuery('#same_address:checked').length;
};

// Get event namespace (prefix)
BillingAddressView.prototype.getEventNamespace = function()
{
  return 'checkout.billingAddress';
};

// Load
core.bind(
  'checkout.main.postprocess',
  function () {
    new BillingAddressView();
  }
);

