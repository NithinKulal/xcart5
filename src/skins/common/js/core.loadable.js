/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Abstract loadable block (widget)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Constructor
function ALoadable(base)
{
  ALoadable.superclass.constructor.apply(this, arguments);

  if (base && this.checkBase(jQuery(base))) {
    this.basePattern = base;
    this.base = jQuery(base);
    this.loadHandlerCallback = _.bind(this.loadHandler, this);
    this.postprocess(true, true);

    if (this.listenToHash && (!jQuery.isEmptyObject(hash.get()))) {
      jQuery(document).ready(_.bind(this.loadByHash, this));
    }
  }
}

extend(ALoadable, Base);

// Base element(s)
ALoadable.prototype.base = null;

// Base pattern
ALoadable.prototype.basePattern = null;

// Shade flag
ALoadable.prototype.isShowModalScreen = false;

// Widget loading flag
ALoadable.prototype.isLoading = false;

// Widget has deferred load operation
ALoadable.prototype.deferredLoad = false;

// Preload event custom handler
ALoadable.prototype.preloadHandler = null;

// Postload event custom handler
ALoadable.prototype.postloadHandler = null;

ALoadable.prototype.listenToHash = false;

ALoadable.prototype.listenToHashPrefix = '';


// Options

// Use shade
ALoadable.prototype.shadeWidget = true;

// Class for shade overlay
ALoadable.prototype.shadeClass = null;

// Use widget blocking
ALoadable.prototype.blockWidget = true;

// Update page title from loaded request or not
ALoadable.prototype.updatePageTitle = false;

// Update breadcrumb last node from loaded request or not
ALoadable.prototype.updateBreadcrumb = false;

// Widget target
ALoadable.prototype.widgetTarget = null;

// Widget parameters
ALoadable.prototype.widgetParams = null;

// Widget class name
ALoadable.prototype.widgetClass = null;

// Page title element pattern
ALoadable.prototype.titlePattern = 'h1:eq(0)';

// Page title element pattern in request's response data
ALoadable.prototype.titleRequestPattern = '.ajax-container-loadable:eq(0)';

// Container element pattern in request's response data
ALoadable.prototype.containerRequestPattern = 'div.ajax-container-loadable';

ALoadable.prototype.reloadIfError = true;

// Check base
ALoadable.prototype.checkBase = function(base)
{
  var result = 0 < base.length;

  if (result) {
    base.map(
      function() {
        if (result && 'undefined' != typeof(this.loadable)) {
          result = false;
        }
      }
    );
  }

  return result;
}

// Load widget
ALoadable.prototype.load = function(params)
{
  if (this.listenToHash) {
    hash.add(params);
  }

  if (!core.isRequesterEnabled) {
    return false;
  }

  if (this.isLoading) {
    this.deferredLoad = true;
    return true;
  }

  this.isLoading = true;
  this.deferredLoad = false;

  this.lastLoadParams = params;

  var url = this.buildWidgetRequestURL(params);

  if (this.base) {
    this.base.trigger('preload', [this, url]);

    if (typeof(this.preloadHandler) == 'function') {

      // Call preload event handler
      this.preloadHandler.call(this.base);
    }
  }

  var state = { 'widget': this, 'url': url, 'options': this.getLoaderOptions() }

  this.triggerVent('preload', state);

  this.saveState();

  this.shade();

  state.xhr = core.get(state.url, this.loadHandlerCallback, null, state.options);

  this.triggerVent('afterload', state);

  return state.xhr;
};

// Build request widget URL (AJAX)
ALoadable.prototype.buildWidgetRequestURL = function(params)
{
  return URLHandler.buildURL(this.getParams(params));
};

// [ABSTRACT] Get additional parameters
ALoadable.prototype.getParams = function(params)
{
  params = params ? params : {};

  // Set widget target and parameters
  if ('undefined' == typeof(params.target) && this.widgetTarget) {
    params.target = this.widgetTarget;
  }

  if (this.widgetParams) {
    for (var i in this.widgetParams) {
      params[i] = this.widgetParams[i];
    }
  }

  // TODO remove if it will be no use!!
  if ('undefined' == typeof(params.sessionCell) && this.sessionCell) {
    params.sessionCell = this.sessionCell;
  }

  if ('undefined' == typeof(params.action)) {
    params.action = '';
  }

  if ('undefined' == typeof(params.widget) && this.widgetClass) {
    params.widget = this.widgetClass;
  }

  return params;
};

// Loader AJAX options
ALoadable.prototype.getLoaderOptions = function()
{
  return {};
}

// onload handler
ALoadable.prototype.loadHandler = function(xhr, s, data)
{
  this.isLoading = false;

  this.unshade();

  if (false !== data) {

    var container = this.getTemporaryContainer();

    container.html(data);

    var uuid = _.uniqueId();

    core.bind(['resources.ready', 'resources.empty'], _.bind(
      function(event, args){
        if (args.uuid === uuid) {
          var content = this.extractRequestData(container);
          container.remove();
          if (this.isValidContent(content)) {
            var processed = false;
            processed = this.placeRequestData(content);
            this.finishLoadHandler(processed);
          }
        }
      },
      this)
    );

    core.parseResources(container, uuid);
  }
};


// Check loaded content
ALoadable.prototype.finishLoadHandler = function(processed)
{
  jQuery(this.base).data('controller', this);

  this.postprocess(processed);

  if (!this.isLoading && this.deferredLoad) {
    this.deferredLoad = false;
    this.load(this.lastLoadParams);

  } else if (processed) {
    if (typeof(this.postloadHandler) == 'function') {
      this.postloadHandler.call(this.base, xhr);
    }

    this.triggerVent('loaded', this);
    core.trigger('loader.loaded', this);

  } else {

    this.triggerVent('loadingError', {'widget': this, 'arguments': arguments});
    if (this.reloadIfError) {
      setTimeout(
        _.bind(
          function() {
            this.load(this.lastLoadParams);
          },
          this
        ),
        300
      );
    }
  }
}

// Check loaded content
ALoadable.prototype.isValidContent = function(content)
{
  return 0 < jQuery(this.containerRequestPattern, content).length;
};

// Get temporary container
ALoadable.prototype.getTemporaryContainer = function()
{
  var div = document.createElement('DIV');
  div.style.display = 'none';
  jQuery('body').get(0).appendChild(div);
  return jQuery(div);
};

// Extract widget data
ALoadable.prototype.extractRequestData = function(div)
{
  div.find('script').first().remove();
  return div;
};

// Place request data
ALoadable.prototype.placeRequestData = function(box)
{
  // Update page title
  if (this.updatePageTitle || this.updateBreadcrumb) {
    this.replacePageTitle(box);
  }

  box = this.extractContent(box).children();

  //TODO: find any reason to add random class.
  var id = 'temporary-ajax-id-' + (new Date()).getTime();

  box.addClass(id);

  this.base.trigger('reload', [box]);

  if (0 < box.length) {
    this.base = this.insertIntoDOM(box);
  } else {
    this.base.empty();
  }

  this.base.removeClass(id);

  return true;
};

// Extract widget content
ALoadable.prototype.extractContent = function(box)
{
  box = jQuery(this.containerRequestPattern, box);
  if (box.children().eq(0).hasClass('block')) {
    box = jQuery('.block:first > .content', box.eq(0));
  }

  return box;
};

// Place parsed request data into DOM
ALoadable.prototype.insertIntoDOM = function(box)
{
  this.base.replaceWith(box);
  return box;
};

// Replace page title
ALoadable.prototype.replacePageTitle = function(box)
{
  var title = jQuery(this.titleRequestPattern, box).eq(0).attr('title');
  if (title) {
    this.placeNewPageTitle(title);
  }

  return title;
};

// Place new page title
ALoadable.prototype.placeNewPageTitle = function(title)
{
  if (this.updatePageTitle) {
    jQuery(this.titlePattern).eq(0).html(title);
    jQuery("head title").eq(0).html(title);
  }

  if (this.updateBreadcrumb) {
    jQuery('#breadcrumb .last').find('a,span').html(title);
  }
};

// [ABSTRACT] Widget post processing (after new widget data placing)
ALoadable.prototype.postprocess = function(isSuccess, initial)
{
  if (isSuccess) {
    var o = this;

    this.base.map(
      function() {
        this.loadable = o;
      }
    );

    if (!initial && 'undefined' != typeof(window.CommonForm)) {
      this.base.filter('form').each(
        function() {
          new CommonForm(this);
        }
      );

      jQuery('form', this.base).each(
        function() {
          new CommonForm(this);
        }
      );

      this.base.filter('*:input').each(
        function() {
          new CommonElement(this);
        }
      );

      jQuery('*:input', this.base).each(
        function() {
          new CommonElement(this);
        }
      );

    }
  }

  this.triggerVent('postprocess', {'widget': this, 'isSuccess': isSuccess, 'initial': initial});
};

// Load content by hash
ALoadable.prototype.loadByHash = function()
{
  if (!this.isHashEqual()) {
    this.load(hash.get());
  }
};

// Check - has is equal arguments of widget
ALoadable.prototype.isHashEqual = function()
{
  var result = true;

  var current = this.base.data('widget-arguments');
  if (current && hash.get()) {
    result = _.every(
      hash.get(),
      function(v, k) {
        return 'undefined' != typeof(current[k]) && current[k] == v;
      }
    );

  } else if (!current && _.size(hash.get()) > 0) {
    result = false;
  }

  return result;
};

// [ABSTRACT] Widget save state (before widget load / reload)
ALoadable.prototype.saveState = function()
{
};

// Show modal screen
ALoadable.prototype.shade = function()
{
  if (!this.base || !this.base.length) {
    return false;
  }

  if (this.isShowModalScreen) {
    return true;
  }

  if (this.isShade())  {
    var overlay = this.assignWaitOverlay(this.getShadeBase());

    if (this.shadeClass) {
      overlay.addClass(this.shadeClass);
    }

    this.triggerVent('shaded', this);

  } else if (this.blockWidget) {

    this.base.click(this.blocker).focus(this.blocker);
  }

  this.isShowModalScreen = true;

  return true;
};

// Assemble overlay
ALoadable.prototype.assignWaitOverlay = function(base)
{
  return assignWaitOverlay(base);
};

// Unshade widget
ALoadable.prototype.unshade = function()
{
  if (!this.base || !this.isShowModalScreen) {
    return false;
  }

  if (_.result(this, 'shadeWidget'))  {
    this.unassignWaitOverlay(this.getShadeBase());
    this.triggerVent('unshaded', this);

  } else if (this.blockWidget) {
    this.base.unbind('click', this.blocker).unbind('focus', this.blocker);
  }

  this.isShowModalScreen = false;

  return true;
};

ALoadable.prototype.unassignWaitOverlay = function(base)
{
  unassignWaitOverlay(base, true);
};

ALoadable.prototype.isShade = function()
{
  var state = {
    widget: this,
    result: _.result(this, 'shadeWidget')
  };

  this.triggerVent('shade', state);

  return state.result;
};

// Get base element for shade / unshade operation
ALoadable.prototype.getShadeBase = function() {
  return this.base;
};

// Event blocker
ALoadable.prototype.blocker = function(event) {
  event.stopPropagation();

  return false;
};

// Submit specified form
ALoadable.prototype.submitForm = function(form, callback)
{
  return form.submitBackground(
    callback,
    false,
    {
      rpc: 'POST' == jQuery(form).attr('method').toUpperCase()
    }
  );
};
