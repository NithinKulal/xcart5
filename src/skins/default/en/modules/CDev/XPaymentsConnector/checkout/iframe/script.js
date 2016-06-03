/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sale widget controller
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

function func_xpc_redirect (returnURL) {
  try {
    parent.jQuery('form.place').addClass('allowed').get(0).setAttribute('action', returnURL);
    parent.jQuery('form.place input[name="action"]').val('return');
    parent.jQuery('.bright').removeClass('disabled').removeClass('submitted').click();
  } catch (err) {
    alert('Internal error. Please contact admin.');
  }
}

function func_xpc_update_height () {
  parent.jQuery('#xpc_iframe').css('height', jQuery(window).height());
}
