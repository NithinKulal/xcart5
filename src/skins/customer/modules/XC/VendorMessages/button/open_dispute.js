/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Open dispute button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonOpenDispute()
{
    PopupButtonOpenDispute.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonOpenDispute, PopupButton);

PopupButtonOpenDispute.prototype.pattern = '.popup-button.open-dispute';

PopupButtonOpenDispute.prototype.afterSubmit = function (selector)
{
    PopupButton.prototype.afterSubmit.apply(this, [selector]);

    if (jQuery('.ui-dialog.block-wait-box').is(':visible')) {
        jQuery('.ui-dialog.block-wait-box').remove();
    }
};

PopupButtonOpenDispute.prototype.callback = function (selector, link)
{
    var obj = this;

    if (this.enableBackgroundSubmit) {
        jQuery('form', selector).each(
            function() {
                jQuery(this).commonController(
                    'enableBackgroundSubmit',
                    function () {
                        return true;
                    },
                    function (event) {
                        popup.close();

                        obj.afterSubmit(selector);

                        // Remove dialog from DOM
                        jQuery(selector).remove();
                        link.linkedDialog = null;

                        return false;
                    }
                );
            }
        );

    } else {
        jQuery('form', selector).each(
            function() {
                this.commonController.backgroundSubmit = false;
            }
        );
    }
};
