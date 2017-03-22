/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Items list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function OrderItemsList(base)
{
  TableItemsList.apply(this, arguments);
}

extend(OrderItemsList, TableItemsList);

OrderItemsList.prototype.e = null;
OrderItemsList.prototype.currentPopover = null;
OrderItemsList.prototype.lastOptionContainer = null;

OrderItemsList.prototype.initialize = function(elem, urlparams, urlajaxparams)
{
  var result = TableItemsList.prototype.initialize.apply(this, arguments);

  if (result) {
    this.e = parseInt(jQuery('.order-info').data('e'));

    this.container.find('tbody.lines tr.line').each(_.bind(this.processLine, this));

    jQuery('body').click(_.bind(this.handleBodyClick, this));

    this.bind('local.line.new.remove', _.bind(this.handleNewLineRemove, this));

    core.bind('order.item.changed', _.bind(this.handleItemChanged, this));

    core.bind('order.items.changed', _.bind(this.handleItemsChanged, this));

    core.bind('model-selector.product.selected', _.bind(this.handleProductModelSelect, this));

    core.bind('itemListNewItemCreated', _.bind(this.handleNewItemCreated, this));

    core.bind('popup.close', _.bind(this.handlePopupClose, this))

    core.bind('afterpopupplace', _.bind(this.handleAfterPopupPlace, this));

    core.bind('order.itemAttributes.changed', _.bind(this.handleOrderItemAttributesChanged, this));

    core.bind('recalculateitem', _.bind(this.handleRecalculateItem, this));
  }

  return result;
}

OrderItemsList.prototype.processLine = function(idx, line)
{
  line = jQuery(line);

  line.find('.edit-options-link a.open-popover').popover({
    html:    true,
    content: this.getPopoverHTML
  });

  line.find('.edit-options-link a.open-popover')
    .on('shown.bs.popover', _.bind(this.handleShowPopover, this))
    .on('hidden.bs.popover', _.bind(this.handleHidePopover, this));

  line.find('.edit-options-link a.open-popup').click(_.bind(this.handleOptionsPopupClick, this));

  line.find('td.price input.price').change(_.bind(this.handlePriceChange, this));
  line.find('td.price .restore-orig-price').eq(0).click(_.bind(this.handleRestorePrice, this));

  line.find('td.amount input.integer').change(_.bind(this.handleAmountChange, this));

  if (!line.hasClass('create-line')) {
    line.find('button.remove').click(_.bind(this.handleRemoveLine, this));
  }
}

OrderItemsList.prototype.getPopoverHTML = function()
{
  jQuery(this).parents('tr').addClass('edit-open-mark');

  return jQuery(this).parents('.cell').find('.edit-options-dialog').html();
}

OrderItemsList.prototype.handleShowPopover = function(event)
{
  this.currentPopover = event.currentTarget;

  jQuery('.popover').click(
    function(event) {
      event.stopPropagation();
    }
  );

  jQuery('.popover .attribute-values select').change(_.bind(this.handlePopoverSelectChange, this));
  jQuery('.popover .attribute-values textarea').change(_.bind(this.handlePopoverTextareaChange, this));
  jQuery('.popover .attribute-values input[type="checkbox"]').change(_.bind(this.handlePopoverCheckboxChange, this));

  jQuery('.order-items .popover .close').click(_.bind(this.handlePopoverClose, this));

  jQuery('form.order-operations').get(0).commonController.bindElements();
}

OrderItemsList.prototype.handlePopoverSelectChange = function(event)
{
  var elm = jQuery(event.currentTarget);
  var box = elm.parents('td').eq(0);
  var text = event.currentTarget.options[event.currentTarget.selectedIndex].text;
  box.find('.av-' + elm.data('attribute-id')).html(text);

  var original = box.find('.edit-options-dialog select[name="' + event.currentTarget.name + '"]').get(0);
  original.selectedIndex = event.currentTarget.selectedIndex;
  jQuery(original.options).removeAttr('selected');
  jQuery(original.options[event.currentTarget.selectedIndex]).attr('selected', 'selected');

  core.trigger('order.itemAttributes.changed', { line: box.parents('tr').eq(0) });
}

OrderItemsList.prototype.handlePopoverTextareaChange = function(event)
{
  var elm = jQuery(event.currentTarget);
  var box = elm.parents('td').eq(0);
  var text = event.currentTarget.value;
  var escapedText = htmlspecialchars(text);
  box.find('.av-' + elm.data('attribute-id'))
    .attr('title', text)
    .tooltip('destroy')
    .tooltip({placement: 'bottom'});
  box.find('.av-' + elm.data('attribute-id') + ' span')
    .html(escapedText);

  var original = box.find('.edit-options-dialog textarea[name="' + event.currentTarget.name + '"]');
  original.val(text);
  original.html(escapedText);
}

OrderItemsList.prototype.handlePopoverCheckboxChange = function(event)
{
  var elm = jQuery(event.currentTarget);
  var box = elm.parents('td').eq(0);

  var checked = elm.is(':checked');

  box.find('.av-' + elm.data('attribute-id'))
    .html(checked ? core.t('Yes') : core.t('No'));

  var original = box.find('.edit-options-dialog input[name="' + event.currentTarget.name + '"]');
  if (checked) {
    original
      .attr('checked', 'checked')
      .get(0).checked = true;

  } else {
    original
      .removeAttr('checked')
      .get(0).checked = false;
  }

  core.trigger('order.itemAttributes.changed', { line: box.parents('tr').eq(0) });
}

OrderItemsList.prototype.handlePopoverClose = function(event)
{
  jQuery('body').click();

  return false;
}

OrderItemsList.prototype.handleHidePopover = function(event)
{
  this.currentPopover = null;
}

OrderItemsList.prototype.handleBodyClick = function(event)
{
  if (this.currentPopover) {
    jQuery(this.currentPopover).popover('hide');
  }
}

OrderItemsList.prototype.handleOptionsPopupClick = function(event)
{
  popup.load(jQuery(event.currentTarget).data('popup-url'));
}

OrderItemsList.prototype.handlePriceChange = function(event)
{
  var field = jQuery(event.currentTarget);
  var mark = field.parents('.cell').find('.restore-orig-price');
  if (field.get(0).commonController.isChanged()) {
    mark.find(':input').val('');
    mark.parents('tr').eq(0).removeClass('ctrl-auto').addClass('ctrl-manual');

  } else {
    mark.find(':input').val('1');
    mark.parents('tr').eq(0).removeClass('ctrl-manual').addClass('ctrl-auto');
  }

  core.trigger('order.item.changed', { line: field.parents('tr') });
}

OrderItemsList.prototype.handleRestorePrice = function(event)
{
  var field = jQuery(event.currentTarget);
  var cell = field.parents('.cell');
  var line = field.parents('tr').eq(0);
  field.find('input').val('1');
  line.removeClass('ctrl-manual')
    .addClass('ctrl-auto')
    .addClass('reassign-price');

  var line = field.parents('tr');

  core.trigger('order.item.changed', { 'line': line });

  this.updateLinePrice(line);
}

OrderItemsList.prototype.handleAmountChange = function(event)
{
  var line = jQuery(event.currentTarget).parents('tr').eq(0);
  if (line.find('td.amount input.integer').get(0).commonController.validate(true)) {
    this.updateLinePrice(line);
    core.trigger('order.item.changed', { 'line': line });
  }
}

OrderItemsList.prototype.handleItemChanged = function(event, data)
{
  var line = jQuery(data.line);
  var price = parseFloat(line.find('td.cell.price .inline-field .field input').val());
  var qty = parseInt(line.find('td.cell.amount .inline-field .field input').val());

  if (isNaN(price)) {
    price = 0;
  }

  if (isNaN(qty)) {
    qty = 0;
  }

  var total = core.round(price * qty, this.e);
  line.find('td.cell.total .value').data('value', total);
  line.find('td.cell.total .value').html(core.numberToString(total, '.', '', this.e));

  if (total <= 0) {
    line.addClass('zero-total');

  } else {
    line.removeClass('zero-total');
  }

  core.trigger('order.items.changed');
}

OrderItemsList.prototype.handleItemsChanged = function(event)
{
  var sum = 0;
  this.container.find('td.total .value').each(
    function() {
      var tr = jQuery(this).parents('tr.line').eq(0);
      if (tr.length > 0 && !tr.hasClass('remove-mark')) {
        sum += parseFloat(jQuery(this).data('value'));
      }
    }
  );
  sum = core.numberToString(sum, '.', '', this.e).split(/\./, 2);

  jQuery('.order-info .totals .subtotal .value .part-integer').html(sum[0]);
  jQuery('.order-info .totals .subtotal .value .part-decimal').html(sum[1]);
}

OrderItemsList.prototype.handleProductModelSelect = function(event, data)
{
  var line = jQuery(data.element).parents('tr.create-line.line').eq(0);

  line.data('clear-price', data.data.clear_price);
  if (data.data.server_price_control) {
    line.addClass('server-price-control');
  }
  var input = line.find('td.price input');
  input.val(core.numberToString(data.data.selected_price, '.', '', this.e));
  input.get(0).commonController.saveValue();

  if (data.data.selected_attributes) {
    var container = jQuery(data.element).parents('div.table-value');
    jQuery(container).after(data.data.selected_attributes);
  }

  var container;
  if (data.data.attributes_widget) {
    container = jQuery(data.element).parents('div.table-value').find('.model-selector.data-type-product .model-is-defined');
    container.find('.item-attributes-box').remove();
    var ul = jQuery('<ul class="item-attributes-box"></ul>');
    var li = jQuery('<li class="sku"></li>');
    li.append(container.html());
    container.children().remove();
    ul.append(li);
    ul.append(data.data.attributes_widget);
    container.append(ul);

    ul.find('.item-attribute-values-list-item-text .text').tooltip({placement: 'bottom'});
  }

  this.updateCellMaxQty(line, 'undefined' == typeof(data.data.max_qty) ? null : data.data.max_qty);

  this.processLine(-1, line);

  line.parents('form').get(0).commonController.bindElements();

  line.find('td.price input').change();

  if (container) {
    container.find('.item-attribute-values-list-item-text .text').tooltip({placement: 'bottom'});
  }
}

OrderItemsList.prototype.handleNewItemCreated = function(event, data)
{
  if (jQuery(data.line).parents('.order-items').length) {
    var ms = jQuery('.model-selector', data.line).get(0);
    ms.model_selector_options.getter = ms.model_selector_options.getter + '&idx=-' + data.idx;

    jQuery(data.line).find('td.cell.price .inline-field :input,td.cell.amount .inline-field :input').change(
      function(event) {
        core.trigger('order.item.changed', { line: data.line });
      }
    );

    jQuery(data.line).find('td.name input.model-input-selector').addClass('no-validate');
    jQuery(data.line).find('td.name input.model-input-selector').last().focus();
  }
}

OrderItemsList.prototype.handlePopupClose = function(event, data)
{
  if (0 < jQuery('.widget-changeattributevalues').length) {
    jQuery('.ui-dialog').remove();
  }
}

OrderItemsList.prototype.handleAfterPopupPlace = function(event, data)
{
  var box = jQuery('.widget-changeattributevalues');
  if (0 < box.length) {
    box.find('button').click(_.bind(this.handleOptionsPopupSubmit, this));
    box.find('select').each(_.bind(this.prepareOptionSelect, this));
    box.find('textarea').each(_.bind(this.prepareOptionTextarea, this));
    box.find('input[type="checkbox"]').each(_.bind(this.prepareOptionCheckbox, this));
  }
}

OrderItemsList.prototype.handleOptionsPopupSubmit = function(event)
{
  var container;
  var box = jQuery(event.currentTarget).parents('div');

  this.lastOptionContainer = null;
  box.find('select').each(_.bind(this.saveOptionSelect, this));
  box.find('textarea').each(_.bind(this.saveOptionTextarea, this));
  box.find('input[type="checkbox"]').each(_.bind(this.saveOptionCheckbox, this));

  core.trigger('order.itemAttributes.changed', { line: this.lastOptionContainer.parents('tr').eq(0) });

  popup.close();
}

OrderItemsList.prototype.saveOptionSelect = function(idx, elm)
{
  var elm = jQuery(elm);
  var name = elm.attr('name');
  var target = this.container.find(':input').filter(
    function() {
      return this.type == 'hidden' && this.name == name;
    }
  );
  target.val(elm.val());
  target.trigger('change');

  var price = jQuery(elm.get(0).options[elm.get(0).selectedIndex]).data('modifier-price');
  target.data('modifier-price', price || 0);

  var text = elm.get(0).options[elm.get(0).selectedIndex].text;
  this.lastOptionContainer = target.parents('td').eq(0);
  this.lastOptionContainer.find('.av-' + elm.data('attribute-id')).html(text);
}

OrderItemsList.prototype.saveOptionTextarea = function(idx, elm)
{
  var elm = jQuery(elm);
  var name = elm.attr('name');
  var target = this.container.find(':input').filter(
    function() {
      return this.type == 'hidden' && this.name == name;
    }
  );
  var text = elm.val();
  target.val(text);
  target.trigger('change');

  this.lastOptionContainer = target.parents('td').eq(0);
  var box = this.lastOptionContainer.find('.av-' + elm.data('attribute-id'))
    .attr('title', text)
    .tooltip('destroy')
    .tooltip({placement: 'bottom'});
  box.find('span').html(htmlspecialchars(text));
}

OrderItemsList.prototype.saveOptionCheckbox = function(idx, elm)
{
  var elm = jQuery(elm);
  var name = elm.attr('name');
  var target = this.container.find(':input').filter(
    function() {
      return this.type == 'hidden' && this.name == name;
    }
  );
  target.val(elm.is(':checked') ? '1' : '');
  target.trigger('change');

  this.lastOptionContainer = target.parents('td').eq(0);
  this.lastOptionContainer
    .find('.av-' + elm.data('attribute-id'))
    .html(target.val() ? core.t('Yes') : core.t('No'));
}

OrderItemsList.prototype.prepareOptionSelect = function(idx, elm)
{
  var elm = jQuery(elm);
  var name = elm.attr('name');
  var target = this.container.find(':input').filter(
    function() {
      return this.type == 'hidden' && this.name == name;
    }
  );
  elm.val(target.val());
}

OrderItemsList.prototype.prepareOptionTextarea = function(idx, elm)
{
  var elm = jQuery(elm);
  var name = elm.attr('name');
  var target = this.container.find(':input').filter(
    function() {
      return this.type == 'hidden' && this.name == name;
    }
  );
  elm.val(target.val());
}

OrderItemsList.prototype.prepareOptionCheckbox = function(idx, elm)
{
  var elm = jQuery(elm);
  var name = elm.attr('name');
  var target = this.container.find(':input').filter(
    function() {
      return this.type == 'hidden' && this.name == name;
    }
  );
  if (target.val()) {
    elm.attr('checked', 'checked');

  } else {
   elm.removeAttr('checked');
  }
}

OrderItemsList.prototype.updateLinePrice = function(line)
{
  line = line.eq(0);

  if (line.hasClass('server-price-control')) {
    this.updateLinePriceServer(line);

  } else {
    this.updateLinePriceLocal(line);
  }
}

OrderItemsList.prototype.updateLinePriceServer = function(line)
{
  line.data('request-id', (new Date()).getTime());

  var formController = jQuery('form.order-operations').get(0).commonController;

  var fields = {
    target:       'order',
    action:       'calculate_price',
    order_number: jQuery('.order-info').data('order-number'),
    item_id:      (line.data('id') || 0),
    amount:       parseInt(line.find('td.amount input.integer').val()),
    product_id:   (parseInt(line.find('td.name :input.model-value').val()) || 0),
    requestId:    line.data('request-id')
  };

  fields[formController.formIdName] = formController.getFormId();

  line.find('td.name :input').each(
    function() {
      if (
        (this.type == 'hidden' || this.type == 'checkbox' || this.nodeName.toLowerCase() == 'select')
        && -1 != this.name.search(/^(order_items|new).+attribute_values/)
      ) {
        if ( this.type == 'checkbox') {
          fields[this.name] = this.checked
              ? this.value
              : jQuery(this).data('unchecked');
        } else {
          fields[this.name] = this.value;
        }
      }
    }
  );

  core.post(
    {
      target: 'order',
      action: 'calculate_price'
    },
    null,
    fields          
  );
}

OrderItemsList.prototype.updateLinePriceLocal = function(line)
{
  var price = parseFloat(line.data('clear-price'))
  if (isNaN(price)) {
    price = 0;
  }

  line.find('td.name .edit-options-dialog .attribute-values select').each(
    function() {
      var elm = jQuery(this.options[this.selectedIndex]);
      if (elm.data('modifier-price')) {
        var modifier = parseFloat(elm.data('modifier-price'));
        if (!isNaN(modifier) && modifier) {
          price += modifier;
        }
      }
    }
  );

  line.find('td.name fieldset.attribute-values-storage input').each(
    function() {
      var elm = jQuery(this);
      if (elm.data('modifier-price')) {
        var modifier = parseFloat(elm.data('modifier-price'));
        if (!isNaN(modifier) && modifier) {
          price += modifier;
        }
      }
    }
  );

  this.updateCell(line, {'price': price}); 
}

OrderItemsList.prototype.handleOrderItemAttributesChanged = function(event, data)
{
  this.updateLinePrice(data.line);
}

OrderItemsList.prototype.handleRecalculateItem = function(event, data)
{
  var line = this.container.find('tr.line').filter(
    function() {
      return jQuery(this).data('request-id') == data.requestId;
    }
  );

  this.updateCell(line, data);
}

OrderItemsList.prototype.updateCell = function(line, data)
{
  // Price
  var price = data.price;
  price = core.numberToString(price, '.', '', this.e);

  var td = line.find('td.price').eq(0);
  if (td.length) {

    var input = td.find('input.price');
    if (line.hasClass('dump-entity') || line.hasClass('reassign-price')) {

      // Change price
      input.val(price);
      input.get(0).commonController.saveValue();
      td.find('.view .value').html(price);

      input.change();

      line.removeClass('reassign-price')

    } else {
      if (line.hasClass('ctrl-auto') && input.get(0).initialValue != price) {
        line.removeClass('ctrl-auto').addClass('ctrl-manual');
      }
    }

    td.find('.restore-orig-price').data('orig-price', price);
    td.find('.fa').attr(
      'title',
      core.t('Current price for the selected configuration and quantity: X', {'price': price})
    );

  }

  // SKU
  if ('undefined' != typeof(data.sku) && data.sku) {
    line.find('.sku-value').html(data.sku);
  }

  // Max amount
  if ('undefined' != typeof(data.max_qty)) {
    this.updateCellMaxQty(line, data.max_qty);
  }

}

OrderItemsList.prototype.updateCellMaxQty = function(line, qty)
{
  var input = line.find('td.amount :input');
  input.data('max', qty);
  var cls = input.attr('class');
  if (null === qty) {
    // Unlimited
    cls = cls.replace(/max\[\d+\]/, '').replace(',,', ',');

  } else if (-1 == cls.search(/max\[\d+\]/)) {
    // After unlimited
    cls = cls.replace(/validate\[/, 'validate[max[' + qty + '],');

  } else {
    // Replace
    cls = cls.replace(/max\[\d+\]/, 'max[' + qty + ']');
  }

  input.attr('class', cls);

  input.get(0).commonController.validate();
}

OrderItemsList.prototype.handleRemoveLine = function(event)
{
  setTimeout(
    function() {
      core.trigger('order.item.changed', { line: jQuery(event.currentTarget).parents('tr') });
    },
    100
  );
}

OrderItemsList.prototype.handleNewLineRemove = function(event)
{
  jQuery('form.order-operations').change();
  core.trigger('order.items.changed');
}

OrderItemsList.prototype.getEventNamespace = function()
{
  return TableItemsList.prototype.getEventNamespace.apply(this, arguments) + '.order.items';
}

