/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add address button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonLogin(base)
{
  PopupButtonLogin.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonLogin, PopupButton);

PopupButtonLogin.prototype.pattern = '.popup-button.popup-login';

decorate(
  'PopupButtonLogin',
  'callback',
  function (selector)
  {
    arguments.callee.previousMethod.apply(this, arguments);
    core.autoload(PopupButton);
  }
);

decorate(
  'PopupButton',
  'callback',
  function (selector)
  {
    arguments.callee.previousMethod.apply(this, arguments);
    core.autoload(PopupButtonLogin);
  }
);

core.autoload(PopupButtonLogin);
