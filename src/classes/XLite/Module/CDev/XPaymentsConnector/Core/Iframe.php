<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Core;

/**
 * XPayments client
 *
 */
class Iframe extends \XLite\Base\Singleton
{
    /**
     * IFRAME actions 
     */
    const IFRAME_DO_NOTHING       = 0;
    const IFRAME_CHANGE_METHOD    = 1;
    const IFRAME_CLEAR_INIT_DATA  = 2;
    const IFRAME_ALERT            = 3;
    const IFRAME_TOP_MESSAGE      = 4;    

    /**
     * Maximum number of IFRAME action
     */
    const IFRAME_MAX_ACTION       = 4;

    /**
     * Iframe flag
     *
     * @var boolean
     */
    protected $enabled = false;

    /**
     * Error
     *
     * @var boolean
     */
    protected $error = false;

    /**
     * Type 
     *
     * @var integer
     */
    protected $type = self::IFRAME_DO_NOTHING;

    /**
     * Is X-Payments iframe
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Is X-Payments iframe
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is X-Payments iframe
     *
     * @param integer $type Type
     *
     * @return boolean
     */
    public function setType($type)
    {
        return $this->type = $type;
    }

    /**
     * Set flag if X-Payments iframe is in iframe
     *
     * @return void
     */
    public function enable()
    {
        if (\XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_use_iframe) {
            $this->enabled = true;
        }
    }

    /**
     * Use iframe or not
     *
     * @return bool
     */
    public function useIframe()
    {
        return \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_use_iframe
            && !\XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::isMobileDeviceCompatible();
    }

    /**
     * Set flag if X-Payments iframe is in iframe
     *
     * @return void
     */
    public function setError($error)
    {
        if (!is_string($error)) {
            $error = var_export($error, true);
        }

        $this->error = $error;
    }

    /**
     * Set flag if X-Payments iframe is in iframe
     *
     * @return void
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * Finalize X-Payments iframe
     *
     * @return void
     */
    public function finalize()
    {
        if ($this->getError()) {
            \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance()->clearInitDataFromSession();
        }

        print ($this->getHTML());

        \XLite\Core\Database::getEM()->flush();
    
        die (0);
    }

    /**
     * Get javascript code 
     *
     * @return string 
     */
    public function getJSCode() 
    {    
        $message = $this->getError()
            ? 'paymentFormSubmitError'
            : 'ready';

        $returnURL = \XLite::getController()->getReturnURL() 
            ? \XLite::getController()->getReturnURL()
            : '';

        $error = $this->getError() 
            ? htmlspecialchars($this->getError(), ENT_QUOTES)
            : '';

        $type = $this->getType();

        return <<<JS
function onload() {

    msg = { 
        message: '$message', 
        params: { 
            height: document.body.clientHeight,
            error: '$error', 
            returnURL: '$returnURL',
            type: $type
        } 
    };

    if (window.parent !== window && window.JSON) {
        window.parent.postMessage(JSON.stringify(msg), '*');
    } else {
        window.location = '$returnURL';
    }
}
JS;
    }

    /**
     * Get body 
     *
     * @return string
     */
    public function getBody()
    {
        if (!$this->getError()) {
            $body = '';

        } elseif (static::IFRAME_DO_NOTHING == $this->getType()) {
            $body = $this->getError();

        } else {
            $body = '';
        }

        return '<div class="body"><i>' . $body . '</i></div>';
    }

    /**
     * Get HTML code
     *
     * @return string
     */
    public function getHTML()
    {
        $js = $this->getJScode();
        $body = $this->getBody();

        return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
<!--
div.body {
    margin-left: 15px;
}
-->
</style>
<script type="text/javascript">$js</script>
</head>
<body onload="javascript: onload();">
$body
</body>
</html>
HTML;
    }
}
