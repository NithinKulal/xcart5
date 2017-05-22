/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Messages list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function MessagesListView()
{
    MessagesListView.superclass.constructor.apply(this, [jQuery('.all-messages .items-list')]);

    this.bind('local.unshade', _.bind(this.forceUnshade, this));
}

extend(MessagesListView, ItemsList);

// Switch subscribing status for order's conversation
MessagesListView.prototype.listeners.switchSubscribe = function(handler)
{
    handler.container.find('.message .watch input').change(
        function(event) {
            var form = jQuery(event.target.form);
            var action = form.find('input[name=action]').val();
            form.find('input[name=action]').val('change_subscriptions');
            form.get(0).commonController.submitBackground();
            form.find('input[name=action]').val(action);
        }
    );
};

// Force unshade
MessagesListView.prototype.forceUnshade = function()
{
    unassignWaitOverlay(jQuery('.all-messages .dialog-content'), true);
};

// Show page by page link
MessagesListView.prototype.showPage = function(handler)
{
    return this.process('pageId', jQuery(handler).data('pageid'));
};

MessagesListView.prototype.listeners.pagesCount = function(handler)
{
    jQuery(':input.page-length', handler.container).change(
        function() {
            if (this.form) {
                var hnd = function() { return false; };
                jQuery(this.form).submit(hnd);
                var f = this.form;
                setTimeout(
                    function() {
                        jQuery(f).unbind('submit', hnd);
                    },
                    500
                );
            }

            return !handler.changePageLength(this);
        }
    );
};

// Get event namespace (prefix)
MessagesListView.prototype.getEventNamespace = function()
{
    return 'list.messages';
};

/**
 * Load product lists controller
 */
core.autoload(MessagesListView);