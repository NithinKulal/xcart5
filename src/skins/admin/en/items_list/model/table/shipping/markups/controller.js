/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping markups controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ShippingMarkupItemsList(base)
{
  var self = this;

  this.bind('local.line.new.add', function () {
    this.removeAddContainer();
    this.updateAddContainer();
    this.checkRemoveButton();
  });

  this.bind('local.line.new.remove', function () {
    this.removeAddContainer();
    this.updateAddContainer();
    this.checkRemoveButton();
  });

  this.bind('local.initialize', function () {
    if (null === this.listFooter) {
      this.listFooter = jQuery('.list-footer', this.container).clone(true, true).find('button').removeAttr('onclick').end();
    }

    if (jQuery('.create tr:visible', this.container).length === 0
        && jQuery('.lines tr', this.container).length === 0
    ) {
      this.showCreateLine();
    } else {
      this.removeAddContainer();
      this.updateAddContainer();
    }

    var tableType = this.container.closest('form').find('#tabletype');
    this.setTableType(tableType.val());
    tableType.change(function () {
      self.setTableType(jQuery(this).val());
    });

    var shippingZone = this.container.closest('form').find('#shippingzone');
    if (this.container.closest('form').find('[name="methodId"]').val()) {
      shippingZone.change(function () {
        self.setURLParam('pageId', '1');
        self.process('shippingZone', jQuery(this).val());
      });
    }

    var formula = this.container.find('.cell.formula');
    formula.each(function () {
      self.formulaHandler(jQuery(this));
    });
  });

  TableItemsList.apply(this, arguments);
}

extend(ShippingMarkupItemsList, TableItemsList);

ShippingMarkupItemsList.prototype.reassign = function()
{
  this.initialize(this.params.cell, this.params.urlparams, this.params.urlajaxparams);
};

ShippingMarkupItemsList.prototype.listFooter = null;

ShippingMarkupItemsList.prototype.removeAddContainer = function () {
  jQuery('.list-footer', this.container).remove();
};

ShippingMarkupItemsList.prototype.updateAddContainer = function () {
  var lastVisibleRowActions = jQuery('tr:not(.create-tpl).line:last .actions.right .cell', this.container);
  if (!jQuery('.list-footer', lastVisibleRowActions).length) {
    this.listFooter.clone(true, true).prependTo(lastVisibleRowActions);
  }
};

ShippingMarkupItemsList.prototype.hideCreateRemoveButton = function () {
  jQuery('.create .actions.right .separator', this.container).css('visibility', 'hidden');
  jQuery('.create .actions.right .remove-wrapper', this.container).css('visibility', 'hidden');
};

ShippingMarkupItemsList.prototype.showCreateRemoveButton = function () {
  jQuery('.create .actions.right .separator', this.container).css('visibility', 'visible');
  jQuery('.create .actions.right .remove-wrapper', this.container).css('visibility', 'visible');
};


ShippingMarkupItemsList.prototype.checkRemoveButton = function () {
  if (jQuery('.create tr:not(.create-tpl)', this.container).length === 1
    && jQuery('.lines tr', this.container).length === 0
  ) {
    this.hideCreateRemoveButton();

  } else {
    this.showCreateRemoveButton();
  }
};

ShippingMarkupItemsList.prototype.setTableType = function (value) {
  var weight = jQuery('.cell.weightRange', this.container).hide();
  var subtotal = jQuery('.cell.subtotalRange', this.container).hide();
  var items = jQuery('.cell.itemsRange', this.container).hide();

  var markupFlat = jQuery('.cell.markup_flat', this.container).toggleClass('break', false);

  switch (value) {
    case 'WSI':
      weight.show();
      subtotal.show();
      items.show();

      markupFlat.toggleClass('break', true);
      break;

    case 'W':
      weight.show();
      break;

    case 'S':
      subtotal.show();
      break;

    case 'I':
      items.show();
      break;
  }
};

ShippingMarkupItemsList.prototype.showCreateLine = function () {
  jQuery('button.create-inline', this.container.closest('form')).click();
};

ShippingMarkupItemsList.prototype.formulaHandler = function (element) {

  var wrapper = element.closest('tr.line');

  var flatInputHandler      = this.creteFormulaElementHandler('flat-rate');
  var perItemInputHandler   = this.creteFormulaElementHandler('items');
  var percentInputHandler   = this.creteFormulaElementHandler('subtotal');
  var perWeightInputHandler = this.creteFormulaElementHandler('weight');

  var flatInput      = jQuery('.cell.markup_flat input', wrapper).change(flatInputHandler);
  var perItemInput   = jQuery('.cell.markup_per_item input', wrapper).change(perItemInputHandler);
  var percentInput   = jQuery('.cell.markup_percent input', wrapper).change(percentInputHandler);
  var perWeightInput = jQuery('.cell.markup_per_weight input', wrapper).change(perWeightInputHandler);

  var handler = _.bind(function () {
    _.bind(flatInputHandler, flatInput.get(0))();
    _.bind(perItemInputHandler, perItemInput.get(0))();
    _.bind(percentInputHandler, percentInput.get(0))();
    _.bind(perWeightInputHandler, perWeightInput.get(0))();
  }, this);
  handler();
};

ShippingMarkupItemsList.prototype.setFormulaElementValue = function (element, value) {
  if (isNaN(value) || 0 == value) {
    element.hide();
  } else {
    element.show();
    jQuery('.part-value', element).text(value);
  }
};

ShippingMarkupItemsList.prototype.creteFormulaElementHandler = function (elementClass) {
  var self = this;
  return function () {
    var wrapper = jQuery(this).closest('tr.line');
    var element = wrapper.find('.formula .' + elementClass);
    var value = parseFloat(jQuery(this).val());

    jQuery('.formula .plus', wrapper).show();
    self.setFormulaElementValue(element, value);
    if (jQuery('.formula > span:visible', wrapper).not('.free').length) {
      jQuery('.formula > span.free', wrapper).hide();
    } else {
      jQuery('.formula > span.free', wrapper).show();
    }
    jQuery('.formula > span:visible:last .plus', wrapper).hide();
  }
};

jQuery(function () {
  var infinitySign = jQuery('<div />').html('&#x221E;').text();

  jQuery('.range-wrapper input.with-infinity').live('keyup', function () {
    var value = jQuery(this).val();
    if (value === '999999999.00' || value === '999999999' || value === '') {
      jQuery(this).val(infinitySign);
    } else if (value !== infinitySign && value.search(infinitySign) !== -1) {
      jQuery(this).val(value.replace(infinitySign, ''));
    }
  });
});
