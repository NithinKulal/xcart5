<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\ItemsList\PinCode\Customer;

/**
 * Account pin codes based on orders
 *
 */
class AccountPinCodes extends \XLite\View\ItemsList\AItemsList
{

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/PINCodes/accountPinCodes/items_list.js';
        
        return $list;
    }   

    /**
     * Returns a list of CSS classes (separated with a space character) to be attached to the items list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' items-list-pin-codes';
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return '../modules/CDev/PINCodes/accountPinCodes';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Customer\Order\Search';
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->hasResults();
    }

    /**
     * Get empty list directory
     *
     * @return boolean
     */
    protected function getEmptyListDir()
    {
        return $this->getPageBodyDir();
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->user = \XLite\Core\Auth::getInstance()->getProfile();

        return \XLite\Core\Database::getRepo('XLite\Model\Order')->searchWithPinCodes($cnd, $countOnly);
    }
}
