<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\View;

/**
 * SubscribeBlock
 *
 * TODO: Make placeholder for subscribed user/profile
 *
 * @ListChild (list="layout.main.footer", weight="50")
 */
class SubscribeBlock extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/NewsletterSubscriptions/form/subscribe.twig';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = array(
            'file'  => 'modules/XC/NewsletterSubscriptions/form/styles.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/NewsletterSubscriptions/form/subscribe.js';

        return $list;
    }

    /**
     * Get successful subscription message
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        return static::t(
            'Thank you for subscribing to the newsletter! We hope you enjoy shopping at {{companyName}}',
            array(
                'companyName' => $this->getCompanyName()
            )
        );
    }

    /**
     * Get failed subscription message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return static::t(
            'Can\'t subscribe you right now. Try later'
        );
    }

    /**
     * Get form label
     *
     * @return string
     */
    public function getFormLabel()
    {
        return static::t(
            'Sign up for {{companyName}} news',
            array(
                'companyName' => $this->getCompanyName()
            )
        );
    }

    /**
     * Check if form input is field only
     *
     * @return boolean
     */
    public function isFieldOnly()
    {
        return true;
    }

    /**
     * Get failed subscription message
     *
     * @return string
     */
    protected function getCompanyName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Widget visibility
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible()
            && !in_array($this->getTarget(), ['checkout', 'checkoutPayment', 'checkoutFailed']);
    }
}
