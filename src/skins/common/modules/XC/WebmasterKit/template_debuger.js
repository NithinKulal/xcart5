/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Templates debugger
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Display debug canvas and info box
function displayTemplateInfo(event)
{
  if (
    typeof(event.target.templates) != 'undefined'
    && event.target.templates.length
    && window.templateDebugSwitcher.enabled
    && (!window.templateDebugSwitcher.current || !window.templateDebugSwitcher.current.isSameNode(event.target))
  ) {

    jQuery('.tpl-debug-canvas').remove();
    window.templateDebugSwitcher.infoContent.children().remove();

    var s = 36;

    for (var i = 0; i < event.target.templates.length; i++) {
      var t = event.target.templates[i];
      var c = document.body.appendChild(document.createElement('div'));
      c.className = i == 0
        ? 'tpl-debug-canvas tpl-debug-current'
        : ('tpl-debug-canvas tpl-debug-' + (t.list ? 'list' : 'tpl') + '-canvas');

      c = jQuery(c);

      c.click(
        function () {
          var infoContent = window.templateDebugSwitcher.infoContent.get(0);
          infoContent.locked = !infoContent.locked;
          if (!infoContent.locked) {
            window.templateDebugSwitcher.infoBox.get(0).scrollTop = 0;
          }
        }
      );

      var beginPos = t.begin.offset();
      var endPos = t.end.offset();

      c.css(
        {
          top:    beginPos.top + 'px',
          left:   beginPos.left + 'px',
          width:  (endPos.left - beginPos.left + t.end.outerWidth()) + 'px',
          height: (endPos.top - beginPos.top + t.end.outerHeight()) + 'px'
        }
      );

      if (i == 0) {
        window.templateDebugSwitcher.region = {
          top:    beginPos.top,
          left:   beginPos.left,
          right:  (endPos.left + t.end.outerWidth()),
          bottom: (endPos.top + t.end.outerHeight())
        };
      }

      var li = document.createElement('li');
      if (i == event.target.templates.length - 1) {
        li.className = 'last';
      }
      li.innerHTML = t.list
        ? '<strong>List:</strong> ' + t.list
        : t.tpl + '<br /><strong>Template ID:</strong> ' + t.id + '; <strong>Class:</strong> ' + (t['class'] ? t['class'] : 'n/a');
      window.templateDebugSwitcher.infoContent.append(li);
    }

    window.templateDebugSwitcher.current = event.target;

    window.templateDebugSwitcher.infoBox.show();
    checkTemplateRegion(event);
  }
}

// Check tempalte region by onmousemove
function checkTemplateRegion(event)
{
  if (
    window.templateDebugSwitcher.region
    && window.templateDebugSwitcher.enabled
    && !window.templateDebugSwitcher.infoContent.get(0).locked
  ) {

    var r = window.templateDebugSwitcher.region;

    if (r.top < event.pageY && r.bottom > event.pageY && r.left < event.pageX && r.right > event.pageX) {

      window.templateDebugSwitcher.infoBox.css(
        {
          top:  (event.pageY + 5) + 'px',
          left: (event.pageX + 5) + 'px'
        }
      );

    } else {

      jQuery('.tpl-debug-canvas').remove();
      window.templateDebugSwitcher.infoBox.hide();
      window.templateDebugSwitcher.current = null;
      window.templateDebugSwitcher.region = null;

    }
  }
}

// Find next sibling elements - visible and not script
function findNextVisibleElement(elm)
{
  while (elm.nextSibling) {
    elm = elm.nextSibling;
    if (elm.nodeType == 1 && elm.tagName.toUpperCase() != 'SCRIPT' && jQuery(elm).is(':visible')/*elm.style.display != 'none'*/) {
      break;
    }
  }

  return elm;
}

// Find previoues sibling elements - visible and not script
function findPreviousVisibleElement(elm)
{
  while (elm.previousSibling) {
    elm = elm.previousSibling;
    if (elm.nodeType == 1 && elm.tagName.toUpperCase() != 'SCRIPT' && jQuery(elm).is(':visible')/*elm.style.display != 'none'*/) {
      break;
    }
  }

  return elm;
}


jQuery(document).ready(
  function() {

    // {{{ Create switcher panel

    var switcher = document.body.appendChild(document.createElement('div'));
    switcher.className = 'tpl-debug-switcher';
    var box = switcher.appendChild(document.createElement('div'));

    // Add service properties and methods
    switcher.enabled = jQuery.cookie('switcher_state') ? ('1' === jQuery.cookie('switcher_state')) : true;

    // Add link
    switcher.link = box.appendChild(document.createElement('a'))
    switcher.link.className = 'switcher' + (switcher.enabled ? '' : ' disabled');
    switcher.link.appendChild(document.createElement('span')).innerHTML = switcher.enabled ? 'Disable tracing' : 'Enable tracing';

    jQuery(switcher.link).click(
      function(event) {
        event.stopPropagation();
        switcher.switchDebugging();
        return false;
      }
    );

    switcher.switchDebugging = function() {
      this.enabled = !this.enabled;

      jQuery('span', switcher.link).html(this.enabled ? 'Disable tracing' : 'Enable tracing');

      if (!this.enabled) {
        jQuery('.tpl-debug-canvas').remove();
        this.infoContent.children().remove();
        this.current = null;
        this.region = null;
        jQuery(switcher.link).addClass('disabled');
        jQuery.cookie('switcher_state', '0');

      } else {
        jQuery(switcher.link).removeClass('disabled');
        jQuery.cookie('switcher_state', '1');
      }
    };

    switcher.infoBox = document.body.appendChild(document.createElement('div'));
    switcher.infoContent = jQuery(switcher.infoBox.appendChild(document.createElement('ul')));
    switcher.infoContent.get(0).locked = false;
    switcher.infoBox = jQuery(switcher.infoBox);
    switcher.infoBox.addClass('tpl-debug-info').hide();

    switcher.current = null;
    switcher.region = null;

    window.templateDebugSwitcher = switcher;
    jQuery(switcher).draggable();
    // }}}

    // Assign global event listener
    jQuery(document.body).mousemove(checkTemplateRegion);

    // Assign event listeners
    jQuery('*').filter(
      function() {
        return this.nodeType == 1 && this.innerHTML.search(/<[a-z]/) == -1;
      }
    )
      .mouseover(displayTemplateInfo)
      .each(
        function() {
          this.templates = [];

          var p = this;
          // Collect comments-with-debug-info
          while (p && p.parentNode) {
            var elm = p;
            while (elm.previousSibling) {
              elm = elm.previousSibling;
              if (elm.nodeType == 8 && elm.data.search(' {{{ ') != -1) {

                // Found begin comment tag
                var begin = elm;

                // Search end comment tag
                var match = elm.data.match(/ \((\d+)\)/);
                var end = null;
                var endPattern = new RegExp(' \}\}\} .+\(' +  match[1] + '\)');
                var ns = p;
                while (ns.nextSibling && !end) {
                  ns = ns.nextSibling;
                  if (ns.nodeType == 8 && ns.data.search(endPattern) != -1) {
                    end = ns;
                  }
                }

                if (end) {

                  // Found begin and end comment tags; parse begin comment tag
                  var m = begin.data.match(/\s+(\S+)\s:\s(\S+)?\s\((\d+)\)\s(?:\['(\S+)')?/);
                  if (m) {
                    this.templates.push(
                      {
                        'begin': jQuery(findNextVisibleElement(begin)),
                        'end':   jQuery(findPreviousVisibleElement(end)),
                        'class': m[1],
                        'tpl':   m[2] || 'n/a',
                        'id':    m[3],
                        'list':  false
                      }
                    );

                    if (m[4]) {

                      // Comment tag is list child - detect list borders
                      var listFirst = null;
                      var listEnd = null;
                      var beginPattern = new RegExp('\'' + m[4] + '\' list child. +\{\{\{');
                      for (var i = 0; begin.parentNode.childNodes.length && !listFirst; i++) {
                        var n = begin.parentNode.childNodes[i];
                        if (n.nodeType == 8 && n.data.search(beginPattern) != -1) {
                          listFirst = n;
                        }
                      }

                      var endPattern = new RegExp('\}\}\} .+\'' + m[4] + '\' list child');
                      for (var i = begin.parentNode.childNodes.length - 1; i >= 0 && !listEnd; i--) {
                        var n = begin.parentNode.childNodes[i];
                        if (n.nodeType == 8 && n.data.search(endPattern) != -1) {
                          listEnd = n;
                        }
                      }

                      if (listFirst && listEnd) {

                        // List borders found
                        this.templates.push(
                          {
                            begin: jQuery(findNextVisibleElement(listFirst)),
                            end:   jQuery(findPreviousVisibleElement(listEnd)),
                            list:  m[4]
                          }
                        );
                      }
                    }
                  }
                }
              }
            }

            p = p.parentNode;
          }
        }
    );
  }
);
