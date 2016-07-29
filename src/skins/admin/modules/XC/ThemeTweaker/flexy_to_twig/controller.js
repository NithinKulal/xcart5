/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// For flexy_to_twig.js
GLOBAL = window;

function FlexyToTwigConverter () {

  if ('undefined' != typeof(window.FlexyToTwig)) {

    // Do pre-initialize language variables
    core.t('Cancel');
    core.t('Convert templates');

    var o = this;
    this.parser = window.FlexyToTwig;

    if ('undefined' != typeof(this.parser)) {
      this.initialize();
    }

    jQuery('#display-all').click(function() {
       jQuery(this).addClass('hidden');
       jQuery('#display-non-converted').removeClass('hidden');
       o.toggleRows(true);
    });

    jQuery('#display-non-converted').click(function() {
      jQuery(this).addClass('hidden');
      jQuery('#display-all').removeClass('hidden');
      o.toggleRows(false);
    });

  } else if ('undefined' != typeof(window.console)) {
    console.log('FlexyToTwig parser object not found');
  }
}

FlexyToTwigConverter.prototype.parser = null;

FlexyToTwigConverter.prototype.allRows = [];

FlexyToTwigConverter.prototype.rows = [];

FlexyToTwigConverter.prototype.stopped = false;

FlexyToTwigConverter.prototype.markBox = null;

FlexyToTwigConverter.prototype.inProgressMark = '<div class="processing"></div>';

FlexyToTwigConverter.prototype.successMark = '<div class="converted"><i class="fa fa-check"></i></div>';

FlexyToTwigConverter.prototype.flexyContentFailedMark = '<div class="failed" title="' + core.t('Cannot get flexy content') + '"><i class="fa fa-exclamation-circle"></i></div>';

FlexyToTwigConverter.prototype.convertFailedMark = '<div class="failed" title="' + core.t('Failure to convert flexy-template. Check for syntax errors') + '"><i class="fa fa-exclamation-circle"></i></div>';

FlexyToTwigConverter.prototype.failedMark = '<div class="failed" title="' + core.t('Check directory permissions') + '"><i class="fa fa-exclamation-circle"></i></div>';

// Initialize
FlexyToTwigConverter.prototype.initialize = function()
{
  var o = this;

  this.allRows = jQuery('.items-list-table.flexy-to-twig table.list tbody.lines tr.line').not('.orphan');

  for (i = 0; i < this.allRows.length; i++) {
    if (0 == jQuery('td.cell.converted .cell div.converted', this.allRows[i]).length) {
      // Row is not processed
      this.rows.push(this.allRows[i]);
    }
  }

  if (0 < this.rows.length) {

    var button = jQuery('.sticky-panel button.action.convert');

    jQuery(button).addClass('always-enabled');

    jQuery(button).click(function() {
      if (false == o.stopped) {
        jQuery(this).removeClass('convert').addClass('cancel').html('<span>' + core.t('Cancel') + '</span>');

        jQuery('.sticky-panel button.action.cancel').click(function() {
          o.stopProcessing();
          jQuery(this).addClass('disabled');
        });

        o.startProcessing();
      }
    });
  }
}

// Roggle rows visibility mode
FlexyToTwigConverter.prototype.toggleRows = function(display)
{
  for (i = 0; i < this.allRows.length; i++) {
    if (0 < jQuery('td.cell.converted .cell div.converted', this.allRows[i]).length) {
      if (display) {
        jQuery(this.allRows[i]).removeClass('hidden');
      } else {
        jQuery(this.allRows[i]).addClass('hidden');
      }
    }
  }
}

// Convert flexy-to-twig content
FlexyToTwigConverter.prototype.convert = function(flexy)
{
  var twig = '';

  if (this.parser) {

    try {
      twig = this.parser.parse(flexy);

    } catch (e) {
      console.log("Flexy parsing error: " + e.message);
    }
  }

  return twig;
}

// Stop rows processing
FlexyToTwigConverter.prototype.stopProcessing = function()
{
  this.stopped = true;

  jQuery('.sticky-panel button.action.cancel')
    .html('<span>' + core.t('Convert templates') + '</span>')
    .addClass('disabled');
}

// Start rows processing
FlexyToTwigConverter.prototype.startProcessing = function()
{
  this.stopped = false;

  this.processRow();
}

// Process first row from rows queue
FlexyToTwigConverter.prototype.processRow = function()
{
  if (false == this.stopped && 0 < this.rows.length) {

    var row = this.rows[0];

    this.markBox = jQuery('td.cell.converted .cell', row);

    this.markRowAsInProgress();

    var flexyFile = jQuery('.cell.flexyTemplate .cell .plain-value .value', row).text();

    this.sendGetFlexyContent(flexyFile);

  } else {
    this.stopProcessing();
  }
}


// Mark row as 'in progress'
FlexyToTwigConverter.prototype.markRowAsInProgress = function()
{
  if (0 < this.markBox.length) {
    this.markBox.html(this.inProgressMark);
  }
}

// Mark row as 'success'
FlexyToTwigConverter.prototype.markRowAsSuccess = function()
{
  if (0 < this.markBox.length) {
    this.markBox.html(this.successMark);
  }
  jQuery('#display-non-converted').removeClass('hidden');
  jQuery('#display-all').addClass('hidden');
}

// Mark row as 'failed'
FlexyToTwigConverter.prototype.markRowAsFailed = function(message)
{
  if (0 < this.markBox.length) {
    this.markBox.html(message);
  }
}

// Send request for content of flexy-template
FlexyToTwigConverter.prototype.sendGetFlexyContent = function(flexyFile)
{
  this.send('get_flexy_content', flexyFile, null, this.processGetFlexyContentResult);
}

// Callback of 'get_flexy_content' HTTP-request
FlexyToTwigConverter.prototype.processGetFlexyContentResult = function(XMLHttpRequest, textStatus, data, o)
{
  var success = false;

  data = jQuery.parseJSON(data);

  if (data.flexyTemplate && data.content) {

    var twigContent = o.convert(data.content);

    if (twigContent) {
      o.send('save_twig_content', data.flexyTemplate, twigContent, o.processSaveTwigContentResult);
      success = true;

    } else {
      o.markRowAsFailed(o.convertFailedMark);
    }

  } else {
    o.markRowAsFailed(o.flexyContentFailedMark);
  }

  if (!success) {
    o.rows.shift();
    o.processRow();
  }
}

// Callback of 'save_twig_content' HTTP-request
FlexyToTwigConverter.prototype.processSaveTwigContentResult = function(XMLHttpRequest, textStatus, data, o)
{
  if ('true' == data) {
    o.markRowAsSuccess();

  } else {
    o.markRowAsFailed(o.failedMark);
  }

  o.rows.shift();
  o.processRow();
}

// Send request
FlexyToTwigConverter.prototype.send = function(action, flexyFile, content, callback)
{
  var o = this;

  var url = URLHandler.buildURL(
    {
      target: 'flexy_to_twig',
      action: action,
      flexyTemplate: flexyFile
    });

  var options = {context: this};
  if (content) {
    options.type = 'POST';
    options.contentType = 'application/x-www-form-urlencoded'
  }

  core.get(url, callback, { content: content }, options);
}

core.autoload(FlexyToTwigConverter);
