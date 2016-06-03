<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View\Shipping;

/**
 * Online shipping carriers list
 */
class OnlineList extends \XLite\View\Shipping\OnlineList implements \XLite\Base\IDecorator
{
    /**
     * Returns online shipping methods (carriers)
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    protected function getMethods()
    {
        $list = parent::getMethods();

        return array_filter(
            $list,
            function($method) {
                return $method->getProcessor() !== 'Pilibaba';
            }
        );

        return $list;
    }
}
