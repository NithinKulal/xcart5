/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Trial notice button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonTrialNotice()
{
  PopupButtonTrialNotice.superclass.constructor.apply(this, arguments);
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonTrialNotice, PopupButton);

// New pattern is defined
PopupButtonTrialNotice.prototype.pattern = '.force-notice';

PopupButtonTrialNotice.prototype.enableBackgroundSubmit = false;

// Remove focus from link within text of trial notice
decorate(
  'PopupButtonTrialNotice',
  'callback',
  function(selector) {
    jQuery('.ui-dialog a').get(0).focus();
    jQuery('.ui-dialog').css('z-index', 10000);
  }
);

// Autoloading new POPUP widget
core.autoload(PopupButtonTrialNotice);

// Auto display popup window
jQuery(document).ready(function () {
  if (jQuery(PopupButtonTrialNotice.prototype.pattern))
    jQuery(PopupButtonTrialNotice.prototype.pattern).click();
})

