/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Canada Post return products button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonCapostReturnProducts(base)
{
  PopupButtonCapostReturnProducts.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonCapostReturnProducts, PopupButton);

PopupButtonCapostReturnProducts.prototype.pattern = '.popup-button.capost-return-products-button';

core.autoload(PopupButtonCapostReturnProducts);
