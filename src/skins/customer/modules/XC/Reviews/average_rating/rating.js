/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Average rating controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.button-average-rating',
    handler: function () {
      jQuery(this)
        .click(function() {
          jQuery('.product-average-rating-container').toggle();
        });
    }
  }
);

CommonForm.elementControllers.push(
  {
    pattern: 'div.vote-bar',
    handler: function () {

      var $tooltip = jQuery(this).closest('div').parent().children('.rating-tooltip');
      var $div = jQuery(this).closest('div');

      var timeout;

      $div.hover(
          function() {
            timeout = setTimeout(function(){
              $tooltip.show(100);
            }, 250);
          },
          function() {
            clearTimeout(timeout);
            $tooltip.hide(100);
          }
        );
    }
  }
);
