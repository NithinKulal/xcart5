/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Bulk edit button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
StickyPanelModelList.prototype.enableBulkEditSelected = function () {
  this.base.find('button.bulk-edit span:first').text(core.t('Bulk edit selected'));
};

StickyPanelModelList.prototype.disableBulkEditSelected = function () {
  this.base.find('button.bulk-edit span:first').text(core.t('Bulk edit all'));
};

var getRealItemsCount = function (itemsListBlock) {
  return jQuery(itemsListBlock).find('.lines .line[class*="entity-"]').length;
};

decorate(
    'StickyPanelModelList',
    'process',
    function () {
      arguments.callee.previousMethod.apply(this, arguments);

      core.loadLanguageHash(core.getCommentedData(this.base.find('.bulk-edit')));
    }
);

decorate(
    'StickyPanelModelList',
    'reposition',
    function (selector) {
      arguments.callee.previousMethod.apply(this, arguments);


      var widget = this.base.parents('form').eq(0).find('.widget.items-list').length > 0
          ? this.base.parents('form').eq(0).find('.widget.items-list').get(0).itemsListController
          : null;

      if (widget) {
        widget.bind('local.selector.checked', _.bind(this.enableBulkEditSelected, this))
            .bind('local.selector.unchecked', _.bind(this.disableBulkEditSelected, this))
            .bind('local.selector.massChecked', _.bind(this.enableBulkEditSelected, this))
            .bind('local.selector.massUnchecked', _.bind(this.disableBulkEditSelected, this));

        core.bind(
            'stickyPanelReposition',
            _.bind(this.disableBulkEditSelected, this)
        );
      }
    }
);

var processButtonState = function (widget) {
  if (!widget
      || !widget.length
      || !widget.siblings('.sticky-panel').find('.bulk-edit').length
  ){
    return;
  }
  var bulkEditButton = widget.siblings('.sticky-panel').find('.bulk-edit');

  if (getRealItemsCount(widget) > 1) {
    bulkEditButton.show();
  } else {
    bulkEditButton.hide();
  }
};

core.bind('list.model.table.initialize', function(event, data) {
  processButtonState(data.widget.container);
  var stickyPanel = data.widget.container.siblings('.sticky-panel');
  
  if (stickyPanel.length && stickyPanel.get(0).controller) {
    stickyPanel.get(0).controller.fixMoreActionButtons();
  }
});

jQuery(function () {
  jQuery('div.bulk-edit > button.regular-button:first').click(function () {
    jQuery(this).closest('.bulk-edit').siblings('div.hidden').find('button').click();
  });
});
