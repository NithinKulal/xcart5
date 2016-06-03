/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Left menu controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'top-links-submenu-reposition',
  function() {
    return jQuery('#top-links li').filter(
      function () {
        return jQuery(this).children('.box').length > 0;
      }
    );
  },
  function()
  {
    var li = jQuery(this);
    var box = li.children('.box');
    var arr = box.children('.arr');
    var header = jQuery('#header');

    jQuery(this).mouseenter(
      function(event) {

        // Fix left position
        var headerRBorder = header.offset().left + header.outerWidth();
        var boxRBorder = box.offset().left + box.outerWidth();

        if (boxRBorder > headerRBorder) {
          var diff = boxRBorder - headerRBorder;
          box.css('left', box.position().left - diff);
        }

        // Fix arr position
        var liCenter = li.offset().left + Math.round(li.outerWidth() / 2);
        arrCenter = arr.offset().left + Math.round(arr.outerWidth() / 2);
        var diff = liCenter - arrCenter;
        if (diff != 0) {
          arr.css('left', arr.position().left + diff);
        }
      }
    );
  }
);
