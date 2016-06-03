<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Cart\Item;

/**
 * Abstract cart item form
 */
abstract class AItem extends \XLite\View\Form\AForm
{
    /**
     * Widget paramater names
     */
    const PARAM_ITEM    = 'item';


    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'cart';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_ITEM] = new \XLite\Model\WidgetParam\TypeObject(
            'Cart item', null, false, '\XLite\Model\OrderItem'
        );
    }

    /**
     * Called before the includeCompiledFile()
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue(
            array(
                'cart_id' => $this->getParam(self::PARAM_ITEM)->getItemId()
            )
        );
    }
}
