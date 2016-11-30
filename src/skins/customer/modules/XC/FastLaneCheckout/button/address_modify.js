/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Modify user button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
var PopupButtonAddressModify = PopupButton.extend({
    pattern: '.address-modify',
    enableBackgroundSubmit: true,
    constructor: function PopupButtonAddressModify() {
        PopupButtonAddressModify.superclass.constructor.apply(this, arguments);
    },
    callback: function(selector) {
        PopupButtonAddressModify.superclass.callback.apply(this, arguments);
        UpdateStatesList(selector);

        var self = this;
        jQuery('form', selector).each(function() {
            jQuery(this).commonController(
              'enableBackgroundSubmit',
              _.bind(self.onBeforeSubmit, self),
              _.bind(self.onAfterSubmit, self)
            );
        });
    },
    beforeLoadDialog: function (elem) {
        $('.ui-widget.default-dialog').remove();
        $('.ajax-container-loadable.widget-xc-fastlanecheckout-blocks-popupaddressform').remove();
    },
    onBeforeSubmit: function() {},
    onAfterSubmit: function() {
        popup.destroy();
    }
});

core.autoload(PopupButtonAddressModify);
