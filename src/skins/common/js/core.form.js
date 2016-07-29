/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common form / element controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Form
 */

// Constructor
function CommonForm(form)
{
  if (!form) {
    return false;
  }

  form = jQuery(form).filter('form').eq(0);
  if (!form.length || form.get(0).commonController) {
    return false;
  }

  this.form = form.get(0);
  this.$form = form;

  var o = this;

  this.form.commonController = this;

  if (!this.form.getAttribute('id')) {
    var d = new Date();
    this.form.setAttribute('id', 'form-' + d.getTime());
  }

  var methods = [
    'validate', 'submitBackground', 'isChanged', 'getElements'
  ];

  for (var i = 0; i < methods.length; i++) {
    var method = methods[i];
    this.form[method] = new Function('', 'return this.commonController.' + method + '.apply(this.commonController, arguments);');
  }

  this.form.isBgSubmitting = false;

  // Central validation before form submit and background submit
  form.submit(
    function(event)
    {
      jQuery('input', this).trigger('sanitize');

      if (this.isBgSubmitting) {
        return false;
      }

      o.triggerVent('submit.prevalidate', o);

      var result = this.validate();

      if (result && o.submitOnlyChanged && !o.isChanged(true)) {
        result = false;
      }

      // Trigger the validationSuccess or validationFailure events accordingly the validation result
      var validationEvent = jQuery.Event(result ? 'validationSuccess' : 'validationFailure');
      o.$form.trigger(validationEvent);

      if (result && o.backgroundSubmit) {
        var e = jQuery.Event('beforeSubmit');
        o.$form.trigger(e);

        o.triggerVent('submit.postvalidate', o);

        if (false !== e.result) {
          o.unmarkAsInvalid();
          if (o.submitBackground(null, true)) {
            result = false;
          }
        }

      } else if (result) {
        o.unmarkAsInvalid();
      }

      return result;
    }
  );

  this.bindElements();

  // Process invalidElement event
  core.bind(
    'invalidElement',
    function(event, data) {
      if (o.form.isBgSubmitting && o.form.elements.namedItem(data.name)) {
        o.form.elements.namedItem(data.name).markAsInvalid(data.message, null, true);
      }
    }
  );

  // Process invalidForm event
  core.bind(
    'invalidForm',
    function(event, data) {
      if (data.name && o.$form.is('.' + data.name)) {
        o.markAsInvalid(data.message);
        o.triggerVent(
          'invalid.form',
          _.extend(_.clone(data), {widget: o})
        );
      }
    }
  );

  var triggerStateEvents = function(form) {
    if (form.get(0).commonController.isChanged()) {
      form.addClass('changed');
      form.trigger('state-changed');

    } else {
      form.removeClass('changed');
      form.trigger('state-initial');
    }
  }
  var debouncedTriggerStateEvents = _.debounce(triggerStateEvents, 100, true)

  var changeHandler = function (event) {
    var obj = event.target || event.srcElement;
    var form = jQuery(this);

    if (
      !obj
      || !jQuery(obj)
      || !jQuery(obj).hasClass('not-significant')
    ) {
      debouncedTriggerStateEvents(form);
    }

    if (!jQuery(obj).hasClass('selectAll')) {
      if (0 < form.find('input.selector:checked').length) {
        if (!form.hasClass('more-action-enabled')) {
          form.addClass('more-action-enabled');
          form.trigger('more-action-enabled');
        }

      } else if (form.hasClass('more-action-enabled')) {
        form.removeClass('more-action-enabled');
        form.trigger('more-action-initial');
      }
    }

    var state = this.commonController.checkDependencyState();
    this.commonController.setDependencyState(state);
  };

  // Process form changed
  form.change(changeHandler);

  var state = this.checkDependencyState();
  this.setDependencyState(state);

  this.triggerVent('initialized', this);
}

extend(CommonForm, Base);

// Element controllers
CommonForm.elementControllers = [];

// Autoload class method
CommonForm.autoload = function()
{
  jQuery('form').each(
    function() {
      new CommonForm(this);
    }
  );
};

// Autoassign class method
CommonForm.autoassign = function(base)
{
  jQuery('form', base).each(
    function() {
      new CommonForm(this);
    }
  );

  jQuery(base).parents('form').each(
    function() {
      if (typeof(this.commonController) != 'undefined') {
        this.commonController.bindElements();
      }
    }
  );
};

// Form DOM element
CommonForm.prototype.form = null;

// Form jQuery element
CommonForm.prototype.$form = null;

CommonForm.prototype.blurTTL = 300;

CommonForm.prototype.elementBlurTO = null;

CommonForm.prototype.isFocus = false;
/**
 * Options
 */

// Auto-submit form in background mode
CommonForm.prototype.backgroundSubmit = false;

// Submit form ony if form changed
CommonForm.prototype.submitOnlyChanged = false;

// POST request as RPC-request
CommonForm.prototype.postAsRPC = true;

// GET request as RPC-request
CommonForm.prototype.getAsRPC = false;

CommonForm.prototype.errorPlace = null;

CommonForm.prototype.formIdName = 'xcart_form_id';
// Get elements jQuery collection
CommonForm.prototype.getElements = function()
{
  var data = {
    widget: this,
    list:  this.$form.find(':input, .separator h2')
  };

  this.triggerVent('filterElements', data);

  return data.list;
};

// Bind form elements
CommonForm.prototype.bindElements = function()
{
  var o = this;
  var form = this.$form;

  this.getElements().each(
    function() {
      if ('undefined' == typeof(this.commonController)) {
        var elm = new CommonElement(this);

        jQuery.each(
          CommonForm.elementControllers,
          function (i, controller) {
            if ('function' == typeof(controller)) {

              // Controller is function-handler
              controller.call(elm);
            }
          }
        );

        this.commonController.bind('local.blur', _.bind(o.handleElementBlur, o));
        this.commonController.bind('local.focus', _.bind(o.handleElementFocus, o));
      }
    }
  );

  jQuery.each(
    CommonForm.elementControllers,
    function (i, controller) {
      if (
        'object' == typeof(controller)
        && 0 < form.find(controller.pattern).length
        && ('undefined' == typeof(controller.condition) || 0 < form.find(controller.condition).length)
      ) {

        // Controller is { pattern: '.element', handler: function () { ... } }
        form.find(controller.pattern).each(
          function () {
            if ('undefined' == typeof(this.assignedElementControllers)) {
              this.assignedElementControllers = [];
            }

            if (-1 == jQuery.inArray(i, this.assignedElementControllers)) {
              controller.handler.call(this, form);
              this.assignedElementControllers.push(i);
            }
          }
        );
      }
    }
  );

  // Cancel button
  this.$form.find('.form-cancel')
    .not('.assigned')
    .click(
      function (event) {
        event.stopPropagation();
        var form = jQuery(this).not('.disabled').parents('form').get(0);
        if (form ) {
          form.commonController.undo();
        }

        return false;
      }
    )
    .addClass('assigned');
};

// Validate form
CommonForm.prototype.validate = function(options)
{
  var silent = options;
  var focus = true;
  if ('object' == typeof(options)) {
    var silent = typeof(options.silent) !== 'undefined' ? options.silent : false;
    var focus = typeof(options.focus) !== 'undefined' ? options.focus : true;
  }

  var options = focus !== false;
  var all = this.getElements();
  var failed = all.filter('input,select,textarea').filter(
    function() {
      return this.commonController && !this.commonController.validate(silent);
    }
  );

  var state = {
    'widget': this,
    'result': 0 == failed.length,
    'all':    all,
    'failed': failed
  };
  this.triggerVent('validate', state);

  if (!state.result && !silent && focus) {
    this.focusOnFirstInvalid();
  }

  return state.result;
};

CommonForm.prototype.focusOnFirstInvalid = function(message)
{
  var elm = this.getElements().filter('.validation-error').eq(0);
  if (elm.length) {
    elm.focus();
  }
};

CommonForm.prototype.markAsInvalid = function(message)
{
  this.getErrorPlace()
    .html(message)
    .show();

  this.$form.addClass('invalid-form')
};

CommonForm.prototype.unmarkAsInvalid = function()
{
  this.getErrorPlace()
    .hide();

  this.$form.removeClass('invalid-form')
};

CommonForm.prototype.getFormId = function () {
  return this.$form
    .find('input[type="hidden"]')
    .filter('input[name="' + this.formIdName + '"]').eq(0)
    .val();
};

CommonForm.prototype.replaceCSRFToken = function (value) {
  return this.$form
    .find('input[type="hidden"]')
    .filter('input[name="' + this.formIdName + '"]').eq(0)
    .val(value);
};

CommonForm.prototype.getErrorPlace = function () {
  if (!this.errorPlace) {
    this.errorPlace = jQuery('<div></div>')
      .addClass('form-error')
      .hide()
      .insertBefore(this.$form.children().eq(0));
  }

  return this.errorPlace;
};

// Undo all changes
CommonForm.prototype.undo = function () {
  this.getElements().filter('input,select,textarea').each(function () {
    this.commonController.undo();
  });
  this.$form.trigger('undo');
  this.$form.change();
};

// Enabled background submit mode and set callbacks
CommonForm.prototype.enableBackgroundSubmit = function(beforeCallback, afterCallback) {
  this.backgroundSubmit = true;

  if (beforeCallback) {
    this.$form.bind('beforeSubmit', beforeCallback);
  }

  if (afterCallback) {
    this.$form.bind('afterSubmit', afterCallback);
  }

  return this;
};

// Submit form in background mode
CommonForm.prototype.submitBackground = function(callback, disableValidation, options)
{
  var result = false;

  if (disableValidation || this.form.validate()) {

    var state = {
      'widget': this,
      'state':  true
    };

    this.triggerVent('beforeSubmit', state);
    if (!state.state) {
      return result;
    }

    this.preprocessBackgroundSubmit();

    var o = this;

    var isPOST = 'POST' == this.$form.attr('method').toUpperCase();
    var method = isPOST ? 'post' : 'get';

    options = options || {};

    if (
      'undefined' == typeof(options.rpc)
      && ((isPOST && this.postAsRPC) || (!isPOST && this.getAsRPC))
    ) {
      options.rpc = true;
    }

    if ('undefined' == typeof(options.timeout)) {
      options.timeout = 60000;
    }

    result = core[method](
      this.$form.attr('action'),
      function(XMLHttpRequest, textStatus, data, isValid) {
        var args = {
          'XMLHttpRequest': XMLHttpRequest,
          'textStatus':     textStatus,
          'data':           data,
          'isValid':        isValid
        };
        o.postprocessBackgroundSubmit();
        o.tryRestoreCSRFToken(XMLHttpRequest);
        o.$form.trigger('afterSubmit', args);
        o.triggerVent('submitted', args);
        if (isValid) {
          o.triggerVent('submit.success', args);

        } else {
          o.triggerVent('submit.error', args);
        }

        return callback ? callback(XMLHttpRequest, textStatus, data, isValid) : true;
      },
      this.getSerializedValues(),
      options
    );

    if (result) {
      this.form.currentSubmitXHR = result;

    } else {
      core.showInternalError();
    }

    this.triggerVent('afterSubmit', {'widget': this, 'state':  result, 'arguments': arguments});
  }

  return result;
};

CommonForm.prototype.getSerializedValues = function()
{
  return this.getElements().serialize();
};

CommonForm.prototype.tryRestoreCSRFToken = function(xhr)
{
  var list = xhr.getAllResponseHeaders().split(/\n/);
  var tokenHeader = _.find(list, function(headerValue){
    return -1 !== headerValue.search(/update-csrf: (.*)/i);
  });

  var newTokenMatches = tokenHeader
    ? tokenHeader.match(/update-csrf: (.*)/i)
    : null;

  if (newTokenMatches && newTokenMatches[1]) {
    var newTokenValue = JSON.parse(newTokenMatches[1]);

    if (newTokenValue.value) {
      this.replaceCSRFToken(newTokenValue.value);
    };
  };
}
// Submit form in force mode
CommonForm.prototype.submitForce = function()
{
  this.skipCurrentBgSubmit();

  var oldValue = this.submitOnlyChanged;
  this.submitOnlyChanged = false;

  this.$form.submit();

  this.submitOnlyChanged = oldValue;
};

// Prepare form before background submit
CommonForm.prototype.preprocessBackgroundSubmit = function()
{
  this.form.isBgSubmitting = true;

  this.triggerVent('submit.preprocess', this);

  this.getElements().commonController('preprocessBackgroundSubmit');
};

// Prepare form after background submit
CommonForm.prototype.postprocessBackgroundSubmit = function()
{
  this.form.isBgSubmitting = false;
  if (this.form.currentSubmitXHR) {
    delete this.form.currentSubmitXHR;
  }

  this.getElements().commonController('postprocessBackgroundSubmit');
};

CommonForm.prototype.isSaveValue = function (element) {
  return true;
};

// Skip current background submit
CommonForm.prototype.skipCurrentBgSubmit = function () {
  if (this.form.isBgSubmitting) {
    this.form.isBgSubmitting = false;

    if (this.form.currentSubmitXHR) {
      this.form.currentSubmitXHR.abort();
      delete this.form.currentSubmitXHR;
    }

    this.getElements().commonController('skipCurrentBgSubmit');
  }
};

// Form has any changed state watcher's elements
CommonForm.prototype.hasChangedWatcher = function () {
  return 0 < this.getElements().filter(function() {
    return this.isChangedWatcher();
  }).length;
};

// Check - form changed or not
CommonForm.prototype.isChanged = function(onlyVisible)
{
  return 0 < this.getElements().filter(
    function() {
      return this.commonController ? this.commonController.isChanged(onlyVisible) : false;
    }
  ).length;
};

// Check - form was filled or not
CommonForm.prototype.wasFilledOnce = function()
{
  var fields = this.getElements().filter(
    function() {
      return this.commonController ? this.commonController.isRequired() : false;
    }
  );

  return  _.every(fields, function(item) {
    return item.commonController.wasFilledOnce() || !item.commonController.isVisible()
  });
};

CommonForm.prototype.handleElementBlur = function(elm)
{
  this.elementBlurTO = setTimeout(
    _.bind(this.triggerBlur, this),
    this.blurTTL
  );
};

CommonForm.prototype.handleElementFocus = function(elm)
{
  if (this.elementBlurTO) {
    clearTimeout(this.elementBlurTO);
    this.elementBlurTO = null;
  }

  if (!this.isFocus) {
    this.isFocus = true;
    this.triggerVent('focus', this);
  }
};

CommonForm.prototype.triggerBlur = function()
{
  if (this.isFocus) {
    this.isFocus = false;
    this.triggerVent('blur', this);
  }
};

CommonForm.prototype.checkDependencyState = function()
{
  var state = {};

  var checkState = _.bind(function (state, field, deps, show) {
    var depField, depValue, value;
    var input, element;
    for (depField in deps) if (deps.hasOwnProperty(depField)) {
      input = jQuery('[name="' + depField + '"]', jQuery(this.form)).not('[type="hidden"]').get(0);
      if (!input || !input.commonController) {
        continue;
      }
      depValue = deps[depField];
      element = input.commonController.$element;

      if (element.is('[type="checkbox"]')) {
        value = element.is(':checked') ? true : false;
      } else {
        value = element.val();
      }

      if (show) {
        if (depValue == value || ((typeof depValue === 'object') && _.contains(depValue, value))) {
          if (state[field] !== false) {
            state[field] = true;
          }
        } else {
          state[field] = false;
        }
      } else {
        if (depValue == value || ((typeof depValue === 'object') && _.contains(depValue, value))) {
          if (state[field] !== true) {
            state[field] = false;
          }
        } else {
          state[field] = true;
        }
      }
    }

    return state;
  }, this);

  _.each(
    this.getDependency(),
    function(value, field) {
      state = checkState(state, field, value.hide, false);
      state = checkState(state, field, value.show, true);
    }
  );

  return state;
};

CommonForm.prototype.getDependency = function()
{
  var dependency = {};

  this.$form.find('.has-dependency :input, .separator.has-dependency h2').each(
    function () {
      if ('undefined' != typeof(this.commonController)) {
        if ('undefined' != typeof(this.commonController.hasDependency) && this.commonController.hasDependency()) {
          var name = this.commonController.element.name || this.commonController.$element.data('name');
          dependency[name] = this.commonController.getDependency()
        }
      }
    }
  );

  return dependency;
};

CommonForm.prototype.setDependencyState = function(state)
{
  _.each(
    state,
    this.setElementDependency,
    this
  );
};

CommonForm.prototype.setElementDependency = function (state, field)
{
  var element = jQuery(this.form.elements.namedItem(field)).add('[data-name="'+field+'"]', this.form).filter('[type!="hidden"]').get(0);
  if (element) {
    var elementController = element.commonController;

    if (state) {
      elementController.showByDependency();

    } else {
      elementController.hideByDependency();
    }
  }
};

// {{{ Form readiness

CommonForm.prototype.controlReadiness = false;

CommonForm.prototype.isReady = null;

CommonForm.prototype.switchControlReadiness = function(flag)
{
  this.controlReadiness = true === flag || 'undefined' == typeof(flag);

  if (this.controlReadiness) {
    this.processReadiness();
  }

  return this;
};

CommonForm.prototype.resetReadiness = function()
{
  this.isReady = null;

  return this;
};

CommonForm.prototype.processReadiness = function()
{
  if (this.isReadinessChanged()) {
    if (this.isReady) {
      this.triggerVent('ready', this);

    } else {
      this.triggerVent('unready', this);
    }
  }
};

CommonForm.prototype.isReadinessChanged = function()
{
  var tmp = this.isReady;
  this.isReady = this.validate(true);

  return tmp != this.isReady;
};

// }}}

/**
 * Element
 */

// Constructor
function CommonElement(elm)
{
  if (elm && !elm.commonController) {
    this.bindElement(elm);
  }
}

extend(CommonElement, Base);

CommonElement.prototype.element = null;

CommonElement.prototype.$element = null;

// Validattion class-base rule pattern
CommonElement.prototype.classRegExp = /^field-(.+)$/;

CommonElement.prototype.watchTTL = 2000;

CommonElement.prototype.promptPosition = 'bottomLeft';

// Bind element
CommonElement.prototype.bindElement = function(elm)
{
  this.element = elm;
  this.$element = jQuery(elm);

  this.element.commonController = this;

  // Add methods and properties
  var methods = [
    'showInlineError', 'hideInlineError', 'markAsInvalid',
    'unmarkAsInvalid', 'markAsProgress',  'unmarkAsProgress',
    'validate',        'isChanged',       'markAsWatcher',
    'toggleActivity',  'isEnabled',       'enable',
    'disable',         'isVisible',
    'showInlineMessage', 'hideInlineMessage'
  ];

  _.each(
    methods,
    function(method) {
      this.element[method] = _.bind(
        function() {
          return this[method].apply(this, arguments);
        },
        this
      );
    },
    this
  );

  this.saveValue();
  this.element.isInitialError = this.$element.hasClass('validation-error');

  this.assignHandlers();

  this.$element.bind('jqv.field.result', _.bind(this.handleValidationEngineResult, this));
  var debounced = _.debounce(_.bind(this.handleKeyUp, this), 100);
  var debouncedChange = _.debounce(_.bind(this.handleChange, this), 100);
  this.$element
    .change(debouncedChange)
    .keyup(debounced)
    .bind('paste', debounced);

  this.triggerVent('bind');
};

CommonElement.prototype.getForm = function () {
  return (this.element.form && this.element.form.commonController)
    ? this.element.form.commonController
    : null;
};

// Get validators by form element
CommonElement.prototype.getValidators = function () {
  var validators = [];

  if (this.element.className) {
    var classes = this.element.className.split(/ /);
    var m, methodName;
    for (var i = 0; i < classes.length; i++) {

      m = classes[i].match(this.classRegExp);

      if (m && m[1]) {
        methodName = m[1].replace(/-[a-z]/, this.buildMethodName);
        methodName = 'validate' + methodName.substr(0, 1).toUpperCase() + methodName.substr(1);
        if (typeof(this[methodName]) !== 'undefined') {
          validators.push(
            {
              key:    m[1],
              method: this[methodName]
            }
          );
        }
      }
    }
  }

  return validators;
};

// Get element label by form element
CommonElement.prototype.getLabel = function()
{
  var label = null;

  if (this.element.id) {
    var lbl = jQuery('label[for="' + this.element.id + '"]');
    if (lbl.length) {
      label = jQuery.trim(lbl.eq(0).html()).replace(/:$/, '').replace(/<.+$/, '');
    }
  }

  return label;
};

// Check - element visible or not
CommonElement.prototype.isVisible = function()
{
  if (this.$element.css('display') == 'none') {
    return false;
  }

  return 0 == this.$element
    .parents()
    .filter(
      function() {
        return $(this).css('display') == 'none';
      }
    )
    .length;
};

// Build validator method name helper
CommonElement.prototype.buildMethodName = function(str)
{
  return str.substr(1).toUpperCase();
};

// Validate form element
CommonElement.prototype.validate = function(silent, noFocus)
{
  var result = true;

  if (!this.isVisible()) {
    return result;
  }

  if (
    this.$element.is(':input')
    && (
      ('undefined' != typeof(this.element.type) && 'hidden' == this.element.type)
      || this.$element.closest('.hidden').length > 0
    )
  ) {
    // Hidden input always validate successfull
    result = true;

  } else if (this.$element.hasClass('no-validate')) {

    // Hidden element always validate successfull
    result = true;

  } else if (this.$element.hasClass('server-validation-error') && !this.isChanged()) {

    // Element is fail server validation and element's value did not changed
    result = false;

  } else {

    this.$element.data('validation-silent', !!silent);

    var validators = this.getValidators();

    if (0 < validators.length && this.isVisible()) {

      // Check by validators
      for (var i = 0; i < validators.length && result; i++) {

        var res = validators[i].method.call(this);
        if (!res.status && res.apply) {
          result = false;

          if (!silent) {
            res.message = core.t(res.message);
            this.markAsInvalid(res.message, validators[i].key);
          }
        }
      }

      if (!result && !silent && this.$element.hasClass('validation-error')) {
        this.unmarkAsInvalid();
      }
    }

    // Validation using validationEngine plugin
    if (result && this.$element.validationEngine && this.$element.attr('id')) {
      this.$element.validationEngine('validate');
      result = !this.$element.data('validation-error');
    }

    var state = {'widget': this, 'result': result, 'silent': silent};
    this.triggerVent('validate', state);

    result = state.result;

    if (result && silent) {
      this.unmarkAsInvalid();
    }
  }

  return result;
};

// Check
CommonElement.prototype.isUseInlineError = function()
{
  return jQuery(this.element.form).hasClass('use-inline-error');
};

// Check
CommonElement.prototype.isUsePromptError = function()
{
  return jQuery(this.element.form).hasClass('use-prompt-error')
    && 'undefined' != typeof(this.$element.validationEngine);
};

// Mark element as invalid (validation is NOT passed)
CommonElement.prototype.markAsInvalid = function(message, key, serverSideError)
{
  key = key || this.element.name;

  this.$element
    .addClass('validation-error')
    .data('lastValidationError', message)
    .data('lastValidationKey', key);

  this.$element.parent().addClass('has-error');

  if (this.isUseInlineError()) {
    this.$element
      .not('.forbid-inline-error')
      .each(
        function() {
          this.hideInlineError();
          this.hideInlineMessage();
          this.showInlineError(message);
        }
      );

  } else if (this.isUsePromptError()) {
    this.$element.validationEngine(
      'showPrompt',
      message,
      null,
      this.promptPosition,
      true
    );

  } else {
    if (this.$element.attr('title')) {
      this.element.oldTitle = this.$element.attr('title');
      this.$element.removeAttr('title');
    }
    if ('undefined' == typeof(this.element.errorTooltipAssigned) || !this.element.errorTooltipAssigned) {
      this.$element.tooltip({
        title:     message,
        placement: 'bottom',
        trigger:   'focus'
      });
      this.element.errorTooltipAssigned = true;
      this.$element.tooltip('show');
    }
  }

  if (serverSideError) {
    this.$element.addClass('server-validation-error');
  }

  this.$element.trigger('invalid');
  this.triggerVent('markAsInvalid', this);
};

// Unmark element as invalid (validation is passed)
CommonElement.prototype.unmarkAsInvalid = function()
{
  if (this.$element.hasClass('validation-error')) {
    this.$element.trigger('valid');
  }

  this.$element
    .data('lastValidationError', null)
    .data('lastValidationKey', null)
    .removeClass('validation-error')
    .removeClass('server-validation-error')
    .each(function() {
      this.hideInlineError();
    });

  this.$element.parent().removeClass('has-error');
  if ('undefined' != typeof(this.element.errorTooltipAssigned) && this.element.errorTooltipAssigned) {
    this.$element.tooltip('destroy');
    this.element.errorTooltipAssigned = false;
  }
  if ('undefined' != typeof(this.element.oldTitle) && this.element.oldTitle) {
    this.$element.attr('title', this.element.oldTitle);
    this.element.oldTitle = null;
  }

  this.triggerVent('unmarkAsInvalid', this);
};

// Mark element as valid (validation is passed)
CommonElement.prototype.markAsValid = function(message)
{
  this.unmarkAsInvalid();

  this.$element
    .data('lastValidationMessage', message)
    .each(
    function() {
      this.hideInlineMessage();
      this.showInlineMessage(message);
    }
  );

  this.triggerVent('markAsValid', this);
};

// Show element inline error message
CommonElement.prototype.showInlineError = function(message)
{
  var elm = jQuery(document.createElement('p'))
    .addClass('error')
    .addClass('inline-error')
    .html(message);
  var placed = false;

  this.triggerVent('placeError', {'widget': this, 'element': elm, 'placed': placed});
  if (!placed) {
    if (this.$element.is(':checkbox') && this.$element.nextAll('label').length) {
      elm.insertAfter(this.$element.nextAll('label').eq(0));

    } else {
      elm.insertAfter(this.$element);
    }
  }
};

// Hide element inline error message
CommonElement.prototype.hideInlineError = function()
{
  return jQuery('p.inline-error', this.element.parentNode).remove();
};

// Show element inline error message
CommonElement.prototype.showInlineMessage = function(message)
{
  var elm = jQuery(document.createElement('p'))
    .addClass('message')
    .addClass('inline-message')
    .html(message);
  var placed = false;

  this.triggerVent('placeMessage', {'widget': this, 'element': elm, 'placed': placed});
  if (!placed) {
    if (this.$element.is(':checkbox') && this.$element.nextAll('label').length) {
      elm.insertAfter(this.$element.nextAll('label').eq(0));

    } else {
      elm.insertAfter(this.$element);
    }
  }
};

// Hide element inline error message
CommonElement.prototype.hideInlineMessage = function()
{
  return jQuery('p.inline-message', this.element.parentNode).remove();
};

// Mark element as in-progress element
CommonElement.prototype.markAsProgress = function()
{
  this.$element.addClass('progress-mark-apply');

  return jQuery('<div></div>')
    .insertAfter(this.$element)
    .addClass('single-progress-mark');
};

// Unmark element as in-progress element
CommonElement.prototype.unmarkAsProgress = function()
{
  this.$element.removeClass('progress-mark-apply');

  return jQuery('.single-progress-mark', this.element.parentNode).remove();
};

// Mark element as change watcher
CommonElement.prototype.markAsWatcher = function(beforeCallback)
{
  var o = this;

  this.element.selfSubmitting = false;
  this.element.selfSubmitTO = null;
  this.element.lastValue = this.element.value;

  var submitElement = function(event) {
    if (o.element.selfSubmitTO) {
      clearTimeout(o.element.selfSubmitTO);
      o.element.selfSubmitTO = null;
    }

    if (
      (o.isChanged() || (o.$element.hasClass('validation-error') && !o.$element.hasClass('server-validation-error')))
      && (!beforeCallback || beforeCallback(o.element))
    ) {
      jQuery(o.element.form).submit();
    }
  };

  var delayedUpdate = function(event) {
    if (this.lastValue != this.value) {

      this.lastValue = this.value;

      if (this.selfSubmitTO) {
        clearTimeout(this.selfSubmitTO);
        this.selfSubmitTO = null;
      }

      this.selfSubmitTO = setTimeout(submitElement, o.watchTTL);
    }
  };

  o.$element
    .blur(submitElement)
    .keyup(delayedUpdate);

  if ('undefined' != typeof(jQuery.fn.mousewheel)) {
    o.$element.mousewheel(delayedUpdate);
  }
};

// Element is column checkboxes switcher
CommonElement.prototype.markAsColumnSwitcher = function()
{
  this.$element.click(
    function() {
      var idx = jQuery(this).parents('th').get(0).cellIndex;
      var newState = this.checked;

      jQuery(this).parents('table').find('tr').each(
        function() {
          jQuery(this.cells[idx]).find(':checkbox').get(0).checked = newState;
        }
      );
    }
  );
};

// Element is mouse wheel controlled
CommonElement.prototype.markAsWheelControlled = function()
{
  var o = this;

  this.$element.mousewheel(
    function(event, delta) {

      // Pull min and max value from the validationEndine class
      if (o.$element.attr('class').match(/min\[(\d+)\].*max\[(\d+)\]/)) {
        o.$element.mousewheel.options = {
          'min': RegExp.$1,
          'max': RegExp.$2
        };
      }

      return o.$element.hasClass('focused') && o.updateByMouseWheel(event, delta);
    }
  );

  if (!this.$element.hasClass('no-wheel-mark') && 0 == this.$element.nextAll('.wheel-mark').length) {
    jQuery(document.createElement('span'))
      .addClass('wheel-mark')
      .html('&nbsp;')
      .insertAfter(this.$element);
  }

  this.$element.addClass('wheel-mark-input');

  this.$element.focus(
    function() {
      jQuery(this).addClass('focused')
    }
  );

  this.$element.blur(
    function() {
      jQuery(this).removeClass('focused')
    }
  );
};

// Update element by mouse wheel
CommonElement.prototype.updateByMouseWheel = function(event, delta)
{
  event.stopPropagation();

  var value = false;
  var mantis = 0;

  if (this.element.value.length == 0) {
    value = 0;

  } else if (this.element.value.search(/^ *[+-]?[0-9]+\.?[0-9]* *$/) != -1) {
    var m = this.element.value.match(/^ *[+-]?[0-9]+\.([0-9]+) *$/);
    if (m && m[1]) {
      mantis = m[1].length;
    }

    value = parseFloat(this.element.value);
    if (isNaN(value)) {
      value = false;
    }
  }

  if (value !== false) {

    value = value + delta;

    if (
      typeof(jQuery(this).mousewheel) != 'undefined'
      && typeof(jQuery(this).mousewheel.options) != 'undefined'
    ) {

      var mwBase = jQuery(this).mousewheel.options,
          min = parseFloat(mwBase.min),
          max = parseFloat(mwBase.max);

      if (typeof(mwBase.min) != 'undefined' && min > value) {
        value = min;
      } else if (typeof(mwBase.max) != 'undefined' && max < value) {
        value = max;
      }
    }

    value = mantis
      ? Math.round(value * Math.pow(10, mantis)) / Math.pow(10, mantis)
      : Math.round(value);

    var oldValue = this.element.value;
    this.element.value = value;

    if (jQuery(this.element).validationEngine('validate')) {
      this.element.value = oldValue;

    } else {
      this.$element.change();
      jQuery(this.element.form).change();

    }

    this.$element.removeClass('wrong-amount');
  }

  return false;
};

// Undo changes
CommonElement.prototype.undo = function()
{
  if (this.isChanged(true)) {
    if (isElement(this.element, 'input') && -1 != jQuery.inArray(this.element.type, ['checkbox', 'radio'])) {
      this.element.checked = this.element.initialValue;

    } else {
      this.element.value = this.element.initialValue;
    }
    this.$element.change();

    this.$element.trigger('undo');
  }
};

// Element is state watcher
CommonElement.prototype.isWatcher = function()
{
  return 'undefined' != typeof(this.element.selfSubmitting);
};

// Element is changed state watcher
CommonElement.prototype.isChangedWatcher = function()
{
  return this.isWatcher() && this.element.selfSubmitTO;
};

// Check - element changed or not
CommonElement.prototype.isChanged = function(onlyVisible)
{
  var result = false;

  if (!(onlyVisible && !this.isVisible()) && this.isSignificantInput()) {

    if (
      (isElement(this.element, 'input') && -1 != jQuery.inArray(this.element.type, ['text', 'password', 'hidden', 'file']))
      || isElement(this.element, 'select')
      || isElement(this.element, 'textarea')
    ) {
      if (isElement(this.element, 'select') && this.$element.prop('multiple')) {
        result = !this.isEqualArrayValues(this.element.initialValue, this.$element.val(), this.$element);

      } else {
        result = !this.isEqualValues(this.element.initialValue, this.element.value, this.$element);
      }

    } else if (isElement(this.element, 'input') && -1 != jQuery.inArray(this.element.type, ['checkbox', 'radio'])) {
      result = !this.isEqualValues(this.element.initialValue, !!this.element.checked, this.$element);
    }
  }

  return result;
};

// Check - element is significant or not
CommonElement.prototype.wasFilledOnce = function ()
{
  if (!this.wasFilled) {
    if (isElement(this.element, 'input') && -1 != jQuery.inArray(this.element.type, ['text', 'password', 'hidden', 'file'])) {
      this.wasFilled = !_.isEmpty(this.element.value) || this.isChanged(true);
    } else {
      this.wasFilled = true;
    }
  }

  return this.wasFilled;
};

// Check - element is significant or not
CommonElement.prototype.isSignificantInput = function ()
{
  return !this.$element.hasClass('not-significant');
};

// Check - element is significant or not
CommonElement.prototype.isRequired = function ()
{
  return this.element.className.indexOf('required') != -1;
};

// Check element old value and new valies - equal or not
CommonElement.prototype.isEqualValues = function(oldValue, newValue)
{
  return oldValue == newValue;
};

// Check array values and return true if they are equal
CommonElement.prototype.isEqualArrayValues = function(oldValue, newValue, element)
{
  var result = false;

  if (oldValue == null) {
    result = (oldValue == newValue);

  } else if (newValue != null && oldValue.length == newValue.length) {

    result = true;

    for (var i = 0; i < oldValue.length; i++) {
      if (oldValue[i] != newValue[i]) {
        result = false;
        break;
      }
    }
  }

  return result;
};

// Save element value as initial value
CommonElement.prototype.saveValue = function()
{
  this.element.initialValue = this.getCanonicalValue();
};

// Get canonical element value
CommonElement.prototype.getCanonicalValue = function()
{
  var result = null;

  if (
    (isElement(this.element, 'input') && -1 != jQuery.inArray(this.element.type, ['text', 'password', 'hidden', 'file']))
    || isElement(this.element, 'select')
    || isElement(this.element, 'textarea')
  ) {
    if (isElement(this.element, 'select') && this.$element.prop('multiple')) {
      result = this.$element.val();

    } else {
      result = this.element.value;
    }

  } else if (isElement(this.element, 'input') && -1 != jQuery.inArray(this.element.type, ['checkbox', 'radio'])) {
    result = this.element.checked;
  }

  return result;
};

// Prepare element before background submit
CommonElement.prototype.preprocessBackgroundSubmit = function()
{
  if (this.$element.hasClass('progress-mark-owner') && this.isVisible() && this.isChanged()) {
    this.markAsProgress();
  }

  if (!this.$element.prop('readonly')) {
    this.$element.prop('readonly', 'readonly');
    this.element.isTemporaryReadonly = true;
  }
};

// Prepare element after background submit
CommonElement.prototype.postprocessBackgroundSubmit = function()
{
  if (this.getForm() && this.getForm().isSaveValue(this)) {
    this.saveValue();
  }

  if (this.$element.hasClass('progress-mark-apply')) {
    this.unmarkAsProgress();
  }

  if (this.element.isTemporaryReadonly) {
    this.$element.removeProp('readonly');
    this.element.isTemporaryReadonly = null;
  }
};

// 'Skip current background submit' form event
CommonElement.prototype.skipCurrentBgSubmit = function()
{
  this.postprocessBackgroundSubmit();
};

CommonElement.prototype.linkWithCountry = function()
{
  var countryName = this.element.name.replace(/state/, 'country');
  var country = this.element.form.elements.namedItem(countryName);

  if (country && 'undefined' != typeof(window.CountriesStates)) {

    this.element.isFocused = false;

    jQuery(this.$element)
      .focus(function() {
        this.isFocused = true;
      })
      .blur(function() {
        this.isFocused = false;
      });

    var stateSwitcher = document.createElement('input');
    stateSwitcher.type = 'hidden';
    stateSwitcher.name = this.element.name.replace(/state/, 'is_custom_state');
    stateSwitcher.value = isElement(this.element, 'input') ? '1' : '';
    country.form.appendChild(stateSwitcher);
    new CommonElement(stateSwitcher);

    this.element.currentCountryCode = false;
    country.stateInput = this.element;

    var o = this;

    o.lastStateText = '';

    var replaceElement = function(type)
    {
      var inp = document.createElement(type);
      if (type == 'input') {
        inp.type = 'text';
      }
      inp.id = this.stateInput.id;
      inp.className = this.stateInput.className;
      inp.name = this.stateInput.name;
      inp.currentCountryCode = this.stateInput.currentCountryCode;

      var isFocused = this.stateInput.isFocused;

      jQuery(this.stateInput).replaceWith(inp);
      this.stateInput = inp;

      if (isFocused) {
        this.stateInput.focus();
      }

      jQuery(this.stateInput)
        .focus(function() {
          this.isFocused = true;
        })
        .blur(function() {
          this.isFocused = false;
        });

      o.bind(inp);
    };

    var change = function()
    {
      if (this.stateInput.currentCountryCode == this.value) {
        return true;
      }

      this.stateInput.currentCountryCode = this.value;

      if ('undefined' == typeof(CountriesStates[this.value])) {

        // As input box
        if (!isElement(this.stateInput, 'input')) {
          replaceElement.call(this, 'input');
          this.stateInput.value = o.lastStateText;
        }

        stateSwitcher.value = '1';

      } else {

        // As select box
        var previousSelected = null;
        if (isElement(this.stateInput, 'select')) {

          if (this.stateInput.options.length > 0 && this.stateInput.options[this.stateInput.selectedIndex].value !== '' ) {
            previousSelected = this.stateInput.options[this.stateInput.selectedIndex].value;
            jQuery('option', this.stateInput).remove();
          }

        } else {
          o.lastStateText = this.stateInput.value;
          replaceElement.call(this, 'select');
        }

        for (var i = 0; i < CountriesStates[this.value].length; i++) {
          var s = CountriesStates[this.value][i];
          this.stateInput.options[i] = new Option(s.state, s.state_code);

          if (previousSelected && previousSelected == s.state_code) {
            this.stateInput.options[i].selected = true;
            this.stateInput.selectedIndex = i;
          }
        }

        stateSwitcher.value = '';
      }
    };

    jQuery(country).change(change);

    change.call(country);
  }
};

// Prepare element with onclick-based location change
CommonElement.prototype.processLocation = function()
{
  if (
    this.$element.attr('onclick')
    && -1 !== this.$element.attr('onclick').toString().search(/\.location[ ]*=[ ]*['"].+['"]/)
  ) {
    var m = this.$element.attr('onclick').toString().match(/\.location[ ]*=[ ]*['"](.+)['"]/);
    this.$element
      .data('location', m[1])
      .removeAttr('onclick');
  }
};

// Toggle element activity
CommonElement.prototype.toggleActivity = function(condition)
{
  if (
    ('undefined' != typeof(condition) && condition)
    || ('undefined' == typeof(condition) && !this.isEnabled())
  ) {
    this.enable();

  } else {
    this.disable();
  }
};

// Check element activity
CommonElement.prototype.isEnabled = function()
{
  return !this.$element.hasClass('disabled');
};

// Disable element
CommonElement.prototype.disable = function()
{
  this.$element
    .addClass('disabled')
    .prop('disabled', 'disabled');
};

// Enable element
CommonElement.prototype.enable = function()
{
  this.$element
    .removeClass('disabled')
    .removeProp('disabled');
};

CommonElement.prototype.handleChange = function(event)
{
  var controlReadiness = this.element.form && this.element.form.commonController.controlReadiness;

  if (controlReadiness || this.$element.hasClass('validation-error')) {
    this.validate();
  }

  if (controlReadiness) {
    this.element.form.commonController.processReadiness();
  }
};

CommonElement.prototype.handleKeyUp = function(event)
{
  if (this.isSignificantInput()) {
    jQuery(this.element.form).change();
  }
};

CommonElement.prototype.handleValidationEngineResult = function(event, field, errorFound, prompText)
{
  if (errorFound) {
    jQuery('.formError').remove();

    if (!field.data('validation-silent')) {
      field.get(0).commonController.markAsInvalid(
        prompText.replace(/^\* /g, '').replace(/<br.>.*$/g, '')
      );
    }
  } else {
    field.get(0).commonController.unmarkAsInvalid();
  }

  field.data('validation-error', errorFound);
};

/**
 * Validators
 */

// E-mail
CommonElement.prototype.validateEmail = function()
{
  var re = new RegExp(
    "^[a-z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z](?:[a-z0-9-]*[a-z0-9])?$",
    'gi'
  );

  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea');

  return {
    status:  !apply || !this.element.value.length || this.element.value.search(re) !== -1,
    message: 'Enter a correct email',
    apply:   apply
  };
};

// Integer
CommonElement.prototype.validateInteger = function()
{
  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea');

  return {
    status:  !apply || !this.element.value.length || -1 != this.element.value.search(/^ *[0-9]+ */),
    message: 'Enter an integer',
    apply:   apply
  };
};

// Float
CommonElement.prototype.validateFloat = function()
{
  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea');

  var value = this.element.value.replace(/^ +/, '').replace(/ +$/, '');
  var sanitized = parseFloat(value);

  return {
    status:  !apply || !this.element.value.length || (!isNaN(sanitized)),
    message: 'Enter a number',
    apply:   apply
  };
};

// Positive number
CommonElement.prototype.validatePositive = function()
{
  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea');

  var value = parseFloat(this.element.value);

  return {
    status:  !apply || !this.element.value.length || (!isNaN(value) && 0 <= value),
    message: 'Enter a positive number',
    apply:   apply
  };
};

// Negative number
CommonElement.prototype.validateNegative = function()
{
  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea');

  var value = parseFloat(this.element.value);

  return {
    status:  !apply || !this.element.value.length || (!isNaN(value) && 0 >= value),
    message: 'Enter a negative number',
    apply:   apply
  };
};

// Non-zero number
CommonElement.prototype.validateNonZero = function()
{
  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea');

  var value = parseFloat(this.element.value);

  return {
    status:  !apply || !this.element.value.length || (!isNaN(value) && 0 != value),
    message: 'Zero cannot be used',
    apply:   apply
  };
};

// Range (min - max) number
CommonElement.prototype.validateRange = function()
{
  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea');

  var result = {
    status:  true,
    message: 'This field is required',
    apply:   apply
  };

  if (apply && this.element.value.length) {

    var value = parseFloat(this.element.value);

    if (isNaN(value)) {
      result.status = false;

    } else if (typeof(this.element.min) !== 'undefined' && this.element.min > value) {
      result.status = false;
      result.message = 'Field too small!';

    } else if (typeof(this.element.max) !== 'undefined' && this.element.max < value) {

      result.status = false;
      result.message = 'Field too big!';
    }
  }

  return result;
};

// Required field
CommonElement.prototype.validateRequired = function()
{
  return {
    status:  this.element.value !== null && 0 < this.element.value.length,
    message: 'Field is required!',
    apply:   true
  };
};

// Handlers mechanism

// Assign handlers
CommonElement.prototype.assignHandlers = function ()
{
  var o = this;

  jQuery.each(
    this.handlers,
    function (index, elm) {
      if (elm.canApply.call(o)) {
        elm.handler.call(o);
      }
    }
  );
};

// Handlers repository
CommonElement.prototype.handlers = [
  {
    canApply: function () {
      return this.$element.hasClass('watcher');
    },
    handler: CommonElement.prototype.markAsWatcher
  },
  {
    canApply: function () {
      return this.$element.hasClass('field-state') && this.$element.hasClass('linked');
    },
    handler: CommonElement.prototype.linkWithCountry
  },
  {
    canApply: function () {
      return this.$element.hasClass('column-switcher') && 0 < this.$element.parents('th').length;
    },
    handler: CommonElement.prototype.markAsColumnSwitcher
  },
  {
    canApply: function () {
      return this.$element.hasClass('wheel-ctrl');
    },
    handler: CommonElement.prototype.markAsWheelControlled
  },
  {
    canApply: function () {
      return this.$element.is('textarea.resizeble-txt');
    },
    handler: function() {
      var min = this.$element.data('min-size-height') || this.$element.height();
      var max = this.$element.data('max-size-height');

      this.$element.TextAreaExpander(min, max);
    }
  },
  {
    canApply: function () {
      return this.$element;
    },
    handler: function() {
      this.$element.blur(
        _.bind(
          function() {
            this.triggerVent('blur', this);
          },
          this
        )
      );
    }
  },
  {
    canApply: function () {
      return this.$element;
    },
    handler: function() {
      this.$element.focus(
        _.bind(
          function() {
            this.triggerVent('focus', this);
          },
          this
        )
      );
    }
  }
];

CommonElement.prototype.hasDependency = function ()
{
  return this.$element.closest('.has-dependency').length > 0;
};

CommonElement.prototype.getDependency = function ()
{
  return this.hasDependency()
    ? core.getCommentedData(this.$element.closest('.has-dependency'), 'dependency')
    : {};
};

CommonElement.prototype.hideByDependency = function ()
{
  this.$element.closest('.has-dependency').addClass('hidden');
};

CommonElement.prototype.showByDependency = function ()
{
  this.$element.closest('.has-dependency').removeClass('hidden');
};

// Autostart
core.autoload(CommonForm);

// Common controller as jQuery plugin
(function ($) {
  jQuery.fn.commonController = function(property) {
    var args = Array.prototype.slice.call(arguments, 1);

    this.each(
      function() {
        if ('undefined' == typeof(this.commonController)) {
          if (isElement(this, 'form')) {
            new CommonForm(this);

          } else if (
            isElement(this, 'input')
            || isElement(this, 'select')
            || isElement(this, 'textarea')
            || isElement(this, 'button')
          ) {
            new CommonElement(this);
          }
        }

        if (
          'undefined' !== typeof(this.commonController)
          && 'undefined' !== typeof(this.commonController[property])
        ) {
          if ('function' == typeof(this.commonController[property])) {
            this.commonController[property].apply(this.commonController, args);

          } else {
            this.commonController[property] = args[0];
          }
        }
      }
    );

    return this;
  };

})(jQuery);
