/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Language controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

function MobileLanguageController(base)
{
  this.callSupermethod('constructor', arguments);
}

extend(MobileLanguageController, AController);

// Controller name
MobileLanguageController.prototype.name = 'MobileLanguageController';

// Find pattern
MobileLanguageController.prototype.findPattern = '.language-selector-mobile';

// Initialize controller
MobileLanguageController.prototype.initialize = function()
{
  if (!this.bodyHandlerBinded) {
    jQuery('body').click(_.bind(
      function (event) {
        this.toggleViewMode(false);
      },
      this
    ));

    this.bodyHandlerBinded = true;
  }

  jQuery(this.base).click(_.bind(
    function(event, box) {
      jQuery('.nav-pills').children('li').removeClass('open');
      event.stopPropagation();

      this.toggleViewMode();

      return false;
    },
    this
  ));

  core.bind(
    'minicart.opened',
    _.bind(
      function(event, widget) {
        this.toggleViewMode(false);
      },
      this
    )
  );

  doPaddingResize();

  jQuery('.items-list', this.base).click(
    function(event) {
      event.stopPropagation();
    }
  );
};

// Toggle view mode
MobileLanguageController.prototype.toggleViewMode = function(expand)
{
  if (expand !== true && expand !== false) {
    expand = !this.base.hasClass('expanded');
  }

  if (expand) {
    this.base.addClass('expanded').removeClass('collapsed');
    this.triggerVent('opened', this);
  } else if(this.base.hasClass('expanded')) {
    this.base.removeClass('expanded').addClass('collapsed');
    this.triggerVent('closed', this);
  }
};

core.autoload(MobileLanguageController);

jQuery(window).resize(
  function () {
    doPaddingResize();
  }
);


// Get event namespace (prefix)
MobileLanguageController.prototype.getEventNamespace = function()
{
  return 'multicurrency.top';
};

decorate(
  'MinicartView',
  'postprocess',
  function (selector)
  {
    arguments.callee.previousMethod.apply(this, arguments);

    core.bind(
      'multicurrency.top.opened',
      _.bind(
        function(event) {
          this.toggleViewMode(false);
        },
        this
      )
    );
  }
);

function doPaddingResize() {
  var minicart = jQuery('.lc-minicart.lc-minicart-horizontal ');
  jQuery('li.dropdown.language-button-mobile').css('padding-right', minicart.outerWidth());
}
