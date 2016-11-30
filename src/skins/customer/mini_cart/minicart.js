/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Minicart controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

function MinicartController(base)
{
  this.callSupermethod('constructor', arguments);

  if (this.base && this.base.length) {
    this.block = new MinicartView(this.base);
    core.bind('updateCart', _.bind(this.handleUpdateCart, this));
  }
}

extend(MinicartController, AController);

// Controller name
MinicartController.prototype.name = 'MinicartController';

// Find pattern
MinicartController.prototype.findPattern = '.lc-minicart';

// Controller associated widget
MinicartController.prototype.block = null;

// Initialize controller
MinicartController.prototype.initialize = function()
{
  var o = this;

  this.base.bind(
    'reload',
    function(event, box) {
      o.bind(box);
    }
  );
}

MinicartController.prototype.handleUpdateCart = function(event, data)
{
  if (data.items || data.total) {
    if ('undefined' != typeof(data.itemsCount)) {
      this.updateItemsCount(data.itemsCount);
    }

    this.block.load();
  }
}

MinicartController.prototype.updateItemsCount = function(count)
{
  this.base.find('.minicart-items-number').html(count);

  if (0 < count) {
    this.base.removeClass('empty');
    this.base.find('.items-list')
      .removeClass('empty-cart')
      .addClass('full-cart');

  } else {
    this.base.addClass('empty');
    this.base.find('.items-list')
      .removeClass('full-cart')
      .addClass('empty-cart');
    this.block.toggleViewMode(false);
  }

}

/**
 * Widget
 */

function MinicartView(base)
{
  this.callSupermethod('constructor', arguments);

  this.bind('local.preload', _.bind(this.handlePreload, this));
  this.bind('local.loadingError', _.bind(this.handleLoaded, this));
}

extend(MinicartView, ALoadable);

// No shade widget
MinicartView.prototype.shadeWidget = false;

// Widget target
MinicartView.prototype.widgetTarget = 'cart';

// Widget class name
MinicartView.prototype.widgetClass = '\\XLite\\View\\Minicart';

// Expanded mode flag
MinicartView.prototype.isExpanded = false;

// Body handler is binded or not
MinicartView.prototype.bodyHandlerBinded = false;

// Postprocess widget
MinicartView.prototype.postprocess = function(isSuccess)
{
  this.callSupermethod('postprocess', arguments);

  if (isSuccess) {

    // Get display mode
    var re = /lc-minicart-([^ ]+)/;
    if (!this.widgetParams && this.base.attr('class') && -1 != this.base.attr('class').search(re)) {
      if (!this.widgetParams) {
        this.widgetParams = {};
      }

      var m = this.base.attr('class').match(re);
      this.widgetParams.displayMode = m[1];
    }

    // Initialize view mode toggle mechanism
    this.base.click(_.bind(
      function(event) {
        this.toggleViewMode();
      },
      this
    ));

    if (!this.bodyHandlerBinded) {
      jQuery('body').click(_.bind(
        function (event) {
          if (event.target !== this.base.get(0)) {
            this.toggleViewMode(false);
          }
        },
        this
      ));

      this.bodyHandlerBinded = true;
    }

    jQuery('.items-list', this.base).click(
      function(event) {
        event.stopPropagation();
      }
    );

    if (this.isExpanded) {
      this.base.addClass('expanded').removeClass('collapsed');
    }

    jQuery('a.item-attribute-values', this.base).map(function() {
      attachTooltip(jQuery('span', this), jQuery(jQuery(this).attr('data-rel')).html(), 'bottom');
    });
  }
};

MinicartView.prototype.handlePreload = function(event, state)
{
  var box = this.base.find('.items-list');

  if (box.length) {
    var overlay = jQuery(document.createElement('div'));
    overlay
      .addClass('wait-overlay')
      .append('<div></div>');
    box.append(overlay);
  }
};

MinicartView.prototype.handleLoaded = function(event, state)
{
  this.base.find('.wait-overlay').remove();
};

// Toggle view mode
MinicartView.prototype.toggleViewMode = function(expand)
{
  var old = this.isExpanded;

  if (expand !== true && expand !== false) {
      expand = !this.base.hasClass('expanded');
  }

  if (expand && this.base.hasClass('empty')) {
    expand = false;
  }

  this.isExpanded = expand;

  if (expand) {
    this.base.addClass('expanded').removeClass('collapsed');
    if (old != this.isExpanded) {
      this.triggerVent('opened', this);
    }

  } else {
    this.base.removeClass('expanded').addClass('collapsed');
    if (old != this.isExpanded) {
      this.triggerVent('closed', this);
    }
  }
};

// Get event namespace (prefix)
MinicartView.prototype.getEventNamespace = function()
{
  return 'minicart';
};

core.autoload(MinicartController);
