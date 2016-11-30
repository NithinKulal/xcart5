/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add address button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var PopupButtonAddAddress = PopupButton.extend({
    pattern: '.add-address',
    enableBackgroundSubmit: true,
    constructor: function PopupButtonAddAddress() {
        PopupButtonAddAddress.superclass.constructor.apply(this, arguments);
    },
    callback: function(selector) {
        PopupButtonAddAddress.superclass.callback.apply(this, arguments);
        // Some autoloading could be added
        UpdateStatesList();

        var self = this;
        jQuery('form', selector).each(function() {
            jQuery(this).commonController(
              'enableBackgroundSubmit',
              _.bind(self.onBeforeSubmit, self),
              _.bind(self.onAfterSubmit, self)
            );
        });
    },
    beforeLoadDialog: function() {
        $('.ajax-container-loadable.widget-address-modify').remove();
    },
    onBeforeSubmit: function() {},
    onAfterSubmit: function() {
        popup.close();
    }
});

core.autoload(PopupButtonAddAddress);
