/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * News messages controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function NewsMessagesItemsListController(base)
{
  NewsMessagesItemsListController.superclass.constructor.apply(this, arguments);
}

extend(NewsMessagesItemsListController, ListsController);

NewsMessagesItemsListController.prototype.name = 'NewsMessagesItemsListController';

NewsMessagesItemsListController.prototype.findPattern += '.news-messages';

NewsMessagesItemsListController.prototype.getListView = function()
{
  return new NewsMessagesItemsListView(this.base);
}

function NewsMessagesItemsListView(base)
{
  NewsMessagesItemsListView.superclass.constructor.apply(this, arguments);
}

extend(NewsMessagesItemsListView, ListView);

NewsMessagesItemsListView.prototype.postprocess = function(isSuccess, initial)
{
  NewsMessagesItemsListView.superclass.postprocess.apply(this, arguments);

  if (isSuccess) {
    // Some routines
  }
}

core.autoload(NewsMessagesItemsListController);
