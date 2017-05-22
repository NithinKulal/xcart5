/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Top message controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var MESSAGE_INFO    = 'info';
var MESSAGE_WARNING = 'warning';
var MESSAGE_ERROR   = 'error';

/**
 * Controller
 */

// Constructor
function TopMessages(container) {
  if (!container) {
    return false;
  }

  this.container = jQuery(container).eq(0);
  if (!this.container.length) {
    return false;
  }

  // Add listeners

  // Close button
  jQuery('a.close', this.container).click(
    _.bind(
      function(event) {
        event.stopPropagation();
        this.clearRecords();

        return false;
      },
      this
    )
  ).hover(
    function() {
      jQuery(this).addClass('close-hover');
    },
    function() {
      jQuery(this).removeClass('close-hover');
    }
  );

  // Global event
  if ('undefined' != typeof(window.core)) {
    core.bind(
      'message',
      _.bind(
        function(event, data) {
          return this.messageHandler(data.message, data.type);
        },
        this
      )
    );
    core.bind(
      'clearMessages',
      _.bind(
        function(event) {
          this.clearRecords();
        },
        this
      )
    );
  }

  // Remove dump items (W3C compatibility)
  jQuery('li.dump', this.container).remove();

  // Fix position: fixed
  this.msie6 = jQuery.browser.msie && parseInt(jQuery.browser.version) < 7;
  if (this.msie6) {
    this.container.css('position', 'absolute');
    this.container.css('border-style', 'solid');
    jQuery('ul', this.container).css('border-style', 'solid');
  }

  // Initial show
  if (!this.isVisible() && jQuery('li', this.container).length) {
    setTimeout(
      _.bind(
        function() {
          this.show();

          // Set initial timers
          jQuery('li.info', this.container).each(
            _.bind(
              function(idx, elm) {
                this.setTimer(elm);
              },
              this
            )
          );
        },
        this
      ),
      1000
    );

  } else {

    // Set initial timers
    jQuery('li.info', this.container).each(
      _.bind(
        function(idx, elm) {
          this.setTimer(elm);
        },
        this
      )
    );
  }
}

/**
 * Properties
 */
TopMessages.prototype.container = null;
TopMessages.prototype.to = null;

TopMessages.prototype.ttl = 10000;

/**
 * Methods
 */

// Check visibility
TopMessages.prototype.isVisible = function () {
  return this.container.css('display') != 'none';
};

// Show widget
TopMessages.prototype.show = function () {
  this.container.slideDown();
};

// Hide widget
TopMessages.prototype.hide = function (callback) {
  this.container.slideUp(callback);
};

TopMessages.prototype.getMessageFromText = function (element) {
  return element.find('.message').length > 0
      ? element.find('.message')
      : element;
};

TopMessages.prototype.getSameRecord = function (ul, text) {
  var self = this;

  return ul.find('li').filter(function () {
    var htmlContent = jQuery('<div />').html(text);
    if (htmlContent.length > 0) {
      text = htmlContent.find('.message').length > 0
          ? htmlContent.find('.message').text()
          : text;
    }

    if (text) {
      text = text.replace(/^[\s\n]+|[\s\n]+$/gm,'');
    }

    var realElement = self.getMessageFromText(jQuery(this));
    var reg = new RegExp(text + " \\\((\\\d*?)\\\)", "i");
    var realText = realElement.text().replace(/^[\s\n]+|[\s\n]+$/gm,'');

    return realText === text || realText.match(reg);
  }).get(0);
};

TopMessages.prototype.updateRecord = function (li) {
  var recordLi = jQuery(li).find('.message').length > 0
      ? jQuery(li).find('.message')
      : jQuery(li);
  var array = /(.*) \((\d*?)\)/i.exec(recordLi.text());
  var oldText = array && array[1]
    ? array[1]
    : recordLi.text();
  var oldIndex = array && array[2]
    ? array[2]
    : 0;

  oldText = oldText.replace(/^[\s\n]+|[\s\n]+$/gm,'');
  recordLi.text(oldText + ' (' + (intval(oldIndex)+1) + ')');
};

// Add record
TopMessages.prototype.addRecord = function (text, type) {
  if (!type
    || (MESSAGE_INFO != type && MESSAGE_WARNING != type && MESSAGE_ERROR != type)
  ) {
    type = MESSAGE_INFO;
  }

  var ul = jQuery('ul', this.container).length
    ? jQuery('ul', this.container)
    : jQuery(document.createElement('UL')).appendTo(this.container);

  var sameLi = this.getSameRecord(ul, text);

  if (sameLi) {
    this.updateRecord(sameLi);
    li = sameLi;
  } else {
    var li = document.createElement('LI');
    li.innerHTML = text;
    li.className = type;
    li.style.display = 'none';

    ul.append(li);
  }

  if (
    ul.find('li').length
    && !this.isVisible()
  ) {
    this.show();
  }

  jQuery(li).slideDown('fast');

  if (type == MESSAGE_INFO) {
    this.setTimer(li);
  }
};

// Clear record
TopMessages.prototype.hideRecord = function(li)
{
  if (jQuery('li:not(.remove)', this.container).length == 1) {
    this.clearRecords();

  } else {
    jQuery(li).addClass('remove').slideUp(
      'fast',
      function() {
        jQuery(this).remove();
      }
    );
  }
};

// Clear all records
TopMessages.prototype.clearRecords = function () {
  var container = this.container;
  this.hide(function () {
    jQuery('li', container).remove();
  });
};

// Set record timer
TopMessages.prototype.setTimer = function (li) {
  li = jQuery(li).get(0);

  if (li.timer) {
    clearTimeout(li.timer);
    li.timer = false;
  }

  var o = this;
  li.timer = setTimeout(
    function () {
      o.hideRecord(li);
    },
    this.ttl
  );
};

// onmessage event handler
TopMessages.prototype.messageHandler = function (text, type) {
  this.addRecord(text, type);
};

jQuery(function () {
  new TopMessages(jQuery('#status-messages'));
});
