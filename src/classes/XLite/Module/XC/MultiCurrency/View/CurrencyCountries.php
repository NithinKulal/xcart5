<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View;

/**
 * Currency countries page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class CurrencyCountries extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'currency_countries';

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

        $return[] = $this->getDir() . LC_DS . 'style.less';

        return $return;
    }

    /**
     * Get directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules' . LC_DS . 'XC' . LC_DS . 'MultiCurrency' . LC_DS . 'currency_countries';
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
     * Get currency name
     *
     * @return string
     *
     * @return string
     */
    protected function getCurrencyName()
    {
        $return = '';

        $activeCurrency = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')->find(
            \XLite\Core\Request::getInstance()->active_currency_id
        );

        if (isset($activeCurrency)) {
            $return = $activeCurrency->getCurrency()->getName();
        }

        return $return;
    }
}
