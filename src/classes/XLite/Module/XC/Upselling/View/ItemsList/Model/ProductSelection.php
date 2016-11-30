<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\ItemsList\Model;

/**
 * Product selection itemlist model
 */
class ProductSelection extends \XLite\View\ItemsList\Model\ProductSelection
{
    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\Upselling\View\StickyPanel\ItemsList\ProductSelection';
    }

    /**
     * Return wrapper form options
     *
     * @return string
     */
    protected function getFormOptions()
    {
        $options = parent::getFormOptions();

        $options['class'] = '\XLite\Module\XC\Upselling\View\Form\ItemsList\ProductSelection\Table';

        return $options;
    }
}