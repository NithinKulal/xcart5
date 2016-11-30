/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function HeaderSettingsController(base)
{
  this.callSupermethod('constructor', arguments);

  if (this.base && this.base.length) {
    this.block = new HeaderSettingsView(this.base);
    core.bind('updateHeaderSettings', _.bind(this.handleUpdateHeaderSettings, this));
    core.bind('checkHeaderSettingsRecentlyUpdated', _.bind(this.checkRecentlyUpdated, this));
  }
}

extend(HeaderSettingsController, AController);

// Controller name
HeaderSettingsController.prototype.name = 'HeaderSettingsController';

// Find pattern
HeaderSettingsController.prototype.findPattern = '.header_settings';

// Initialize controller
HeaderSettingsController.prototype.initialize = function()
{
  var o = this;

  this.base.bind(
    'reload',
    function(event, box) {
      o.bind(box);
    }
  );
};

HeaderSettingsController.prototype.handleUpdateHeaderSettings = function(event, data)
{
  this.block.load();
};

HeaderSettingsController.prototype.isRecentlyUpdated = function()
{
  return false;
};

HeaderSettingsController.prototype.checkRecentlyUpdated = function(event, data)
{
  if (this.isRecentlyUpdated()) {
    this.base.addClass('recently-updated');
  } else {
    this.base.removeClass('recently-updated');
  }
};

function HeaderSettingsView(base)
{
  this.callSupermethod('constructor', arguments);

  this.bind('local.preload', _.bind(this.handlePreload, this));
  this.bind('local.loadingError', _.bind(this.handleLoaded, this));
}

extend(HeaderSettingsView, ALoadable);
// No shade widget
HeaderSettingsView.prototype.shadeWidget = false;

// Widget target
HeaderSettingsView.prototype.widgetTarget = 'main';

// Widget class name
HeaderSettingsView.prototype.widgetClass = '\\XLite\\Module\\XC\\CrispWhiteSkin\\View\\HeaderSettings';

HeaderSettingsView.prototype.handlePreload = function(event, state)
{
  var box = this.base.find('.dropdown-menu');

  if (box.length) {
    var overlay = jQuery(document.createElement('div'));
    overlay
      .addClass('wait-overlay')
      .append('<div></div>');
    box.append(overlay);
  }
};

HeaderSettingsView.prototype.handleLoaded = function(event, state)
{
  this.base.find('.wait-overlay').remove();
};

// Get event namespace (prefix)
HeaderSettingsView.prototype.getEventNamespace = function()
{
  return 'header_settings';
};

core.autoload(HeaderSettingsController);