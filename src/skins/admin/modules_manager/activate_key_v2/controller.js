/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Activate license key
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'ActivateKeyActivateKeyForm',
  '.activate-key-block .open-license-key-form',
  function () {
    jQuery(this).click(function () {
      jQuery(this).parents('.activate-key-block').find('.activate-key-form').toggle();
      return false;
    })
  }
);