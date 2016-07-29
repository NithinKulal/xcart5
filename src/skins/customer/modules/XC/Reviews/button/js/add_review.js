/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add review button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonAddReview()
{
  PopupButtonAddReview.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonAddReview, PopupButton);

PopupButtonAddReview.prototype.pattern = '.add-review';

PopupButton.prototype.enableBackgroundSubmit = false;

core.autoload(PopupButtonAddReview);

// Required for ability to add review after ajax reloading of reviews list
core.bind('block.product.details.postprocess', function() {
  core.autoload(PopupButtonAddReview);
});

core.microhandlers.add(
  'emailTooltip',
  '.modify-review-dialog .tooltip-main .help-text',
  function() {
    attachTooltip(
      jQuery(this).closest('.tooltip-main').find('.help-icon'),
      jQuery(this).html()
    );
  }
);