/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * RSS feed controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function LazyLoadWidget(base)
{
  this.callSupermethod('constructor', arguments);

  this.widgetClass = jQuery(base).data('lazyClass');

  jQuery(_.bind(this.load, this, {}));

  this.lazyEvent = jQuery(base).data('lazyEvent');

  if (this.lazyEvent) {
    var events = this.lazyEvent.split(',');
    for (i = 0; i < events.length; i++) {
      var e = events[i].trim();
      core.bind(e, _.bind(this.handleLazyEvent, this));
    }
  }
}

extend(LazyLoadWidget, ALoadable);

LazyLoadWidget.prototype.reloadIfError = false;

LazyLoadWidget.prototype.shadeWidget = false;

// Use this flag to prevent extra widget reloads
LazyLoadWidget.prototype.isScheduledReload = false;

LazyLoadWidget.autoload = function() {
  jQuery('.lazy-load.active').each(function () {
    new LazyLoadWidget(this);
  });
};

LazyLoadWidget.prototype.handleLazyEvent = function(event, data) {
  // Schedule widget reload
  var obj = this;
  obj.isScheduledReload = true;
  setTimeout(
    function() {
      if (obj.isScheduledReload) {
        // Reload only if there are scheduled events
        obj.load();
        obj.isScheduledReload = false;
      }
    },
    1000
  );
}

core.autoload('LazyLoadWidget');
