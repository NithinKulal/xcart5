<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Controller\Admin;

/**
 * Attribute page controller
 */
class Attribute extends \XLite\Controller\Admin\Attribute implements \XLite\Base\IDecorator
{

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Category')->removeProductFilterCache();

        parent::doActionUpdate();
    }
}