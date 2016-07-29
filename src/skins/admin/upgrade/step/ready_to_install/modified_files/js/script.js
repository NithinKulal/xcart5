/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Modified files list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function makeSmallHeight(button)
{
  switchHeight('.modified-files-block');
}

function makeLargeHeight(button)
{
  switchHeight('.modified-files-block');
}

function switchHeight(area)
{
  var max = "600px";

  if ("undefined" === typeof(jQuery(area).attr("old_height"))) {
    jQuery(area).attr("old_height", jQuery(area).css("height"));
  }

  if (max === jQuery(area).css("height")) {
    jQuery(area).css("height", jQuery(area).attr("old_height"));
  } else {
    jQuery(area).css("height", max);
  }
}

function attachClickOnSelectAll() {
  jQuery('a.select-all').each(function () {
    jQuery(this).click(function () {
      jQuery('.modified-file input[type=checkbox]').prop('checked', 'checked');
    });
  });
}

function attachClickOnUnselectAll() {
  jQuery('a.unselect-all').each(function () {
    jQuery(this).click(function () {
      jQuery('.modified-file input[type=checkbox]').prop('checked', '');
    });
  });
}

core.bind(
  'load',
  function () {
    jQuery('#radio-select-all').click(function () {
      jQuery('.modified-file input[type=checkbox]')
      .prop('checked', '')
      .prop('readonly', 'readonly')
      .addClass('readonly');

      jQuery('a.unselect-all, a.select-all').unbind('click');
    });

    jQuery('#radio-unselect').click(function () {
      attachClickOnSelectAll();
      attachClickOnUnselectAll();

      jQuery('.modified-file input[type=checkbox]')
      .removeProp('readonly')
      .removeClass('readonly');
    });

    attachClickOnSelectAll();

    attachClickOnUnselectAll();

    // Must be selected by default
    jQuery('#radio-select-all').click();
  }
  );
