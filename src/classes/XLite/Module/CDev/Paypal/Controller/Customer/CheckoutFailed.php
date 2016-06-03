<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

/**
 * Checkout controller
 */
class CheckoutFailed extends \XLite\Controller\Customer\CheckoutFailed implements \XLite\Base\IDecorator
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (\XLite\Core\Session::getInstance()->inContextRedirect) {
            unset(\XLite\Core\Session::getInstance()->inContextRedirect);

            echo <<<HTML
            <html><head></head><body>
<script type="text/javascript">

(function(d, s, id){
  var js, ref = d.getElementsByTagName(s)[0];
  if (!d.getElementById(id)){
    js = d.createElement(s); js.id = id; js.async = true;
    js.src = "//www.paypalobjects.com/js/external/paypal.v1.js";
    ref.parentNode.insertBefore(js, ref);
  }
}(document, "script", "paypal-js"));

</script>
            </body></html>
HTML;
            exit;

        } elseif (\XLite\Core\Session::getInstance()->cancelUrl) {
            $this->setReturnURL(\XLite\Core\Session::getInstance()->cancelUrl);

            unset(\XLite\Core\Session::getInstance()->cancelUrl);
        } else {
            parent::doNoAction();
        }
    }
}
