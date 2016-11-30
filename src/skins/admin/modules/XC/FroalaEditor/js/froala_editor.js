/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * TinyMCE-based textarea controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var FroalaEditor = CommonElement.extend({
  constructor: function FroalaEditor(base) {
    if (base.length < 1) {
      console.err('[FroalaEditor] got empty element in constructor');
      return;
    }

    var element = base.get(0);
    element.commonController = undefined;

    FroalaEditor.superclass.constructor.apply(this, [element]);

    this.initialize();
  },

  initialize: function () {
    this.$element.froalaEditor(this.getEditorOptions());
    this.$element.on('froalaEditor.contentChanged', _.bind(this.onContentChange, this));

    this.bind('local.validate', _.bind(this.specialValidate, this));

    core.trigger('froala.initialized', { sender: this, element: this.element });
  },

  getEditorOptions: function () {
    var options = core.getCommentedData(this.$element.parent());
    return options;
  },

  onContentChange: _.throttle(function (e, editor) {
    jQuery(e.target).trigger('change');
  }, 200),

  isVisible: function() {
    return this.$element.parent().is(':visible');
  },

  isVueControlled: function() {
    return typeof(this.element.form.__vue__) !== 'undefined';
  },

  handleChange: function() {
    if (this.isVueControlled()) {
      // stub unneccessary function
      return true;
    } else {
      FroalaEditor.superclass.handleChange.apply(this, arguments);
    }
  },

  specialValidate: function (event, state) {
    if (!this.isRequired()) {
      return;
    }

    if (this.$element.length && this.$element.froalaEditor('html.get') === '') {
      var name = '';
      var label = jQuery('label[for=' + this.element.id + ']');
      if (label && label.length > 0) {
        name = label.attr('title');
      } else {
        name = id;
      }

      core.trigger(
        'message',
        {
          type: 'error',
          message: core.t('The X field is empty', {name: name})
        }
      );

      this.markAsInvalid();
      this.$element.froalaEditor('events.focus');

      state.result = false;
    } else {
      this.unmarkAsInvalid();
    }
  },

  isRequired: function () {
    var rulesParsing = this.$element.attr('class');
    var getRules = /validate\[(.*)\]/.exec(rulesParsing);

    if (!getRules) {
      return false;
    }

    var str = getRules[1];
    var rules = str.split(/\[|,|\]/);
    return -1 !== rules.indexOf('required');
  } 
});

core.autoload('FroalaEditor', 'textarea.fr-instance');