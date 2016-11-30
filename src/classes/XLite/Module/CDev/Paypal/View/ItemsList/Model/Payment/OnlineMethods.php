<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\ItemsList\Model\Payment;

/**
 * Methods items list
 */
class OnlineMethods extends \XLite\View\ItemsList\Model\Payment\OnlineMethods implements \XLite\Base\IDecorator
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Payment\Method::P_EXCLUDED_SERVICE_NAMES} = [\XLite\Module\CDev\Paypal\Main::PP_METHOD_PC];

        return $result;
    }
}