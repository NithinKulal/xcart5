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

function PopupButtonAddReviewAutoload()
{
  core.autoload(PopupButtonAddReview);
}

extend(PopupButtonAddReview, PopupButton);

PopupButtonAddReview.prototype.pattern = '.add-review';

PopupButtonAddReviewAutoload();
