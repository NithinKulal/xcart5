/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Canada Post post offices list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CapostPostOfficesView(base)
{
  var args = Array.prototype.slice.call(arguments, 0);

  if (!base) {
    args[0] = jQuery('.step-capost-offices').eq(0);
  }

  if (args[0].length) {
    
    // Assign handlers to the local events
    this.bind('local.postprocess', _.bind(this.assignHandlers, this))
      .bind('local.loaded', _.bind(this.triggerChange, this));

    core.bind('updateCart', _.bind(this.handleUpdateCart, this))
      .bind('createShippingAddress', _.bind(this.handleCreateAddress, this))
//      .bind('checkout.shippingAddress.submitted', _.bind(this.handleshippingAddressSubmit, this))
      .bind('checkout.common.readyCheck', _.bind(this.handleCheckoutReadyCheck, this));

  }

  CapostPostOfficesView.superclass.constructor.apply(this, args);
}

extend(CapostPostOfficesView, ALoadable);

// Shade widget
CapostPostOfficesView.prototype.shadeWidget = true;

// Update page title
CapostPostOfficesView.prototype.updatePageTitle = false;

// Widget target
CapostPostOfficesView.prototype.widgetTarget = 'checkout';

// Widget class name
CapostPostOfficesView.prototype.widgetClass = '\\XLite\\Module\\XC\\CanadaPost\\View\\Checkout\\PostOfficesList';

// Postprocess widget (assign handlers)
CapostPostOfficesView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {

    if (this.base.find('form').get(0)) {

      // Bind event to the "Deliver to Post Office" checkbox
      this.base.find('#capost-deliver-to-po').change(_.bind(this.handleDeliverToPo, this));

      // Bind event to the post office change (selection)
      this.base.find('li input').change(_.bind(this.handleMethodChange, this));

      this.base.find('form').get(0).commonController
        .enableBackgroundSubmit()
        .bind('local.submit.preprocess', _.bind(this.triggerChange, this))
        .bind('local.submit.success', _.bind(this.triggerChange, this))
        .bind('local.submit.success', _.bind(this.unshadeDelayed, this))
        .bind('local.submit.error', _.bind(this.unshadeDelayed, this));
    }
  }
}

// Event handler :: post office changed (or selected) 
CapostPostOfficesView.prototype.handleMethodChange = function(event)
{
  this.shade();

  return this.base.find('form').submit();
}

// Event handler :: "Deliver to Post Office" checked / unckecked
CapostPostOfficesView.prototype.handleDeliverToPo = function(event)
{
  if (this.isDeliveryToPOEnabled()) {

    this.base.find('.capost-offices-list')
      .addClass('offices-visible')
      .removeClass('offices-invisible');

  } else {

    this.base.find('.capost-offices-list')
      .addClass('offices-invisible')
      .removeClass('offices-visible');
  }

  this.handleMethodChange();
}

// Event handler :: cart updated 
CapostPostOfficesView.prototype.handleUpdateCart = function(event, data)
{
  if (
    'undefined' != typeof(data.shippingMethodId)
    || 'undefined' != typeof(data.capostShippingZipCode)
  ) {
    // Reload widget (offices list) is shipping method was changed
    this.load();
  }
}

// Event handler :: shipping address created
CapostPostOfficesView.prototype.handleCreateAddress = function()
{
  this.load();
}

// Event handler :: shipping form was submitted
CapostPostOfficesView.prototype.handleshippingAddressSubmit = function(event, data)
{
  this.load();
}

// Event handler :: checking is the checkout ready or not
CapostPostOfficesView.prototype.handleCheckoutReadyCheck = function(event, state)
{
  // Disable checkout 
  state.result = this.isCheckoutEnabled() && state.result;

  // Block checkout (without graying out the button)
  state.blocked = !this.isOfficesFormReady()
    || this.isLoading
    || state.blocked;
}

// Check :: is checkout button enabled or not (all necessary options has been selected)
CapostPostOfficesView.prototype.isCheckoutEnabled = function()
{
  return (
    !this.isDeliveryToPOEnabled() 
    || (
      this.isDeliveryToPOEnabled() 
      && this.isPostOfficeSelected()
    )
  );
}

// Check :: is post offices selection form ready 
CapostPostOfficesView.prototype.isOfficesFormReady = function()
{
  return (
    !this.base.find('form').get(0)
    || !(
      this.base.find('form').get(0).isBgSubmitting
      || this.base.find('form').get(0).commonController.isChanged()
    )
  );
}

// Check :: is deliver to post office enabled or not
CapostPostOfficesView.prototype.isDeliveryToPOEnabled = function()
{
  return 0 < this.base.find('#capost-deliver-to-po:checked').length;
}

// Check :: is one of the post offices selected or not
CapostPostOfficesView.prototype.isPostOfficeSelected = function()
{
  return 0 < this.base.find('li input:checked').length;
}

// Action :: trigger default event
CapostPostOfficesView.prototype.triggerChange = function()
{
  core.trigger('checkout.common.anyChange', this);
}

// Action :: unshade widget with a delay
CapostPostOfficesView.prototype.unshadeDelayed = function()
{
  setTimeout(
    _.bind(this.unshade, this),
    500
  );
}

// Action :: get event namespace (prefix)
CapostPostOfficesView.prototype.getEventNamespace = function()
{
  return 'checkout.capostOffices';
}

// Load
core.bind(
  'checkout.main.postprocess',
  function () {
    new CapostPostOfficesView();
  }
);

