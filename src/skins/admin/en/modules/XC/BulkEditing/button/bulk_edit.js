/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Remove button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
StickyPanelModelList.prototype.enableBulkEditSelected = function () {
  var exportBtn = this.base.find('div.bulk-edit button.regular-button:first');
	exportBtn.find('span:first').text(core.t('Edit selected'));
};

StickyPanelModelList.prototype.disableBulkEditSelected = function () {
	var exportBtn = this.base.find('div.bulk-edit button.regular-button:first');
	exportBtn.find('span:first').text(core.t('Edit all'));
};

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

jQuery(function () {
  jQuery('div.bulk-edit > button.regular-button:first').click(function () {
    jQuery(this).closest('.bulk-edit').siblings('div.hidden').find('button').click();
  });
});
