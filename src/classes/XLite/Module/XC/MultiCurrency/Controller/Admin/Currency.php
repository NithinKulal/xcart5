<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Controller\Admin;

/**
 * Currency management page controller
 */
class Currency extends \XLite\Controller\Admin\Currency implements \XLite\Base\IDecorator
{
    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        \XLite\Core\Operator::redirect($this->buildURL('currencies'), true);
    }
}