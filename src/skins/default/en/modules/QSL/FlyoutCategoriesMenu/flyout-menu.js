/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Flyout-menu functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
jQuery(document).ready(function(){
    jQuery('ul.flyout-menu > li:not(.leaf) > a').on("touchstart", function (e) {
        
        jQuery('ul.flyout-menu').addClass('touch');        
        
        var link = jQuery(this);
        var li = jQuery(this).parent();
        if (link.hasClass('hover')) {
            li.addClass('hover');
            return true;
        } else {
            link.addClass('hover');
            jQuery('ul.flyout-menu > li > a').not(this).removeClass('hover');
            
            li.closest('ul.flyout-menu').find('li').removeClass('hover');
            li.addClass('hover');
            
            e.preventDefault();
            return false;
        }
    });    
});