/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Access control
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'access-timer',
  '.access-control-locked',
  function() {
    var timeLeft = jQuery(this).data('time-left');
    if (timeLeft) {
      (function() {
        timeLeft--;
        if (0 < timeLeft) {
          var min = parseInt(timeLeft / 60);
          var sec = timeLeft % 60;
          jQuery('#timer').text((10 > min ? '0' : '') + min +  ':' + (10 > sec ? '0' : '') + sec);
          setTimeout(arguments.callee, 1000);

        } else {
          self.location.reload();
        }
      })()
    }
  }
);