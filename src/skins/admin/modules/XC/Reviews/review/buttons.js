/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Remove/Approve buttons controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function onRemoveButton(obj)
{
  var entityId = jQuery(obj).parents('form').find('input[name="id"]').eq(0).val();
  var pattern = '.entity-' + entityId + ' td.cell.actions.right .cell .action button.remove';

  if (jQuery(pattern).length > 0) {
    // Mark current entity as prepared to be removed in the items list
    jQuery(pattern).click();
    popup.close();

  } else {
    // Remove current entity
    var form = jQuery(obj).parents('form').eq(0);
    form.find('input[name=action]').val('delete');
    form.submit();
  }
}

function onApproveButton(obj)
{
  var form = jQuery(obj).parents('form').eq(0);
  form.find('input[name=action]').val('approve');
  form.submit(); 
}
