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

MessagesListController.prototype.findPattern += '.items-list.order-messages';

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
        var btn = this.base.find('button.open-dispute');
        if (btn.length > 0) {
            new PopupButtonOpenDispute(btn);
        }

        this.base.parents('form').get(0).commonController.enableBackgroundSubmit();

        this.base.find('.order-items .do-show').click(_.bind(this.handleShowOrderItems, this));
        this.base.find('.order-items .do-hide').click(_.bind(this.handleHideOrderItems, this));

        this.base.find('.action-buttons a').click(_.bind(this.handleBackgroundAction, this));

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

MessagesListView.prototype.handleShowOrderItems = function(event)
{
    this.base.find('.order-items').removeClass('state-hidden').addClass('state-visible');
    this.base.find('.order-items-list').collapse('show');

    return false;
};

MessagesListView.prototype.handleHideOrderItems = function(event)
{
    this.base.find('.order-items').removeClass('state-visible').addClass('state-hidden');
    this.base.find('.order-items-list').collapse('hide');

    return false;
};

MessagesListView.prototype.handleBackgroundAction = function(event)
{
    core.post(jQuery(event.target).attr('href'));

    return false;
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
