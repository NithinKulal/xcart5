/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common items list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ItemsListQueue()
{
  jQuery('.widget.items-list').each(function(index, elem){
    new ItemsList(jQuery(elem));
  });
}

// Main class
function ItemsList(elem, urlparams, urlajaxparams)
{
  this.initialize(elem, urlparams, urlajaxparams);
}

extend(ItemsList, Base);

ItemsList.prototype.container = null;

ItemsList.prototype.params = null;

ItemsList.prototype.listeners = {};

ItemsList.prototype.listeners.pager = function(handler)
{
  jQuery('.pagination a', handler.container).click(
    function() {
      jQuery('html, body').animate({scrollTop: handler.container.offset().top});

      return !handler.showPage(this);
    }
  );
};

ItemsList.prototype.listeners.pagesCount = function(handler)
{
  jQuery('input.page-length', handler.container).change(
    function() {
      if (this.form) {
        var hnd = function() { return false; }
        jQuery(this.form).submit(hnd);
        var f = this.form;
        setTimeout(function() { jQuery(f).unbind('submit', hnd); }, 500);
      }

      return !handler.changePageLength(this);
    }
  );
};

ItemsList.prototype.listeners.checkboxes = function(handler)
{
  jQuery('input:checkbox.check-all', handler.container).click(
    function() {
      return handler.checkAll(this);
    }
  );
};

ItemsList.prototype.listeners.sortByModes = function(handler)
{
  jQuery('.sort-order .part.sort-crit a', handler.container).click(
    function() {
      return !handler.changeSortByMode(this);
    }
  );
};

ItemsList.prototype.listeners.sortOrderModes = function(handler)
{
  jQuery('.sort-order .part.order-by a', handler.container).click(
    function() {
      return !handler.changeSortOrder();
    }
  );

};

// Change sort criterion
ItemsList.prototype.changeSortByMode = function(handler)
{
  return this.process('sortBy', jQuery(handler).attr('class'));
};

// Change sort order
ItemsList.prototype.changeSortOrder = function()
{
  return this.process(
    'sortOrder',
    (typeof(this.params.urlparams['sortOrder']) == 'undefined' || 'asc' == this.params.urlparams['sortOrder']) ? 'desc' : 'asc'
  );
};


// Check all checkboxes in list
ItemsList.prototype.checkAll = function(handler)
{
  return this.container.find('input:checkbox.checkbox').prop('checked', jQuery(handler).prop('checked') ? 'checked' : '');
};

// Change current page
ItemsList.prototype.showPage = function(handler)
{
//TODO change to getCommentedData() -> also in templates
  return this.process('pageId', core.getValueFromClass(handler, 'page'));
};

// Change items per page number
ItemsList.prototype.changePageLength = function(handler)
{
  var count = parseInt(jQuery(handler).val());

  if (isNaN(count)) {
    count = typeof(this.params.urlparams['itemsPerPage']) != 'undefined' ? this.params.urlparams['itemsPerPage'] : 1;

  } else if (count < 1) {
    count = 1;
  }

  if (count != handler.value) {
    handler.value = count;
  }

  return this.process('itemsPerPage', count);
};

ItemsList.prototype.initialize = function(elem, urlparams, urlajaxparams)
{
  var result = false;

  if (typeof(urlparams) == 'undefined') {
    // Initialize widget from the scratch
    this.container = elem;
    this.params = core.getCommentedData(elem);

  } else {
    // Initialize widget by the sessionCell class identification
    this.container = jQuery('.sessioncell-' + elem);

    this.params = {
      'cell'          : elem,
      'urlparams'     : urlparams,
      'urlajaxparams' : urlajaxparams
    };
  }

  if (this.container.length) {
    // check if already initialized
    if (undefined !== this.container.get(0).itemsListController) {
      return false;
    }

    this.container.get(0).itemsListController = this;

    // Common form support
    CommonForm.autoassign(this.container);

    this.addListeners();

    this.triggerVent('initialize', {'widget': this});

    result = true;
  }

  return result
}

// Add event listeners
ItemsList.prototype.addListeners = function()
{
  _.each(this.listeners, _.bind(this.addListener, this));

  core.microhandlers.runAll(this.container);

  this.triggerVent('addListeners', {'widget': this});
};

// Add listener
ItemsList.prototype.addListener = function(listener)
{
  listener(this);
}

// Change URL param
ItemsList.prototype.setURLParam = function(paramName, paramValue)
{
  var result = (paramValue != this.params.urlparams[paramName]) || (paramValue != this.params.urlajaxparams[paramName]);

  if (result) {
    this.params.urlparams[paramName] = paramValue;
    this.params.urlajaxparams[paramName] = paramValue;
  }

  return result;
};

// Clean URL params
ItemsList.prototype.cleanURLParams = function()
{
  this.params.urlparams = [];
  this.params.urlajaxparams = [];
}

// Set a param and send the request
ItemsList.prototype.process = function(paramName, paramValue)
{
  if (this.setURLParam(paramName, paramValue)) {
    this.loadWidget();
  }

  return true;
};

// Load (reload) widget
ItemsList.prototype.loadWidget = function(callback)
{
  if (hasAJAXSupport()) {

    this.showModalScreen();

    var data = {
      type:     'get',
      url:      this.buildURL(true),
      timeout:  15000,
      complete: _.bind(this.loadHandler, this),
    }
    this.loadCallback = callback;
    this.triggerVent('preload', {'widget': this, 'data': data});

    jQuery.ajax(data);

  } else {
    self.location = this.buildURL();
  }
};

// Show modal screen
ItemsList.prototype.showModalScreen = function()
{
  assignWaitOverlay(this.container);
  this.triggerVent('shade', {'widget': this});
}

// Hide modal screen
ItemsList.prototype.hideModalScreen = function()
{
  unassignWaitOverlay(this.container);
  this.triggerVent('unshade', {'widget': this});
};

// Build URL
ItemsList.prototype.buildURL = function(forAJAX)
{
  var list = forAJAX ? this.params.urlajaxparams : this.params.urlparams;

  if (typeof(list.sessionCell) != 'undefined') {
      list.sessionCell = null;
      delete list.sessionCell;
  }

  return URLHandler.buildURL(list);
};

// AJAX onload event handler
ItemsList.prototype.loadHandler = function(xhr, s)
{
  var processed = false;
  var flag = xhr.status == 200 && xhr.responseText;

  if (flag) {
    if (this.loadCallback) {
      this.loadCallback(xhr.responseText);
    };
    this.placeNewContent(xhr.responseText);
    processed = true;
  }

  this.hideModalScreen();

  if (!processed) {
    self.location = this.buildURL();
  }

  if (!flag) {
    this.addListeners();
  }
};

// Place new list content
ItemsList.prototype.placeNewContent = function(content)
{
  this.container.replaceWith(jQuery('.items-list.sessioncell-' + this.params.cell, content));

  this.triggerVent('place', {'widget': this});

  this.reassign();
};

// Reassign items list controller
ItemsList.prototype.reassign = function()
{
  this.initialize(this.params.cell, this.params.urlparams, this.params.urlajaxparams);
};
