/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Coupons widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Widget
 */
function DiscountCouponsView(base)
{
  this.bind('local.postprocess', _.bind(this.assignHandlers, this));
  core.bind('updateCart', _.bind(this.handleUpdateCart, this));

  core.bind('checkout.cartItems.postprocess', _.bind(this.assignItemsHandlers, this));

  // only on fastlane checkout
  if (jQuery('.checkout_fastlane_container').length > 0) {
    jQuery('.discount-coupons-panel li a').click(_.bind(this.handleRemoveCoupon, this));
  }

  this.callSupermethod('constructor', Array.prototype.slice.call(arguments, 0));
}

extend(DiscountCouponsView, ALoadable);

// No shade widget
DiscountCouponsView.prototype.shadeWidget = false;

// Widget target
DiscountCouponsView.prototype.widgetTarget = 'cart';

// Widget class name
DiscountCouponsView.prototype.widgetClass = '\\XLite\\Module\\CDev\\Coupons\\View\\CartCoupons';

// Postprocess widget
DiscountCouponsView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {

    // Form box
    this.base.find('.new a').click(_.bind(this.handleAdd, this));

    // Form
    var form = this.base.find('form').get(0);
    if (form) {
      if ('undefined' == typeof(form.commonController)) {
        new CommonForm(form);
      }
      form.commonController.backgroundSubmit = true;
    }
  }
}

// Postprocess widget
DiscountCouponsView.prototype.assignItemsHandlers = function(event, state)
{
  if (state.isSuccess) {
    // Remove links
    jQuery('.discount-coupons-panel li a').click(_.bind(this.handleRemoveCoupon, this));
  }
}

DiscountCouponsView.prototype.handleRemoveCoupon = function(event)
{
  return !core.post(event.currentTarget.href);
}

DiscountCouponsView.prototype.handleAdd = function(event)
{
  var box = this.base.find('.add-coupon');
  var link = this.base.find('.new');

  if (box.hasClass('visible')) {
    box.hide().removeClass('visible');

  } else {
    box.show().addClass('visible');
  }

  return false;
}

DiscountCouponsView.prototype.handleUpdateCart = function(event, data)
{
  if (data.coupons) {
    var found = _.find(
      data.coupons,
      function(coupon) {
        return 'added' == coupon.state;
      }
    );
    if (found && this.base) {
      this.base.find('input[name="code"]').val('');
    }
  }
  this.load();
}

// Get event namespace (prefix)
DiscountCouponsView.prototype.getEventNamespace = function()
{
  return 'checkout.coupon';
}

// Load
core.bind(
  ['checkout.main.postprocess','checkout.cart_items.ready'],
  function () {
    core.autoload(DiscountCouponsView, '.coupons');
  }
);

core.bind(
  'cart.main.postprocess',
  function (event, state) {
    view = new DiscountCouponsView('.coupons');
    view.assignItemsHandlers(event, state);
  }
);

