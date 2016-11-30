/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Css hot editor button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var PopupButtonCssEditor = PopupButton.extend({
  constructor: function PopupButtonCssEditor() {
    PopupButtonCssEditor.superclass.constructor.apply(this, arguments);
  },

  pattern: '.custom-css-editor-button',

  enableBackgroundSubmit: true,

  callback: function(selector, link) {
    PopupButtonCssEditor.superclass.callback.apply(this, arguments);
    core.autoload(CodeMirrorWidget, 'textarea.codemirror');

    this.assignElements(selector);
    this.assignHandlers();
  },

  assignElements: function (selector) {
    this.$dialog = $(selector).closest('.ui-dialog');
    this.$button = $('.layout-editor-custom_css_button');
    this.$form = $('form', selector);
    this.$css = $('[data-custom-css]');
    this.$editor = $('textarea.codemirror', selector);
    this.$cmWidget = $('.CodeMirror', selector);
  },

  assignHandlers: function () {
    this.$form.bind('beforeSubmit', _.bind(this.onFormSubmit, this));
    this.$form.bind('afterSubmit', _.bind(this.afterFormSubmit, this));
    this.$form.find('#use_custom').change(_.bind(this.onSwitcherChange, this));

    this.$editor.change(_.bind(this.onEditorChange, this));

    core.bind('popup.open', _.bind(this.onPopupOpen, this));
    core.bind('popup.close', _.bind(this.onPopupClose, this));

    this.onPopupOpen();
  },

  onEditorChange: _.debounce(function() {
    this.$css.text(this.$editor.text());
  }, 200),

  onSwitcherChange: function(event) {
    var text = this.$css.text();

    if (event.target.checked) {
      this.$css.replaceWith('<style rel="stylesheet" type="text/css" media="screen" data-custom-css>');
    } else {
      this.$css.replaceWith('<script type="text/css" data-custom-css>');
    }

    this.$css = $('[data-custom-css]').text(text);
  },

  onPopupOpen: function() {
    if (this.$editor.closest('.ui-dialog').is(':visible')) {
      this.disableButton(this.$button);
      $('body').addClass('live-css-reloading');

      var initial = this.getInitialPosition();

      this.$dialog.css('position', 'fixed')
        .css('top', initial.top).css('left', initial.left);

      var self = this;

      this.$dialog.draggable({
        scroll: false,
        containment: 'window',
        cancel: 'form',
        stop: function(event, ui) {
          self.setInitialPosition(ui.position);
        }
      });
      this.$dialog.draggable('enable');
    }
  },

  getInitialPosition: function() {
    var position = JSON.parse(localStorage.getItem('layout-editor_css-position'));

    return position && position.window == $(window).width() ? position : this.getDefaultPosition();
  },

  setInitialPosition: function(position) {
    var data = _.extend(position, {
      window: $(window).width()
    });

    localStorage.setItem('layout-editor_css-position',  JSON.stringify(data));
  },

  getDefaultPosition: function() {
    return {
      top: 20,
      left: $(window).width() - this.$dialog.outerWidth() - 40,
      window: $(window).width()
    };
  },

  disableButton: function(button) {
    button.addClass('disabled');
    button.prop('disabled', true);
  },

  enableButton: function(button) {
    button.removeClass('disabled');
    button.prop('disabled', false);
  },
  
  onPopupClose: function() {
    $('body').removeClass('live-css-reloading');
    this.enableButton(this.$button);
  },

  onFormSubmit: function() {
    this.$cmWidget.addClass('reloading');
    var textarea = this.$form.find('textarea.codemirror');
    textarea.val(textarea.text());
  },

  afterFormSubmit: function() {
    this.$cmWidget.removeClass('reloading');
  }
});

core.autoload(PopupButtonCssEditor);