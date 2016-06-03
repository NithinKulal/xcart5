<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Address;

/**
 * Profile abstract form
 */
class Address extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'save';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();
        $addressId = $this->getCurrentForm()->getRequestAddressId();

        if ($addressId) {
            $result['address_id'] = $addressId;
        } else {
            $profileId = $this->getCurrentForm()->getRequestProfileId();
            if ($profileId) {
                $result['profile_id'] = $profileId;
            }
        }

        $result[\XLite\Controller\AController::RETURN_URL] = $this->buildURL('address_book');
        
        return $result;
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'address-form use-inline-error';
    }
}
