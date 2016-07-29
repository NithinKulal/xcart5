<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\StickyPanel\Product\Admin;

/**
 * Product list sticky panel
 */
abstract class AAdmin extends \XLite\View\StickyPanel\Product\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['bulk_edit'] = $this->getWidget(
            [],
            'XLite\Module\XC\BulkEditing\View\Button\Product'
        );

        return $list;
    }
}
