/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Amazon login controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('Amazon/LoginWithAmazon', ['js/jquery', 'Amazon/PayWithAmazon'], function ($, Amazon) {
  Amazon.lwaButton('loginWithAmazonDiv_button');
  Amazon.lwaIcon('loginWithAmazonDiv_icon');

  core.microhandlers.add(
    'loginWithAmazonDiv_button',
    '#loginWithAmazonDiv_button',
    function () {
      var id = 'loginWithAmazonDiv_button_' + (new Date().getTime());
      $(this).attr('id', id);
      Amazon.lwaButton(id);
    }
  );
});