<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\Model;

/**
 * Settings dialog model widget
 *
 * @Decorator\Depend ("XC\ProductFilter")
 */
class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        switch ($option->getName()) {
            case 'attributes_filter_by_category':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_HIDE => array(
                        'enable_tags_filter' => array(false),
                        'enable_attributes_filter' => array(false),
                    ),
                );
                break;

            case 'attributes_filter_cache_mode':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'attributes_filter_by_category' => array(true),
                    ),
                    static::DEPENDENCY_HIDE => array(
                        'enable_tags_filter' => array(false),
                        'enable_attributes_filter' => array(false),
                    ),
                );
                break;
        }

        return $cell;
    }
}
