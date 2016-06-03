/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Reloadable layout block widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function LayoutBlockWidget(base)
{
  LayoutBlockWidget.superclass.constructor.apply(this, arguments);

  this.widgetClass = this.base.data('widget');

  this.widgetTarget = core.getTarget();

  core.bind('layout.block.reload', _.bind(this.reload, this));

  this.base.data('controller', this);
}

extend(LayoutBlockWidget, ALoadable);

LayoutBlockWidget.prototype.reloadIfError = false;

LayoutBlockWidget.prototype.shadeWidget = true;

LayoutBlockWidget.prototype.defineWidgetParams = function() {
  return {
    'displayGroup': this.base.data('display')
  };
};

LayoutBlockWidget.prototype.reload = function(event, args) {
  if (args.id == this.base.data('id')) {
    this.widgetParams = this.defineWidgetParams();
    this.load();
  }
};

// Place parsed request data into DOM
LayoutBlockWidget.prototype.insertIntoDOM = function(box)
{
  this.base.find('.list-item-content').html(box);
  return this.base;
};

// Extract widget content
LayoutBlockWidget.prototype.extractContent = function(box)
{
  box = jQuery(this.containerRequestPattern, box);
  return box;
};

LayoutBlockWidget.autoload = function() {
  jQuery('.list-item[data-display]').each(function () {
    new LayoutBlockWidget(this);
  });
};

core.autoload(LayoutBlockWidget);
