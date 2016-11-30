/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Powered by widget style
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var correctGroups = function($this, listId) {
  $this.find('.group-title').hide();
  $this.find('.group-names').hide();
  $this.find('.group-title.list-' + listId).show();
  $this.find('.group-names.list-' + listId).show();
};

core.microhandlers.add(
    'select_subscriptions',
    '.subscriptions-list-container',
    function() {
      var $this = jQuery(this);
      var select = $this.find('select#subscribe');

      if (select.length) {
        select.change(function() {
          correctGroups($this, select.val());
        });

        correctGroups($this, select.val());
      }
    }
);