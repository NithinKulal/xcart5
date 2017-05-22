<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Form\ItemsList\Messages\Admin;

/**
 * Admin order messages (search)
 */
class Search extends \XLite\View\Form\AForm
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'search';
    }
}