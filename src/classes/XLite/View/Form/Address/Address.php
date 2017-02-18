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
    const PARAM_FORM_RETURN_URL = 'returnURL';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_FORM_RETURN_URL => new \XLite\Model\WidgetParam\TypeString(
                'Return url', $this->getDefaultReturnURL()
            ),
        );
    }

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
        return \XLite\Core\Request::getInstance()->requestedAction ?: 'save';
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

        if (\XLite\Core\Request::getInstance()->atype) {
            $result['atype'] = \XLite\Core\Request::getInstance()->atype;
        }

        $result['returnURL'] = $this->getParam(static::PARAM_FORM_RETURN_URL) ?: $this->getDefaultReturnURL();
        
        return $result;
    }
    
    /**
     * Returns default returnURL param
     * 
     * @return string
     */
    protected function getDefaultReturnURL()
    {
        return $this->buildURL('address_book');
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
