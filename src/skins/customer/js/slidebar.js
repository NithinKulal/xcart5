/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function slidebar(){
    var self = this;
    jQuery(function () {
        core.trigger('mm-menu.before_create',  jQuery('#slidebar'));
        jQuery('#slidebar').mmenu(self.options, self.configuration);

        var api = jQuery('#slidebar').data('mmenu');
        core.trigger('mm-menu.created', api);
        api.bind('closed', function () {
            api.closeAllPanels();
        });

        var isIE11 = !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
        if(isIE11){
            jQuery('html').addClass('ie11');
        }

        jQuery('.dropdown-menu#search_box').parent().on('shown.bs.dropdown', function () {
            jQuery('#header').addClass('hidden');
        });

        jQuery('.dropdown-menu#search_box').parent().on('hidden.bs.dropdown', function () {
            jQuery('#header').removeClass('hidden');
        });
    });
}

slidebar.prototype.options = {
    extensions: ['pagedim-black'],
    offCanvas: {
        pageSelector: "#page-wrapper"
    }
};

slidebar.prototype.configuration = {};

core.autoload(slidebar);
