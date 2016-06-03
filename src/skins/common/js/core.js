/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * javascript core
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * OOP core
 */

// OOP extends emulation
function extend(child, parent) {
  var F = function() { };
  F.prototype = parent.prototype;
  child.prototype = new F();
  child.prototype.constructor = child;
  child.superclass = parent.prototype;
}

// Decorate / add method
function decorate(c, methodName, method)
{
  c = getClassByName(c);

  var result = false;

  if (c) {
    method.previousMethod = 'undefined' == typeof(c.prototype[methodName]) ? null : c.prototype[methodName];
    c.prototype[methodName] = method;
    result = true;
  }

  return result;
}

// Get class object by name (or object)
function getClassByName(c)
{
  if (c && c.constructor == String) {
    c = eval('(("undefined" != typeof(window.' + c + ') && ' + c + '.constructor == Function) ? ' + c + ' : null)');

  } else if (!c || c.constructor != Function) {
    c = null;
  }

  return c;
}

// Base class
function Base()
{
  this.triggerVent('initialize');
}

// Get superclass without class name
Base.prototype.getSuperclass = function()
{
    var m = this.constructor.toString().match(/function ([^\(]+)/);

    return eval(m[1] + '.superclass');
}

// Call parent method by name nad arguments list
Base.prototype.callSupermethod = function(name, args)
{
    superClass = this.getSuperclass();

    return superClass[name].apply(this, args);
}

// Bind event handler
Base.prototype.bind = function(name, handler)
{
  jQuery(this).bind(name, handler);

  return this;
}

// Unbind event handler
Base.prototype.unbind = function(name, handler)
{
  jQuery(this).unbind(name, handler);

  return this;
}

// Trigger event on common mediator object
Base.prototype.triggerVent = function(name, data)
{
  var ns = this.getEventNamespace();
  if (ns) {
    core.trigger(ns + '.' + name, data);
  }

  return jQuery(this).trigger('local.' + name, data);
}

// Get event namespace (prefix)
Base.prototype.getEventNamespace = function()
{
  return null;
}

// Core definition
window.core = {

  isDebug: false,

  isReady: false,

  isRequesterEnabled: false,

  savedEvents: [],

  messages: jQuery({}),

  resources: [],

  // Collections of the getters which return the parameters of the widgets to get via widgets collection process
  widgetsParamsGetters: {},

  doShadeWidgetsCollection: true,

  registerWidgetsParamsGetter: function (widgetsId, getter)
  {
    if (!this.widgetsParamsGetters[widgetsId]) {
        this.widgetsParamsGetters[widgetsId] = [];
    }
    this.widgetsParamsGetters[widgetsId].push(getter);
  },

  getWidgetsParams: function (widgetsId, paramsToCall)
  {
    var params = [];
    for (var key in this.widgetsParamsGetters[widgetsId]) {
      params = array_merge(params, this.widgetsParamsGetters[widgetsId][key](paramsToCall));
    }
    return params;
  },

  widgetsTriggers: {},

  registerWidgetsTriggers: function (widgetsId, triggersGetter)
  {
    if (!this.widgetsTriggers[widgetsId]) {
      this.widgetsTriggers[widgetsId] = [];
    }
    this.widgetsTriggers[widgetsId].push(triggersGetter);
  },

  getWidgetsTriggers: function (widgetsId)
  {
    var triggers = [];
    for (var key in this.widgetsTriggers[widgetsId]) {
      triggers.push(this.widgetsTriggers[widgetsId][key]());
    }
    return triggers;
  },

  shadowWidgets: {},

  registerShadowWidgets: function (widgetsId, shadowGetter)
  {
    if ("undefined" === typeof(this.shadowWidgets[widgetsId])) {
      this.shadowWidgets[widgetsId] = [];
    }
    this.shadowWidgets[widgetsId].push(shadowGetter);
  },

  getShadowWidgets: function (widgetsId)
  {
    var shadow = [];
    for (var key in this.shadowWidgets[widgetsId]) {
      shadow.push(this.shadowWidgets[widgetsId][key]());
    }
    return array_unique(shadow);
  },

  widgetsTriggersBind: {},

  registerTriggersBind: function (widgetsId, triggersGetter)
  {
    if (!this.widgetsTriggersBind[widgetsId]) {
      this.widgetsTriggersBind[widgetsId] = [];
    }
    this.widgetsTriggersBind[widgetsId].push(triggersGetter);
  },

  callTriggersBind: function (widgetsId)
  {
    for (var key in this.widgetsTriggersBind[widgetsId]) {
      this.widgetsTriggersBind[widgetsId][key]();
    }
  },

  shadeWidgetsCollection: function (base)
  {
    if (this.doShadeWidgetsCollection) {
      jQuery(base).append('<div class="single-progress-mark"><div></div></div>');
    }
    this.doShadeWidgetsCollection = true;
  },

  unshadeWidgetsCollection: function (base)
  {
    jQuery(base + ' .single-progress-mark').remove();
  },

  processUpdateWidgetsCollection: function (widgetsId, widgetsCollection, widgetsCollectionParamsCommon, base)
  {
    // Shadows triggers (unbind and disable the trigger elements)
    var triggers = this.getWidgetsTriggers(widgetsId);
    for (var k in triggers) {
      jQuery(triggers[k]).unbind(k).prop('disabled', 'disabled');
    }
    this.shadeWidgetsCollection('.product-details-info');

    // Shadow widgets collection
    //this.shadowCollection(base, this.getShadowWidgets(widgetsId));

    // Load widgets
    this.loadWidgetsCollection(
      base,
      widgetsCollection,
      array_merge(
        widgetsCollectionParamsCommon,
        this.getWidgetsParams(widgetsId, widgetsCollectionParamsCommon)
      ),
      function ()
      {
        // Unshadow triggers (bind and enable trigger elements)
        for (var k in triggers) {
          jQuery(triggers[k]).removeProp('disabled');
        }
        core.unshadeWidgetsCollection('.product-details-info');
        core.callTriggersBind(widgetsId);
      }
    );
  },

  // Trigger common message
  trigger: function(name, params)
  {
    var result = true;

    name = name.toLowerCase();

    if (this.isReady) {

      if (this.isDebug && 'undefined' != typeof(window.console)) {
        if (params) {
          console.log('Fire \'' + name + '\' event with arguments: ' + var_export(params, true));

        } else {
          console.log('Fire \'' + name + '\' event');
        }
      }

      result = this.messages.trigger(name, [params]);

    } else {
      this.savedEvents.push(
        {
          name: name,
          params: params
        }
      );
    }

    return result;
  },

  // Bind on common messages
  bind: function(name, callback)
  {
    if (_.isArray(name)) {
      for(var key in name) {
        this.messages.bind(name[key].toLowerCase(), callback);
      }
    } else {
      this.messages.bind(name.toLowerCase(), callback);
    }

    return this;
  },

  // Unbind on common messages
  unbind: function(name, callback)
  {
    if (_.isArray(name)) {
      for(var key in name) {
        this.messages.unbind(name[key].toLowerCase(), callback);
      }
    } else {
      this.messages.unbind(name.toLowerCase(), callback);
    }

    return this;
  },

  // Get HTML data from server
  get: function(url, callback, data, options)
  {
    if (_.isObject(url)) {
      url = URLHandler.buildURL(url);
    }

    options = options || {};

    options = jQuery.extend(
      {
        async:       true,
        cache:       false,
        complete:    function(XMLHttpRequest, textStatus)
          {
            var callCallback = core.preprocessResponse(XMLHttpRequest, options, callback);
            data = core.processResponse(XMLHttpRequest);

            return (callCallback && callback) ? callback(XMLHttpRequest, textStatus, data) : true;
          },
        contentType: 'text/html',
        global:      false,
        timeout:     15000,
        type:        'GET',
        url:         url,
        data:        data
      },
      options
    );

    return jQuery.ajax(options);
  },

  // Post form data to server
  post: function(url, callback, data, options)
  {
    if (_.isObject(url)) {
      url = URLHandler.buildURL(url);
    }

    options = options || {};

    options = jQuery.extend(
      {
        async:       true,
        cache:       false,
        complete:    function(XMLHttpRequest, textStatus)
          {
            var callCallback = core.preprocessResponse(XMLHttpRequest, options, callback);
            data = core.processResponse(XMLHttpRequest);
            var notValid = !!XMLHttpRequest.getResponseHeader('not-valid');

            return (callCallback && callback) ? callback(XMLHttpRequest, textStatus, data, !notValid) : true;
          },
        contentType: 'application/x-www-form-urlencoded',
        global:      false,
        timeout:     15000,
        type:        'POST',
        url:         url,
        data:        data
      },
      options
    );

    return jQuery.ajax(options);
  },

  // Response preprocess (run callback or not)
  preprocessResponse: function(xhr, options, callback)
  {
    var result = true;

    var responseStatus = parseInt(xhr.getResponseHeader('ajax-response-status'));

    if (200 == xhr.status && (270 == responseStatus || 279 == responseStatus) && xhr.getResponseHeader('AJAX-Location') && (!options || !options.rpc)) {
      core.get(
        xhr.getResponseHeader('AJAX-Location'),
        callback
      );

      result = false;

    } else if (278 == responseStatus) {

      // Redirect
      var url = xhr.getResponseHeader('AJAX-Location');

      if (url) {
        self.location = url;
      } else {
        self.location.reload(true);
      }

      result = false;
    }

    return result;
  },

  // Process response from server
  processResponse: function(xhr)
  {
    var responseStatus = parseInt(xhr.getResponseHeader('ajax-response-status'));

    if (4 == xhr.readyState && 200 == xhr.status) {
      var list = xhr.getAllResponseHeaders().split(/\n/);

      for (var i = 0; i < list.length; i++) {
        if (-1 !== list[i].search(/^event-([^:]+):(.+)/i)) {

          // Server-side event
          var m = list[i].match(/event-([^:]+):(.+)/i);
          core.trigger(m[1].toLowerCase(), eval('(' + m[2] + ')'));
        }
      }
    }

    return (4 == xhr.readyState && 200 == xhr.status) ? xhr.responseText : false;
  },

  showInternalError: function()
  {
    return this.showError(this.t('Javascript core internal error. Page will be refreshed automatically'));
  },

  showServerError: function()
  {
    return this.showError(this.t('Background request to server side is failed. Page will be refreshed automatically'));
  },

  showError: function(message)
  {
    core.trigger(
      'message',
      {'type': 'error', 'message': message}
    );
  },

  languageLabels: [],

  t: function(label, substitute)
  {
    var found = false;
    for (var i = 0; i < this.languageLabels.length && !found; i++) {
      if (this.languageLabels[i].name == label) {
        label = this.languageLabels[i].label;
        found = true;
      }
    }

    // TODO - add request language label from server-side
    if (!found) {
      var loadedLabel = core.rest.get('translation', label, false);
      if (loadedLabel) {
        this.languageLabels.push(
          {
            name:  label,
            label: loadedLabel
          }
        );
        label = loadedLabel;
      }
    }

    if (substitute) {
      for (var i in substitute) {
        label = label.replace('{{' + i + '}}', substitute[i]);
      }
    }

    return label;
  },

  loadResource: function(resource, type)
  {
    var deferred = new $.Deferred();
    if (_.isString(resource)) {
      resource = {
        url: resource,
        widget: ''
      }
    }

    type = type || core.getExtension(resource.url);

    if (core.isLoadedResource(resource.url)) {
      return;
    }

    LazyLoad[type](resource.url, function() {
      core.registerResources([resource.url]);
      core.trigger('resources.loaded', {file: resource.url, widget: resource.widget});
      deferred.resolve();
    });

    return deferred.promise();
  },

  isLoadedResource: function(url)
  {
    return _.contains(core.resources, url);
  },

  registerResources: function(list)
  {
    for (var key in list) {
      core.resources.push(list[key]);
    }
  },

  parseResources: function(element, uuid)
  {
    var restoreCallback = core.microhandlers.supressRun();
    uuid = uuid || '';
    element = jQuery(element);
    var resource = element.find('script[data-resource]').first();

    if (resource.length) {
      var data = JSON.parse(resource.html());
      resource.remove();

      if (data.css || data.js) {
        var cssLoaded = core.loadResources(data.css, 'css');
        var jsLoaded = core.loadResources(data.js, 'js');

        jQuery.when(cssLoaded, jsLoaded).done(
          function(){
            restoreCallback();
            core.trigger('resources.ready', {widget: data.widget, uuid: uuid});
          }
        );
      } else {
        restoreCallback();
        core.trigger('resources.empty', {widget: data.widget, uuid: uuid});
      }
    }
  },

  loadResources: function(list, type)
  {
    var deferred = new $.Deferred();
    var promises = [];

    for (var key in list) {
      var resource = list[key];

      promises.push(core.loadResource(resource, type));
    }

    $.when.apply($, promises).then(
      function(){
        deferred.resolve();
      }
    );

    return deferred.promise();
  },

  loadLanguageHash: function(hash)
  {
    _.each(
      hash,
      _.bind(
        function (data, label) {
          var found = false;
          for (var i = 0; i < this.languageLabels.length && !found; i++) {
            if (this.languageLabels[i].name == label) {
              found = true;
            }
          }

          if (!found) {
            this.languageLabels.push(
              {
                name:  label,
                label: data
              }
            );
          }
        },
        this
      )
    );
  },

  rest: {

    lastResponse: null,

    request: function(type, name, id, data, callback)
    {
      if (!type || !name) {
        return false;
      }

      this.lastResponse = null;

      var xhr = jQuery.ajax(
        {
          async: false !== callback,
          cache: false,
          complete: function(xhr, status) {
            return this.callback(xhr, status, callback);
          },
          context: this,
          data: data,
          timeout: 30000,
          type: ('get' == type ? 'GET' : 'POST'),
          url: URLHandler.buildURL(
            {
              target: 'rest',
              action: type,
              name:   name,
              id:     id
            }
          )
        }
      );

      if (false === callback) {
        xhr = (this.lastResponse && this.lastResponse.status == 'success') ? this.lastResponse.data : null;
      }

      return xhr;
    },

    get: function(name, id, callback) {
      return this.request('get', name, id, null, callback);
    },

    post: function(name, id, data, callback) {
      return this.request('post', name, id, data, callback);
    },

    put: function(name, data, callback) {
      return this.request('put', name, null, data, callback);
    },

    'delete': function(name, id, callback) {
      return this.request('delete', name, id, null, callback);
    },

    callback: function(xhr, status, callback)
    {
      try {
        var data = jQuery.parseJSON(xhr.responseText);

      } catch(e) {
        var data = null;
      }

      if (false === callback) {
        core.rest.lastResponse = data;

      } else if (callback) {
        callback(xhr, status, data);
      }
    }
  },


  clearHash: function (paramName) {
    var scrollV, scrollH, loc = window.location;
    var newHash = '';

    if (paramName !== undefined) {
      var pattern = '&?(' + paramName + '(\\[\\S*\\])*=[^&]*&?)';
      var re = new RegExp(pattern, "gi");

      newHash = loc.hash.replace(re, '').slice(1);
    };

    // Prevent scrolling by storing the page's current scroll offset
    scrollV = document.body.scrollTop;
    scrollH = document.body.scrollLeft;

    loc.hash = newHash;

    // Restore the scroll offset, should be flicker free
    document.body.scrollTop = scrollV;
    document.body.scrollLeft = scrollH;
  },

  autoload: function(className, element)
  {
    if ('function' == typeof(className)) {
      var m = className.toString().match(/function ([^\(]+)/);
      window[m[1]] = className;
      className = m[1];
    }

    var classObject = window[className];

    jQuery(document).ready(
      function() {
        if ('undefined' != typeof(classObject)) {
          if ('undefined' != typeof(element)) {
            jQuery(element).each(function(index, el) {
             jQuery(el).data('controller', new (classObject)(jQuery(el)));
           });
          } else if ('function' == typeof(classObject.autoload)) {
            classObject.autoload();

          } else {
            new (classObject)();
          }
        }
      }
    );
  },

  // Return value of variable that is given in class attribute: e.g. class="superclass productid-100001 test"
  getValueFromClass: function(obj, prefix)
  {
    var m = jQuery(obj)
      .attr('class')
      .match(new RegExp(prefix + '-([^ ]+)( |$)'));

    return m ? m[1] : null;
  },

  // Return value of variable that is given in comment block: e.g. <!-- 'productid': '100001', 'var': 'value', -->"
  getCommentedData: function(obj, name)
  {
    var children = jQuery(obj).get(0).childNodes;
    var m = false;

    for (var i = 0; i < children.length && !m; i++) {
      if ('SCRIPT' == children[i].nodeName && 'text/x-cart-data' == children[i].type) {
        m = children[i].innerHTML;
        m = m.replace(/^\n\r/, '').replace(/\r\n$/, '');
        try {
          m = eval('(' + m + ')');
        } catch(e) {
          m = false;
        }
      }
    }

    if (m && name) {
      m = 'undefined' == typeof(m[name]) ? null : m[name];
    }

    return m ? m : null;
  },

  // Toggle link text and toggle obj visibility
  toggleText : function (link, text, obj)
  {
    if (undefined === link.prevValue) {
      link.prevValue = jQuery(link).html();
    }
    jQuery(link).html(jQuery(link).html() === text ? link.prevValue : text);
    jQuery(obj).toggle();
  },

  setRelArray: function ($obj, relArray)
  {
    $obj.attr('data-rel', _.map(_.pairs(relArray), function (value) {return value.join(": ");}).join(", "));
  },

  getRelArray: function ($obj)
  {
    var rel = $obj.attr('data-rel');

    return _.object(_.map(rel.split(','), function (value, key, list) {
      return _.map(_.trim(value).split(':'), function (value) {return _.trim(value);});
    }));
  },

  // Decorate class after page loading
  decorate: function(className, methodName, func)
  {
    core.bind(
      'load',
      function() {
        decorate(className, methodName, func);
      }
    );
  },

  // Decorate some class after page loading
  decorates: function(list, func)
  {
    core.bind(
      'load',
      function() {
        for (var i = 0; i < list.length; i++) {
          decorate(list[i][0], list[i][1], func);
        }
      }
    );
  },

  stringToNumber: function(number, dDelim, tDelim)
  {
    if ("" !== tDelim) {
      number = number.replace(new RegExp("\\" + tDelim, 'g'), '');
    }

    var a = number.split(dDelim);

    return parseFloat(a[0] + '.' + a[1]);
  },

  numberToString: function(number, dDelim, tDelim, precision)
  {
    dDelim = dDelim || '.';
    tDelim = tDelim || '';
    precision = precision || 0;

    precision = Math.max(parseInt(precision), 0);

    number = this.round(number, precision).toString();
    /*
      Author: Robert Hashemian
      http://www.hashemian.com/

      You can use this code in any manner so long as the author's name,
      Web address and this disclaimer is kept intact.
     ********************************************************/

    var a = number.split('.');
    var x = a[0]; // decimal
    var y = a[1]; // fraction
    var z = '';

    if (typeof(x) != 'undefined') {
      // reverse the digits. regexp works from left to right.
      for (var i = x.length - 1; i >= 0; i--) {
        z += x.charAt(i);
      }

      // add separators. but undo the trailing one, if there
      z = z.replace(/(\d{3})/g, "$1" + tDelim);

      if (z.slice(-tDelim.length) == tDelim){
        z = z.slice(0, -tDelim.length);
      }

      x = '';

      // reverse again to get back the number
      for ( i = z.length - 1; i >= 0; i--) {
        x += z.charAt(i);
      }

      // add the fraction back in, if it was there
      if (typeof(y) != 'undefined' && y.length > 0) {
        x += dDelim + y + this.repeat('0', precision - y.length);

      } else if (precision > 0) {
        x += dDelim + this.repeat('0', precision);
      }
    }

    return x;
  },

  getExtension: function(path)
  {
    return path.slice((path.lastIndexOf(".") - 1 >>> 0) + 2);
  },

  repeat: function(s, n)
  {
    var a = [];
    while (a.length < n){
        a.push(s);
    }

    return a.join('');
  },

  round: function(number, precision)
  {
    precision = precision || 0;
    precision = Math.max(parseInt(precision), 0);

    var pow = Math.pow(10, precision);

    return Math.round(number * pow) / pow;
  },

  loadWidgetsCollection: function (base, collectionName, params, restoreCallback)
  {
    this.get(
      URLHandler.buildURL(array_merge({'target': 'widgets_collection', 'action': 'get', 'widget': collectionName}, params)),
      function (XMLHttpRequest, textStatus, data) {
        try {
          data = JSON.parse(data);
          for (var key in data) {
            jQuery('.' + data[key].view, jQuery(base)).replaceWith(data[key].content);
//            core.unshadowWidget(base, '.' + data[key].view);
          }
          restoreCallback();

        } catch (error) {
          console.log(error.message);
        }
      }
    );
  },

  shadowCollection: function (base, collection) {
    for (key in collection) {
      jQuery(collection[key], jQuery(base)).after('<div class="single-progress-mark"></div>');
    }
  },

  unshadowWidget: function (base, widget) {
    jQuery('.single-progress-mark', jQuery(widget, jQuery(base)).parent()).remove();
  },

  getURLParam: function (name) {
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec(window.location.href);
    if( results == null )
        return "";
    else
        return results[1];
  },

  getTarget: function () {
    return xliteConfig.target;
  },

  getFormIdString: function () {
    return xliteConfig.form_id_name + '=' + xliteConfig.form_id;
  }

};

// HTTP requester detection
try {

  var xhr = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
  core.isRequesterEnabled = xhr ? true : false;

} catch(e) { }

// Common onready event handler
jQuery(document).ready(
  function() {
    core.isReady = true;
    core.trigger('load');
    for (var i = 0; i < core.savedEvents.length; i++) {
        core.trigger(core.savedEvents[i].name, core.savedEvents[i].params);
    }
    core.savedEvents = [];
  }
);

/**
 * Common functions
 */

// Check - specified object is HTML element or not
function isElement(obj, type)
{
  return obj && typeof(obj.tagName) != 'undefined' && obj.tagName.toLowerCase() == type;
}

core.bind(
  'load',
  function () {
    jQuery('input[type=checkbox]').each(
      function () {
        var checkbox = this;

        jQuery(checkbox).bind(
          'click',
          function () {
            return !jQuery(checkbox).prop('readonly');
          }
        );
      }
    );

    jQuery('.model-form-buttons .button:first-child button').addClass('btn-warning');
  }
);

/* Microhandlers */

core.microhandlers = {}

core.microhandlers.list = {};

core.microhandlers.initialRunned = false;
core.microhandlers.supressRunOnAdd = false;

core.microhandlers.supressRun = function() {
  var previous = this.supressRunOnAdd;
  this.supressRunOnAdd = true;

  return _.bind(
    function() {
      this.supressRunOnAdd = previous;
    },
    this
  );
};

core.microhandlers.add = function(name, pattern, handler)
{
  this.list[name] = {
    'pattern': pattern,
    'handler': handler
  };

  if (this.initialRunned && !this.supressRunOnAdd) {
    this.run(name);
  }
};

core.microhandlers.run = function(name, base)
{
  base = base || document;

  if ('undefined' != typeof(this.list[name]) && this.list[name]) {
    if (_.isString(this.list[name].pattern)) {
      jQuery(this.list[name].pattern, base)
        .filter(_.bind(this.filterByMark(name), this))
        .each(this.list[name].handler)
        .each(_.bind(this.assignMark(name), this));

    } else if (_.isFunction(this.list[name].pattern)) {
      this.list[name].pattern()
        .filter(_.bind(this.filterByMark(name), this))
        .each(this.list[name].handler)
        .each(_.bind(this.assignMark(name), this));
    }
  }
};

core.microhandlers.runInitial = function()
{
  this.initialRunned = true;
  this.runAll(document);
};

core.microhandlers.runAll = function(base)
{
  _.each(
    _.keys(this.list),
    function(name) {
      this.run(name, base);
    },
    this
  );
};

core.microhandlers.filterByMark = function(name)
{
  return function(idx, elm) {
    return !jQuery(elm).data('microhandler-' + name);
  };
};

core.microhandlers.assignMark = function(name)
{
  return function(idx, elm) {
    jQuery(elm).data('microhandler-' + name, true);
  };
};

core.microhandlers.add(
  'BootstrapTooltip',
  '[data-toggle="tooltip"]',
  function(event) {
    jQuery(this).tooltip();
  }
);

core.bind(
  'load',
  function() {
    core.microhandlers.runInitial();
  }
);

core.bind(
  'loader.loaded',
  function(event, widget) {
    core.microhandlers.runAll(widget.base);
  }
);
