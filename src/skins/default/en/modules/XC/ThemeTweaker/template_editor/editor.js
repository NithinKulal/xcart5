/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Templates debugger
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var TreeView = function (container) {

  if (!container) {
    return false;
  }

  this.container = jQuery(container).eq(0);
  if (!this.container.length) {
    return false;
  }

  this.preventEdit = false;

  this.container.jstree();
  this.container.get(0).TreeViewController = this;
};

TreeView.prototype.callMethod = function (methodName, args) {
  if (this.container.length) {
    this.container.jstree.apply(this.container, arguments);
  }
};

TreeView.prototype.selectTemplate = function (id, preventEdit) {
  this.callMethod('deselect_all');
  this.callMethod('close_all');
  this.preventEdit = !!preventEdit;
  this.callMethod('select_node', 'template_' + id, true);
  this.preventEdit = false;
};

var TemplateNavigator = function () {
  this.enabled = jQuery.cookie('TemplateNavigator') ? ('1' === jQuery.cookie('TemplateNavigator')) : true;

  this.templates = [];

  this.current = null;

  var elements = jQuery('*').filter(
    function() {
      return this.nodeType == 1/* && this.innerHTML.search(/<[a-z]/) == -1*/;
    }
  );

  var self = this;

  elements.filter(
    function() {
      return !this.innerHTML || this.innerHTML.search(/<[a-z]/) == -1;
    }
  ).mousemove(
    function (event) {
      if (self.enabled) {
        self.markTemplate(this, event);
      }
    }
  );

  // jQuery('#page-wrapper').mousemove(this.checkTemplateRegion.bind(this));
  jQuery(document.body).live('mousemove',
    function (event) {
      if (!jQuery('#themeTweaker_tree').find(event.target).length) {
        self.checkTemplateRegion(event);
      }
    }
  );

  this.mapElements(elements);
};

TemplateNavigator.prototype.toggleEnabled = function () {
  this.enabled = !this.enabled;
  jQuery.cookie('TemplateNavigator', this.enabled ? '1' : '0');
};

TemplateNavigator.prototype.mapElements = function (elements) {
  if (
    typeof elements == 'undefined'
    || !elements.length
  ) {
    return false;
  }

  var self = this;
  core.shadeWidgetsCollection('body');
  setTimeout(function () {
    elements.each(
      function () {
        self.mapElement(this);
      }
    );
    core.unshadeWidgetsCollection('body');
  }, 2000);
};

TemplateNavigator.prototype.mapElement = function (element) {
  element = jQuery(element).get(0);

  if (typeof element == 'undefined') {
    return false;
  }

  element.branch = this.getTemplatesBranch(element);
};

TemplateNavigator.prototype.getTemplatesBranch = function (element) {
  var branch = [];
  var leaf = null;

  while (element && element.parentNode) {
    leaf = this.getTemplate(element);

    if (leaf) {
      branch = branch.concat(leaf);
    }

    element = element.parentNode;
  }

  return branch;
};

TemplateNavigator.prototype.getTemplate = function (element) {
  var first = this.getFirst(element);
  var last = this.getLast(element, first);

  if (!first || !last) {
    return false;
  }

  var data = first.data.match(/\s+(\S+)\s:\s(\S+)?\s\((\d+)\)\s(?:\['(\S+)')?/);

  if (!data) {
    return false;
  }

  var result = {
    begin: jQuery(this.getNextVisibleElement(first)),
    end:   jQuery(this.getPreviousVisibleElement(last)),
    class: data[1],
    tpl:   data[2] || 'n/a',
    id:    data[3],
    list:  false
  };

  if (data[2]) {
    this.addTemplateElement(data[3], result);
  }

  var list = null;
  if (data[4]) {
    list = this.getListTemplate(first, data[4]);
  }

  if (list) {
    result = [result];
    result.push(list);
  }

  return result;
};

TemplateNavigator.prototype.getFirst = function (element) {
  var first = null;

  while (element.previousSibling) {
    element = element.previousSibling;
    if (element.nodeType == 8 && element.data.search(' {{{ ') != -1) {
      first = element;

      break;
    }
  }

  return first;
};

TemplateNavigator.prototype.getLast = function (element, first) {
  var last = null;

  if (!first) {
    return null;
  }

  var match = first.data.match(/ \((\d+)\)/);
  var lastPattern = new RegExp(' \}\}\} .+\(' +  match[1] + '\)');

  while (element.nextSibling) {
    element = element.nextSibling;
    if (element.nodeType == 8 && element.data.search(lastPattern) != -1) {
      last = element;

      break;
    }
  }

  return last;
};

TemplateNavigator.prototype.getNextVisibleElement = function (element) {
  while (element.nextSibling) {
    element = element.nextSibling;
    if (this.isVisible(element)) {
      break;
    }
  }

  return element;
};

TemplateNavigator.prototype.getPreviousVisibleElement = function (element) {
  var result = null;
  while (element.previousSibling) {
    element = element.previousSibling;
    if (this.isVisible(element)) {
      result = element;
      break;
    }
  }

  return result;
};

TemplateNavigator.prototype.isVisible = function (element) {
  return element.nodeType == 1
    && element.tagName.toUpperCase() != 'SCRIPT'
    && jQuery(element).is(':visible');
};

TemplateNavigator.prototype.addTemplateElement = function (id, element) {
  if (!this.templates[id]) {
    this.templates[id] = element.begin;
  }
};

TemplateNavigator.prototype.getListTemplate = function (element, name) {
  var first = this.getListFirst(element, name);
  var last = this.getListLast(element, name);

  if (!first || !last) {
    return false;
  }

  return {
    begin: jQuery(this.getNextVisibleElement(first)),
    end:   jQuery(this.getPreviousVisibleElement(last)),
    list:  name
  }
};

TemplateNavigator.prototype.getListFirst = function (element, name) {
  var result = null;
  var pattern = new RegExp('\'' + name + '\' list child. +\{\{\{');
  var siblings = element.parentNode.childNodes;

  for (var i = 0; i < siblings.length; i++) {
    var _element = siblings[i];
    if (_element.nodeType == 8 && _element.data.search(pattern) != -1) {
      result = _element;

      break;
    }
  }

  return result;
};

TemplateNavigator.prototype.getListLast = function (element, name) {
  var result = null;
  var pattern = new RegExp('\}\}\} .+\'' + name + '\' list child');
  var siblings = element.parentNode.childNodes;

  for (var i = siblings.length - 1; i >= 0; i--) {
    var _element = siblings[i];
    if (_element.nodeType == 8 && _element.data.search(pattern) != -1) {
      result = _element;

      break;
    }
  }

  return result;
};

TemplateNavigator.prototype.markTemplate = function (element, event) {
  element = jQuery(element).get(0);

  if (
    typeof element == 'undefined'
    || typeof element.branch == 'undefined'
    || !element.branch.length
    || (this.current && this.current.isSameNode(element))
  ) {
    return false;
  }

  jQuery('.tpl-debug-canvas').remove();
  var templates = element.branch;

  for (var i = 0; i < templates.length; i++) {
    var t = templates[i];
    var c = document.body.appendChild(document.createElement('div'));
    c.className = i == 0
      ? 'tpl-debug-canvas tpl-debug-current'
      : ('tpl-debug-canvas tpl-debug-' + (t.list ? 'list' : 'tpl') + '-canvas');

    c = jQuery(c);

    c.bind('click',
      function () {
        var treeView = jQuery('#themeTweaker_tree').get(0).TreeViewController;
        treeView.selectTemplate(templates[0].id, true);

        var top = jQuery('.jstree-anchor.jstree-clicked').get(0).offsetTop;
        var left = jQuery('.jstree-anchor.jstree-clicked').get(0).offsetLeft;

        jQuery('#themeTweaker_tree').get(0).scrollTop = top - 30;
        jQuery('#themeTweaker_tree').get(0).scrollLeft = left - 30;
      }
    );

    c.bind('dblclick',
      function () {
        var treeView = jQuery('#themeTweaker_tree').get(0).TreeViewController;
        treeView.selectTemplate(templates[0].id, false);
      }
    );

    var beginPos = t.begin.offset();
    var endPos = t.end.offset();

    var width = t.end.outerWidth();
    var height = t.end.outerHeight();

    c.css(
      {
        top:    beginPos.top + 'px',
        left:   beginPos.left + 'px',
        width:  (endPos.left - beginPos.left + width) + 'px',
        height: (endPos.top - beginPos.top + height) + 'px'
      }
    );

    if (0 == i) {
      this.region = {
        top:    beginPos.top,
        left:   beginPos.left,
        right:  (endPos.left + width),
        bottom: (endPos.top + height)
      };
    }
  }

  this.current = element;

  if (event) {
    this.checkTemplateRegion(event);
  }
};

TemplateNavigator.prototype.unMarkTemplate = function () {
  jQuery('.tpl-debug-canvas').remove();
  this.current = null;
  this.region = null;
};

TemplateNavigator.prototype.markTemplateById = function (id) {
  if (this.templates[id]) {
    this.markTemplate(this.templates[id]);
    var selection = jQuery('.tpl-debug-current');

    if (selection.length) {
      jQuery(document).scrollTop(selection.offset().top);
    }
  } else {
    this.unMarkTemplate();
  }
};

TemplateNavigator.prototype.checkTemplateRegion = function (event) {
  if (
    this.region
  ) {

    var r = this.region;

    if (r.top < event.pageY && r.bottom > event.pageY && r.left < event.pageX && r.right > event.pageX) {

    } else {
      this.unMarkTemplate();
    }
  }
};

jQuery(document).ready(
  function() {
    var treeView = new TreeView('#themeTweaker_tree');
    var templateNavigator = new TemplateNavigator();

    jQuery('#themeTweaker_wrapper').resizable(
      {
        resize: function (event, ui) {
          jQuery('body').css('margin-left', ui.size.width);
        }
      }
    ).show();

    var tree = jQuery('#themeTweaker_tree');

    tree.on('select_node.jstree', function (event, data) {
      if (!treeView.preventEdit) {
        URLHandler.baseURLPart = 'admin.php';
        var url = URLHandler.buildURL({
          target: 'theme_tweaker_template',
          template: data.node.data.templatePath
        });
        URLHandler.baseURLPart = 'cart.php';

        var wnd = window.open(url, 'TTEditor', 'width=1050px,height=550px,menubar=no,toolbar=no,location=no,directories=no,status=no');
        wnd.focus();
      }
    });

    tree.on('hover_node.jstree', function (event, data) {
      templateNavigator.markTemplateById(data.node.data.templateId);
    });

    tree.on('dehover_node.jstree', function (event, data) {
      templateNavigator.unMarkTemplate();
    });

    var controlPanel = jQuery('#themeTweaker_wrapper .themeTweaker-control-panel');
    switcher = jQuery('#themeTweaker-switcher', controlPanel).change(function (event) {
      templateNavigator.toggleEnabled();
    });
    switcher.prop('checked', templateNavigator.enabled);

    tree.prepend(controlPanel);
  }
);
