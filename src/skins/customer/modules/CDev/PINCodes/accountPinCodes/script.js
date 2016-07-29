/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Products list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PinCodesListView(base) {
  PinCodesListView.superclass.constructor.apply(this, arguments);
}

extend(PinCodesListView, ListView);

// PinCodes list class
function PinCodesListController(base) {
  PinCodesListController.superclass.constructor.apply(this, arguments);
}

extend(PinCodesListController, ListsController);

PinCodesListController.prototype.name = 'PinCodesListController';

PinCodesListController.prototype.findPattern += '.items-list-pin-codes';

PinCodesListController.prototype.getListView = function() {
  return new PinCodesListView(this.base);
}
core.autoload(PinCodesListController);
