<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model;

/**
 * XPayments payment processor
 *
 */
class Module extends \XLite\Model\Module implements \XLite\Base\IDecorator
{

    /**
     * If we can proceed with checkout with current cart
     *
     * @return boolean
     */
    public function getDescription()
    {
        $description = parent::getDescription();

        if ('CDev\XPaymentsConnector' == $this->getActualName()) {
            $description = \XLite\Module\CDev\XPaymentsConnector\Main::getDescription();
        }

        return $description;
    }
}
