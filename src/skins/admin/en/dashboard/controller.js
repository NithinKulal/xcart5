/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'dashboard-tabs',
  '.js-tabs.dashboard-tabs .tabs li a',
  function() {
    var link = jQuery(this);
    var li = link.parent();
    link.click(
      function(event) {
        if (!li.hasClass('tab-current')) {

          var id = link.data('id');

          li.parent().find('li.tab-current').removeClass('tab-current');
          li.addClass('tab-current');

          var box = li.parents('.js-tabs.dashboard-tabs').eq(0);
          box.find('.tab-content').hide();
          box.find('#' + id).show();
        }

        return false;
      }
    );
  }
);
