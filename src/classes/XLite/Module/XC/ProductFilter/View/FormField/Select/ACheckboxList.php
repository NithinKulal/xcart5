<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\FormField\Select;

/**
 * Checkbox list (abstract) 
 * 
 */
abstract class ACheckboxList extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
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
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductFilter/form_field/select/checkbox_list';
    }

    /**
     * Get label container class
     *
     * @return string
     */
    protected function getLabelContainerClass()
    {
        return parent::getLabelContainerClass()
            . $this->getCommonClass();
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass()
            . ' checkbox-list'
            . $this->getCommonClass();
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getCommonClass()
    {
        $class = ' collapsible';
        $value = $this->getValue();
        if (
            !$value
            || !is_array($value)
            || isset($value[0])
        ) {
            $class .= ' collapsed';
        }

        return $class;
    }
}
