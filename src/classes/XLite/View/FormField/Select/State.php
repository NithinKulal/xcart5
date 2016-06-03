<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\State
 */
class State extends \XLite\View\FormField\Select\Regular
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
        return 'select_state.twig';
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $result = $this->getParam(static::PARAM_OPTIONS);

        if (!$result) {
            if ($this->getParam(static::PARAM_COUNTRY)) {
                $result = \XLite\Core\Database::getRepo('\XLite\Model\State')->findByCountryCodeGroupedByRegion(
                    $this->getParam(static::PARAM_COUNTRY)
                );
            }else{
                $result = \XLite\Core\Database::getRepo('\XLite\Model\State')->findAllStatesGrouped();
            }
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
