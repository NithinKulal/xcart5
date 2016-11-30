<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * HTTPS settings page controller
 */
class HttpsSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('HTTPS settings');
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['switch_customer_security']);
    }

    /**
     * Return url for read more link on invalid SSL
     *
     * @return string
     */
    public function getReadMoreUrl(){
        return 'https://www.sslshopper.com/ssl-checker.html';
    }

    /**
     * Enable HTTPS
     *
     * @return void
     */
    public function doActionEnableHTTPS()
    {
        $this->switchHTTPS(true);

        \XLite\Core\TopMessage::addInfo('HTTPS option has been successfully enabled');

        $this->setReturnURL($this->buildURL($this->get('target')));
    }

    /**
     * Disable HTTPS
     *
     * @return void
     */
    public function doActionDisableHTTPS()
    {
        $this->switchHTTPS(false);

        \XLite\Core\TopMessage::addInfo('HTTPS option has been disabled');

        $this->setReturnURL($this->buildURL($this->get('target')));
    }

    /**
     * Actions to enable the clean URL functionality
     *
     * @return void
     */
    public function doActionSwitchCustomerSecurity()
    {
        $oldValue = \XLite\Core\Config::getInstance()->Security->force_customers_to_https;

        $ajaxResponse = array(
            'Success'       => true,
            'NewState'      => !(bool) $oldValue
        );

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'Security',
                'name'     => 'force_customers_to_https',
                'value'    => !(bool) $oldValue
            )
        );

        $this->printAJAX($ajaxResponse);
        $this->silent = true;
        $this->setSuppressOutput(true);
    }

    /**
     * Send specific headers and print AJAX data as JSON string
     *
     * @param array $data
     *
     * @return void
     */
    protected function printAJAX($data)
    {
        // Move top messages into headers since we print data and die()
        $this->translateTopMessagesToHTTPHeaders();

        $content = json_encode($data);

        header('Content-Type: application/json; charset=UTF-8');
        header('Content-Length: ' . strlen($content));
        header('ETag: ' . md5($content));

        print ($content);
    }

    /**
     * Switch HTTPS options
     *
     * @param boolean $enableHTTPS Value
     *
     * @return void
     */
    protected function switchHTTPS($enableHTTPS)
    {
        $options = array(
            'customer_security',
            'admin_security',
        );

        foreach ($options as $option) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'name'     => $option,
                    'category' => 'Security',
                    'value'    => $enableHTTPS,
                )
            );
        }
    }
}
