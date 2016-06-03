/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Stripe initialize
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PaypalLoginClass () {
  this.popupWindow = null;
  this.active = false;
}

PaypalLoginClass.prototype.autoload = function () {
};

PaypalLoginClass.prototype.openPopup = function (element) {
  if (this.active) {
    if (this.popupWindow) {
      this.popupWindow.focus();
    }

  } else {
    this.openPopupWindow(element);
    this.setChecker();
  }
};

PaypalLoginClass.prototype.openPopupWindow = function (element) {
  element = jQuery(element);

  var height = 550;
  var width = 400;
  var top = (screen.height/2)-(height/2);
  var left = (screen.width/2)-(width/2);

  this.popupWindow = window.open(
    element.attr('rel'),
    'paypalLoginWindow',
    'location=yes,status=no,scrollbars=no,menubar=no,toolbar=no,width='+width+',height='+height+',top='+top+',left='+left
  );

  this.active = !!this.popupWindow;

  return true;
};

PaypalLoginClass.prototype.setChecker = function () {
  var interval = setInterval(_.bind(function () {
    try {
      if (!this.popupWindow || this.popupWindow.closed) {
        clearInterval(interval);
        this.active = false;
      }
    } catch (e) {}
  }, this), 1000);
};


var PaypalLogin = new PaypalLoginClass();
core.autoload(PaypalLogin);
