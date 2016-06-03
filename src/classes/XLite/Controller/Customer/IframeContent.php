<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Iframe content controller
 */
class IframeContent extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters list
     *
     * @var array
     */
    protected $params = array('target');

    /**
     * Preprocessor for no-action reaction
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        $content = \XLite\Core\Request::getInstance()->id
            ? \XLite\Core\Database::getRepo('XLite\Model\IframeContent')->find(\XLite\Core\Request::getInstance()->id)
            : null;

        if ($content) {

            $method = $content->getMethod();
            $url = $content->getUrl();
            $body = $this->assembleFormBody($content);

            $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onload="javascript: document.getElementById('payment_form').submit();">
  <form method="$method" id="payment_form" name="payment_form" action="$url">
    <fieldset style="display: none;">
$body
    </fieldset>
  </form>
</body>
</html>
HTML;

            print ($html);
            $this->setSuppressOutput(true);

        } else {
            $this->redirect(\XLite\Core\Converter::buildURL('checkout'));
        }
    }

    /**
     * Assemble form body (field set)
     *
     * @return string HTML
     */
    protected function assembleFormBody(\XLite\Model\IframeContent $content)
    {
        $inputs = array();
        foreach ($content->getData() as $name => $value) {
            $inputs[] = '<input type="hidden" name="' . htmlspecialchars($name)
                . '" value="' . htmlspecialchars($value) . '" />';
        }

        if ($inputs) {
            $body = '      ' . implode("\n" . '      ', $inputs);
        }

        return $body;
    }
}
