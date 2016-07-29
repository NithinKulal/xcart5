/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Install addon button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonInstallAddon()
{
  PopupButtonInstallAddon.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonInstallAddon, PopupButton);

PopupButtonInstallAddon.prototype.pattern = '.install-addon-button';

decorate(
  'PopupButtonInstallAddon',
  'callback',
  function (selector)
  {
    // Autoloading of switch button and license agreement widgets.
    // They are shown in License agreement popup window.
    // TODO. make it dynamically and move it to ONE widget initialization (Main widget)
    core.autoload(SwitchButton);
    core.autoload(LicenseAgreement);
    core.autoload(PopupButtonSelectInstallationType);
  }
);

core.autoload(PopupButtonInstallAddon);
