<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Common surcharge
 */
class Surcharge extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_SURCHARGE = 'surcharge';
    const PARAM_CURRENCY  = 'currency';
    const PARAM_PURPOSE   = 'purpose';


    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'common/surcharge.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/surcharge.twig';
    }

    /**
     * Return surcharge
     *
     * @return float
     */
    protected function getSurcharge()
    {
        return $this->getParam(self::PARAM_SURCHARGE);
    }

    /**
     * Return currency
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        return $this->getParam(self::PARAM_CURRENCY);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SURCHARGE => new \XLite\Model\WidgetParam\TypeFloat('Surcharge', null),
            self::PARAM_CURRENCY  => new \XLite\Model\WidgetParam\TypeObject(
                'Currency',
                \XLite::getInstance()->getCurrency(),
                false,
                'XLite\Model\Currency'
            ),
            self::PARAM_PURPOSE   => new \XLite\Model\WidgetParam\TypeString('Purpose', null),
        );
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !is_null($this->getParam(self::PARAM_SURCHARGE));
    }
}
