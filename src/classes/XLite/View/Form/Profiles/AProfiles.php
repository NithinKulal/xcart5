<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Profiles;

/**
 * Profiles abstract form
 */
abstract class AProfiles extends \XLite\View\Form\ItemsList\AItemsListSearch
{

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'profiles-form';
    }
}
