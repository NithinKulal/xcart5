/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller-decorator for Install modules (LAs of modules) popup button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonInstallModules()
{
  PopupButtonInstallModules.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonInstallModules, PopupButton);

PopupButtonInstallModules.prototype.pattern = '.install-modules-button';

decorate(
  'PopupButtonInstallModules',
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

decorate(
  'PopupButtonInstallModules',
  'getURLParams',
  function (button)
  {
    var params = core.getCommentedData(button, 'url_params');
    var moduleIds = [];

    jQuery('input[type="hidden"]', jQuery(button)).each(function (index, elem) {moduleIds.push(jQuery(elem).val());});
    params.moduleIds = moduleIds;

    return params;
  }
);

core.autoload(PopupButtonInstallModules);
