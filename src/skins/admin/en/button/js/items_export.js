/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Remove button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
StickyPanelModelList.prototype.enableExportSelected = function () {
  var exportBtn = this.base.find('div.items-export button.regular-button:first');
	exportBtn.find('span:first').text(core.t('Export selected'));
};

StickyPanelModelList.prototype.disableExportSelected = function () {
	var exportBtn = this.base.find('div.items-export button.regular-button:first');
	exportBtn.find('span:first').text(core.t('Export all'));
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
	    widget.bind('local.selector.checked', _.bind(this.enableExportSelected, this))
            .bind('local.selector.unchecked', _.bind(this.disableExportSelected, this))
            .bind('local.selector.massChecked', _.bind(this.enableExportSelected, this))
	          .bind('local.selector.massUnchecked', _.bind(this.disableExportSelected, this));
      core.bind(
        'stickyPanelReposition',
        _.bind(this.disableExportSelected, this)
      );
	  }
  }
);

jQuery(function () {
  jQuery('div.items-export > button.regular-button:first').click(function () {
    jQuery(this).closest('.items-export').siblings('div.hidden').find('button').click();
  });
});
