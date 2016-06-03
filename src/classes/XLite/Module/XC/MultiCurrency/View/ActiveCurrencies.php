<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View;

/**
 * Attribute page view
 */
class ActiveCurrencies extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'currencies';

        return $return;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $return = parent::getJSFiles();

        $return[] = $this->getDir() . LC_DS . 'script.js';

        return $return;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $return = parent::getCSSFiles();

        $return[] = $this->getDir() . LC_DS . 'style.css';

        return $return;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . LC_DS . 'body.twig';
    }

    /**
     * Get directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules' . LC_DS . 'XC' . LC_DS . 'MultiCurrency' . LC_DS . 'multi_currency';
    }

    /**
     * Get last update date
     *
     * @return string
     */
    protected function getLastUpdateDate()
    {
        $lastUpdateDate = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
            ->getLastRateUpdateDate();

        return \XLite\Core\Converter::formatTime($lastUpdateDate);
    }
}