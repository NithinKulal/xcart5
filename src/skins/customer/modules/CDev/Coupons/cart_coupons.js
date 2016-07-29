/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Coupons widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function DiscountPanelView(base)
{
  this.base = base;
  core.bind('checkout.cartItems.postprocess', _.bind(this.assignItemsHandlers, this));

  // only on fastlane checkout
  if (jQuery('.checkout_fastlane_container').length > 0) {
    jQuery(this.base).find('li a').click(_.bind(this.handleRemoveCoupon, this));
  }
}

DiscountPanelView.autoload = function()
{
  new DiscountPanelView('.discount-coupons-panel');
}

// Postprocess widget
DiscountPanelView.prototype.assignItemsHandlers = function(event, state)
{
  if (state.isSuccess) {
    // Remove links
    jQuery(this.base).find('li a').click(_.bind(this.handleRemoveCoupon, this));
  };
}

DiscountPanelView.prototype.handleRemoveCoupon = function(event)
{
  return !core.post(event.currentTarget.href);
}

/**
 * Widget
 */
function DiscountCouponsView(base)
{
  this.bind('local.postprocess', _.bind(this.assignHandlers, this));
  core.bind('updateCart', _.bind(this.handleUpdateCart, this));

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
    this.base.find('#code').focusout(_.bind(this.codeFocusOut, this));

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

DiscountCouponsView.prototype.codeFocusOut = function(event) {
  var codeInput = jQuery(event.target);
  if (!codeInput.val() && event.target.commonController) {
    event.target.unmarkAsInvalid()
  };
}

DiscountCouponsView.prototype.handleAdd = function(event)
{
  var box = this.base.find('.add-coupon');
  var link = this.base.find('.new');

  if (box.hasClass('visible')) {
    box.hide().removeClass('visible');
    this.base.removeClass('opened');
    this.base.parents('#cart').removeClass('opened');

  } else {
    box.show().addClass('visible');
    this.base.addClass('opened');
    this.base.addClass('opened');
    this.base.parents('#cart').addClass('opened');

    if(window.innerWidth < 769) {
      jQuery('html,body').animate(
        {scrollTop: this.base.offset().top + this.base.height()
      }, 1000);
    }
  }

  return false;
}

DiscountCouponsView.prototype.reload = _.debounce(function() {
  this.load();
}, 100);

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

  this.reload();
}

// Get event namespace (prefix)
DiscountCouponsView.prototype.getEventNamespace = function()
{
  return 'checkout.coupon';
}

// Load
core.bind(
  'checkout.main.postprocess',
  function () {
    core.autoload(DiscountCouponsView, '.coupons');
    core.autoload(DiscountPanelView);
  }
);

var debouncedAutoloader = _.debounce(function() {
  core.autoload(DiscountCouponsView, '.coupons');
  core.autoload(DiscountPanelView);
}, 100);

// Fastlane checkout
core.bind('checkout.cart_items.ready', debouncedAutoloader);

core.bind(
  'cart.main.postprocess',
  function (event, state) {
    view = new DiscountCouponsView('.coupons');

    view = new DiscountPanelView('.discount-coupons-panel');
    view.assignItemsHandlers(event, state);
  }
);

