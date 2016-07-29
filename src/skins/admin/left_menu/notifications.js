/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Notifications controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
  core.microhandlers.add(
    'updateLeftMenuNotificationCount',
    '.notification-menu',
    function() {
      var $self = jQuery(this);
      if (0 < $self.data('unread-count') && !$self.parent('.box').is('.read')) {
        var label = $self.closest('.menu-item').find('.line .label span');
        label.text($self.data('unread-count'));
        label.css('opacity', 1);
      }
    }
  );

  jQuery('#leftMenu .menu-item.notification').live('mouseenter', function () {
    var element = jQuery(this);
    var menuType = jQuery('.notification-menu', element).data('menu-type');
    this.readTimer = setTimeout(function () {
      jQuery('.lazy-load.box', element).toggleClass('read', true);
      element.closest('.menu-item').find('.line .label span').css('opacity', 0);

      jQuery.ajax({
        url: xliteConfig.script + "?target=main&action=set_notifications_as_read&menuType=" + menuType
      }).done(function() {
      });
    }, 2000)
  });

  jQuery('#leftMenu .menu-item.notification').live('mouseleave', function () {
    clearInterval(this.readTimer);
  });
});
