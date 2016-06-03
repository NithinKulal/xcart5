/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

$('.desktop-header').affix({
  offset: {
    top: function () {
        return 140;
    },
  }
});

$('.mobile_header .nav').affix({
  offset: {
    top: function () {
        return 140;
    },
  }
});

$(document).on('click', '#header-area .dropdown .dropdown-menu', function (e) {
  e.stopPropagation();
});

var panel = $('.header_search-panel');
panel.on('hidden.bs.collapse', function() {
	$(this).siblings('a').addClass('collapsed');
});

panel.on('show.bs.collapse', function() {
  $(this).siblings('a').removeClass('collapsed');
});

$(document).click(function(event) {
    if(!$(event.target).closest('.simple-search-box').length &&
       !$(event.target).is('.simple-search-box')) {
      if (panel.hasClass('in')) {
        panel.collapse('hide');
      }
    }
});