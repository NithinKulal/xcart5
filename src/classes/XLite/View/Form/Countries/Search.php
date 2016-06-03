<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Countries;

/**
 * Countries search form
 */
class Search extends \XLite\View\Form\ItemsList\AItemsListSearch
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'countries';
    }
}
