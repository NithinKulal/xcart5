<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Controller\Admin;

/**
 * Products list controller
 */
class ProductList extends \XLite\Controller\Admin\ProductList implements \XLite\Base\IDecorator
{
    /**
     * Do action update
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if (\XLite\Core\Request::getInstance()->delete) {
            \XLite\Core\Database::getRepo('XLite\Model\Category')->removeProductFilterCache();
        }

        parent::doActionUpdate();
    }

    /**
     * Do action delete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Category')->removeProductFilterCache();

        parent::doActionDelete();
    }
}