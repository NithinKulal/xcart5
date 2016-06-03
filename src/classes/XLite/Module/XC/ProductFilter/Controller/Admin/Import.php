<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Controller\Admin;

/**
 * Import page controller
 */
class Import extends \XLite\Controller\Admin\Import implements \XLite\Base\IDecorator
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (\XLite\Core\Request::getInstance()->completed) {
            \XLite\Core\Database::getRepo('XLite\Model\Category')->removeProductFilterCache();
        }

        parent::doNoAction();
    }
}