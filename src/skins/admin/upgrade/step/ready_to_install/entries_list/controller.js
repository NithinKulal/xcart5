/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Upgrade: entries list
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('table.downloaded-components tr td.changelog-link .changelog-link').click(
      function(event) {
        var parentTr = jQuery(this).parents('tr.module-entry').get(0);
        var boxId;
        if (0 < jQuery(parentTr).length) {
          boxId = jQuery(parentTr).attr('id');
          if (boxId) {
            var changelogBox = jQuery('#' + boxId + '-changelog');
            if (0 < jQuery(changelogBox).length) {
              if (jQuery(changelogBox).hasClass('opened')) {
                jQuery(changelogBox).removeClass('opened');
                jQuery(parentTr).removeClass('changelog-opened');

              } else {
                jQuery(changelogBox).addClass('opened');
                jQuery(parentTr).addClass('changelog-opened');
              }
            }
          }
        }
      }
    );
  }
);
