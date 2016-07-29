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

function PopupButtonEditReviewAutoload()
{
  core.autoload(PopupButtonEditReview);
}

extend(PopupButtonEditReview, PopupButton);

PopupButtonEditReview.prototype.pattern = '.edit-review';

PopupButtonEditReviewAutoload();
