/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Import language button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonImportLanguage()
{
  PopupButtonImportLanguage.superclass.constructor.apply(this, arguments);
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonImportLanguage, PopupButton);

// New pattern is defined
PopupButtonImportLanguage.prototype.pattern = '.force-import-language';

PopupButtonImportLanguage.prototype.enableBackgroundSubmit = false;

// Autoloading new POPUP widget
core.autoload(PopupButtonImportLanguage);

// Auto display popup window
jQuery(document).ready(function () {
  if (jQuery(PopupButtonImportLanguage.prototype.pattern))
    jQuery(PopupButtonImportLanguage.prototype.pattern).click();
})
