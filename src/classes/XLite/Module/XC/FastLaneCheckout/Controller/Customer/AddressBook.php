<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\Controller\Customer;

/**
 * Addresses management controller
 */
abstract class AddressBook extends \XLite\Controller\Customer\AddressBook implements \XLite\Base\IDecorator
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return ($this->isAJAX() && \XLite\Core\Request::getInstance()->widget_title)
            ? \XLite\Core\Request::getInstance()->widget_title 
            : parent::getTitle();
    }
    
    protected function prepareRequestForActionSelect()
    {
        \XLite\Core\Request::getInstance()->addressId = $this->getAddress()->getUniqueIdentifier();
    }

    protected function doActionSaveAndApply()
    {
        $result = $this->getModelForm()->performAction('update');

        $atype = \XLite\Core\Request::getInstance()->atype;

        if ($result && $this->getAddress()->isPersistent() && $atype) {
            $sameAddressState = \XLite\Core\Session::getInstance()->same_address !== null 
                ? \XLite\Core\Session::getInstance()->same_address
                : $this->getCart()->getProfile()->isEqualAddress();

            $preserveSameAddress = ($sameAddressState && $atype == 's');

            $this->selectCartAddress($atype, $this->getAddress()->getUniqueIdentifier(), false, $preserveSameAddress);
        } 

        $this->setReturnURL($this->buildURL('checkout'));

        return $result;
    }
}
