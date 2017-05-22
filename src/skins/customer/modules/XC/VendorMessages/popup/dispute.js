/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Open dispute button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
    'openDisputePopupForm',
    'form.dispute',
    function(event) {
        if (jQuery(this).parents('.popup-window-entry').length) {
            this.commonController.enableBackgroundSubmit();
        }
    }
);
