<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Pre-checkout signin widget
 */
class Signin extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'checkout';

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/signin.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     * FIXME - decompose these files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/login.js';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'signin';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Defines the store name
     *
     * @return string
     */
    protected function getStoreName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Defines the anonymous title box
     *
     * @return string
     */
    protected function getSigninAnonymousTitle()
    {
        return static::t('Go to checkout as a New customer');
    }
}
