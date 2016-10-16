/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * create backup controller
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2015 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

core.bind(
  'load',
  function () {
    $(window).scroll(function() {
      if($(window).height() < ($(document).height() - 100) && $(window).scrollTop() + $(window).height() >= ($(document).height() - 100)) {
        $('.backup-focus-overlay').addClass('active');
        $('.create-backup-section').addClass('focus');
      } else {
        $('.backup-focus-overlay').removeClass('active');
        $('.create-backup-section').removeClass('focus');
      }
    });
  }
);