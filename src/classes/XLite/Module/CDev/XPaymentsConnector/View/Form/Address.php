<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Form;

/**
 * Profile abstract form
 */
class Address extends \XLite\View\Form\Address\Address implements \XLite\Base\IDecorator 
{
    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();

        if (\XLite\Core\Request::getInstance()->zero_auth) {
            $result[\XLite\Controller\AController::RETURN_URL] = $this->buildURL('add_new_card');
        }

        return $result;
    }
}
