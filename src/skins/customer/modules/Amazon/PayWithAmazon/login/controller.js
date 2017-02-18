/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Amazon login controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('Amazon/LoginWithAmazon', ['js/jquery', 'Amazon/PayWithAmazon'], function ($, Amazon) {
  jQuery('.social-net-button.social-net-Amazon > div').each(function () {
    Amazon.lwaButton(this.id);
  });

  jQuery('.social-net-icon.social-net-Amazon > div').each(function () {
    Amazon.lwaIcon(this.id);
  });

  core.microhandlers.add(
    'loginWithAmazonDiv_button',
    '.social-net-button.social-net-Amazon > div',
    function () {
      Amazon.lwaButton(this.id);
    }
  );
});
