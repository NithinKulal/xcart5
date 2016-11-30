<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\View;

/**
 * Contact us widget
 *
 * @ListChild (list="center", zone="customer")
 */
class ContactUs extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('contact_us'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/ContactUs/contact_us/style.css';

        return $list;
    }

    /**
     * Return captcha
     *
     * @return string
     */
    protected function getCaptcha()
    {
        return \XLite\Module\CDev\ContactUs\Core\ReCaptcha::getInstance()->getWidget();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/ContactUs/contact_us/body.twig';
    }

}
