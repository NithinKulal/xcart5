/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */
function TopSellersList(base)
{
    TopSellersList.superclass.constructor.apply(this, arguments);

    this.bind('local.loaded', _.bind(this.setHandler, this));
    this.setHandler();
}

extend(TopSellersList, ALoadable);

TopSellersList.autoload = function()
{
    jQuery('div.top-sellers').each(
        function() {
            new TopSellersList(this);
        }
    );
};

TopSellersList.initialRequested = false;

TopSellersList.prototype.shadeWidget = true;

TopSellersList.prototype.widgetTarget = 'main';

TopSellersList.prototype.widgetClass = 'XLite\\View\\Product\\TopSellersBlock';

TopSellersList.prototype.setHandler = function () {
    var self = this;
    jQuery('.period-box .field select', 'div.top-sellers').change(function () {
        self.load(self.getURLParams());
    });
    jQuery('.availability-box .field select', 'div.top-sellers').change(function () {
        self.load(self.getURLParams());
    });
};

TopSellersList.prototype.getURLParams = function () {
    return {
        period: jQuery('.period-box .field select option:selected', 'div.top-sellers').val(),
        availability: jQuery('.availability-box .field select option:selected', 'div.top-sellers').val()
    };
};

core.autoload(TopSellersList);
