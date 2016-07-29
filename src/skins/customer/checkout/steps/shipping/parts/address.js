/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common address view
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CheckoutAddressView(base)
{
  var args = Array.prototype.slice.call(arguments, 0);
  if (!base) {
    args[0] = jQuery(this.addressBoxPattern).eq(0);
  }

  core.bind('updateCart', _.bind(this.handleUpdateCart, this))
    .bind('loginExists', _.bind(this.handleLoginExists, this));

  this.bind('local.postprocess', _.bind(this.assignHandlers, this))
    .bind('local.loaded', _.bind(this.triggerChange, this));

  core.bind('checkout.common.readyCheck', _.bind(this.handleCheckoutReadyCheck, this));

  CheckoutAddressView.superclass.constructor.apply(this, args);
}

extend(CheckoutAddressView, ALoadable);

CheckoutAddressView.preventSameAddressChange = false;

CheckoutAddressView.prototype.addressBoxPattern = null;

CheckoutAddressView.prototype.submitPressedTO = null;

CheckoutAddressView.prototype.submitPressedTTL = 2000;

CheckoutAddressView.prototype.blockLoadByUpdateCart = false;

CheckoutAddressView.prototype.loadByUpdateCartTO = null;
// Shade widget
CheckoutAddressView.prototype.shadeWidget = true;

// Update page title
CheckoutAddressView.prototype.updatePageTitle = false;

// Widget target
CheckoutAddressView.prototype.widgetTarget = 'checkout';

CheckoutAddressView.prototype.loadIntoSubmit = false;

CheckoutAddressView.prototype.createProfileError = false;

CheckoutAddressView.prototype.SAVE_ONLY_EMAIL       = 1;
CheckoutAddressView.prototype.SAVE_ONLY_GUEST_AGREE = 2;

CheckoutAddressView.prototype.assignHandlers = function(event, state)
{
  if (state.isSuccess) {

    this.base.find('a.address-book').each(
      function() {
        var elm = jQuery(this);
        if (elm.attr('onclick')) {
          var m = elm.attr('onclick').toString().match(/\.location[ ]*=[ ]*['"](.+)['"]/);
          elm.data('location', m[1]);
          elm.removeAttr('onclick');
        }
      }
    );

    var form = this.getForm().get(0);

    if (form) {
      form.commonController
        .enableBackgroundSubmit()
        .bind('local.submit.success', _.bind(this.handleFormSubmit, this))
        .bind('local.submit.error', _.bind(this.handleFormError, this))
        .bind('local.submit.success', _.bind(this.triggerChange, this))
        .bind('local.blur', _.bind(this.saveFormCallback, this))
        .bind('local.submit.prevalidate', _.bind(this.handleFormPreValidateSubmit, this))
        .bind('local.filterElements', _.bind(this.filterInvisible, this))
        .bind('local.ready', _.bind(this.triggerChange, this))
        .bind('local.unready', _.bind(this.triggerChange, this));

      var elms = form.getElements();
      elms.keyup(_.bind(this.handleElementKeypress, this))
        .each(_.bind(this.assignElementHandlers, this));
      elms.filter('input:checkbox')
        .change(_.bind(this.handleElementKeypress, this));
      elms.filter('select')
        .change(_.bind(this.handleElementKeypress, this));

      var field = form.getElements().filter('.field-password');
      if (field.length) {
        field.get(0).commonController.bind('local.validate', _.bind(this.handlePasswordValidate, this));
      }

      jQuery('#create_profile', form).change(_.bind(this.handleCreateProfile, this));
      jQuery('#email', form)
        .change(_.bind(this.handleEmailChange, this))
        .bind('invalid', _.bind(this.handleEmailInvalid, this))
        .bind('valid', _.bind(this.handleEmailValid, this));

      this.base.find('a.address-book').click(_.bind(this.openAddressBook, this));

      UpdateStatesList(form);

      this.base.find('.create-warning a.continue').click(_.bind(this.handleContinueLink, this));

      setTimeout(
        function() {
          form.commonController.switchControlReadiness();
        },
        300
      );
    }
  }
};

CheckoutAddressView.prototype.assignElementHandlers = function(idx, element)
{
  element.commonController.bind('local.markAsInvalid', _.bind(this.handleMarkElementAsInvalid, this));
  element.commonController.bind('local.unmarkAsInvalid', _.bind(this.handleUnmarkElementAsInvalid, this));
};

CheckoutAddressView.prototype.handleFormPreValidateSubmit = function(event, widget)
{
  if (
    widget.form.getElements().filter('#email').get(0)
    && widget.form.getElements().filter('#email').get(0).commonController.isChanged()
  ) {
    this.getForm().find('.item-password').addClass('hidden');

    this.unmarkCreateProfile(widget);
  }
};

CheckoutAddressView.prototype.unmarkCreateProfile = function(widget)
{
  var createProfile = jQuery("#password", widget.form).get(0);

  jQuery('#create_profile', widget.form).prop('checked', false);

  if (createProfile && jQuery(createProfile).hasClass('validation-error')) {
    createProfile.commonController.unmarkAsInvalid();
  }

  this.getForm().get(0).commonController.isReady = true;
};

CheckoutAddressView.prototype.handleFormSubmit = function()
{
  this.createProfileError = false;

  this.triggerVent('submitted', this);

  if (this.loadIntoSubmit) {
    this.loadIntoSubmit = false;
    this.load();
  }
};

CheckoutAddressView.prototype.triggerChange = function()
{
  var state = {};
  core.trigger('checkout.common.getState', state)

  if (state.result != this.getForm().get(0).validate(true)) {
    core.trigger('checkout.common.anyChange', this);
  }
};

CheckoutAddressView.prototype.handleFormError = function()
{
  core.microhandlers.runAll(this.base);
};

CheckoutAddressView.prototype.handleEmailChange = function(event)
{
  this.unmarkCreateProfile(event.target);

  var elm = this.getForm().find('#email').get(0);
  if (this.getForm().find('#password').length && elm.commonController.isChanged()) {
    this.saveForm(true);
  }

  this.base.find('a.log-in').data('login', elm.value);

  // We send the change event to the checkout widget
  core.trigger('checkout.common.anyChange', this);
};

CheckoutAddressView.prototype.handleMarkElementAsInvalid = function(event, ctrl)
{
  ctrl.$element.parents('li').eq(0).addClass('error');
};

CheckoutAddressView.prototype.handleUnmarkElementAsInvalid = function(event, ctrl)
{
  ctrl.$element.parents('li').eq(0).removeClass('error');
};

CheckoutAddressView.prototype.handleCheckoutReadyCheck = function(event, state)
{
  state.result = this.getForm().get(0).validate(state.supressErrors)
    && state.result;

  state.blocked = this.getForm().get(0).isBgSubmitting
    || this.getForm().get(0).commonController.isChanged()
    || this.isLoading
    || state.blocked;
};

CheckoutAddressView.prototype.handleContinueLink = function()
{
  this.getForm().find('[name="guest_agree"]').val(1);
  this.saveForm(this.SAVE_ONLY_GUEST_AGREE);

  return false;
};

CheckoutAddressView.prototype.handleElementKeypress = function(event)
{
  if (this.submitPressedTO) {
    clearTimeout(this.submitPressedTO);
    this.submitPressedTO = null;
  }

  this.submitPressedTO = setTimeout(
    _.bind(this.saveFormCallback, this),
    this.submitPressedTTL
  );
};

CheckoutAddressView.prototype.handleCreateAddress = function()
{
  if (this.loadByUpdateCartTO) {
    clearTimeout(this.loadByUpdateCartTO);
    this.loadByUpdateCartTO = null;
  }

  this.blockLoadByUpdateCart = true;
};

CheckoutAddressView.prototype.handleCreateProfile = function(event)
{
  var password = this.getForm().find('.item-password');

  if (this.getForm().find('#create_profile:checked').length) {
    password.removeClass('hidden');

  } else {
    password.addClass('hidden');
    this.unmarkCreateProfile(event.target);
  }

  core.trigger('checkout.common.anyChange', this);

  return true;
};

CheckoutAddressView.prototype.handlePasswordValidate = function(event, state)
{
  if (
    state.result
    && state.widget.$element.is(':visible')
    && !state.widget.$element.val()
  ) {

    // Visible and empty - error
    state.result = false;
    if (!state.silent) {
      state.widget.markAsInvalid(core.t('Field is required!'), 'password');
    }

  } else if (
    !state.result
    && !state.widget.$element.is(':visible')
  ) {

    // Invisible and has error - valid
    state.result = true;
  }
};

CheckoutAddressView.prototype.handleLoginExists = function(event, data)
{
  var box = this.base.find('li.item-email');

  box.removeClass('create-profile-warning')
    .removeClass('create-profile-note')
    .removeClass('allow-create-profile');

  if (data.value) {
    if (data.agree) {
      box.addClass('create-profile-note');
      box.find('[name="guest_agree"]').val(1);

    } else {
      box.addClass('create-profile-warning');
      box.find('[name="guest_agree"]').val(0);
    }

  } else {
    box.addClass('allow-create-profile');
  }
};

CheckoutAddressView.prototype.handleEmailInvalid = function(event)
{
  this.getForm().find('.item-email').addClass('invalid');
};

CheckoutAddressView.prototype.handleEmailValid = function(event)
{
  this.getForm().find('.item-email').removeClass('invalid');
};


// Open Address book popup
CheckoutAddressView.prototype.openAddressBook = function(event, elm)
{
  event.stopPropagation();

  var elm = jQuery(event.target);
  if (!elm.is('button') && elm.parents('button').length) {
    elm = elm.parents('button').eq(0);
  }

  popup.load(elm.eq(0));

  return false;
};

CheckoutAddressView.prototype.getForm = function()
{
  return this.base.find('form');
};

CheckoutAddressView.prototype.load = function()
{
  if (this.getForm().get(0).isBgSubmitting) {
    this.loadIntoSubmit = true;

  } else {
    CheckoutAddressView.superclass.load.apply(this, arguments);
  }
};

CheckoutAddressView.prototype.saveFormCallback = function()
{
  CheckoutAddressView.preventSameAddressChange = true;
  setTimeout(function () { CheckoutAddressView.preventSameAddressChange = false;}, 500);

  this.saveForm();
};

CheckoutAddressView.prototype.saveForm = function(saveState)
{
  var form = this.getForm().get(0);
  var onlyEmail = this.SAVE_ONLY_EMAIL === saveState;
  var onlyGuestAgree = this.SAVE_ONLY_GUEST_AGREE === saveState;

  if ((form.commonController.isChanged() || this.isNeedRecheck()) && !form.isBgSubmitting) {
    var same_address = jQuery(form).find('#same_address').get(0);
    if (!onlyEmail && !onlyGuestAgree && form.validate(true)) {

      // Save form
      this.triggerVent('formSubmitFull', this);
      jQuery(form).submit();

    } else if (onlyGuestAgree) {

      // Save only guest agree
      var handler = _.bind(this.filterGuestAgreeFields, this);
      form.commonController.bind('local.filterElements', handler);
      this.triggerVent('formSubmitGuestAgree', this);
      jQuery(form).submit();
      form.commonController.unbind('local.filterElements', handler);


    } else if (
      form.elements.namedItem('email')
      && (
        form.elements.namedItem('email').commonController.isChanged()
        || (form.elements.namedItem('create_profile') && form.elements.namedItem('create_profile').commonController.isChanged())
        || this.isNeedRecheck()
      )
      && form.elements.namedItem('email').commonController.validate(true)
    ) {

      // Save only email
      form.commonController.getElements().filter('#email').addClass('progress-mark-owner');
      var handler = _.bind(this.filterAddressFields, this);
      form.commonController.bind('local.filterElements', handler);
      this.triggerVent('formSubmitEmail', this);
      jQuery(form).submit();
      form.commonController.unbind('local.filterElements', handler);
      form.commonController.getElements().filter('#email').removeClass('progress-mark-owner');

    } else if (
        same_address
        && same_address.commonController.isChanged()
        && same_address.commonController.validate(true)
    ) {

      // Same address only
      var handler = _.bind(this.filterSameAddressField, this);
      form.commonController.bind('local.filterElements', handler);
      this.triggerVent('formSubmitSameAddress', this);
      jQuery(form).submit();
      form.commonController.unbind('local.filterElements', handler);
    }
  }
};

CheckoutAddressView.prototype.isNeedRecheck = function()
{
  return this.createProfileError && !this.getForm().find('#create_profile').hasClass('server-validation-error');
};

CheckoutAddressView.prototype.filterAddressFields = function(event, data)
{
  data.list = data.list.filter(
    function() {
      return 'hidden' == this.type || -1 == this.name.search(/Address|password/);
    }
  );
};

CheckoutAddressView.prototype.filterSameAddressField = function(event, data)
{
  data.list = data.list.filter(
    function() {
      return 'hidden' == this.type || 'same_address' == this.name;
    }
  );
};

CheckoutAddressView.prototype.filterGuestAgreeFields = function(event, data)
{
  data.list = data.list.filter(
    function() {
      return 'hidden' == this.type
        || 'guest_agree' == this.name
        || 'email' == this.name;
    }
  );
};

CheckoutAddressView.prototype.filterInvisible = function(event, data)
{
  data.list = data.list.filter(
    function() {
      return 'hidden' === this.type
        || 'password' === this.type
        || jQuery(this).is(':visible')
        || (-1 != this.name.search(/custom_state|state_id/) && jQuery(this).parents('.address-visible,.step-shipping-address').length);
    }
  );
};

