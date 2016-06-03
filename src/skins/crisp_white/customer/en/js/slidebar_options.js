/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

slidebar.prototype.options = _.extend(slidebar.prototype.options, {
  offCanvas: _.extend(slidebar.prototype.options.offCanvas, {
    position: "right",
    zposition: "front"
  })
});