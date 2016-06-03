<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Controller\Admin;

/**
 * Main page controller
 */
class Main extends \XLite\Controller\Admin\Main implements \XLite\Base\IDecorator
{
    /**
     * Return 'Taxes' url
     *
     * @return string
     */
    public function getTaxesURL()
    {
        return $this->buildURL('sales_tax');
    }
}
