<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Controller\Admin;

abstract class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * return string
     */
    public function getConciergeCategory()
    {
        return 'Product';
    }
}
