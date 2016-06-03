<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Order\Admin;

/**
 * Abstract order panel for admin interface
 */
abstract class AAdmin extends \XLite\View\StickyPanel\Order\AOrder
{
	/**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            array(),
            'XLite\View\Button\ItemsExport\Order'
        );
        return $list;
    }
}

