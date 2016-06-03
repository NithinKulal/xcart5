/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Checkout controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Main widget
 */
function CheckoutView(base)
{
  core.bind('checkout.main.postprocess', _.bind(this.assignHandlers, this))
    .bind('checkout.common.nonready', _.bind(this.handleNonReady, this))
    .bind('common.shaded', _.bind(this.handleCheckoutBlock, this))
    .bind('common.unshaded', _.bind(this.handleCheckoutUnblock, this))
    .bind('afterPopupPlace', _.bind(this.handleOpenPopup, this));

  jQuery('form.place').submit(_.bind(this.handlePlaceOrder, this));
  CheckoutView.superclass.constructor.apply(this, arguments);

  // Preload language labels
  core.loadLanguageHash(core.getCommentedData(jQuery('.checkout-block')));
}

extend(CheckoutView, ALoadable);

CheckoutView.autoload = function()
{
  jQuery('.checkout-block .steps').each(
    function() {
      new CheckoutView(this);
    }
  );
};

// Shade widget
CheckoutView.prototype.shadeWidget = true;

// Update page title
CheckoutView.prototype.updatePageTitle = false;

// Widget target
CheckoutView.prototype.widgetTarget = 'checkout';

// Widget class name
CheckoutView.prototype.widgetClass = '\\XLite\\View\\Checkout\\Steps';

CheckoutView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {
    this.base.find('form.place')
      .removeAttr('onsubmit')
      .get(0).commonController
      .switchControlReadiness()
      .resetReadiness()
      .bind('local.ready', _.bind(this.handleChange, this))
      .bind('local.unready', _.bind(this.handleChange, this));
    this.base.find('form .agree-note a').click(_.bind(this.handleOpenTerms, this));
  }
};

CheckoutView.prototype.handleOpenPopup = function()
{
  jQuery('form.select-address .addresses > li').click(_.bind(this.handleSelectAddress, this));
};

CheckoutView.prototype.appendInput = function (form, name, value) {
  var input = document.createElement('input');
  input.name = name;
  input.value = value;
  form.appendChild(input);
};

CheckoutView.prototype.getAddressForm = function(type) {
  var className = null;

  if (type === 's') {
    className = '.step-shipping-address form.shipping-address';
  } else {
    className = '.step-billing-address form.billing-address';
  };

  var formController = null;
  if (className !== null && 0 < jQuery(className).length) {
    formController = jQuery(className).get(0).commonController;
  };

  return formController;
};

CheckoutView.prototype.handleSelectAddress = function(event)
{
  var addressId = core.getValueFromClass(event.currentTarget, 'address');
  if (addressId) {
    var form = jQuery(event.target).parents('form').eq(0);
    if (form.length) {
      form.get(0).elements.namedItem('addressId').value = addressId;

      var formController = this.getAddressForm(
        form.get(0).elements.namedItem('atype').value
      );

      if (formController) {
        this.appendInput(form.get(0), 'hasEmptyFields', !formController.validate({silent: true}));
      };

      popup.openAsWait();
      form.get(0).submitBackground(
        function() {
          popup.close();
        }
      );
    }
  }

  return false;
};

CheckoutView.prototype.handleCheckoutBlock = function()
{
  this.base.find('button.place-order')
    .prop('disabled', 'disabled')
    .addClass('disabled')
    .attr('title', core.t('Order can not be placed because not all required fields are completed. Please check the form and try again.'));
};

CheckoutView.prototype.handleCheckoutUnblock = function()
{
  this.base.find('button.place-order')
    .removeProp('disabled')
    .removeClass('disabled')
    .removeAttr('title');
};

CheckoutView.prototype.handleOpenTerms = function(event)
{
  return !popup.load(
    event.currentTarget,
    {
      dialogClass: 'terms-popup'
    }
  );
};

CheckoutView.prototype.handlePlaceOrder = function(event)
{
  this.shade();
};

CheckoutView.prototype.handleNonReady = function(event)
{
  this.unshade();
};

CheckoutView.prototype.handleChange = function(event)
{
  this.base.find('form.place').get(0).commonController.resetReadiness();
  core.trigger('checkout.common.anyChange', this);
};

// Get event namespace (prefix)
CheckoutView.prototype.getEventNamespace = function()
{
  return 'checkout.main';
};

// Autoload
core.autoload(CheckoutView);
