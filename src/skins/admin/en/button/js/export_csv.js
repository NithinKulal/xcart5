/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonExportCSV() {
  core.bind('export.failed', _.bind(this.handleExportFinish, this));
  core.bind('export.completed', _.bind(this.handleExportFinish, this));
  PopupButtonExportCSV.superclass.constructor.apply(this, arguments);
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonExportCSV, PopupButton);

// New pattern is defined
PopupButtonExportCSV.prototype.pattern = '.export-csv';

PopupButtonExportCSV.prototype.enableBackgroundSubmit = false;

PopupButtonExportCSV.prototype.handleExportFinish = function () {
  var elem = jQuery(this.pattern);
  core.bind('afterPopupPlace', _.once(_.bind(this.postprocessFinish, this)));
  popup.load(URLHandler.buildURL(core.getCommentedData(elem, 'url_params')));
  popup.pseudoClose = true;
};

PopupButtonExportCSV.prototype.postprocessFinish = function () {
  jQuery('a[data-autodownload]').each(function () {
    this.click();
  });
};

PopupButtonExportCSV.prototype.restoreState = function () {
  jQuery('.ui-dialog-content').dialog('destroy');
  jQuery('.widget-popupexport').parent().remove();
  core.unbind('eventTaskRun');
};

PopupButtonExportCSV.prototype.getSelectionFromForm = function(elem) {
  var form = jQuery(elem).closest('form');
  var checked = jQuery(form).serializeArray().filter(function(value) {
    return value.name.search('select') >= 0;
  });
  return checked.map(function(value) {
    return /^select\[(.*)\]$/.exec(value.name)[1];
  });
};

PopupButtonExportCSV.prototype.startExport = function (elem, items) {
  var data = core.getCommentedData(elem, 'export');
  data[xliteConfig.form_id_name] = xliteConfig.form_id;
  var filter = this.getSelectionFromForm(elem);
  if (filter.length > 0) {
    data['options']['selection'] = filter;
  }
  return core.post(
    {
      target: 'export'
    },
    null,
    data
  );
};

PopupButtonExportCSV.prototype.cancelExport = function (widget, box) {
  this.restoreState();
  var data = [];
  data[xliteConfig.form_id_name] = xliteConfig.form_id;

  return core.post(
    {
      target: 'export',
      action: 'cancel'
    },
    null,
    data
  );
}

decorate(
  'PopupButtonExportCSV',
  'callback',
  function (selector, link) {
    // previous method call
    arguments.callee.previousMethod.apply(this, arguments);
    core.bind('popup.beforeClose', _.once(_.bind(this.cancelExport, this)));
    core.autoload(EventTaskProgress);
    core.autoload(PopupExportController);
  }
);

decorate(
  'PopupButtonExportCSV',
  'eachClick',
  function (elem) {
    this.restoreState();
    var xhr = this.startExport(elem);
    // previous method call
    var self = this;
    var args = arguments;
    xhr.always(function() {
      args.callee.previousMethod.apply(self, args);
    });
  }
);

// Autoloading new POPUP widget
core.autoload(PopupButtonExportCSV);
