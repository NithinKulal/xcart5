/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Place order controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PlaceOrderButtonView(base)
{
  var args = Array.prototype.slice.call(arguments, 0);
  if (!base) {
    args[0] = jQuery('button.place-order').eq(0);
  }

  this.bind('local.postprocess', _.bind(this.assignHandlers, this))
  core.bind('updateCart', _.bind(this.handleUpdateCart, this));
  core.bind('checkout.common.anyChange', _.bind(this.handleAnyFormChange, this));
  core.bind('checkout.common.block', _.bind(
    function() {
      this.blocked = true;
    }, this)
  );
  core.bind('checkout.common.unblock', _.bind(
    function() {
      this.blocked = false;
    }, this)
  );

  core.bind('checkout.common.getState', _.bind(this.handleGetState, this));
  core.bind('checkout.shippingMethods.error', _.bind(this.handleUpdatePlaceButtonErrorMessage, this));

  PlaceOrderButtonView.superclass.constructor.apply(this, args);
}

extend(PlaceOrderButtonView, ALoadable);

// Shade widget
PlaceOrderButtonView.prototype.shadeWidget = false;

PlaceOrderButtonView.prototype.blocked = false;

// Update page title
PlaceOrderButtonView.prototype.updatePageTitle = false;

// Widget target
PlaceOrderButtonView.prototype.widgetTarget = 'checkout';

// Widget class name
PlaceOrderButtonView.prototype.widgetClass = '\\XLite\\View\\Button\\PlaceOrder';

// Postprocess widget
PlaceOrderButtonView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {
    this.base.click(_.bind(this.handlePlaceOrder, this));
    this.handleAnyFormChange();

    if (this.base.parents('form').eq(0).width() < this.base.width()) {
      this.base.addClass('degrade-1');
    }
  }
};

PlaceOrderButtonView.prototype.handleUpdateCart = function(event, data)
{
  if ('undefined' != typeof(data.total)) {
    this.load();
  }
};

// Get event namespace (prefix)
PlaceOrderButtonView.prototype.getEventNamespace = function()
{
  return 'checkout.placeOrderButton';
};

PlaceOrderButtonView.prototype.handlePlaceOrder = function(event)
{
  var result = !this.base.hasClass('submitted');

  if (result) {
    result = this.checkState();
    result = result.result && !result.blocked;

    if (result) {
      var state = {widget: this, state: result};
      core.trigger('checkout.common.ready', state);
      core.trigger('clearMessages');
      result = state.state;

    } else {
      core.trigger('checkout.common.nonready', this);
    }
  }

  if (result) {
    this.base.addClass('submitted');
  }

  return result;
};

PlaceOrderButtonView.prototype.checkState = function(supressErrors)
{
  supressErrors = !!supressErrors;

  var state = {
    widget:        this,
    result:        this.base.parents('form').get(0).validate(supressErrors) && !this.blocked,
    blocked:       this.isLoading || this.blocked,
    supressErrors: supressErrors
  };

  core.trigger('checkout.common.readyCheck', state);

  if (!state.result) {
    core.trigger('checkout.common.state.nonready', state);

  } else if (!state.blocked) {
    core.trigger('checkout.common.state.blocked', state);

  } else {
    core.trigger('checkout.common.state.ready', state);
  }

  return {'result': state.result, 'blocked': state.blocked};
};

PlaceOrderButtonView.prototype.handleAnyFormChange = function()
{
  if (this.checkState(true).result) {
    this.base
      .removeClass('disabled')
      .removeAttr('title');

  } else {
    var errorMsg = this.base.data('errorMsg')
      || core.t('Order can not be placed because not all required fields are completed. Please check the form and try again.')

    this.base
      .addClass('disabled')
      .attr('title', errorMsg);
  }
};

PlaceOrderButtonView.prototype.handleUpdatePlaceButtonErrorMessage = function(event, data)
{
  if (data && data.errorMsg) {
    this.base.data('errorMsg', data.errorMsg);
  };
};

PlaceOrderButtonView.prototype.handleGetState = function(event, state)
{
  state.result = !this.base.hasClass('disabled');
};

// Load
core.bind(
  'checkout.main.postprocess',
  function () {
    new PlaceOrderButtonView();
  }
);
