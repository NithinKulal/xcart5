<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Web-based payment method return
 */
class PaymentReturn extends \XLite\Controller\Customer\ACheckoutReturn
{
    /**
     * Define the detection method to check the ownership of the transaction
     *
     * @return string
     */
    protected function getDetectionMethodName()
    {
        return 'getReturnOwnerTransaction';
    }

    /**
     * Hard-coded value to prevent the doAction{action}() calls if the request goes with the "action" parameter
     *
     * @return string
     */
    public function getAction()
    {
        return 'return';
    }

    /**
     * Updates order state by transaction
     *
     * @param \XLite\Model\Payment\Transaction $txn Processed payment transaction
     *
     * @return void
     */
    public function updateOrderState($txn)
    {
        $txn->registerTransactionInOrderHistory('web');
    }

    /**
     * Process return
     *
     * @return void
     */
    protected function doActionReturn()
    {
        $txn = $this->detectTransaction();

        if ($txn) {
            $txn->getPaymentMethod()->getProcessor()->processReturn($txn);

            $this->updateOrderState($txn);

            $urlParams = array();
            $urlParams['order_id'] = $txn->getOrder()->getOrderId();

            if ($txn->getNote()) {
                $urlParams['txnNote'] = base64_encode(static::t($txn->getNote()));
                $urlParams['txnNoteType'] = $txn->isFailed() ? \XLite\Core\TopMessage::ERROR : \XLite\Core\TopMessage::INFO;
            }

            \XLite\Core\Database::getEM()->flush();

            $url = $this->getShopURL(
                $this->buildURL('checkout', 'return', $urlParams),
                \XLite\Core\Request::getInstance()->isHTTPS() || \XLite\Core\Config::getInstance()->Security->customer_security
            );

            switch ($txn->getPaymentMethod()->getProcessor()->getReturnType()) {
                case \XLite\Model\Payment\Base\WebBased::RETURN_TYPE_HTML_REDIRECT:
                    $this->doHTMLRedirect($url);
                    break;

                case \XLite\Model\Payment\Base\WebBased::RETURN_TYPE_HTML_REDIRECT_WITH_IFRAME_DESTROYING:
                    $this->doHTMLRedirectWithIframeDestroying($url);
                    break;

                case \XLite\Model\Payment\Base\WebBased::RETURN_TYPE_CUSTOM:
                    $txn->getPaymentMethod()->getProcessor()->doCustomReturnRedirect();
                    break;

                default:
                    $this->setReturnURL($url);
            }

        } else {
            // Log error
            \XLite\Logger::getInstance()->log(
                'Requested payment return page with undefined payment transaction' . PHP_EOL
                . 'IP address: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL
                . 'Data: ' . var_export(\XLite\Core\Request::getInstance()->getData(), true),
                LOG_ERR
            );

            // Add top message
            \XLite\Core\TopMessage::addWarning('Unknown payment. If you see this message after returning from the payment page, please contact the store administrator for information about the status of your payment.');

            // Set return URL
            $url = $this->buildURL('');

            // Make redirect with destroying iframe (as it's unknown whether payment was via iframe method or not)
            $this->doHTMLRedirectWithIframeDestroying($url);
        }
    }

    /**
     * Do HTML-based redirect
     *
     * @param string  $url  URL
     * @param integer $time Redirect delay OPTIONAL
     *
     * @return void
     */
    protected function doHTMLRedirect($url, $time = 1)
    {
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Refresh" content="$time;URL=$url" />
</head>
<body>
If the page is not updated in $time; seconds, please follow this link: <a href="$url">continue &gt;&gt;</a>
</body>
</html>
HTML;

        print ($html);
        exit (0);
    }

    /**
     * Do HTML-based redirect with destroying an iframe window
     *
     * @param string $url URL
     *
     * @return void
     */
    protected function doHTMLRedirectWithIframeDestroying($url)
    {
        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <script type="text/javascript">
    top.location.href='$url';
  </script>
</head>
<body>
If this page does not redirect <a href="$url" target="top">Click Here</a>
</body>
</html>
HTML;

        print ($html);
        exit (0);
    }

    /**
     * Check - is service controller or not
     *
     * @return boolean
     */
    protected function isServiceController()
    {
        return true;
    }

}
