<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Currencies list
 */
class Currency extends \XLite\View\FormField\Select\Regular
{
    /**
     * Additional widget param
     */
    const PARAM_USE_CODE_AS_KEY = 'useCodeAsKey';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Currency')->findAllSortedByName() as $currency) {
            $list[$currency->getCurrencyId()] = $this->getOptionName($currency);
        }

        asort($list);

        return $list;
    }

    /**
     * Get options list
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = array();

        if ($this->getParam(self::PARAM_USE_CODE_AS_KEY)) {
            foreach (\XLite\Core\Database::getRepo('XLite\Model\Currency')->findAllSortedByName() as $currency) {
                $list[$currency->getCode()] = $this->getOptionName($currency);
            }

            asort($list);

        } else {
            $list = parent::getOptions();
        }

        return $list;
    }

    /**
     * Returns option name
     *
     * @param \XLite\Model\Currency $currency Currency
     *
     * @return string
     */
    protected function getOptionName($currency)
    {
        return sprintf('%s - %s', $currency->getCode(), $currency->getName());
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_USE_CODE_AS_KEY => new \XLite\Model\WidgetParam\TypeBool('Use currency codes as keys', false),
        );
    }
}
