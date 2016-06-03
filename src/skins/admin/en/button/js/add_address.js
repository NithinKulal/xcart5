/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add address button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonAddAddress()
{
  PopupButtonAddAddress.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonAddAddress, PopupButton);

PopupButtonAddAddress.prototype.pattern = '.add-address';

decorate(
  'PopupButtonAddAddress',
  'callback',
  function (selector)
  {
    // Some autoloading could be added
    UpdateStatesList();
  }
);

core.autoload(PopupButtonAddAddress);
