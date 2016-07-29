<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Address book (at add new card page) 
 */
class AddressBook extends \XLite\Controller\Customer\AddressBook implements \XLite\Base\IDecorator 
{
    /**
     * Save address
     *
     * @return boolean
     */
    protected function doActionSave()
    {
        $result = parent::doActionSave();

        if (
            $result
            && $this->getModelForm()->getModelObject()
            && $this->getModelForm()->getModelObject()->getAddressId()
        ) {

            // New address is not yet saved in profile
            \XLite\Core\Database::getEM()->flush();

            $addresses = $this->getProfile()->getAddresses();

            foreach ($addresses as $address) {
                if ($this->getModelForm()->getModelObject()->getAddressId() == $address->getAddressId()) {
                    $address->setIsBilling(true);
                } else {
                    $address->setIsBilling(false);
                }
            }

            // For those, who doesn't understand from the first time
            $this->getModelForm()->getModelObject()->setIsBilling(true);

            \XLite\Core\Database::getEM()->flush();
        }
        
        return $result;
    }
}
