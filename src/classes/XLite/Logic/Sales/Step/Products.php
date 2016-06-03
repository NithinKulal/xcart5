<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Sales\Step;

use XLite\Core;
/**
 * Products
 */
class Products extends AStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\Product
     */
    protected function getRepository()
    {
        return Core\Database::getRepo('XLite\Model\Product');
    }

    // }}}
}
