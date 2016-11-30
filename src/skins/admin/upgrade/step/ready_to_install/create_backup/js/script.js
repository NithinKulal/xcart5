/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Create backup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


core.bind(
  'load',
  function () {
    $(window).scroll(function() {
      if($(window).height() < ($(document).height() - 100) && $(window).scrollTop() + $(window).height() >= ($(document).height() - 100)) {
        $('.backup-focus-overlay').addClass('active');
        $('.create-backup-section').addClass('focus');
      } else {
        $('.backup-focus-overlay').removeClass('active');
        $('.create-backup-section').removeClass('focus');
      }
    });
  }
);