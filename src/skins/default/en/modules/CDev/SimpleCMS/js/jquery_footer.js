/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * JQuery footer
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// function footer () {
//   var fh = jQuery('#footer-area').height();
//   if (fh > 150) {
//     jQuery('#page').css('padding-bottom', fh);
//   }
// }

function mobileDropdown () {
  if (jQuery('#top-menu.dropdown-menu').hasClass('scrollable') == true) {
    jQuery('#top-menu.dropdown-menu').removeClass('scrollable').removeAttr('style');
  }
  var h1 = jQuery(window).height();
  var h2 = jQuery('#top-menu.dropdown-menu').height();
  var h3 = jQuery('.mobile_header ul.nav-pills').height();
  if (h1 < h2+h3) {
    jQuery('#top-menu.dropdown-menu').addClass('scrollable').height(h1-h3);
  }
}

jQuery(function () {
  mobileDropdown();
  jQuery(window).resize(mobileDropdown);
  jQuery(function () {
    jQuery('.top-main-menu a, .top-main-menu .primary-title').on('touchstart', function (e) {
      if (jQuery(this).hasClass('tap') !== true && jQuery(this).parent().parent().hasClass('tap') !== true) {
        jQuery('ul.tap, a.tap, .primary-title.tap').removeClass('tap');
      }
      if (jQuery(this).next('ul').length !== 0) {
        jQuery(this).toggleClass('tap');
        if (jQuery(this).hasClass('tap') == true) {
          e.preventDefault();
        }
        jQuery(this).next('ul').toggleClass('tap');
      }
      jQuery(document).on('touchstart', function (e) {
        if (jQuery(e.target).closest('.tap').length == 0) {
          jQuery('ul.tap, a.tap, .primary-title.tap').removeClass('tap');
        }
      });
    });
  });
});
