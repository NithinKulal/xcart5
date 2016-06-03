<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\FormField\Select;

/**
 * On-demand services selector
 */
class OnDemandServices extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = array();

        /** @var \XLite\Model\Repo\Shipping\Method $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
        $methods = $repo->findMethodsByProcessor('auctionInc', false);

        foreach ($methods as $method) {
            if (preg_match('/^(DHL|FEDEX|UPS|USPS)/', $method->getCode())) {
                $result[$method->getCode()] = $method->getName();
            }
        }

        return $result;
    }
}
