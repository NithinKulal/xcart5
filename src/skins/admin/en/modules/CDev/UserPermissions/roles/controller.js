/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

TableItemsList.prototype.listeners.unremovableRole = function(handler)
{
  jQuery('.permanent .input-field-wrapper.switcher input', handler.container).prop('disabled', 'disabled');
};

