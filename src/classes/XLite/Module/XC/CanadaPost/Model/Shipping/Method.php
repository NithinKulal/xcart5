<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Shipping;

/**
 * Shipping method model
 */
class Method extends \XLite\Model\Shipping\Method implements \XLite\Base\IDecorator
{
    /**
     * get Shipping Method name
     * for Canada Post add '(Canada Post)' (except admin area, shipping methods page)
     *
     * @return string
     */
    public function getName()
    {
        $name = parent::getName();

        if ('capost' == $this->getProcessor() && !(\XLite::isAdminZone() && \XLite::getController() instanceof \XLite\Controller\Admin\ShippingMethods)) {

            $name = 'Canada Post ' . $name;
        }

        return $name;
    }
}
