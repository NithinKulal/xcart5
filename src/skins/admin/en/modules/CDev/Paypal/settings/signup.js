/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * PayPal In-Constext Boarding SignUp
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
(function(d, s, id){
  var js, ref = d.getElementsByTagName(s)[0];

  if (!d.getElementById(id)){
    js = d.createElement(s); js.id = id; js.async = true;
    js.src = "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";

    ref.parentNode.insertBefore(js, ref);
  }
}(document, "script", "paypal-js"));
