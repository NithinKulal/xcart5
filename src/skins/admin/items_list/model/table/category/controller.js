/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Category page
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'category-page-controller',
  'body.target-categories',
  function ()
  {
    if (self.location.search.search(/add_new=1/) != -1) {
      setTimeout(
        function() {
          jQuery('.items-list.categories button.create-inline').click();
        },
        500
      );
    }
  }
);
