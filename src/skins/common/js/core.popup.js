/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Popup-singleton
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var popup = {};

/**
 * Properties
 */

// Loading status
popup.isLoading = false;

// Request type status - POST or GET
popup.isPostRequest = false;

// Current unblock event handler
popup.currentUnblockCallback = null;

// Current popup
popup.currentPopup = null;

// Load options
popup.loadOptions = null;

// Do not do real close popup in popup.close()
popup.pseudoClose = false;

/**
 * Methods
 */

// Load data to popup
popup.load = function(url, unblockCallback, timeout)
{
  if (_.isObject(unblockCallback) && !_.isFunction(unblockCallback)) {
    var options = unblockCallback;
    options.unblockCallback = 'undefined' == typeof(options.unblockCallback) ? null : options.unblockCallback;
    options.timeout = timeout || null;

  } else {
    var options = {
      'unblockCallback': unblockCallback,
      'timeout':         timeout
    };
  }

  this.loadOptions = options;

  var result = false;
  if (core.isRequesterEnabled) {
    var method = null;

    if (_.isObject(url) && !_.isElement(url)) {
      url = url.get(0);
    }

    if (url.constructor == String) {
      method = 'loadByURL';

    } else if (url.constructor == HTMLFormElement) {
      method = 'loadByForm';

    } else if (url.constructor == HTMLAnchorElement) {
      method = 'loadByLink';

    } else if (url.constructor == HTMLButtonElement) {
      method = 'loadByButton';

    }

    if (method) {
      this.isLoading = true;
      this.currentUnblockCallback = options.unblockCallback;

      if (this.currentPopup) {
        // Reload popup content only
        this.shade();
      } else {
        // Open waiting dialog
        this.openAsWait();
      }

      this.isPostRequest = false;
      result = this[method](url, options.timeout);
    }
  }

  return result;
};

// Load by URL
popup.loadByURL = function(url, timeout)
{
  return core.get(
    this.preprocessURL(url),
    _.bind(this.postprocessRequest, this),
    null,
    {
      timeout: timeout
    }
  );
};

// Load by form element
popup.loadByForm = function(form)
{
  form = jQuery(form).get(0);

  form.setAttribute('action', this.preprocessURL(form.getAttribute('action')));

  return form ? form.submitBackground(_.bind(this.postprocessRequest, this)) : false;
};

// Load by link element
popup.loadByLink = function(link)
{
  link = jQuery(link).eq(0);

  var href = (1 == link.length && link.attr('href')) ? link.attr('href') : false;

  return href
    ? core.get(this.preprocessURL(href), _.bind(this.postprocessRequest, this))
    : false;
};

// Load by button element
popup.loadByButton = function(button)
{
  var result = false;

  button = jQuery(button);

  if (button.attr('onclick') && -1 !== button.attr('onclick').toString().search(/\.location[ ]*=[ ]*['"].+['"]/)) {

    // By onclick attribute
    var m = button.attr('onclick').toString().match(/\.location[ ]*=[ ]*['"](.+)['"]/);
    result = core.get(this.preprocessURL(m[1]), _.bind(this.postprocessRequest, this));

  } else if (button.data('location')) {

    // By kQuery data cell
    result = core.get(this.preprocessURL(button.data('location')), _.bind(this.postprocessRequest, this));

  } else if (0 < button.parents('form').length) {

    // By button's form
    result = this.loadByForm(jQuery(button).parents('form').eq(0));

  }

  return result;
};

// Preprocess URL
popup.preprocessURL = function(url)
{
  if (url && -1 == url.search(/only_center=1/)) {
    url += (-1 == url.search(/\?/) ? '?' : '&') + 'only_center=1';
  }

  return url;
};

// Postprocess request
popup.postprocessRequest = function(XMLHttpRequest, textStatus, data, isValid)
{
  if (XMLHttpRequest && XMLHttpRequest instanceof jQuery.Event && textStatus) {
    var event      = XMLHttpRequest;
    XMLHttpRequest = textStatus.XMLHttpRequest;
    data           = textStatus.data;
    isValid        = textStatus.isValid;
    textStatus     = textStatus.textStatus;
  }

  var responseStatus = 4 == XMLHttpRequest.readyState ? parseInt(XMLHttpRequest.getResponseHeader('ajax-response-status')) : 0;

  if (4 != XMLHttpRequest.readyState) {

    // Connection failed
    this.destroy();
    // TODO - add top message

  } else if (278 == responseStatus) {

    // Redirect
    this.destroy();
    var url = XMLHttpRequest.getResponseHeader('AJAX-Location');
    if (url) {
      self.location = url;

    } else {
      self.location.reload(true);
    }

  } else if (279 == responseStatus) {

    // Internal redirect
    var url = XMLHttpRequest.getResponseHeader('AJAX-Location');
    if (url) {
      this.load(url, this.currentUnblockCallback);

    } else {
      self.location.reload(true);
    }

  } else if (277 == responseStatus) {

    // Close popup in silence
    this.close();

  } else if (200 == XMLHttpRequest.status) {

    // Load new content
    this.unshade();

    var uuid = _.uniqueId();

    if (data) {
      core.bind(['resources.ready', 'resources.empty'], _.bind(
        function(event, args){
          if (args.uuid === uuid) {
            this.place(data, this.loadOptions);
            core.trigger(
              'afterPopupPlace',
              {
                'XMLHttpRequest': XMLHttpRequest,
                'textStatus':     textStatus,
                'data':           data,
                'isValid':        isValid
              }
            );
          }
        },
        this)
      );

      core.parseResources(jQuery.parseHTML(data), uuid);

    } else {
      this.unfreezePopup();
    }

  } else {

    // Loading failed
    this.close();

  }
};

// Place request data
popup.place = function(data, options)
{
  this.isLoading = false;

  if (false !== data) {
    data = this.extractRequestData(data);
    this.assignHandlers(data);
    this.open(data, options);
  }
};

// Extract widget data
popup.extractRequestData = function(data)
{
  return jQuery(data);
};

popup.assignHandlers = function(data)
{
  // Do not need this assignment since the microhandlers would be run after the popup placement
  // core.microhandlers.runAll(data);
};

popup.shade = function()
{
  this.currentPopup.widget.find('.ui-dialog-content')
    .append('<div class="wait-overlay"><div class="wait-overlay-progress"><div></div></div></div>');
};

popup.unshade = function()
{
  if (this.currentPopup) {
    this.currentPopup.widget.find('.wait-overlay').remove();
  }
};

// Popup post processing
popup.postprocess = function()
{
  // If for some cases you need direct submit of the form (inside the popup widget)
  // then provide the form with the "no-popup-ajax-submit" CSS class
  this.currentPopup.widget
    .find('form')
    .not('.no-popup-ajax-submit')
    .commonController(
      'enableBackgroundSubmit',
      _.bind(this.freezePopup, this),
      _.bind(this.postprocessRequest, this)
    );

  core.microhandlers.runAll(this.currentPopup.widget);
};

// Freeze popup content
popup.freezePopup = function()
{
  if (this.currentPopup) {
    this.currentPopup.widget.find('form').each(
      function() {
        jQuery('button,input:image,input:submit', this).each(
          function() {
            if (!this.disabled) {
              this.temporaryDisabled = true;
              jQuery(this).prop('disabled', 'disabled');
            }
          }
        );
      }
    );

    this.shade();
  }
};

// Unfreeze popup content
popup.unfreezePopup = function()
{
  if (this.currentPopup) {
    this.currentPopup.widget.find('form').each(
      function() {
        jQuery('button,input:image,input:submit', this).each(
          function() {
            if (this.temporaryDisabled) {
              jQuery(this).removeProp('disabled');
              this.temporaryDisabled = true;
            }
          }
        );
      }
    );
  }
};

// Open as wait box
popup.openAsWait = function()
{
  this.open(jQuery('<div data-dialog-class="block-wait-box"><div class="block-wait"><div></div></div></div>'));
};

// Open-n-display popup
popup.open = function(box, additionalOptions)
{
  additionalOptions = additionalOptions || {};

  if (this.currentPopup) {
    this.destroy();
  }

  if (box && _.isString(box)) {
    box = jQuery('<div></div>')
      .html(box);
  }

  box = jQuery(box);
  if (box.attr('title')) {
    box.data('title', box.attr('title'));
    additionalOptions.title = box.data('title');

  } else if (box.data('title')) {
    box.attr('title', box.data('title'));
    additionalOptions.title = box.data('title');
  }

  box.dialog(
    this.extendOptions(this.getPopupOptions(box), additionalOptions)
  );

  this.currentPopup = {
    'box':    box,
    'widget': jQuery(box).dialog('widget')
  };

  this.reposition();

  this.postprocess();
};

popup.getPopupOptions = function(box)
{
  return {
    autoOpen:      true,
    closeOnEscape: true,
    dialogClass:   (box.data('dialog-class') ? box.data('dialog-class') : 'default-dialog')
      + ' ' + (box.attr('title') ? 'has-title' : 'no-title'),
    draggable:     false,
    height:        'auto',
    modal:         (typeof(box.data('dialog-modal')) !== 'undefined' ? box.data('dialog-modal') : true),
    position:      { my: "center", at: "center", of: window },
    resizable:     false,
    width:         'auto',
    minWidth:      300,
    minHeight:     200,

    open:          _.bind(
      function(event, ui) {
        jQuery('.overlay-blur-base').addClass('overlay-blur');
        core.trigger('popup.open', { widget: this });
      },
      this
    ),
    close:         _.bind(
      function(event, ui) {
        jQuery('.overlay-blur-base').removeClass('overlay-blur');
        this.currentPopup.widget.find('form').each(
          function (index, elem) {
            jQuery(elem).validationEngine('hide');
          }
        );

        var box = this.currentPopup.box;

        this.close();
      },
      this
    ),

    beforeClose:  _.bind(this.beforeClose, this)
  };
};

popup.extendOptions = function(first, second)
{
  _.each(
    ['dialogClass'],
    function (key) {
      if (_.isString(first[key]) && _.isString(second[key])) {
        first[key] += ' ' + second[key];
        delete second[key];
      }
    }
  );

  return _.extend(first, second);
};

// Reposition (center) popup
popup.reposition = function()
{
  if (!this.currentPopup) {
    return;
  }

  var box = this.currentPopup.box;

  if (box.length) {
    box.dialog(
      "option",
      "position",
      {
        my: "center",
        at: "center",
        of: window
      }
    );
  }
};

// DestroDestroy
popup.destroy = function()
{
  if (this.currentPopup && this.currentPopup.box) {
    var box = this.currentPopup.box;
    this.close();
    box.dialog('destroy');
    box.remove();
  }
};


// Close popup
popup.close = function()
{
  var box;
  if (this.currentPopup && this.currentPopup.box) {
    box = this.currentPopup.box;
    if (!this.pseudoClose) {
      box.dialog('close');
    }

    if (this.currentUnblockCallback && _.isFunction(this.currentUnblockCallback)) {
      this.currentUnblockCallback();
    }
  }

  this.currentUnblockCallback = null;
  this.currentPopup = null;

  core.trigger('popup.close', { 'widget': this, 'box': box });
};

popup.beforeClose = function(event)
{
  var box;
  if (this.currentPopup && this.currentPopup.box) {
    box = this.currentPopup.box;
  }

  core.trigger('popup.beforeClose', { 'widget': this, 'box': box });
};

jQuery(window).resize(
  function(event) {
    popup.reposition();
  }
);
