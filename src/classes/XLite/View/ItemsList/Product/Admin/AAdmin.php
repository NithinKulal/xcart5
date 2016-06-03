<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Admin;

/**
 * AAdmin
 */
abstract class AAdmin extends \XLite\View\ItemsList\Product\AProduct
{
    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.admin';
    }

    /**
     * getDisplayMode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return $this->getChangeabilityType() . '.' . $this->getDisplayStyle();
    }

    /**
     * getDisplayStyle
     *
     * @return string
     */
    protected function getChangeabilityType()
    {
        return 'modify';
    }

    /**
     * getDisplayStyle
     *
     * @return string
     */
    protected function getDisplayStyle()
    {
        return 'common';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }
}
