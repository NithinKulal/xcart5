<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\View;

/**
 * Taxes widget (admin)
 */
class Taxes extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/SalesTax/admin.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/SalesTax/admin.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SalesTax/edit.twig';
    }

    /**
     * Get CSS classes for dialog block
     *
     * @return string
     */
    protected function getDialogCSSClasses()
    {
        $result = 'edit-sales-tax';

        if (\XLite\Core\Config::getInstance()->CDev->SalesTax->ignore_memberships) {
            $result .= ' no-memberships';
        }

        if ('P' != \XLite\Core\Config::getInstance()->CDev->SalesTax->taxableBase) {
            $result .= ' no-taxbase';
        }

        return $result;
    }

    /**
     * Return true if list of tax rates on shipping cost is displayed
     *
     * @return boolean
     */
    protected function isShippingRatesDisplayed()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->{\XLite\Module\CDev\SalesTax\Model\Repo\Tax\Rate::PARAM_TAXABLE_BASE}
            = \XLite\Module\CDev\SalesTax\Model\Tax\Rate::TAXBASE_SHIPPING;

        $ratesCount = \XLite\Core\Database::getRepo('XLite\Module\CDev\SalesTax\Model\Tax\Rate')->search($cnd, true);

        return 0 < $ratesCount;
    }
}
