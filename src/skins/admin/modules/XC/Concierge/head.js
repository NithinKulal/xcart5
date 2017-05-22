/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

ConciergeController = function () {
  Base.apply(this);

  core.bind('concierge.push', _.bind(this.handleConciergePush, this));
  core.bind('concierge.track', _.bind(this.handleConciergeTrack, this));

  this.switchDebug(false);
};

extend(ConciergeController, Base);

ConciergeController.prototype.ready = false;

ConciergeController.prototype.runMethod = function (type, args) {
  if (args.length) {
    args[args.length - 1].context.screen = this.screen;
    args[args.length - 1].context.timezone = this.timezone;
  }

  return args
    ? analytics[type].apply(analytics, args)
    : analytics[type]();
};

// todo: rewrite with promise
ConciergeController.prototype.switchDebug = function (enable) {
  if (typeof(analytics.debug) == 'undefined') {
    setTimeout(
      _.bind(
        function () {
          this.switchDebug(enable);
        },
        this
      ),
      1000
    );

    return;
  }

  analytics.debug();
  this.ready = true;
  this.initialize();
  this.triggerVent('ready', {controller: this});
};

ConciergeController.prototype.screen = [];
ConciergeController.prototype.timezone = '';

// todo: decompose
ConciergeController.prototype.initialize = function () {
  this.screen = {
    width: jQuery(window).width(),
    height: jQuery(window).height(),
    density: typeof(window.devicePixelRatio) != 'undefined' ? window.devicePixelRatio : 1,
  };

  if (
    typeof(window.Intl) != 'undefined'
    && typeof(window.Intl.DateTimeFormat) != 'undefined'
    && typeof(window.Intl.DateTimeFormat().resolvedOptions) != 'undefined'
  ) {
    var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (tz) {
      this.timezone = tz;
    }
  }

  // Sent stored messages
  if (concierge.messages) {
    _.each(
      concierge.messages,
      function (message) {
        this.runMethod(message.type, message.arguments);
      },
      this
    );
    concierge.messages = null;
    delete concierge.messages;
  }

  this.addClickHandlers();
};

function contextAsArgument(func) {
  return function () {
    _.partial(func, this).apply(this, arguments);
  }
}

ConciergeController.prototype.addClickHandlers = function () {
  // handlers can be specified as hash-map (key for selector, value for title)
  var handlers = {};

  for (var selector in handlers) if (handlers.hasOwnProperty(selector)) {
    jQuery(selector).live('click', _.partial(_.bind(this.handleClick), handlers[selector]));
  }

  // Mark segment click handler by adding data-segment-click with value as a Link Name
  // <a href="..." data-segment-click>Contact Us</a>
  // <a href="..." data-segment-click="Contact Us Link">Contact Us</a>
  // You can specify internal handler by adding segment-click-handler with value as a selector based on element with
  // data-segment-click attribute
  // <div data-segment-click data-segment-click-handler="a"><a href="...">Contact Us</a></div>
  jQuery('[data-segment-click]').live('click', contextAsArgument(_.bind(this.handleClickEvent, this)));
};

ConciergeController.prototype.handleClickEvent = function (element, event) {
  var $element = jQuery(element);
  var segmentClickHandler = $element.data('segment-click-handler');
  var $handler = segmentClickHandler ? jQuery(segmentClickHandler, element) : $element;

  if ($handler.get(0) === event.target || $handler.find(event.target).length) {
    this.handleClick($element.data('segment-click') || $handler.text());
  }
};

ConciergeController.prototype.handleClick = function (linkName) {
  core.trigger('concierge.track', ['Clicked Link', {'Link Name': linkName}, {context: {}}]);
};

ConciergeController.prototype.handleConciergePush = function (event, data) {
  _.each(
    data.list,
    function (e) {
      this.runMethod(e.type, e.arguments);
    },
    this
  );
};

ConciergeController.prototype.handleConciergeTrack = function (event, data) {
  this.runMethod('track', data);
};

// ConciergeController.prototype.assembleHelpMessage = function(link)
// {
//   return [
//     link.get(0),
//     'Go to Help page',
//     {
//       name: link.text()
//     }
//   ];
// };

ConciergeController.prototype.getEventNamespace = function () {
  return 'conciergeController';
};

// ConciergeController.prototype.isTrackAllowed = function(name)
// {
//   return true;
// };

core.autoload('ConciergeController');