/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * X-Payments common iframe features for checkout and add new card
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * IFRAME actions
 */
var XPC_IFRAME_DO_NOTHING       = 0;
var XPC_IFRAME_CHANGE_METHOD    = 1;
var XPC_IFRAME_CLEAR_INIT_DATA  = 2;
var XPC_IFRAME_ALERT            = 3;
var XPC_IFRAME_TOP_MESSAGE      = 4;

/**
  * X-Payments 2.x internal error message and its friendly replacement
  */
var XPC_INTERNAL_ERROR = 'The payment processing system is temporary unavailable due to an internal error.';
var XPC_INTERNAL_ERROR_REPLACE = 'Oops, something wrong has happened. Try to reload the page.';


/**
 * Convert event message to object
 */
function getXpcIframeEventDataObject(event) 
{
  var msg = false;

  try {
    msg = _.isString(event.originalEvent.data)
      ? JSON.parse(event.originalEvent.data)
      : event.originalEvent.data;

  } catch (e) {
  }

  if (
    msg.params
    && msg.params.error == XPC_INTERNAL_ERROR
  ) {
    msg.params.error = XPC_INTERNAL_ERROR_REPLACE;
  }

  return msg;
}

/**
 * Check message from X-Payments
 */
function checkXpcIframeMessage(msg) 
{
  return msg
    && msg.message
    && (
      'paymentFormSubmitError' == msg.message
      || 'paymentFormSubmit' == msg.message
      || 'ready' == msg.message
      || 'showMessage' == msg.message
    );
}

/**
 * Save checkout data before submitting X-Payments iframe form
 */
function saveCheckoutFormDataXpc(notesSelector, saveCardSelector)
{
  var formData = {};

  formData['notes'] = jQuery(notesSelector).val();
  formData['save_card'] = jQuery(saveCardSelector).is(':checked') ? 'Y' : 'N';
  formData[xliteConfig.form_id_name] = xliteConfig.form_id;

  jQuery.post('cart.php?target=checkout&action=save_checkout_form_data', formData);
}
