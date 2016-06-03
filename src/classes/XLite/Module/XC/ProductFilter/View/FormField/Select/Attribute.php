<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\FormField\Select;

/**
 * Attribute options 
 * 
 */
class Attribute extends \XLite\Module\XC\ProductFilter\View\FormField\Select\ACheckboxList
{
    /**
     * Common params
     */
    const PARAM_ATTRIBUTE  = 'attribute';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ATTRIBUTE => new \XLite\Model\WidgetParam\TypeObject(
                'Attribute', null, false, 'XLite\Model\Attribute'
            ),
        );
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array();
    }

    /**
     * Return options list
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        if (empty($list)) {
            foreach ($this->getParam(self::PARAM_ATTRIBUTE)->getAttributeOptions() as $option) {
                $list[$option->getId()] = $option->getName();
            }
        }

        return $list;
    }
}
