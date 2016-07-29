/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Edit review button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonEditReview()
{
  PopupButtonEditReview.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonEditReview, PopupButton);

PopupButtonEditReview.prototype.pattern = '.edit-review';

PopupButton.prototype.enableBackgroundSubmit = false;

core.autoload(PopupButtonEditReview);

// Required for ability to edit review after ajax reloading of reviews list
core.bind('list.reviews.postprocess', function() {
  core.autoload(PopupButtonEditReview);
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