/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var fixInputPadding = function(elem) {
  var input = elem.siblings('.table-value').find('input');
  var width = input.offset().left + input.outerWidth() - elem.offset().left;

  if (width < input.outerWidth()) {
    input.css('padding-right', width + 'px');
  };
}

var assignHandlers = function(line) {
  var modifiers = line.find('.modifiers');
  var input = line.find('input');
  var fixInputPaddingPartial = _.partial(fixInputPadding, modifiers);

  fixInputPaddingPartial();
  var debounced = _.debounce(fixInputPaddingPartial, 10);
  line.mouseenter(fixInputPaddingPartial);
  line.mouseleave(fixInputPaddingPartial);
  input.one('keydown', debounced);
  input.one('keyup', debounced);
}

core.bind('attributes.modifiers.change', function(event, options){
  fixInputPadding(options.element);
});
core.bind('attributes.modifiers.new', function(event, options){
  assignHandlers(options.element);
});

core.microhandlers.add(
  'MarketplaceSearch',
  '.attributes .modifiers',
  function () {
    assignHandlers(jQuery(this).parent('.value').parent('.line'));
  }
);
