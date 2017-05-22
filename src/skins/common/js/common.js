/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
var URLHandler = {

  excluded: {
    'base': true
  },
  baseURLPart: ('undefined' != typeof(window.xliteConfig) ? xliteConfig.script : 'admin.php'),
  querySeparator: '?',
  argSeparator: '&',
  nameValueSeparator: '=',

  // Return query param
  getParamValue: function(name, params)
  {
    return name
      + this.nameValueSeparator
      + encodeURIComponent(typeof params[name] === 'boolean' ? Number(params[name]) : params[name]);
  },

  // Get param value for the remained params
  getQueryParamValue: function(name, params)
  {
    return URLHandler.getParamValue(name, params);
  },

  // Build HTTP query
  implodeParams: function(params, method)
  {
    result = '';
    isStarted = false;

    for (x in params) {

      if (isStarted) {
        result += this.argSeparator;
      } else {
        isStarted = true;
      }

      result += method(x, params);
    }

    return result;
  },

  // Implode remained params
  implodeQueryParams: function(params)
  {
    return this.implodeParams(params, this.getQueryParamValue);
  },

  // Unset some params
  clearParams: function(params, excluded)
  {
    // clone object
    var result = {};

    for (key in params) {
      if (params[key] !== undefined && params[key] !== null && !(key in excluded)) {
        result[key] = params[key];
      }
    }

    return result;
  },

  preprocessParams: function(params)
  {
    return this.clearParams(params, this.excluded);
  },

  // Get base url
  buildBaseUrl: function(params)
  {
    return params.base || this.baseURLPart;
  },

  // Compose query params
  buildQueryParams: function(params)
  {
    return this.querySeparator + this.implodeQueryParams(this.preprocessParams(params));
  },

  getBuildURLPrefix: function()
  {
    return xliteConfig.ajax_prefix
        ? (xliteConfig.ajax_prefix + '/')
        : '';
  },

  // Compose URL
  buildURL: function(params)
  {
    return this.getBuildURLPrefix() + this.buildBaseUrl(params) + this.buildQueryParams(params);
  }
};

/**
 * Columns selector
 */
jQuery(document).ready(
  function() {
    jQuery('input.column-selector').click(
      function(event) {
        if (!this.columnSelectors) {
          var idx = jQuery(this).parents('th').get(0).cellIndex;
          var table = jQuery(this).parents('table').get(0);
          this.columnSelectors = jQuery('tr', table).find('td:eq('+idx+') :checkbox');
        }

        this.columnSelectors.prop('checked', this.checked ? 'checked' : '');
      }
    );

    jQuery('.promo-block .close').click(
      function (event) {
        var block = jQuery(this).parents('.promo-block').get(0);
        var blockId = jQuery(block).data('promo-id');
        if (0 < blockId.length) {
          blockId = blockId + 'PromoBlock';
          document.cookie = blockId + '=1';
        }
        jQuery(block).hide();
      }
    );
  }
);

// Dialog

// Abstract open dialog
function openDialog(selector, additionalOptions)
{
  additionalOptions = additionalOptions || {};

  var box = jQuery(selector);

  _.each(
    ['h2','h1'],
    function(tag) {
      var elm = box.find(tag);
      if ('undefined' == typeof(additionalOptions.title) || !additionalOptions.title) {
        additionalOptions.title = elm.html();
      }
      elm.remove();
    }
  );

  return popup.open(jQuery(selector), additionalOptions);
}

// Loadable dialog
function loadDialog(url, dialogOptions, callback, link, $this)
{
  openWaitBar();

  var selector = 'tmp-dialog-' + (new Date()).getTime() + '-' + jQuery(link).attr('class').toString().replace(/ /g, '-');

  core.get(
    url,
    function(xhr, status, data) {
      if (data) {
        var div = jQuery(document.body.appendChild(document.createElement('div'))).hide();

        var uuid = _.uniqueId();

        core.bind(['resources.ready', 'resources.empty'], _.bind(
          function(event, args){
            if (args.uuid === uuid) {
              if (1 == div.get(0).childNodes.length) {
                div = jQuery(div.get(0).childNodes[0]);
              }

              // Specific CSS class to manage this specific popup window
              div.addClass(selector);

              // Every popup window (even hidden one) has this one defined CSS class.
              // You should use this selector to manage any popup window entry.
              div.addClass('popup-window-entry');

              openDialog('.' + selector, dialogOptions);

              if (callback) {
                callback.call($this, '.' + selector, link);
              }
            }
          },
          this)
        );

        div.html(jQuery.trim(data));

        core.parseResources(div, uuid);
      }
    }
  );

  return '.' + selector;
}

// Load dialog by link
function loadDialogByLink(link, url, options, callback, $this)
{
  if (!link.linkedDialog || 0 == jQuery(link.linkedDialog).length || jQuery(link).hasClass('always-reload')) {
    link.linkedDialog = loadDialog(url, options, callback, link, $this);

  } else {
    openDialog(link.linkedDialog, options, callback);
  }
}

function openWaitBar()
{
  popup.openAsWait();
}

function closeWaitBar()
{
  popup.close();
}

// Check for the AJAX support
function hasAJAXSupport()
{
  if (typeof(window.ajaxSupport) == 'undefined') {
    window.ajaxSupport = false;
    try {

      var xhr = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
      window.ajaxSupport = xhr ? true : false;

    } catch(e) { }
  }

  return window.ajaxSupport;
}

// Check list of checkboxes
function checkMarks(form, reg, lbl) {
  var is_exist = false;

  if (!form || form.elements.length == 0)
    return true;

  for (var x = 0; x < form.elements.length; x++) {
    if (
      form.elements[x].type == 'checkbox'
      && form.elements[x].name.search(reg) == 0
      && !form.elements[x].disabled
    ) {
      is_exist = true;

      if (form.elements[x].checked)
        return true;
    }
  }

  if (!is_exist)
    return true;

  if (lbl) {
    alert(lbl);

  } else if (lbl_no_items_have_been_selected) {
    alert(lbl_no_items_have_been_selected);

  }

  return false;
}

/*
  Parameters:
  checkboxes       - array of tag names
  checkboxes_form    - form name with these checkboxes
*/
function change_all(flag, formname, arr) {
  if (!formname)
    formname = checkboxes_form;
  if (!arr)
    arr = checkboxes;
  if (!document.forms[formname] || arr.length == 0)
    return false;
  for (var x = 0; x < arr.length; x++) {
    if (arr[x] != '' && document.forms[formname].elements[arr[x]] && !document.forms[formname].elements[arr[x]].disabled) {
         document.forms[formname].elements[arr[x]].checked = flag;
      if (document.forms[formname].elements[arr[x]].onclick)
        document.forms[formname].elements[arr[x]].onclick();
    }
  }
}

function checkAll(flag, form, prefix) {
  if (!form) {
    return;
  }

  if (prefix) {
    var reg = new RegExp('^' + prefix, '');
  }
  for (var i = 0; i < form.elements.length; i++) {
    if (
      form.elements[i].type == "checkbox"
      && (!prefix || form.elements[i].name.search(reg) == 0)
      && !form.elements[i].disabled
    ) {
      form.elements[i].checked = flag;
    }
  }
}

/*
  Opener/Closer HTML block
*/
function visibleBox(id, skipOpenClose)
{
  var elm1 = document.getElementById('open' + id);
  var elm2 = document.getElementById('close' + id);
  var elm3 = document.getElementById('box' + id);

  if(!elm3) {
    return false;
  }

  if (skipOpenClose) {
    elm3.style.display = (elm3.style.display == '') ? 'none' : '';

  } else if (elm1) {
    if (elm1.style.display == '') {
      elm1.style.display = 'none';

      if (elm2) {
        elm2.style.display = '';
      }

      elm3.style.display = 'none';
      jQuery('.DialogBox').css('height', '1%');

    } else {
      elm1.style.display = '';
      if (elm2) {
        elm2.style.display = 'none';
      }

      elm3.style.display = '';
    }
  }

  return true;
}

/**
 * Attach tooltip to some element on hover action
 */
function attachTooltip(elm, content, forcePlacement, ttl)
{
  var placement = 'right';

  elm = jQuery(elm);

  if (
    elm.length
    && (
      (jQuery(window).width() - elm.offset().left) < 200
      || (
        elm.parents('.ui-dialog').length
        && (elm.parents('.ui-dialog').offset().left + elm.parents('.ui-dialog').width() - elm.offset().left) < 200
      )
    )
  ) {
    placement = 'left';
  }
  placement = forcePlacement || placement;

  jQuery(elm).each(
    function () {
      if (isBootstrapUse()) {

        if ('undefined' == typeof(this.tooltipAssigned) || !this.tooltipAssigned) {

          var to;
          var obj = jQuery(this);
          if (undefined === ttl) {
            ttl = 500;
          };

          var options = {
            html:      true,
            title:     content,
            placement: placement,
            trigger:   'manual'
          };

          if (elm.data('container')) {
            options['container'] = elm.data('container');
          } else if (elm.parents('.ui-dialog').length > 0) {
            options['container'] = '.ui-dialog';
          };
          obj.tooltip(options);

          obj.mouseover(
            function() {
              if (to) {
                clearTimeout(to);
                to = null;
              }
              if (!obj.next('.tooltip').length) {
                jQuery(this).tooltip('show');
              }
            }
          );

          obj.mouseout(
            function() {
              to = setTimeout(
                function() {
                  obj.tooltip('hide');
                },
                ttl
              );
            }
          );

          obj.on(
            'shown.bs.tooltip',
            function(event) {
              var next = jQuery(event.currentTarget).next();
              if ('undefined' == typeof(next.get(0).tooltipAssigned) || !next.get(0).tooltipAssigned) {            
                next
                  .mouseover(function() { obj.mouseover(); })
                  .mouseout(function() { obj.mouseout(); });
                next.get(0).tooltipAssigned = true;
              }
            }
          );

          this.tooltipAssigned = true;

        }

      } else {
        jQuery(this).tooltip({
          items:     this,
          'content': content
        });

      }

      jQuery(document).on('click' , '.tooltip-main .tooltip', function (evt) {
        return false;
      });

      jQuery(document).on('click' , '.tooltip-main .tooltip a', function (evt) {
        evt.stopPropagation();
      });
    }
  );
}

/**
 * Overlay registry
 */
var waitOverlayRegistry = {};

function assignWaitOverlay(elem)
{
  pattern = elem.prop('class');
  if (!_.isUndefined(elem.get(0).waitOverlay) && elem.get(0).waitOverlay) {
    unassignWaitOverlay(elem);
  }

  var div = jQuery('<div class="wait-block-overlay"><div class="wait-block"><div></div></div></div>');

  div.css({
    width:          elem.outerWidth() + 'px',
    height:         elem.outerHeight() + 'px'
  });

  // We do not show the overlay if the element has zero width or height (the element is not visible)
  if (0 !== elem.outerWidth() && 0 !== elem.outerHeight()) {
    elem.prepend(div)
  }
  var leftOffset  = elem.offset().left - div.offset().left;
  var topOffset   = elem.offset().top - div.offset().top;
  div.css('margin-left',  leftOffset + 'px');
  div.css('margin-top',   topOffset + 'px');

  waitOverlayRegistry[pattern] = div;
  elem.get(0).waitOverlay = div;

  elem.trigger('assignOverlay', { widget: elem });

  return div;
}

function unassignWaitOverlay(elem, force)
{
  pattern = elem.prop('class');
  var overlay = null;
  if (waitOverlayRegistry[pattern]) {
    overlay = waitOverlayRegistry[pattern];

  } else if (force) {
    overlay = jQuery('.wait-block-overlay').eq(0);
  }

  if (overlay) {
    overlay.remove();
    if (elem.get(0)) {
      elem.trigger('unassignOverlay', { widget: elem });
      elem.get(0).waitOverlay = null;
    }
  }
}

/**
 * Shade overlay
 */
var shadeOverlayRegistry = {};

function assignShadeOverlay(elem)
{
  pattern = elem.prop('class');
  if (!_.isUndefined(elem.get(0).shadeOverlay) && elem.get(0).shadeOverlay) {
    unassignShadeOverlay(elem);
  }

  var div = jQuery('<div class="shade-block-overlay"></div>');

  div.css({
    width:  elem.outerWidth() + 'px',
    height: elem.outerHeight() + 'px'
  });

  // We do not show the overlay if the element has zero width or height (the element is not visible)
  if (0 !== elem.outerWidth() && 0 !== elem.outerHeight()) {
    elem.before(div)
  }

  var leftOffset  = elem.offset().left - div.offset().left;
  var topOffset   = elem.offset().top - div.offset().top;
  div.css('margin-left',  leftOffset + 'px');
  div.css('margin-top',   topOffset + 'px');
  shadeOverlayRegistry[pattern] = div;
  elem.get(0).shadeOverlay = div;

  elem.trigger('assignOverlay', { widget: elem });

  return div;
}

function unassignShadeOverlay(elem, force)
{
  pattern = elem.prop('class');
  var overlay = null;
  if (shadeOverlayRegistry[pattern]) {
    overlay = shadeOverlayRegistry[pattern];

  } else if (force) {
    overlay = jQuery('.shade-block-overlay').eq(0);
  }

  if (overlay) {
    overlay.remove();
    if (elem.get(0)) {
      elem.trigger('unassignOverlay', { widget: elem });
      elem.get(0).waitOverlay = null;
    }
  }
}

function isBootstrapUse()
{
  return 'undefined' != typeof(jQuery.fn.modal)
    && _.isFunction(jQuery.fn.modal);
}

/**
 * State widget specific objects and methods (used in select_country.js )
 * @TODO : Move it to the one object after dynamic loading widgets JS implementation
 */
var statesList = [];
var stateSelectors = [];

function UpdateStatesList(base)
{
  var _stateSelectors;

  base = base || document;

  jQuery('.country-selector', base).each(function (index, elem) {
    statesList = array_merge(statesList, core.getCommentedData(elem, 'statesList'));
    _stateSelectors = core.getCommentedData(elem, 'stateSelectors');

    stateSelectors[_stateSelectors.fieldId] = new StateSelector(
      _stateSelectors.fieldId,
      _stateSelectors.stateSelectorId,
      _stateSelectors.stateInputId
    );
  });
}

function setPriceElement(element, value, e)
{
  e = e || 2;

  var str = core.numberToString(value, '.', '', e);
  var parts = str.split('.');

  // Sign
  if (!element.find('.part-sign').length) {
    if (!element.find('.part-prefix').length) {
      element.find('.part-integer').before('<span class="part-sign"></span>');

    } else {
      element.find('.part-prefix').before('<span class="part-sign"></span>');
    }
  }
  if (value >= 0) {
    element.find('.part-sign').html('');

  } else {
    element.find('.part-sign').html('&minus;&#8197;');
  }

  element.find('.part-integer').html(Math.abs(parseInt(parts[0])));
  if (parts[1]) {
    element.find('.part-decimal').html(parts[1]);

  } else {
    element.find('.part-decimal').html('');
  }
}

function CacheEngine ()
{
  this.cache = [];
}

CacheEngine.prototype.add = function (key, value) {
  var updated = false;

  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      updated = true;
      this.cache[i].value = value;

      break;
    }
  }

  if (!updated) {
    this.cache.push({key: key, value: value});
  }
};

CacheEngine.prototype.get = function (key) {
  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      return this.cache[i].value;
    }
  }
};

CacheEngine.prototype.has = function (key) {
  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      return true;
    }
  }
};

CacheEngine.prototype.remove = function (key) {
  var index = null;

  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      index = i;

      break;
    }
  }

  this.cache.splice(index, 1);
};

CacheEngine.prototype.clear = function () {
  this.cache = [];
};

jQuery(document).ready(
  function() {
    var isIE11 = !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);

    if(isIE11){
        jQuery('body').addClass('ie11');
    }

    // Open warning popup
    core.microhandlers.add(
      'OverlayHeightResize',
      '>*:first',
      function(event) {
        jQuery('.ui-widget-overlay').css('height', jQuery(document).height());
        jQuery('.ui-widget-overlay').css('width', jQuery('body').innerWidth());
      }
    );

    core.microhandlers.add(
      'PopupModelButtonWidthFix',
      '.model-form-buttons',
      function (event) {
        jQuery('.ajax-container-loadable .model-form-buttons')
          .each(function (index, elem) {
            jQuery('.button', elem).width(jQuery(elem).width());
          });
      }
    );

    core.microhandlers.add(
      'HideEmptySidebars',
      '.sidebar',
      function (event) {
        var appendClass = _.bind(function() {
          var className = this.attr('id') + '-empty';
          jQuery('body').removeClass(className);

          var visibleBlockCount = jQuery('.list-container *:visible', this).length

          if (visibleBlockCount == 0) {
            jQuery('body').addClass(className);
          } else {
            jQuery('body').removeClass(className);
          };
        }, jQuery(this));

        appendClass();

        jQuery(window).resize(_.debounce(appendClass, 30));
      }
    );

    core.bind('popup.open', function() {
      jQuery('html').addClass('popup-opened');
    });
    core.bind('popup.close', function() {
      jQuery('html').removeClass('popup-opened');
    });
});
