/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add PIN codes button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonAddPinCodesButton() {
  PopupButtonAddPinCodesButton.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonAddPinCodesButton, PopupButton);

PopupButtonAddPinCodesButton.prototype.pattern = '.add-pin-codes-button';

decorate(
  'PopupButtonAddPinCodesButton',
  'afterSubmit',
  function (selector) {
    jQuery('.items-list')[0].itemsListController.loadWidget();
  }
);

decorate(
  'PopupButtonAddPinCodesButton',
  'eachClick',
  function (elem) {

    if (elem.linkedDialog) {
      jQuery(elem.linkedDialog).dialog('close').remove();
      elem.linkedDialog = undefined;
    }

    return arguments.callee.previousMethod.apply(this, arguments);
  }
);

core.autoload(PopupButtonAddPinCodesButton);
