/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sale widget controller
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

decorate(
  'CheckoutView',
  'postprocess',
  function(isSuccess, initial)
  {
    if (jQuery('.review-step.current').length) {
        if (jQuery('#xpc_iframe').length) {
            jQuery('.review-step.current').addClass('xpc');

            jQuery('div.hide-submit').mouseover(
              function() {
                jQuery('.terms').addClass('non-agree');
              }
            ).mouseout(
              function() {
                jQuery('.terms').removeClass('non-agree');
              }
            );

            jQuery('#place_order_agree').change(
              function() {
                if (jQuery(this).prop('checked')) {
                  jQuery('div.hide-submit').hide();
                } else {
                  jQuery('div.hide-submit').show();
                }
              }
            );

        } else {
            jQuery('.review-step.current').removeClass('xpc');
        }
    }

    arguments.callee.previousMethod.apply(this, arguments);
  }
);
