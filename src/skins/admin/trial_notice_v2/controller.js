/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'TrialNoticeActivateKeyForm',
  '.trial-notice-block .open-license-key-form',
  function () {
    jQuery(this).click(function () {
      jQuery(this).parents('.trial-notice-block').find('.activate-key-form').toggle();
      return false;
    })
  }
);