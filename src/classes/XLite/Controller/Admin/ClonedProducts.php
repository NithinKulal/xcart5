<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Cloned products controller
 */
class ClonedProducts extends \XLite\Controller\Admin\ProductList
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Cloned products');
    }

    /**
     * Preprocessor for no-action ren
     *
     * @return void
     */
    protected function doNoAction()
    {
        $itemList = new \XLite\View\ItemsList\Model\Product\Admin\Cloned;
        if (0 == \XLite\Core\Database::getRepo('\XLite\Model\Product')->search($itemList->getSearchCondition(), true)) {
            $this->redirect($this->buildUrl('product_list', null, array('mode' => 'search')));
        }
    }
}
