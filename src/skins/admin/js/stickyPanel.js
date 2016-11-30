/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky panel controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function StickyPanel(base)
{
  base = jQuery(base);
  if (0 < base.length && base.hasClass('sticky-panel') && !base.get(0).controller) {
    base.get(0).controller = this;
    this.base = base;

    this.process();
    if (!this.isFormDoNotChangeActivation()) {
      this.unmarkAsChanged();
      this.unmarkMoreActionAsEnabled();
    }
  }
}

extend(StickyPanel, Base);

// Autoloader
StickyPanel.autoload = function()
{
  jQuery('.sticky-panel')
    .not('.another-sticky')
    .not('.model-list')
    .each(
      function() {
        new StickyPanel(this);
      }
    );
};

// Default options
StickyPanel.prototype.defaultOptions = {
  bottomPadding:       0,
  parentContainerLock: true
};

// Panel
StickyPanel.prototype.panel = null;

// Timer resource
StickyPanel.prototype.timer = null;

// Current document
StickyPanel.prototype.doc = null;

// Last scroll top
StickyPanel.prototype.lastScrollTop = null;

// Last height
StickyPanel.prototype.lastHeight = null;

// Panel height
StickyPanel.prototype.panelHeight = null;

// Parent container top range
StickyPanel.prototype.parentContainerTop = null;

StickyPanel.prototype.moreActionAsEnabled = false;

// Process widget (initial catch widget)
StickyPanel.prototype.process = function()
{
  // Initialization
  this.panel = this.base.find('.box').eq(0);

  this.base.height(this.panel.outerHeight() + 3);

  if (!this.isModal()) {
    this.processReposition();
  }

  // Form change activation behavior
  if (this.isFormChangeActivation()) {
    var form = this.base.parents('form').eq(0);
    form.bind(
      'state-changed',
      _.bind(this.markAsChanged, this)
    );
    form.bind(
      'state-initial',
      _.bind(this.unmarkAsChanged, this)
    );
    form.bind(
      'more-action-enabled',
      _.bind(this.markMoreActionAsEnabled, this)
    );
    form.bind(
      'more-action-initial',
      _.bind(this.unmarkMoreActionAsEnabled, this)
    );
  }

  this.fixMoreActionButtons();
};

// Check - sticky panel in dialog widget or not
StickyPanel.prototype.isModal = function()
{
  return this.base.parents('.ui-dialog').length > 0
    || this.base.parents('.ajax-container-loadable').length > 0;
};

// Process reposition behaviour
StickyPanel.prototype.processReposition = function ()
{
  this.doc = jQuery(window.document);
  this.lastScrollTop = this.doc.scrollTop();
  this.lastHeight = jQuery(window).height();
  this.panelHeight = this.base.height();
  this.parentContainerTop = this.base.parent().offset().top;

  // Assign move operators
  jQuery(window)
    .scroll(_.bind(this.checkRepositionEvent, this))
    .resize(_.bind(this.checkRepositionEvent, this));

  core.bind(
    'stickyPanelReposition',
    _.bind(this.reposition, this)
  );
  this.reposition();
};

// Get options
StickyPanel.prototype.getOptions = function()
{
  var options = this.base.data('options') || {};

  jQuery.each(
    this.defaultOptions,
    function (key, value) {
      if ('undefined' == typeof(options[key])) {
        options[key] = value;
      }
    }
  );

  return options;
};

// Check reposition - need change behavior or not
StickyPanel.prototype.checkRepositionEvent = function()
{
  if (this.timer) {
    clearTimeout(this.timer);
    this.timer = null;
  }

  this.timer = setTimeout(
    _.bind(this.checkRepositionEventTick, this),
    50
  );
};

// Check reposition - need change behavior or not (on set timer)
StickyPanel.prototype.checkRepositionEventTick = function()
{
  var scrollTop = this.doc.scrollTop();
  var height = jQuery(window).height();
  if (Math.abs(scrollTop - this.lastScrollTop) > 0 || height != this.lastHeight) {
    var resize = height != this.lastHeight;
    this.lastScrollTop = scrollTop;
    this.lastHeight = height;
    this.reposition(resize);
  }
};

// Reposition
StickyPanel.prototype.reposition = function(isResize)
{
  var options = this.getOptions();

  this.panel.stop();

  var boxScrollTop = this.base.offset().top;
  var docScrollTop = this.doc.scrollTop();
  var windowHeight = jQuery(window).height();
  var diff = windowHeight - boxScrollTop + docScrollTop - this.panel.outerHeight() - options.bottomPadding;

  if (0 > diff) {
    if (options.parentContainerLock && this.parentContainerTop > (boxScrollTop + diff)) {
      this.panel.css({position: 'absolute', top: this.parentContainerTop - boxScrollTop});

    } else if ('fixed' != this.panel.css('position')) {
      this.panel.css({
        position: 'fixed',
        top: windowHeight - this.panel.outerHeight() - options.bottomPadding
      });
      this.panel.addClass('sticky');

    } else if (isResize) {
      this.panel.css({position: 'fixed', top: windowHeight - this.panel.outerHeight() - options.bottomPadding});
    }

  } else if (this.panel.css('top') != '0px') {
    this.panel.css({position: 'absolute', top: 0});
    this.panel.removeClass('sticky');
  }
};

// Check - form change activation behavior
StickyPanel.prototype.isFormChangeActivation = function()
{
  return this.base.hasClass('form-change-activation');
};

// Check - form present but do not change activation
StickyPanel.prototype.isFormDoNotChangeActivation = function()
{
  return this.base.hasClass('form-do-not-change-activation');
};

// Mark as changed
StickyPanel.prototype.markAsChanged = function()
{
  this.triggerVent('markAsChanged', { 'widget': this });

  this.getFormChangedButtons().each(
    _.bind(
      function(index, button) {
        this.enableButton(button);
      },
      this
    )
  );

  this.getFormChangedLinks().removeClass('disabled');
};

// Unmark as changed
StickyPanel.prototype.unmarkAsChanged = function()
{
  this.triggerVent('unmarkAsChanged', { 'widget': this });

  this.getFormChangedButtons().each(
    _.bind(
      function(index, button) {
        this.disableButton(button);
      },
      this
    )
  );

  this.getFormChangedLinks().addClass('disabled');
};

// Mark as changed
StickyPanel.prototype.markMoreActionAsEnabled = function()
{
  if (!this.moreActionAsEnabled) {
    this.getMoreActionButtons().each(
      _.bind(
        function(index, button) {
          this.enableButton(button);
        },
        this
      )
    );

    this.fixMoreActionButtons();

    this.moreActionAsEnabled = true;
  }
};

// Unmark as changed
StickyPanel.prototype.unmarkMoreActionAsEnabled = function()
{
  if (this.moreActionAsEnabled) {
    this.getMoreActionButtons().each(
      _.bind(
        function(index, button) {
          if (!jQuery(button).hasClass('always-enabled')) {
            this.disableButton(button);
          }
        },
        this
      )
    );

    this.fixMoreActionButtons();

    this.moreActionAsEnabled = false;
  }
};

// Enable button
StickyPanel.prototype.enableButton = function(button)
{
  var state = {
    'state':   true,
    'inverse': false,
    'button':  button,
    'widget':  this
  };

  this.triggerVent('check.button.enable', state);

  if (state.state) {
    if (button.enable) {
      button.enable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).removeClass('hidden');
    }

  } else if (state.inverse) {
    if (button.disable) {
      button.disable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).addClass('hidden');
    }
  }
};

// Disable button
StickyPanel.prototype.disableButton = function(button)
{
  var state = { 'state': true, 'inverse': false, 'button': button, 'widget': this };

  this.triggerVent('check.button.disable', state);

  if (state.state) {
    if (button.disable) {
      button.disable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).addClass('hidden');
    }

  } else if (state.inverse) {
    if (button.enable) {
      button.enable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).removeClass('hidden');
    }
  }
};

// Get a form button, which should change as the state of the form
StickyPanel.prototype.getFormChangedButtons = function()
{
  var buttons = this.base.find('button, div.divider');

  // If there is any element inside the dropdown menu with the "always-enabled" state
  // then we do not disable the toggle list action button
  return (this.base.find('.dropdown-menu .always-enabled').length > 0)
    ? buttons.not('.always-enabled, .toggle-list-action')
    : buttons.not('.always-enabled, .more-action, .more-actions');
};

StickyPanel.prototype.getMoreActionButtons = function()
{
  return this.base.find('.more-action, .more-actions');
};

StickyPanel.prototype.fixMoreActionButtons = function()
{
  this.getMoreActionButtons().removeClass('fist-visible').removeClass('last-visible')
    .filter(':visible')
    .first().addClass('first-visible')
    .end()
    .last().addClass('last-visible');

  this.base.find('.additional-buttons .or').toggle(!!this.getMoreActionButtons().filter(':visible').length);
};

// Get a form links, which should change as the state of the form
StickyPanel.prototype.getFormChangedLinks = function()
{
  return this.base.find('.cancel');
};

// Get event namespace (prefix)
StickyPanel.prototype.getEventNamespace = function()
{
  return 'stickypanel';
};

// Autoload
core.microhandlers.add(
  'sticky-panel',
  '.sticky-panel',
  function () {
    core.autoload(StickyPanel);
  }
);
