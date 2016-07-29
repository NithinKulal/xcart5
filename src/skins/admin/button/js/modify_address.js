/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Modify button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonModifyAddress()
{
  PopupButtonModifyAddress.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonModifyAddress, PopupButton);

PopupButtonModifyAddress.prototype.pattern = '.modify-address';

decorate(
  'PopupButtonModifyAddress',
  'callback',
  function (selector)
  {
    // Some autoloading could be added
    UpdateStatesList();
  }
);

core.autoload(PopupButtonModifyAddress);
