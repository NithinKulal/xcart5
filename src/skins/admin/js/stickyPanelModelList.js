/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky panel controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function StickyPanelModelList(base)
{
  StickyPanel.apply(this, arguments);
}

extend(StickyPanelModelList, StickyPanel);

// Autoloader
StickyPanelModelList.autoload = function()
{
  jQuery('.sticky-panel.model-list').each(
    function() {
      new StickyPanelModelList(this);
    }
  );
};

// Process widget (initial catch widget)
StickyPanelModelList.prototype.reposition = function(isResize)
{
  StickyPanel.prototype.reposition.apply(this, arguments);

  var widget = this.base.parents('form').eq(0).find('.widget.items-list').length > 0
    ? this.base.parents('form').eq(0).find('.widget.items-list').get(0).itemsListController
    : null;

  if (widget) {
    widget.bind('local.selector.checked', _.bind(this.markAllListActions, this))
      .bind('local.selector.unchecked', _.bind(this.unmarkAllListActions, this));
  }
};

StickyPanelModelList.prototype.markAllListActions = function()
{
  this.getListActionButtons().each(
    function() {
      this.enable();
    }
  );

  this.getListActionButtons().removeClass('disabled');
};

StickyPanelModelList.prototype.unmarkAllListActions = function()
{
  this.getListActionButtons().each(
    function() {
      this.disable();
    }
  );

  this.getListActionButtons().addClass('disabled');
};

StickyPanelModelList.prototype.getListActionButtons = function()
{
  return this.base.find('button.list-action')
    .not('.always-enabled');
};

core.microhandlers.add(
  'NoItemsList',
  '.no-items',
  function (event) {
    var sticky = jQuery('.sticky-panel');
    if (jQuery(this).css('display') == 'none') {
      // We show the sticky panel when the no-items element is hidden (the list contains some elements)
      sticky.css({display : '', position: 'relative'});
    } else if (!sticky.hasClass('always-visible')) {
      // We hide the sticky panel and move it into the initial state
      // You can add "always-visible" CSS class to the sticky panel if you need to show it anyway
      sticky.css('display', 'none');
      sticky.get(0).controller ? sticky.get(0).controller.unmarkAsChanged() : null;
    }
  }
);

// Autoload
core.microhandlers.add(
  'StickyPanelModelList',
  '.sticky-panel.model-list',
  function () {
    core.autoload(StickyPanelModelList);
  }
);
