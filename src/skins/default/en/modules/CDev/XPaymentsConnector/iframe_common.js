/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * X-Payments common iframe features for checkout and add new card
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
