/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Browser server button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonEnterLicenseKey()
{
  PopupButtonEnterLicenseKey.superclass.constructor.apply(this, arguments);
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonEnterLicenseKey, PopupButton);

// New pattern is defined
PopupButtonEnterLicenseKey.prototype.pattern = '.enter-license-key';

PopupButtonEnterLicenseKey.prototype.enableBackgroundSubmit = false;

PopupButtonEnterLicenseKey.prototype.callback = function (selector, link)
{
  PopupButton.prototype.callback.apply(this, arguments);
};

// Autoloading new POPUP widget
core.autoload(PopupButtonEnterLicenseKey);

core.microhandlers.add(
  'PopupButtonEnterLicenseKey',
  PopupButtonEnterLicenseKey.prototype.pattern,
  function (event) {
    core.autoload(PopupButtonEnterLicenseKey);
  }
);
