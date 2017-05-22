/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Messages list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Products list class
function MessagesListController(base)
{
    MessagesListController.superclass.constructor.apply(this, arguments);
};

extend(MessagesListController, ListsController);

MessagesListController.prototype.name = 'MessagesListController';

MessagesListController.prototype.findPattern += '.items-list.all-messages';

MessagesListController.prototype.getListView = function()
{
    return new MessagesListView(this.base);
};

function MessagesListView(base)
{
    MessagesListView.superclass.constructor.apply(this, arguments);
}

extend(MessagesListView, ListView);

MessagesListView.prototype.postprocess = function(isSuccess, initial)
{
    MessagesListView.superclass.postprocess.apply(this, arguments);

    if (isSuccess) {
        this.base.find('.separator.closed a').click(_.bind(this.handleOpenList, this));
        this.base.find('.separator.opened a').click(_.bind(this.handleCloseList, this));

        this.base.parents('form').get(0).commonController.enableBackgroundSubmit();

        core.bind('ordermessagescreate', _.bind(this.handleCreateMessage, this));
    }
};

MessagesListView.prototype.handleOpenList = function(event)
{
    this.load({ display_all: 1 });

    return false;
};

MessagesListView.prototype.handleCloseList = function(event)
{
    this.load({ display_all: 0 });

    return false;
};

MessagesListView.prototype.handleCreateMessage = function(event)
{
    this.load();
};


// Get event namespace (prefix)
MessagesListView.prototype.getEventNamespace = function()
{
    return 'list.order.messages';
};

/**
 * Load product lists controller
 */
core.autoload(MessagesListController);
