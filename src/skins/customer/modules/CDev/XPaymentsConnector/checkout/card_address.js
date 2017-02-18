/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * X-Payments common functions for saved card address switcher
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function switchAddress(addressId)
{  
  var params = {};
  params['address_id'] = addressId;
  params[xliteConfig.form_id_name] = xliteConfig.form_id;

  core.post(
    URLHandler.buildURL({
      target: 'checkout',
      action: 'set_card_billing_address'
    }),
    null,
    params,
    {
      async:  false
    }
  );
}

function getLabelForCard(elm)
{
  var cardId = jQuery(elm).val();
  return jQuery('#saved-card-label-' + cardId);
}

function showAddressPopover(elm)
{
  if (typeof elm == 'undefined') {
    elm = "[name='payment[saved_card_id]']:checked";
  }

  elm = getLabelForCard(elm);

  if (!elm || !elm.length) {
    return;
  }

  var addressId = elm.data('address-id');
  var cardId = elm.data('card-id');

  if (
    !cardId
    || !addressId
    || addressId == xpcBillingAddressId
  ) {
    // Billing address match so just hide popover
    hideAddressPopovers();

    return;
  }

  var opts = {
    placement: 'top',
    closeable: true,
    multi: false,
    cache: false,
    trigger: 'manual',
    width: '300px',
  };

  elm
    .webuiPopover('destroy')
    .webuiPopover(opts)
    .webuiPopover('show');
}

function hideAddressPopovers()
{
  WebuiPopovers.hideAll();
}

function initAddressPopovers() {
  jQuery("[name='payment[saved_card_id]']").change( 
    function(elm) {

      var params = {};
      params['selected_card_id'] = getLabelForCard(this).data('card-id');
      params[xliteConfig.form_id_name] = xliteConfig.form_id;

      core.post(
        URLHandler.buildURL({
          target: 'checkout',
          action: 'save_selected_card_id',
        }),
        null,
        params
      );

      showAddressPopover(this);
    }
  );
}
