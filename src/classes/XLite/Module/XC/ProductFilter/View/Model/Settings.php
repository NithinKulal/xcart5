<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Model;

/**
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        if (('cache_management' === $this->getTarget()
                || ('module' === $this->getTarget()
                    && $this->getModule()
                    && 'XC\ProductFilter' === $this->getModule()->getActualName()
                )
            )
            && \XLite\Core\Config::getInstance()->XC->ProductFilter->attributes_filter_by_category
            && \XLite\Core\Config::getInstance()->XC->ProductFilter->attributes_filter_cache_mode
        ) {
            $result['remove_product_filter_cache'] = new \XLite\View\Button\Tooltip(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL  => 'Remove product filter cache',
                    \XLite\View\Button\Regular::PARAM_ACTION => 'remove_product_filter_cache',
                    \XLite\View\Button\Tooltip::PARAM_SEPARATE_TOOLTIP => static::t('Remove product filter cache tooltip'),
                    \XLite\View\Button\AButton::PARAM_STYLE  => 'action always-enabled'
                )
            );
        }

        return $result;
    }

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
                    static::DEPENDENCY_SHOW => array(
                        'enable_attributes_filter' => array(true),
                    ),
                );
                break;

            case 'attributes_filter_cache_mode':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'attributes_filter_by_category' => array(true),
                        'enable_attributes_filter' => array(true),
                    ),
                );
                break;

            case 'attributes_sorting_type':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'enable_attributes_filter' => array(true),
                    ),
                );
                break;
        }

        return $cell;
    }
}
