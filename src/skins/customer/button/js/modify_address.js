/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Modify user button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
var PopupButtonModifyAddress = PopupButton.extend({
    pattern: '.modify-address',
    enableBackgroundSubmit: true,
    addressId: null,
    constructor: function PopupButtonModifyAddress() {
        PopupButtonModifyAddress.superclass.constructor.apply(this, arguments);
    },
    callback: function(selector) {
        PopupButtonModifyAddress.superclass.callback.apply(this, arguments);
        // Some autoloading could be added
        UpdateStatesList();

        var self = this;
        this.addressId = this.findAddressId(selector);
        jQuery('form', selector).each(function() {
            jQuery(this).commonController(
              'enableBackgroundSubmit',
              _.bind(self.onBeforeSubmit, self),
              _.bind(self.onAfterSubmit, self)
            );
        });
    },
    onBeforeSubmit: function() {},
    onAfterSubmit: function() {
        core.trigger('address.modified', this);
    },
    findAddressId: function(selector) {
        var form = $(selector).find('form');

        if (form.length > 0) {
            return form.get(0).elements['address_id'].value;
        }
    }
});

core.autoload(PopupButtonModifyAddress);