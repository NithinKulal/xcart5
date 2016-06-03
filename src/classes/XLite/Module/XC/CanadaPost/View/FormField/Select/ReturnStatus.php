<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\FormField\Select;

/**
 * Post return status selector
 */
class ReturnStatus extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget param names
     */
    const PARAM_ALL_OPTION = 'allOption';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/CanadaPost/form_field/return_status.js';

        return $list;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'capost-return-status';

        if ($this->isAllParamOptionEnabled()) {
            $classes[] = 'no-disable';
        }

        return $classes;
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
            static::PARAM_ALL_OPTION  => new \XLite\Model\WidgetParam\TypeBool(
                'Show "All statuses" option', false, false
            ),
        );
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = $this->getDefaultOptions();

        if ($this->isAllParamOptionEnabled()) {
            // Add new element to the top of the list
            $list = array_merge(
                array('' => static::t('All statuses')),
                $list
            );
        }

        return $list;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = \XLite\Module\XC\CanadaPost\Model\ProductsReturn::getAllowedStatuses();

        foreach ($list as $k => $v) {
            $list[$k] = static::t($v);
        }

        return $list;
    }

    /**
     * Check - is PARAM_ALL_OPTION option is enabled
     *
     * @return boolean
     */
    protected function isAllParamOptionEnabled()
    {
        return $this->getParam(static::PARAM_ALL_OPTION);
    }
}
