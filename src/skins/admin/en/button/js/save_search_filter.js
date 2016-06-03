/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * 'Save search filter' button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('div.save-search-filter .button-label').click(
      function () {
        //jQuery(this).hide();
        var boxAction = jQuery(this).parent().find('.button-action').eq(0);
        if (0 < boxAction.length) {
          var visibility = 'visible';
          if (visibility == jQuery(boxAction).css('visibility')) {
            visibility = 'hidden';
          }
          jQuery(boxAction).css('visibility', visibility);
        }
      }
    );
  }
);
