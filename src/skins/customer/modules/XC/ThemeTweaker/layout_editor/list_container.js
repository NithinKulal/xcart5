/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ListContainer(element)
{
    this.base = $(element);
    this.instances = [];

    var self = this;
    var groups = $(this.base).children('.list-items-group');

    savedState = localStorage.getItem('layout-editor-mode');
    if (savedState === null) {
        savedState = true;
    } else {
        savedState = (savedState === "true");
    }

    this.base.toggleClass('disabled', !savedState);

    groups.each(function(){
        var groupName = $(self.base).data('group') || '';
        jQuery(this).data('group', groupName);
        var instance = Sortable.create(this, {
            animation: 150,
            group: 'common',
            disabled: !savedState,
            filter: ".list-item-actions, .list-item-action",
            onStart: _.bind(self.onStart, self),
            onEnd: _.bind(self.onEnd, self),
            onAdd: _.bind(self.onAdd, self),
            onUpdate: _.bind(self.onUpdate, self),
            onRemove: _.bind(self.onRemove, self),
            onFilter: _.bind(self.onFilter, self),
            onMove: _.bind(self.onMove, self),
            forceFallback: true,
            scroll: true, // or HTMLElement
            scrollSensitivity: 300, // px, how near the mouse must be to an edge to start scrolling.
            scrollSpeed: 30, // px
        });

        self.instances.push(instance);
    });
}

ListContainer.prototype.toggle = function() {
    var state = null;
    this.instances.forEach(function(item){
        state = item.option("disabled");
        item.option('disabled', !state);
    });

    this.base.toggleClass('disabled', !state);
};

ListContainer.prototype.onStart = function (event) {
    $('.list-item').addClass('list-item__not-hoverable');
    $('.list-items-group').addClass('list-items-group__on-move');
};


ListContainer.prototype.onEnd = function (event) {
    $('.list-item').removeClass('list-item__not-hoverable');
    $('.list-items-group').removeClass('list-items-group__on-move');
};


ListContainer.prototype.onAdd = function (event) {
    var oldViewlist = $(event.from).data('list');
    var newViewlist = $(event.to).data('list');
    var displayGroup = $(event.to).data('group');
    var itemId = $(event.item).data('id');
    var newPosition = this.calculateWeight(event.item);

    if (!_.isEmpty(jQuery(event.item).data('display'))) {
        jQuery(event.item).data('display', displayGroup);

        core.trigger(
            'layout.block.reload',
            {
                id: itemId,
                displayGroup: displayGroup,
            }
        );
    }

    core.trigger(
        'layout.moved',
        {
            id: itemId,
            from: oldViewlist,
            list: newViewlist,
            position: newPosition
        }
    );
};


ListContainer.prototype.onUpdate = function (event) {
    var list = $(event.to).data('list');
    var itemId = $(event.item).data('id');
    var newPosition = this.calculateWeight(event.item);
    core.trigger(
        'layout.rearranged',
        {
            id: itemId,
            list: list,
            was: event.oldIndex,
            position: newPosition
        }
    );
};

ListContainer.prototype.calculateWeight = function (element) {
    var next = parseInt($(element).next().data('weight')) || 0;
    var prev = parseInt($(element).prev().data('weight')) || 0;

    var weight = Math.ceil(
        (next + prev) / 2
    );

    $(element).data('weight', weight);

    return weight;
};

ListContainer.prototype.onRemove = function (event) {

};


ListContainer.prototype.onFilter = function (event) {

};

ListContainer.prototype.onMove = function (event) {

};

core.autoload(ListContainer, '.list-container');
