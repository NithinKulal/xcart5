<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Completed;

/**
 * EntriesList
 *
 * @ListChild (list="admin.center", weight="300", zone="admin")
 */
class EntriesList extends \XLite\View\Upgrade\Step\Completed\ACompleted
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/entries_list';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.entries_list';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Updated components';
    }
}
