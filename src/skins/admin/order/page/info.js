/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order info form controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function OrderInfoForm()
{
  this.base = jQuery(this.formContainer);

  this.initialize();
}

OrderInfoForm.prototype.formContainer = '.order-info form.order-operations';

OrderInfoForm.prototype.base = null;

OrderInfoForm.prototype.e = null;

OrderInfoForm.prototype.elementsState = null;

OrderInfoForm.prototype.recalculated = null;

OrderInfoForm.prototype.forbidden = false;

OrderInfoForm.prototype.initialize = function()
{
  this.e = parseInt(jQuery('.order-info').data('e'))

  this.base.get(0).commonController.switchControlReadiness(true);
  this.base.get(0).commonController.submitOnlyChanged = true;
  this.base.get(0).commonController.isSaveValue = function(element)
  {
    return false;
  }

  this.base.get(0).commonController.preprocessBackgroundSubmit = function()
  {
  }

  this.base.find('.order-shippingId select').change(_.bind(this.handleChangeShippingMethod, this));

  this.base.find('.sticky-panel .sendNotification label')
    .addClass('disabled');
  this.base.find('.sticky-panel .sendNotification :checkbox')
    .addClass('disabled')
    .attr('disabled', 'disabled');

  this.base.change(_.bind(this.handleFormChange, this));

  this.base.find('.btn.recalculate')
    .removeAttr('onclick')
    .removeProp('onclick')
    .click(_.bind(this.handleRecalculate, this));

  this.base.find('.totals input.price').bind(
    'input',
    _.bind(this.handleTotalChange, this)
  );
  this.base.find('.totals input.price').change(_.bind(this.handleTotalChange, this));

  this.base.find('.totals .restore-orig-price a').click(_.bind(this.handleRestore, this));

  core.bind('stickypanel.check.button.enable', _.bind(this.handleCheckEnableButton, this));
  core.bind('stickypanel.check.button.disable', _.bind(this.handleCheckDisableButton, this));
  core.bind('stickypanel.markAsChanged', _.bind(this.handleUpdateButtonsState, this));
  core.bind('stickypanel.unmarkAsChanged', _.bind(this.handleUpdateButtonsState, this));

  core.bind('recalculateOrder', _.bind(this.handleRecalculateOrderEvent, this));

  var table = this.base.find('.items-list.tracking-number').get(0);
  if (table) {
    setTimeout(
      _.bind(
        function() {
          table.itemsListController.bind('local.line.new.remove', _.bind(this.handleRemoveTracking, this));
        },
        this
      ),
      100
    );
  }

  core.bind(
    'afterPopupPlace',
    function() {
      UpdateStatesList();
    }
  );

  core.bind('list.model.table.order.items.newLineCreated', _.bind(this.handleCreateNewLine, this));

  this.setRecalculatedValues();
}

OrderInfoForm.prototype.handleFormChange = function(event)
{
  this.recalculated = !this.isChangedAfterLastRecalculate();
}

OrderInfoForm.prototype.isChangedAfterLastRecalculate = function()
{
  var changedCount = this.base.get(0).commonController.getElements()
    .filter(
      _.bind(
        function(index, element) {
          return this.isElementAffectRecalculate(element);
        },
        this
      )
    )
    .filter(
      _.bind(
        function(index, element) {
          return this.isElementChangedAfterLastRecalculate(element);
        },
        this
      )
    )
    .length;

  if (this.getCreatedOrderItemsCount() != this.recalculatedCreatedOrderItemsCount) {
    changedCount++;
  };

  return changedCount > 0;
}

OrderInfoForm.prototype.getCreatedOrderItemsCount = function(){
  return jQuery(this.base).find('.items-list-table.order-items .list .create .create-line').length;
}

OrderInfoForm.prototype.isElementChangedAfterLastRecalculate = function(element)
{
  return typeof(element.recalculatedValue) != 'undefined'
    && element.recalculatedValue != element.commonController.getCanonicalValue();
}

OrderInfoForm.prototype.handleRecalculate = function(event)
{
  var form = this.base.get(0);

  var action = form.elements.namedItem('action');
  var old = action.value;
  action.value = 'recalculate';
  var result = form.commonController.submitBackground(_.bind(this.handleRecalculateSubmit, this));
  action.value = old;

  if (result) {
    this.recalculatedCreatedOrderItemsCount = this.getCreatedOrderItemsCount();
    this.shade();
  }

  return false;
}

OrderInfoForm.prototype.handleRecalculateSubmit = function(XMLHttpRequest, textStatus, data, isValid)
{
  this.unshade();

  return true;
}

OrderInfoForm.prototype.handleRecalculateOrderEvent = function(event, data)
{
  this.updateTotals(data);

  this.forbidden = 'undefiend' == typeof(data.forbidden) ? false : data.forbidden;
  this.recalculated = !this.forbidden;

  if (this.recalculated) {
    this.setRecalculatedValues();
  }

  this.base.change();
}

OrderInfoForm.prototype.setRecalculatedValues = function()
{
  this.base.get(0).commonController.getElements()
    .each(_.bind(this.setRecalculatedValue, this));
}

OrderInfoForm.prototype.isElementAffectRecalculate = function(element)
{
  return !jQuery(element).hasClass('not-affect-recalculate')
    && jQuery(element).parents('.tracking-number').length == 0
    && (-1 == element.name.search(/auto.surcharges./) || element.value == '1')
    && jQuery(element).parents('.popover-content').length == 0
    && jQuery(element).parents('.payment-method-data').length == 0;
}

OrderInfoForm.prototype.setRecalculatedValue = function(index, element)
{
  element.recalculatedValue = element.commonController.getCanonicalValue();
}

OrderInfoForm.prototype.handleTotalChange = function(event)
{
  var box = jQuery(event.currentTarget).parents('li.order-modifier').eq(0);

  if (event.currentTarget.commonController.isChanged() && !box.hasClass('ctrl-manual')) {
    box.removeClass('ctrl-auto').addClass('ctrl-manual');
    box.find('.restore-orig-price :input').val('');
  }
}

OrderInfoForm.prototype.handleRestore = function(event)
{
  var box = jQuery(event.currentTarget).parents('.order-modifier');
  if (box.hasClass('ctrl-manual')) {
    box.removeClass('ctrl-manual').addClass('ctrl-auto');
    box.find('.restore-orig-price :input').val('1');

  } else {
    box.removeClass('ctrl-auto').addClass('ctrl-manual');
    box.find('.restore-orig-price :input').val('');
  }

  box.parents('form').eq(0).change();

  core.trigger('order.modifier.changed', { modifier: box });

  return false;
}

OrderInfoForm.prototype.handleChangeShippingMethod = function(event)
{
  var elm = jQuery(event.currentTarget);

  this.base.find('.order-shippingId select').each(
    function() {
      var elm2 = jQuery(this);
      if (elm2.val() != elm.val()) {
        elm2.val(elm.val());
        elm2.parents('.inline-field').get(0).saveField();
      }
    }
  );
}

OrderInfoForm.prototype.updateTotals = function(data)
{
  _.each(data, _.bind(this.updateTotalElement, this));
}

OrderInfoForm.prototype.updateTotalElement = function(value, name)
{
  var result = true;

  var base;
  switch (name) {
    case 'subtotal':
      base = this.base.find('.totals .subtotal');
      this.setPriceElement(base.find('.value'), value);
      break;

    case 'total':
      base = this.base.find('.totals .total');
      this.setPriceElement(base.find('.value'), value);
      break;

    case 'modifiers':
      _.each(value, _.bind(this.updateModifierElement, this));
      break;

    default:
      result = false;
  }

  if (base) {
    if (0 > value) {
      base.addClass('negative');

    } else {
      base.removeClass('negative');
    }
  }

  return result;
}

OrderInfoForm.prototype.updateModifierElement = function(value, code)
{
  var pattern = '.totals .order-modifier.ctrl-auto .surcharge-' + code.replace(/\./g, '\\.');
  var box = jQuery(pattern).eq(0);

  if (box.length) {
    var str = core.numberToString(value, '.', '', parseInt(jQuery('.order-info').data('e')));
    var input = box.find('input.price');
    if (input.length) {
      input.val(str);
      input.get(0).commonController.saveValue();
    }
    box.get(0).saveField();
  }
}

OrderInfoForm.prototype.setPriceElement = function(element, value)
{
  setPriceElement(element, value, this.e);
}

OrderInfoForm.prototype.shade = function()
{
  assignWaitOverlay(this.getShadeBase());
}

OrderInfoForm.prototype.unshade = function()
{
  unassignWaitOverlay(this.getShadeBase(), true);
}

OrderInfoForm.prototype.getShadeBase = function()
{
  return jQuery('.order-info');
}

OrderInfoForm.prototype.handleUpdateButtonsState = function(event, data)
{
  this.elementsState = this.aggregateElementsState();
}

OrderInfoForm.prototype.aggregateElementsState = function()
{
  var state = {
    needRecalculate: false,
    needSave:        false
  };
  var ctrl = this.base.get(0).commonController;

  if (ctrl.validate(true)) {
    ctrl.getElements().each(
      _.bind(
        function(index, element) {
          this.processElementState(element, state);
        },
        this
      )
    );

    if (this.recalculatedCreatedOrderItemsCount
      && this.getCreatedOrderItemsCount() != this.recalculatedCreatedOrderItemsCount
    ) {
      if (!state.needRecalculate) {
        state.needRecalculate = true;
        state.needSave = false;
      }
    };

    if (!state.needRecalculate && !state.needSave && this.recalculated && !this.forbidden && this.base.get(0).commonController.isChanged()) {
      state.needSave = true;
    }
  }

  return state;
}

OrderInfoForm.prototype.processElementState = function(element, state)
{
  if (this.isElementChangedAfterLastRecalculate(element)) {
    var needRecalculate = this.isNeedElementRecalculate(element);
    var needSave = this.isNeedElementSave(element);
    if (!state.needRecalculate && needRecalculate) {
      state.needRecalculate = true;
      state.needSave = false;

    } else if (!state.needRecalculate && !state.needSave && needSave) {
      state.needSave = true;
    }
  }
}

OrderInfoForm.prototype.isNeedElementRecalculate = function(element)
{
  return this.isElementAffectRecalculate(element);
}

OrderInfoForm.prototype.isNeedElementSave = function(element)
{
  return (!this.isElementAffectRecalculate(element) || this.recalculated)
    && !jQuery(element).hasClass('not-saved');
}

OrderInfoForm.prototype.handleCheckEnableButton = function(event, state)
{
  if (this.canEnableButton(state.button)) {
    state.state = true;
    state.inverse = false;

  } else {
    state.state = false;
    state.inverse = true;
  }

  if (jQuery(state.button).hasClass('submit')) {
    if (state.state) {
      this.enableSendNotification();

    } else if (state.inverse) {
      this.disableSendNotification();
    }
  }
}

OrderInfoForm.prototype.handleCheckDisableButton = function(event, state)
{
  if (!this.canEnableButton(state.button)) {
    state.state = true;
    state.inverse = false;

  } else {
    state.state = false;
    state.inverse = true;
  }

  if (jQuery(state.button).hasClass('submit')) {
    if (state.state) {
      this.disableSendNotification();

    } else if (state.inverse) {
      this.enableSendNotification();
    }
  }
}

OrderInfoForm.prototype.canEnableButton = function(button)
{
  return (this.elementsState.needRecalculate && jQuery(button).hasClass('recalculate'))
    || (this.elementsState.needSave && !jQuery(button).hasClass('recalculate'));
}

OrderInfoForm.prototype.enableSendNotification = function()
{
  // Send notification
  this.base.find('.sticky-panel .sendNotification label')
    .removeClass('disabled');
  this.base.find('.sticky-panel .sendNotification :checkbox')
    .removeClass('disabled')
    .removeAttr('disabled');

  // Button title
  this.base.find('.sticky-panel .panel-cell.save').removeAttr('title');
  this.base.find('.sticky-panel .panel-cell.save').removeClass('disabled');
}

OrderInfoForm.prototype.disableSendNotification = function()
{
  // Send notification
  this.base.find('.sticky-panel .sendNotification label')
    .addClass('disabled');
  this.base.find('.sticky-panel .sendNotification :checkbox')
    .addClass('disabled')
    .attr('disabled', 'disabled');

  // Button title
  var base = this.base.find('.sticky-panel .panel-cell.save');
  base.attr('title', base.data('title'));
  base.addClass('disabled');
}

OrderInfoForm.prototype.handleCreateNewLine = function(event, data)
{
  jQuery(data.line).find(':input')
    .each(_.bind(this.setRecalculatedValue, this));
}

OrderInfoForm.prototype.handleRemoveTracking = function(event)
{
  this.base.change();
}

core.autoload(OrderInfoForm);
