<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Pager\Message\Admin;

/**
 * Pager for messages list
 */
class All extends \XLite\View\Pager\Admin\Model\Table
{

    /**
     * @inheritdoc
     */
    protected function getListName()
    {
        return 'order.messages.pager';
    }

}
