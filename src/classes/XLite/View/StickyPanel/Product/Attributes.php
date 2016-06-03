<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Product;

/**
 * Attributes
 */
class Attributes extends \XLite\View\StickyPanel\Product\AProduct
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = array();
        
        if ('global' == \XLite\Core\Request::getInstance()->spage) {
            $list['saveMode'] = $this->getWidget(
                array(
                    'fieldOnly'  => true,
                    'fieldName'  => 'save_mode',
                    'attributes' => array(
                        'disabled' => 'disabled',
                        'class'    => 'not-significant',
                    ),
                ),
                'XLite\View\FormField\Select\AttributeSaveMode'
            );
            $list['saveModeTooltips'] = $this->getWidget(
                array(),
                'XLite\View\Product\Details\Admin\SaveModeTooltips'
            );
        }

        return array_merge(parent::defineButtons(), $list);
    }
}
