/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product selection button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonProductSelector(base)
{
  PopupButtonProductSelector.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonProductSelector, PopupButton);

PopupButtonProductSelector.prototype.pattern = '.popup-button.popup-product-selection';

decorate(
  'PopupButtonProductSelector',
  'callback',
  function (selector)
  {
    // Some autoloading could be added
    core.autoload(TableItemsListQueue);
    core.autoload(CommonForm);
    core.autoload(StickyPanelProductSelection);
    SearchConditionBox(true);
  }
);

decorate(
  'PopupButtonProductSelector',
  'getURLParams',
  function ()
  {
    var params = arguments.callee.previousMethod.apply(this, arguments);

    return params;
  }
);

core.autoload(PopupButtonProductSelector);

core.microhandlers.add(
  'PopupButtonProductSelector',
  PopupButtonProductSelector.prototype.pattern,
  function (event) {
    core.autoload(PopupButtonProductSelector);
  }
);
