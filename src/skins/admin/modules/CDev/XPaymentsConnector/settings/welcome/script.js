/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sale widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


function markStep(step) {

    var selector = '.top-line .step' + step + ',.top-line .step' + step + ' *';

  jQuery(selector).mouseenter(
      function(e) {
        jQuery(selector).addClass('hover');
      }
    ).mouseout(
      function() {
        jQuery(selector).removeClass('hover');
      }
    );
}

jQuery(document).ready(function() {

  for (var i = 1; i <= 3; i++) {

    markStep(i);
  }


});
