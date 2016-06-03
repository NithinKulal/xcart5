<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\Region
 */
class Region extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget param names
     */
    const PARAM_COUNTRY = 'country';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_COUNTRY => new \XLite\Model\WidgetParam\TypeString('Country', ''),
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'select_region.twig';
    }

    /**
     * Get options to select
     *
     * @return array
     */
    protected function getOptions()
    {
        $result = $this->getParam(static::PARAM_OPTIONS);

        if (!$result) {
            $result = $this->getParam(static::PARAM_COUNTRY)
                ? \XLite\Core\Database::getRepo('\XLite\Model\Region')->findByCountryCode($this->getParam(static::PARAM_COUNTRY))
                : \XLite\Core\Database::getRepo('\XLite\Model\Region')->findAllRegions();
        }

        return $result;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array();
    }
}
