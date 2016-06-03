<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isIncludeController()) {
            $list[] = 'modules/CDev/GoogleAnalytics/drupal.js';

        } else {
            $list[] = 'modules/CDev/GoogleAnalytics/common.js';
        }

        return $list;
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        if ('module' == $this->getTarget()) {
            $list[] = 'modules/CDev/GoogleAnalytics/style.css';
        }

        return $list;
    }

    /**
     * Display widget as Standalone-specific
     *
     * @return boolean
     */
    protected function isIncludeController()
    {
        return \XLite\Core\Operator::isClassExists('\XLite\Module\CDev\DrupalConnector\Handler')
            && \XLite\Module\CDev\DrupalConnector\Handler::getInstance()->checkCurrentCMS()
            && !$this->useUniversalAnalytics()
            && function_exists('googleanalytics_theme');

    }

    /**
     * Use Universal Analytics
     *
     * @return boolean
     */
    protected function useUniversalAnalytics()
    {
        return 'U' == \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_code_version;
    }
}
