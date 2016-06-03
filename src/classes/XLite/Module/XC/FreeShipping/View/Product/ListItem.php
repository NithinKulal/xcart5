<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\View\Product;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/FreeShipping/label/style.css';

        return $list;
    }

    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return parent::getLabels() + \XLite\Module\XC\FreeShipping\Core\Labels::getLabel($this->getProduct());
    }
}