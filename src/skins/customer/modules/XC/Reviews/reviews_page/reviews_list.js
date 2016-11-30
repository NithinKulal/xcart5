/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Reviews list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ReviewsListView(base)
{
  ReviewsListView.superclass.constructor.apply(this, arguments);
}

extend(ReviewsListView, ListView);

// Reviews list class
function ReviewsListController(base)
{
  ReviewsListController.superclass.constructor.apply(this, arguments);
}

extend(ReviewsListController, ListsController);

ReviewsListController.prototype.name = 'ReviewsListController';
ReviewsListController.prototype.findPattern = '.product-reviews';

ReviewsListController.prototype.getListView = function()
{
  return new ReviewsListView(this.base);
};

// Get event namespace (prefix)
ReviewsListView.prototype.getEventNamespace = function()
{
  return 'list.reviews';
};

/**
 * Load reviews lists controller
 */
core.autoload(ReviewsListController);
