<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SurchargeInfo;

/**
 * ASurchargeInfo
 */
abstract class ASurchargeInfo extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_SURCHARGE = 'surcharge';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SURCHARGE => new \XLite\Model\WidgetParam\TypeObject('Surcharge', null),
        );
    }

    /**
     * Get surcharge object
     * 
     * @return \XLite\Model\Base\Surcharge
     */
    protected function getSurcharge()
    {
        $surchargeParam = $this->getParam(static::PARAM_SURCHARGE);

        return isset($surchargeParam['object'])
            ? $surchargeParam['object']
            : null;
    }
}
